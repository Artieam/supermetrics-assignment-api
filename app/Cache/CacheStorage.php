<?php

declare(strict_types=1);

namespace app\Cache;

class CacheStorage
{
    private string $path;

    public function __construct() {
        $this->path = 'tmp/';

        if (! is_dir($this->path) && ! mkdir($concurrentDirectory = $this->path, 0777, false)
            && ! is_dir($concurrentDirectory)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * Write cache to file
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     *
     * @return bool
     * @throws \JsonException
     */
    public function put(string $key, string $value, int $ttl = 60): bool
    {
        $cached = fopen($this->path . $key, 'wb');
        $data = json_encode((object) [
            'value' => $value,
            'ttl'   => $ttl
        ], JSON_THROW_ON_ERROR);
        fwrite($cached, $data);
        fclose($cached);

        return true;
    }

    /**
     * Get cached value from file
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $result = null;

        if ($this->exists($key)) {
            $fileData = json_decode(file_get_contents($this->path . $key, true), false);

            if (time() - $fileData->ttl < filemtime($this->path . $key)) {
                $result = $fileData->value;
            }
        }

        return $result;
    }

    /**
     * Check file existence
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        if (file_exists($this->path . $key)) {
            return true;
        }

        return false;
    }
}
