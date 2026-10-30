<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuickContact;
use Illuminate\Http\Request;

class QuickContactController extends Controller
{
    public function edit()
    {
        return view('admin.quick-contact.edit', ['quick' => QuickContact::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-().]{6,30}$/'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-().]{6,30}$/'],
        ], [
            'phone.regex' => 'Enter a valid contact number.',
            'whatsapp.regex' => 'Enter a valid WhatsApp number (include the country code).',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $quick = QuickContact::current();
        $quick->fill($data)->save();

        return back()->with('success', 'Quick contact saved.');
    }
}
