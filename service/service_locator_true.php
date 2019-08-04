<?php
/** @noinspection ALL */

class ModelSaver {
    private $repository;
    private $cache;

    public function __construct(Repository $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function save(string $firstName, string $lastName)
    {
        $record = $this->repository->create($firstName, $lastName);
        $this->cache->set($record->id, $record);
    }

}

interface CacheInterface{
    public function set(string $id, $object): void;
}

class RedisCache implements CacheInterface {}
class MemcacheCache implements CacheInterface {}
class FileCache implements CacheInterface {}
class AnotherCache implements CacheInterface {}
class TestCache implements CacheInterface {}