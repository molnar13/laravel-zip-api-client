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

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // 2. Regisztráció elküldése az API-nak
    public function register(Request $request)
    {
        // Validálás a kliens oldalon is
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed', // a 'confirmed' miatt kell password_confirmation mező is
        ]);

        try {
            // Adatok küldése az API /register végpontjára
            $response = $this->api->post('register', [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
            ]);

            if ($response->successful()) {
                // Siker esetén átirányítjuk a bejelentkezéshez
                return redirect()->route('login')->with('success', 'Sikeres regisztráció! Most már bejelentkezhetsz.');
            } else {
                // Ha az API hibát dob (pl. foglalt email)
                // Megpróbáljuk kinyerni a hibaüzenetet
                $errorMsg = $response->json()['message'] ?? 'A regisztráció sikertelen.';
                return back()->withErrors(['email' => $errorMsg])->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Nem sikerült elérni a szervert.'])->withInput();
        }
    }
}