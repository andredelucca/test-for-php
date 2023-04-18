<?php

declare(strict_types=1);

namespace App;

use function file_get_contents;
use function http_build_query;
use function json_decode;
use function json_encode;
use function stream_context_create;

class HttpRequest
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    
    public function call(string $method, string $url, array $parameters = null, array $data = null): array
    {
        // Verificar se a resposta está no cache
        $cache_key = $this->getCacheKey($method, $url, $parameters, $data);
        $cache_data = $this->cache->get($cache_key);
        if ($cache_data !== null) {
            return $cache_data;
        }
        
        // Fazer a requisição
        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => 'Content-type: application/json',
                'content' => $data ? json_encode($data) : null
            ]
        ];

        $url .= ($parameters ? '?' . http_build_query($parameters) : '');
        
        $response = file_get_contents($url, false, stream_context_create($opts));
   
        $data = json_decode($response, true);

        // Salvar a resposta no cache
        if ($method == 'GET') {
            $cache_data = $data;
            $this->cache->put($cache_key, $cache_data);
        }
 
        return $data;
    }

    public function getCacheKey(string $method, string $url, array $parameters = null, array $data = null) 
    {
        $cache_key = $method . '_' . $url;
        if ($parameters) {
            $cache_key .= '?' . http_build_query($parameters);
        }
        if ($data) {
            $cache_key .= '_' . md5(json_encode($data));
        }
        return $cache_key;
    }
}
