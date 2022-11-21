<?php
namespace App\Services\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

Class ApiService
{


    protected $token;
    protected $headers;

    protected $base_url;
    public function __construct($base_url , $token=null , $headers = [])
    {
        $this->base_url = $base_url;
        $this->token = $token;
        $this->headers = $headers ?? config('bol-control-api.headers');
    }


    /**
     * REST API get method
     * @param string $url represnts the endpoint
     * @param array $param represents the query parameters
     * @param
     */
    public function get($endpoint , $param = [])
    {
        $url = $this->base_url.$endpoint;
        $response = Http::withToken($this->token)
                    ->withHeaders($this->headers)
                    ->get($url , $param)->body();
        $response = $this->decodeJsonResponse($response);
        return $response;
    }


    /**
     * Make Basic Auth
     */
    public function authWithBasic($endpoint , $credits , $params = [])
    {
        $response = Http::withBasicAuth($credits['clientId'] , $credits['clientSecret'])
                ->withHeaders($this->headers)
                ->post('https://login.bol.com/token?grant_type=client_credentials')->body();
        return $this->decodeJsonResponse($response);
    }


    /**
     * Decode The JSON Response
     */
    public function decodeJsonResponse($response)
    {
        return  is_array($response) ? $response : json_decode($response , true);
    }


    public function setToken($token)
    {
        $this->token = $token;
    }

}
