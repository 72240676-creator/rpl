<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChargingController extends Controller
{
    public function scan()
    {
        return view('scan-charge');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $image = $request->file('qr_image');

        return view('scan-result', [
            'image' => $image->getClientOriginalName(),
        ]);
    }

    public function show($charger)
    {
        return view('charger-detail', compact('charger'));
    }
}