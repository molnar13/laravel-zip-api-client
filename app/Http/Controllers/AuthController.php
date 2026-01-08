public function login(Request $request, ApiService $api) {
    $response = $api->post('login', $request->only('email', 'password'));
    
    if ($response->successful()) {
        session(['token' => $response->json('token'), 'user' => $response->json('user')]);
        return redirect()->route('home');
    }
    return back()->withErrors(['message' => 'Hibás adatok!']);
}

public function logout(ApiService $api) {
    $api->post('logout', []);
    session()->forget(['token', 'user']);
    return redirect()->route('login');
}