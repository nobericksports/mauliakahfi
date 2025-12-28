<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Gallery;

use Inertia\Inertia;

class WeddingController extends Controller
{
    public function index(Request $request)
{
    $guestCode = $request->query('guest');
    $guest = Guest::where('guest_code', $guestCode)->first();
    $gallery = Gallery::orderBy('order', 'asc')->get();

    return Inertia::render('Wedding', [
        'guestData' => $guest ? [
            'name' => $guest->name,
            'code' => $guest->guest_code, // HARUS 'code' biar sinkron sama RSVPSection
            'city' => $guest->city,
        ] : null,
        'galleryData' => $gallery
    ]);
}
}
