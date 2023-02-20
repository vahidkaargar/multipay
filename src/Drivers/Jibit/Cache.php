<?php

namespace Shetabit\Multipay\Drivers\Jibit;

/*
 * Cache class for save Jibit tokens
 * Thanks to ChatGPT for generation this class
 */

class Cache
{
    private $cache_dir;

    public function __construct($cache_dir = __DIR__ . '/cache')
    {
        $this->cache_dir = $cache_dir;
    }

    public function get($key)
    {
        $cache_file = $this->getCacheFilename($key);

        if (!file_exists($cache_file)) {
            return null;
        }

        $cache_data = file_get_contents($cache_file);
        $cache_data = unserialize($cache_data);

        if ($cache_data['expire'] != 0 && time() > $cache_data['expire']) {
            unlink($cache_file);
            return null;
        }

        return $cache_data['data'];
    }

    public function set($key, $data, $expire = 0) // expire in seconds
    {
        $cache_file = $this->getCacheFilename($key);
        $cache_data = array(
            'data' => $data,
            'expire' => ($expire != 0) ? (time() + $expire) : 0
        );
        $cache_data = serialize($cache_data);
        file_put_contents($cache_file, $cache_data);
    }

    private function getCacheFilename($key)
    {
        return $this->cache_dir . '/' . md5($key);
    }
}
