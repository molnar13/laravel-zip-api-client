<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class CountyController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $counties = $this->api->get('counties')->json();
        return view('counties.index', compact('counties'));
    }

    public function create()
    {
        return view('counties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        
        $response = $this->api->post('counties', $data);

        if ($response->successful()) {
            return redirect()->route('counties.index')->with('success', 'Megye sikeresen létrehozva!');
        }
        return back()->withErrors('Hiba történt a mentéskor.');
    }

    public function edit($id)
    {
        // Ha az API támogatja a GET /counties/{id} lekérést
        $county = $this->api->get("counties/{id}")->json();
        
        // Ha véletlenül nem támogatná, és a listából kell kikeresni (fallback):
        // $all = $this->api->get('counties')->json();
        // $county = collect($all)->firstWhere('id', $id);

        return view('counties.edit', compact('county'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        
        $response = $this->api->put("counties/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('counties.index')->with('success', 'Megye frissítve!');
        }
        return back()->withErrors('Hiba történt a frissítéskor.');
    }

    public function destroy($id)
    {
        $this->api->delete("counties/{$id}");
        return redirect()->route('counties.index')->with('success', 'Megye törölve.');
    }
}