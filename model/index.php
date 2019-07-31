<?php /** @noinspection PhpUndefinedMethodInspection */

class Yii{public static $app;}

class Model
{
    private $id;

    public function save()
    {
        Yii::$app->redis->delete($this->id);
    }
}

class ModelRepository{
    public function create(string $firstName, string $lastName)
    {
        $model = new Model();
        $model->setAttributes([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
        $model->save();
    }
}

class ModelSaver{
    private $repository;
    private $redis;

    public function __construct(ModelRepository $repository, Redis $redis)
    {
        $this->repository = $repository;
        $this->redis = $redis;
    }

    public function save(string $firstName, string $lastName)
    {
        $this->repository->create($firstName, $lastName);
        $this->redis->set();
    }
}


class ModelSaver {
    private $repository;
    private $cache;

    public function __construct(ModelRepository $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function save(string $firstName, string $lastName)
    {
        $this->repository->create($firstName, $lastName);
        $this->cache->set();
    }

}

interface CacheInterface{}

class RedisCache implements CacheInterface {}
class MemcacheCache implements CacheInterface {}
class FileCache implements CacheInterface {}
class AnotherCache implements CacheInterface {}
class TestCache implements CacheInterface {}