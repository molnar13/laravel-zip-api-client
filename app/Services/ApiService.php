namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url', env('API_URL'));
    }

    protected function getHeaders()
    {
        $token = session('api_token');
        return [
            'Accept' => 'application/json',
            'Authorization' => $token ? "Bearer $token" : '',
        ];
    }

    public function get($endpoint) {
        return Http::withHeaders($this->getHeaders())->get("{$this->baseUrl}/$endpoint");
    }

    public function post($endpoint, $data) {
        return Http::withHeaders($this->getHeaders())->post("{$this->baseUrl}/$endpoint", $data);
    }

    public function put($endpoint, $data) {
        return Http::withHeaders($this->getHeaders())->put("{$this->baseUrl}/$endpoint", $data);
    }

    public function delete($endpoint) {
        return Http::withHeaders($this->getHeaders())->delete("{$this->baseUrl}/$endpoint");
    }
}