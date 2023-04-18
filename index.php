<?php
include_once('src/HttpRequest.php');
include_once('src/CacheInterface.php');
use App\HttpRequest;

$cache = new App\MemoryCache();
$http = new HttpRequest($cache);

$insert = ["title"=>'foo', "body"=>'bar', "userId"=>1];
$update = ["id"=>101, "title"=>'teste', "body"=>'teste', "userId"=>1];

echo "<p><strong>GET </strong>".json_encode($http->call('GET', 'https://jsonplaceholder.typicode.com/posts/1'))."</p>";
echo "<p><strong>POST </strong>".json_encode($http->call('POST', 'https://jsonplaceholder.typicode.com/posts', null, $insert))."</p>";
echo "<p><strong>PUT </strong>".json_encode($http->call('PUT', 'https://jsonplaceholder.typicode.com/posts/1', null, $update))."</p>";
echo "<p><strong>DELETE </strong>".json_encode($http->call('DELETE', 'https://jsonplaceholder.typicode.com/posts/1'))."</p>";


