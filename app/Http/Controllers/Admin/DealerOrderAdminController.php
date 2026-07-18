<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuilderModel;
use App\Models\DealerOrder;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DealerOrderAdminController extends Controller
{
    /**
     * Display a listing of B2B wholesale orders in admin panel.
     */
    public function index()
    {
        $orders = DealerOrder::with(['dealer', 'statusDetails'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.dealer-orders.index', compact('orders'));
    }

    /**
     * Display the wholesale order details in admin panel.
     */
    public function show($id)
    {
        $order = DealerOrder::with(['dealer', 'items.savedDesign'])->findOrFail($id);

        foreach ($order->items as $item) {
            if ($item->savedDesign) {
                $builderModel = BuilderModel::whereRaw('LOWER(name) = ?', [strtolower($item->savedDesign->model_name)])->first();
                $item->model_url = $builderModel ? $builderModel->model_url : ($item->savedDesign->design_data['modelUrl'] ?? null);
                $item->layers_metadata = $builderModel ? ($builderModel->layers_metadata ?? []) : ($item->savedDesign->design_data['layers_metadata'] ?? []);
            }
        }

        $statuses = OrderStatus::all();

        return view('admin.dealer-orders.show', compact('order', 'statuses'));
    }

    /**
     * Update B2B wholesale order delivery status and admin notes.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'exists:order_statuses,name'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $order = DealerOrder::findOrFail($id);

        $order->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        $order->load('dealer');

        // Notify dealer
        try {
            Mail::send('emails.dealer-order-status-changed-customer', ['order' => $order, 'dealer' => $order->dealer], function ($message) use ($order) {
                $message->to($order->dealer->email)
                    ->subject('Your Wholesale Order Status Updated: #B2B-'.$order->id.' ('.$order->status.')');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send dealer order status update email: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Order successfully updated!');
    }

    /**
     * Generate B2B wholesale order invoice page for printing to PDF.
     */
    public function pdf($id)
    {
        $order = DealerOrder::with(['dealer', 'items.savedDesign'])->findOrFail($id);

        foreach ($order->items as $item) {
            if ($item->savedDesign) {
                $builderModel = BuilderModel::whereRaw('LOWER(name) = ?', [strtolower($item->savedDesign->model_name)])->first();
                $item->model_url = $builderModel ? $builderModel->model_url : ($item->savedDesign->design_data['modelUrl'] ?? null);
                $item->layers_metadata = $builderModel ? ($builderModel->layers_metadata ?? []) : ($item->savedDesign->design_data['layers_metadata'] ?? []);
            }
        }

        return view('admin.dealer-orders.pdf', compact('order'));
    }
}
