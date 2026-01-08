<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService; // Fontos: Ez hivatkozik a szervizre
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $api;

    // Dependency Injection: A Laravel itt tölti be automatikusan az ApiService-t
    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    // A bejelentkezési űrlap megjelenítése
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // A bejelentkezés feldolgozása
    public function login(Request $request)
    {
        // 1. Validálás: Email és jelszó kötelező
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 2. Kérés küldése az API-nak (ApiService használatával)
            // Csak az emailt és a jelszót küldjük el
            $response = $this->api->post('login', [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

        } catch (\Exception $e) {
            // Ha az API szerver nem elérhető (pl. nem fut)
            return back()->withErrors(['email' => 'Hiba: Nem sikerült elérni az API szervert.']);
        }

        // 3. Válasz ellenőrzése
        if ($response->successful()) {
            $data = $response->json();
            
            // Megpróbáljuk kinyerni a tokent (lehet 'token' vagy 'access_token' kulcs is)
            $token = $data['token'] ?? $data['access_token'] ?? null;
            
            if ($token) {
                // Token mentése a sessionbe
                session([
                    'token' => $token,
                    'user_name' => $data['user']['name'] ?? 'Felhasználó'
                ]);
                
                // Siker! Átirányítás a városok listájára
                return redirect()->route('settlements.index')->with('success', 'Sikeres bejelentkezés!');
            }
        }

        // 4. Ha sikertelen volt a bejelentkezés (pl. hibás jelszó)
        return back()->withErrors(['email' => 'Hibás email cím vagy jelszó.']);
    }

    // Kijelentkezés
    public function logout()
    {
        // Töröljük a tokent a sessionből
        session()->forget(['token', 'user_name']);
        
        return redirect()->route('login')->with('success', 'Kijelentkezve.');
    }
}