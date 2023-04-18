<?php

declare(strict_types=1);

namespace App;

interface CacheInterface
{
        public function get(string $key);
        public function put(string $key, $value, int $ttl);
        public function delete(string $key);
}    

class MemoryCache implements CacheInterface {
    private $cache = array();

    public function get(string $key) {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        return null;
    }

    public function put(string $key, $value, int $ttl = 0) {
        $this->cache[$key] = $value;
    }

    public function delete(string $key) {
        unset($this->cache[$key]);
    }
}

class FileCache implements CacheInterface {
    private $cache_dir;

    public function __construct(string $cache_dir) {
        $this->cache_dir = $cache_dir;
    }

    public function get(string $key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $data = unserialize($content);
            if ($data['ttl'] === 0 || $data['ttl'] >= time()) {
                return $data['value'];
            }
            unlink($filename);
        }
        return null;
    }

    public function put(string $key, $value, int $ttl = 0) {
        $filename = $this->getFilename($key);
        $data = array(
            'value' => $value,
            'ttl' => $ttl == 0 ? 0 : time() + $ttl
        );
        $content = serialize($data);
        file_put_contents($filename, $content);
    }

    public function delete(string $key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    private function getFilename(string $key) {
        return $this->cache_dir . '/' . md5($key);
    }
}
