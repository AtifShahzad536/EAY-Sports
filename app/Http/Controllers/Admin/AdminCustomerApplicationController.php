<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerApplicationController extends Controller
{
    /**
     * Display a listing of customer applications in the admin panel.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = User::where('role', 'customer')
            ->orderBy('created_at', 'desc');

        if (in_array($status, ['pending', 'active', 'rejected'])) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.customer-applications.index', compact('applications', 'status'));
    }

    /**
     * Display the specified customer application details.
     */
    public function show($id)
    {
        $application = User::where('role', 'customer')->findOrFail($id);

        return view('admin.customer-applications.show', compact('application'));
    }

    /**
     * Approve the customer application.
     */
    public function approve($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'This application has already been processed.');
        }

        $user->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.customer-applications.index')
            ->with('success', 'Customer registration application successfully approved! The customer can now log in.');
    }

    /**
     * Reject the customer application.
     */
    public function reject($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'This application has already been processed.');
        }

        $user->update([
            'status' => 'rejected',
            'approved_at' => null,
        ]);

        return redirect()->route('admin.customer-applications.index')
            ->with('success', 'Customer registration application has been rejected.');
    }
}
