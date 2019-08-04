<?php
/** @noinspection ALL */

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
    private $cache;

    public function __construct(ModelRepository $repository, CacheInterface $redis)
    {
        $this->repository = $repository;
        $this->cache = $redis;
    }

    public function save(string $firstName, string $lastName)
    {
        $record = $this->repository->create($firstName, $lastName);
        $this->cache->set();
    }
}