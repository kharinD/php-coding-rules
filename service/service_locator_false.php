<?php
/** @noinspection ALL */

class ModelSaver
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function save(string $firstName, string $lastName)
    {
        $record = $this->repository->create($firstName, $lastName);
        Yii::$app->cache->set($record->id, serialize($record));
    }

}
