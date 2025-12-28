<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Inertia\Inertia;

class GuestController extends Controller {

    public function index() {
        // 1. Ambil data dengan pagination
        $paginated = Guest::latest()->paginate(25);

        // 2. Transform isinya biar ada QR Code dan Link di setiap item
        $paginated->getCollection()->transform(function($guest) {
            $url = url('/?guest=' . $guest->guest_code);
            return [
                'id' => $guest->id,
                'name' => $guest->name,
                'city' => $guest->city,
                'link' => $url,
                'qrcode' => QrCode::size(100)->generate($url)->toHtml()
            ];
        });

        return Inertia::render('Admin/GuestIndex', [
            'guests' => $paginated
        ]);
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        
        // FIX: Pake toArray biar datanya masuk ke variabel $rows
        $data = Excel::toArray([], $file);
        $rows = $data[0]; // Ambil sheet pertama

        // Skip header (asumsi baris 1 adalah header: nama, kota)
        foreach (array_slice($rows, 1) as $row) {
            // Mapping kolom (0 = nama, 1 = kota) -> sesuaikan dengan file excel lo
            if (!empty($row[0])) {
                Guest::updateOrCreate(
                    ['name' => $row[0]], 
                    ['city' => $row[1] ?? '-']
                );
            }
        }

        return back()->with('success', 'Data tamu berhasil di-import!');
    }

    public function show($code) {
        $guest = Guest::where('guest_code', $code)->firstOrFail();

        return response()->json([
            'name' => $guest->name,
            'code' => $guest->guest_code,
            'quota' => $guest->quota,
            'city' => $guest->city
        ]);
    }

    public function printLabels() {
        $guests = Guest::orderBy('name', 'asc')->get()->map(function($guest) {
            $url = url('/?guest=' . $guest->guest_code);
            return [
                'name' => $guest->name,
                'qrcode' => QrCode::size(80)->margin(0)->generate($url)->toHtml()
            ];
        });

        return Inertia::render('Admin/GuestPrintLabels', [
            'guests' => $guests
        ]);
    }

    public function updateRsvp(Request $request, $code) {
        $guest = Guest::where('guest_code', $code)->firstOrFail();
        $guest->update([
            'is_rsvp' => 1,
            'attendance' => $request->status,
        ]);

        return response()->json(['message' => 'Data Berhasil Masuk, Bre!']);
    }

    public function destroy($id) {
        Guest::findOrFail($id)->delete();
        return back();
    }

    // app/Http/Controllers/GuestController.php

public function truncate() 
{
    // Sapu bersih database tabel guests
    \App\Models\Guest::truncate();

    return back()->with('success', 'Semua data tamu telah dimusnahkan, Bre!');
}
}