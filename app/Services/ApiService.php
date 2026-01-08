<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        // Ha nincs beállítva az .env-ben, alapértelmezettként a localhost:8000-et használja
        $this->baseUrl = env('API_URL', 'http://localhost:8000/api');
    }

    public function get($endpoint)
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->get("{$this->baseUrl}/{$endpoint}");
    }

    public function post($endpoint, $data)
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->post("{$this->baseUrl}/{$endpoint}", $data);
    }

    public function put($endpoint, $data)
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->put("{$this->baseUrl}/{$endpoint}", $data);
    }

    public function delete($endpoint)
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->delete("{$this->baseUrl}/{$endpoint}");
    }
}