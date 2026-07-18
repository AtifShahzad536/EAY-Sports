<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DealerCredentialsMail;
use App\Models\User;
use App\Rules\PhoneValidation;
use App\Traits\FlashNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminDealerController extends Controller
{
    use FlashNotifications;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'dealer')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dealers = $query->paginate(20)->withQueryString();

        return view('admin.dealers.index', compact('dealers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.dealers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', new PhoneValidation],
            'address' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'city' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string',
        ]);

        // Store the plain password before hashing (for email)
        $plainPassword = $data['password'];

        $data['role'] = 'dealer';
        $data['password'] = Hash::make($plainPassword);
        $data['approved_at'] = now();

        $dealer = User::create($data);

        // Send credentials email to the dealer
        try {
            Mail::to($dealer->email)->send(
                new DealerCredentialsMail($dealer->name, $dealer->email, $plainPassword)
            );
        } catch (\Exception $e) {
            // Log the error but don't block dealer creation
            \Log::error('Failed to send dealer credentials email: '.$e->getMessage());
        }

        $this->successNotification('Dealer created successfully. Credentials email sent to '.$dealer->email);

        return to_route('admin.dealers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $dealer = User::where('role', 'dealer')->findOrFail($id);

        return view('admin.dealers.edit', compact('dealer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('role', 'dealer')->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', new PhoneValidation],
            'address' => 'nullable|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'city' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $user->update($request->all());

        $this->successNotification('Dealer updated successfully.');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::where('role', 'dealer')->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dealers.index')
            ->with('success', 'Dealer deleted successfully.');
    }
}
