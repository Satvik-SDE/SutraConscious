<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $customer = ($user && ! $user->is_admin) ? $user : null;

        return view('shop.pages.contact', [
            'defaults' => [
                'name' => old('name', $customer?->name),
                'email' => old('email', $customer?->email),
                'phone' => old('phone'),
            ],
        ]);
    }

    public function submit(Request $request)
    {
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('status', 'Thanks — we received your message and will reply soon.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $supportEmail = config('shop.support_email');

        if (! $supportEmail) {
            Log::error('contact.support_email_missing');

            return back()
                ->withInput()
                ->withErrors(['message' => 'We could not send your message right now. Please email us directly at support@sutraconscious.com.']);
        }

        try {
            Mail::to($supportEmail)->send(new ContactMessageMail(
                name: $data['name'],
                email: $data['email'],
                phone: $data['phone'] ?? null,
                message: $data['message'],
            ));
        } catch (\Throwable $exception) {
            Log::error('contact.message_failed', [
                'email' => $data['email'],
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['message' => 'We could not send your message right now. Please try again or email support@sutraconscious.com directly.']);
        }

        return redirect()->route('contact')->with('status', 'Thanks — we received your message and will reply soon.');
    }
}
