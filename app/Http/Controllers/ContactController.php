<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ContactQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Show the contact page (if needed, though routes file has direct inertia render)
     */
    public function index()
    {
        return Inertia::render('Contact');
    }

    /**
     * Handle form submission
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'subject.required' => 'Please enter your subject.',
            'message.required' => 'Please enter your message.',
            'email.email' => 'Please enter a valid email address.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.max' => 'Message cannot exceed 5000 characters.',
            'name.max' => 'Name cannot exceed 255 characters.',
        ]);
        // 1. Store in Database
        $query = ContactQuery::create($validated);

        // 2. Send email notifications
        // To Admin
        try {
            $adminEmails = Admin::pluck('email')->filter()->toArray();
            if (empty($adminEmails)) {
                $adminEmails = ['admin@eaysports.com'];
            }

            Mail::send('emails.contact-query-admin', ['query' => $query], function ($message) use ($adminEmails, $query) {
                $message->to($adminEmails)
                    ->subject('New Contact Query: '.$query->subject);
            });
        } catch (\Exception $e) {
            logger()->error('Failed to send admin contact email: '.$e->getMessage());
        }

        // To Customer
        try {
            Mail::send('emails.contact-query-customer', ['query' => $query], function ($message) use ($query) {
                $message->to($query->email)
                    ->subject('We Received Your Message: '.$query->subject);
            });
        } catch (\Exception $e) {
            logger()->error('Failed to send customer contact confirmation email: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
