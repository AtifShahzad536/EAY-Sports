<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\DealerOrder;
use App\Models\DealerOrderItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SavedDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a list of order records for the authenticated customer.
     */
    public function index(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Smart dynamic query with relational eager loading of ordered items
        $orders = Order::with('items.savedDesign')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => '#'.(1000 + $order->id),
                    'real_id' => $order->id,
                    'date' => $order->created_at->format('Y-m-d'),
                    'items_count' => $order->items->sum('quantity'),
                    'price' => (float) $order->total,
                    'status' => $order->status,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->product_name,
                            'price' => (float) $item->price,
                            'qty' => $item->quantity,
                            'size' => $item->size,
                            'color' => $item->color,
                            'custom_name' => $item->custom_name,
                            'custom_number' => $item->custom_number,
                            'image' => $item->image,
                            'saved_design' => $item->savedDesign ? [
                                'id' => $item->savedDesign->id,
                                'name' => $item->savedDesign->name,
                                'design_data' => $item->savedDesign->design_data,
                            ] : null,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Store a newly created order record (Checkout processing).
     * Validation is handled by CheckoutRequest Form Request class.
     */
    public function store(CheckoutRequest $request)
    {
        // Smart MVC Best Practice: Wrap order writes inside database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $isDealer = $user && $user->role === 'dealer';

            // Preemptive Stock Verification
            foreach ($request->items as $cartItem) {
                $rawProductId = isset($cartItem['id']) && is_numeric($cartItem['id']) ? (int) $cartItem['id'] : null;
                if ($rawProductId) {
                    $product = Product::find($rawProductId);
                    if ($product && $product->track_stock) {
                        $requestedQty = (int) $cartItem['qty'];
                        if ($product->stock <= 0) {
                            throw new \Exception("Product '{$product->title}' is out of stock.");
                        }
                        if ($product->stock < $requestedQty) {
                            throw new \Exception("Product '{$product->title}' has insufficient stock. Only {$product->stock} units are available.");
                        }
                    }
                }
            }

            // Save custom design if design_data is provided
            $savedDesignId = null;
            if ($request->filled('design_data')) {
                $design = SavedDesign::create([
                    'user_id' => $user->id,
                    'name' => 'Order Design - '.($request->model_name ?? 'Jersey').' ('.now()->format('Y-m-d H:i').')',
                    'model_name' => $request->model_name ?? 'jersey',
                    'design_data' => $request->design_data,
                    'thumbnail' => $request->thumbnail ?? 'https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=600&h=400&fit=crop&q=80',
                ]);
                $savedDesignId = $design->id;
            }

            // Coupon Processing
            $couponId = null;
            $couponCode = null;
            $discountAmount = 0;

            if ($request->filled('coupon_code')) {
                $code = strtoupper(trim($request->coupon_code));
                $coupon = Coupon::where('code', $code)->first();

                if (! $coupon) {
                    throw new \Exception('Invalid coupon code.');
                }

                $validationResult = $coupon->validateForUser($user, $request->subtotal);
                if ($validationResult !== true) {
                    throw new \Exception($validationResult);
                }

                $couponId = $coupon->id;
                $couponCode = $coupon->code;
                $discountAmount = $coupon->calculateDiscount($request->subtotal);
            }

            // Recalculate server total
            $serverTotal = max(0, $request->subtotal - $discountAmount + $request->shipping + $request->tax);

            if ($isDealer) {
                // Route to B2B Dealer Order
                $order = DealerOrder::create([
                    'dealer_id' => $user->id,
                    'total_price' => $serverTotal,
                    'status' => 'pending', // B2B orders default to pending
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'country' => $request->country ?? 'USA',
                    'notes' => $request->notes ?? 'Placed via Storefront Checkout',
                ]);

                // Save Coupon Usage if any
                if ($couponId) {
                    CouponUse::create([
                        'coupon_id' => $couponId,
                        'user_id' => $user->id,
                        'order_id' => null, // DealerOrder doesn't reference standard order_id
                        'discount_amount' => $discountAmount,
                    ]);

                    // Increment coupon used_count
                    $coupon->increment('used_count');
                }

                foreach ($request->items as $cartItem) {
                    // Verify product exists in DB before inserting — avoids FK constraint violations
                    $rawProductId = isset($cartItem['id']) && is_numeric($cartItem['id']) ? (int) $cartItem['id'] : null;
                    $productId = ($rawProductId && Product::where('id', $rawProductId)->exists()) ? $rawProductId : null;

                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $product->track_stock) {
                            $product->decrement('stock', (int) $cartItem['qty']);
                        }
                    }

                    // Extract specifications
                    $size = isset($cartItem['size']) ? $cartItem['size'] : 'M';
                    $customName = isset($cartItem['customName']) ? $cartItem['customName'] : null;
                    $customNumber = isset($cartItem['customNumber']) ? $cartItem['customNumber'] : null;

                    if (isset($cartItem['tags']) && is_array($cartItem['tags'])) {
                        foreach ($cartItem['tags'] as $tag) {
                            if (str_starts_with($tag['label'], 'Size: ')) {
                                $size = str_replace('Size: ', '', $tag['label']);
                            } elseif (str_starts_with($tag['label'], 'Name: ')) {
                                $customName = str_replace('Name: ', '', $tag['label']);
                            } elseif (str_starts_with($tag['label'], 'Number: ')) {
                                $customNumber = str_replace('Number: ', '', $tag['label']);
                            }
                        }
                    }

                    DealerOrderItem::create([
                        'dealer_order_id' => $order->id,
                        'product_id' => $productId,
                        'saved_design_id' => $savedDesignId ?: ($cartItem['saved_design_id'] ?? null),
                        'product_name' => $cartItem['name'],
                        'qty' => $cartItem['qty'],
                        'size' => $size,
                        'custom_name' => $customName,
                        'custom_number' => $customNumber,
                        'unit_price' => $cartItem['price'],
                        'image' => isset($cartItem['image']) ? $cartItem['image'] : ($request->thumbnail ?? null),
                    ]);
                }

                DB::commit();

                // Send email notifications
                try {
                    // Send to admin
                    $adminEmails = Admin::pluck('email')->filter()->toArray();
                    if (empty($adminEmails)) {
                        $adminEmails = ['admin@eaysports.com'];
                    }
                    Mail::send('emails.dealer-order-placed-admin', ['order' => $order, 'dealer' => $user], function ($message) use ($adminEmails, $order) {
                        $message->to($adminEmails)
                            ->subject('New Dealer Wholesale Order Received: #B2B-'.$order->id);
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send admin wholesale order notification: '.$e->getMessage());
                }

                try {
                    // Send to dealer
                    Mail::send('emails.dealer-order-placed-customer', ['order' => $order, 'dealer' => $user], function ($message) use ($user, $order) {
                        $message->to($user->email)
                            ->subject('Your Wholesale Order Placed Successfully: #B2B-'.$order->id);
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send dealer wholesale order notification: '.$e->getMessage());
                }

                session(['placed_dealer_order_id' => $order->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Wholesale order placed successfully!',
                    'order_id' => '#B2B-'.$order->id,
                    'id' => $order->id,
                ]);

            } else {
                // Route to Retail Customer Order
                $order = Order::create([
                    'user_id' => Auth::id(), // Associated with user if logged in
                    'billing_name' => $request->billing_name,
                    'billing_email' => $request->billing_email,
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'phone' => $request->phone,
                    'payment_method' => $request->payment_method ?? 'Cash on Delivery',
                    'notes' => $request->notes,
                    'subtotal' => $request->subtotal,
                    'shipping' => $request->shipping,
                    'tax' => $request->tax,
                    'total' => $serverTotal,
                    'status' => 'Processing',
                    'coupon_id' => $couponId,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
                ]);

                // Save Coupon Usage
                if ($couponId) {
                    CouponUse::create([
                        'coupon_id' => $couponId,
                        'user_id' => Auth::id(),
                        'order_id' => $order->id,
                        'discount_amount' => $discountAmount,
                    ]);

                    // Increment coupon used_count
                    $coupon->increment('used_count');
                }

                foreach ($request->items as $cartItem) {
                    // Verify product exists in DB before inserting — avoids FK constraint violations
                    $rawProductId = isset($cartItem['id']) && is_numeric($cartItem['id']) ? (int) $cartItem['id'] : null;
                    $productId = ($rawProductId && Product::where('id', $rawProductId)->exists()) ? $rawProductId : null;

                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $product->track_stock) {
                            $product->decrement('stock', (int) $cartItem['qty']);
                        }
                    }

                    $size = isset($cartItem['size']) ? $cartItem['size'] : null;
                    $color = isset($cartItem['color']) ? $cartItem['color'] : null;
                    $customName = isset($cartItem['customName']) ? $cartItem['customName'] : null;
                    $customNumber = isset($cartItem['customNumber']) ? $cartItem['customNumber'] : null;

                    if (isset($cartItem['tags']) && is_array($cartItem['tags'])) {
                        foreach ($cartItem['tags'] as $tag) {
                            if (str_starts_with($tag['label'], 'Size: ')) {
                                $size = str_replace('Size: ', '', $tag['label']);
                            } elseif (str_starts_with($tag['label'], 'Color: ')) {
                                $color = str_replace('Color: ', '', $tag['label']);
                            } elseif (str_starts_with($tag['label'], 'Name: ')) {
                                $customName = str_replace('Name: ', '', $tag['label']);
                            } elseif (str_starts_with($tag['label'], 'Number: ')) {
                                $customNumber = str_replace('Number: ', '', $tag['label']);
                            }
                        }
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'saved_design_id' => $savedDesignId ?: ($cartItem['saved_design_id'] ?? null),
                        'product_name' => $cartItem['name'],
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['qty'],
                        'size' => $size,
                        'color' => $color,
                        'custom_name' => $customName,
                        'custom_number' => $customNumber,
                        'image' => isset($cartItem['image']) ? $cartItem['image'] : ($request->thumbnail ?? null),
                    ]);
                }

                DB::commit();

                // Load items for mail rendering
                $order->load('items');

                // Send email notifications
                try {
                    // Send to admin
                    $adminEmails = Admin::pluck('email')->filter()->toArray();
                    if (empty($adminEmails)) {
                        $adminEmails = ['admin@eaysports.com'];
                    }
                    Mail::send('emails.order-placed-admin', ['order' => $order], function ($message) use ($adminEmails, $order) {
                        $message->to($adminEmails)
                            ->subject('New Storefront Order Received: #'.(1000 + $order->id));
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send admin retail order notification: '.$e->getMessage());
                }

                try {
                    // Send to customer
                    Mail::send('emails.order-placed-customer', ['order' => $order], function ($message) use ($order) {
                        $message->to($order->billing_email)
                            ->subject('Thank You for Your Order: #'.(1000 + $order->id));
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send customer retail order confirmation email: '.$e->getMessage());
                }

                session(['placed_order_id' => $order->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => '#'.(1000 + $order->id),
                    'id' => $order->id,
                ]);
            }

        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields correctly.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the full error server-side — never expose raw SQL/stack traces to users
            Log::error('Order placement failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'We could not process your order. Please try again or contact support.',
            ], 400);
        }
    }

    /**
     * Display the order success / receipt page.
     */
    public function successPage($type, $id)
    {
        $sessionPlacedId = ($type === 'dealer') ? session('placed_dealer_order_id') : session('placed_order_id');
        $isAuthorized = false;
        $order = null;

        if ($type === 'dealer') {
            $order = DealerOrder::with('items')->find($id);
            if ($order) {
                if ($sessionPlacedId && (int) $sessionPlacedId === (int) $order->id) {
                    $isAuthorized = true;
                } elseif (Auth::check() && $order->dealer_id === Auth::id()) {
                    $isAuthorized = true;
                }
            }
        } else {
            $order = Order::with('items.product')->find($id);
            if ($order) {
                if ($sessionPlacedId && (int) $sessionPlacedId === (int) $order->id) {
                    $isAuthorized = true;
                } elseif (Auth::check() && $order->user_id === Auth::id()) {
                    $isAuthorized = true;
                }
            }
        }

        if (! $isAuthorized) {
            if (! Auth::check()) {
                return redirect()->route('auth')->with('error', 'Please login to view receipt.');
            }
            abort(403, 'Unauthorized access to order.');
        }

        $user = Auth::user();

        if ($type === 'dealer') {
            // Map order data
            $mappedOrder = [
                'order_id' => '#B2B-'.$order->id,
                'type' => 'dealer',
                'date' => $order->created_at->format('M d, Y h:i A'),
                'billing_name' => ($user ? ($user->name.' '.$user->last_name) : 'Wholesale Partner'),
                'billing_email' => ($user ? $user->email : ''),
                'shipping_address' => $order->shipping_address,
                'city' => $order->city,
                'zip_code' => '',
                'phone' => ($user ? ($user->phone ?? '') : ''),
                'payment_method' => 'Cash on Delivery',
                'notes' => $order->notes,
                'subtotal' => (float) $order->total_price,
                'shipping' => 0.0,
                'tax' => 0.0,
                'discount' => 0.0,
                'total' => (float) $order->total_price,
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'qty' => $item->qty,
                        'price' => (float) $item->unit_price,
                        'size' => $item->size,
                        'custom_name' => $item->custom_name,
                        'custom_number' => $item->custom_number,
                        'image' => $item->image,
                    ];
                }),
            ];
        } else {
            // Map order data
            $mappedOrder = [
                'order_id' => '#'.(1000 + $order->id),
                'type' => 'retail',
                'date' => $order->created_at->format('M d, Y h:i A'),
                'billing_name' => $order->billing_name,
                'billing_email' => $order->billing_email,
                'shipping_address' => $order->shipping_address,
                'city' => $order->city,
                'zip_code' => $order->zip_code,
                'phone' => $order->phone,
                'payment_method' => $order->payment_method,
                'notes' => $order->notes,
                'subtotal' => (float) $order->subtotal,
                'shipping' => (float) $order->shipping,
                'tax' => (float) $order->tax,
                'discount' => (float) $order->discount_amount,
                'coupon_code' => $order->coupon_code,
                'total' => (float) $order->total,
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'qty' => $item->quantity,
                        'price' => (float) $item->price,
                        'size' => $item->size,
                        'color' => $item->color,
                        'custom_name' => $item->custom_name,
                        'custom_number' => $item->custom_number,
                        'image' => $item->image,
                        'show_price' => $item->product ? ($item->product->show_price !== false) : true,
                    ];
                }),
            ];
        }

        return Inertia::render('CheckoutSuccess', ['order' => $mappedOrder]);
    }

    /**
     * Upload custom decal/logo image.
     */
    public function uploadDecal(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('order_decals', 'public');

            return response()->json([
                'success' => true,
                'url' => asset('storage/'.$path),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    /**
     * Upload custom 3D model file (.glb/.gltf).
     */
    public function uploadModel(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store model in imported_models folder inside public storage
            $path = $file->store('imported_models', 'public');

            return response()->json([
                'success' => true,
                'url' => asset('storage/'.$path),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }
}
