<?php

declare(strict_types=1);

namespace Tests;

include_once('src/CacheInterface.php');

use App\HttpRequest;
use App\MemoryCache;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase
{
    public function testCallMethodReturnsExpectedData(): void
    {
        $cache = new MemoryCache();
        $request = new HttpRequest($cache);
        $method = 'GET';
        $url = 'https://jsonplaceholder.typicode.com/posts/1';

        // Primeira chamada deve buscar dados da API
        $response = $request->call($method, $url);
        $expectedResponse = [
            'userId' => 1,
            'id' => 1,
            'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'body' => 'quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto',
        ];
        $this->assertSame($expectedResponse, $response);

        // Segunda chamada deve retornar dados do cache
        $response = $request->call($method, $url);
        $this->assertSame($expectedResponse, $response);

        // Testando com outros parâmetros
        $parameters = ['foo' => 'bar'];
        $data = ['key' => 'value'];
        $url = 'https://jsonplaceholder.typicode.com/posts/1';
        $cacheKey = $request->getCacheKey($method, $url, $parameters, $data);
        
        // Verificando se o cache foi criado corretamente
        $cachedData = $cache->get($cacheKey);
        $this->assertSame($expectedResponse, $cachedData);
    }
}

