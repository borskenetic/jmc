<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Support\QrCodePng;
use Illuminate\Http\Request;

class VisitorRegistrationController extends Controller
{
    public function create()
    {
        return view('visitors.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['qrcode'] = Visitor::allocateQrCode();

        $visitor = Visitor::create($validated);

        return redirect()->route('visitors.pass', $visitor);
    }

    public function pass(Visitor $visitor)
    {
        $qrBase64 = QrCodePng::toBase64($visitor->qrcode, 280, 2);

        return view('visitors.pass', compact('visitor', 'qrBase64'));
    }
}
