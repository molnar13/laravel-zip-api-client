<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class SettlementController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    // 1. Városok listázása (Alapértelmezett nézet)
    public function index()
    {
        $response = $this->api->get('settlements');
        $settlements = $response->json();
        return view('settlements.index', compact('settlements'));
    }

    // 2. A speciális ABC szűrő nézete
    public function filterView()
    {
        // Lekérjük a megyéket a lenyíló listához
        $counties = $this->api->get('counties')->json();
        return view('settlements.filter', compact('counties'));
    }

    // 3. Új város létrehozása (Form megjelenítése)
    public function create()
    {
        // Lekérjük a megyéket az API-tól, hogy a lenyíló listába tehessük őket
        $response = $this->api->get('counties');
        $counties = $response->json(); // Vagy $response->json()['data'] az API szerkezetétől függően
        
        return view('settlements.create', compact('counties'));
    }

    // 4. Új város mentése (API POST hívás)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'county_id' => 'required|integer',
            'zip_code' => 'required|string',
        ]);

        $response = $this->api->post('settlements', $data);

        if ($response->successful()) {
            return redirect()->route('settlements.index')->with('success', 'Város sikeresen hozzáadva!');
        }
        return back()->withErrors('Hiba történt a mentés során.');
    }
    public function edit($id)
    {
        // Lekérjük az adatokat
        $response = $this->api->get("settlements/{$id}");
        $settlement = $response->json();

        // (A kód többi része most nem fut le)
        $counties = $this->api->get('counties')->json();
        return view('settlements.edit', compact('settlement', 'counties'));
    }

    // 2. A módosítás elküldése az API-nak
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'zip_code' => 'required|string',
            'county_id' => 'required|integer',
        ]);

        // PUT kérés küldése
        $response = $this->api->put("settlements/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('settlements.index')->with('success', 'Város sikeresen frissítve!');
        }

        return back()->withErrors('Nem sikerült a frissítés. Ellenőrizd az adatokat.');
    }

    // --- TÖRLÉS (DESTROY) ---

    public function destroy($id)
    {
        $response = $this->api->delete("settlements/{$id}");

        if ($response->successful()) {
            return redirect()->route('settlements.index')->with('success', 'Város törölve.');
        }

        return back()->withErrors('Hiba történt a törlés során.');
    }

    public function exportPdf(Request $request)
    {
        // Lekérjük az adatokat az API-tól (szűrés szerint, ha kell)
        $response = $this->api->get('settlements');
        $settlements = $response->json();

        $pdf = Pdf::loadView('settlements.pdf', compact('settlements'));
        
        // Letöltés indítása
        return $pdf->download('telepulesek.pdf');
    }

    // --- CSV EXPORTÁLÁS ---
    public function exportCsv()
    {
        // 1. Adatok lekérése az API-tól
        $response = $this->api->get('settlements');
        $settlements = $response->json();

        // 2. CSV Fájlnév és Fejlécek beállítása
        $filename = "telepulesek.csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 3. Callback függvény, ami generálja a fájlt
        $callback = function() use($settlements) {
            $file = fopen('php://output', 'w');
            
            // BOM karakter hozzáadása, hogy az Excel helyesen kezelje az ékezeteket
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Fejléc sora
            fputcsv($file, ['Irányítószám', 'Település', 'Megye'], ';');

            // Adatok kiírása soronként
            foreach ($settlements as $s) {
                // Ellenőrizzük, hogy van-e megye adat, ha nincs, üres stringet írunk
                $countyName = $s['county']['name'] ?? ''; 
                // Figyelünk az esetleges eltérő mezőnevekre (zip_code vagy postal_code)
                $zip = $s['zip_code'] ?? $s['postal_code'] ?? '';

                fputcsv($file, [$zip, $s['name'], $countyName], ';');
            }

            fclose($file);
        };

        // 4. Válasz visszaküldése streamként (így nem fogyaszt sok memóriát)
        return response()->stream($callback, 200, $headers);
    }
}