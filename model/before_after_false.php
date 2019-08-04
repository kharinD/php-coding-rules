<?php
/** @noinspection ALL */

// Используем файловый кеш для кеширования

class Model
{
    private $id;

    public function afterSave()
    {
        Yii::$app->cache->set($this->id, $this);
    }
}

// Пришли правки, нужно поменять файлы на редис

class Model
{
    private $id;

    public function afterSave()
    {
        Yii::$app->redis->set($this->id, $this);
    }
}

// Теперь задача стоит в том, чтобы модели с четным ID хранить на сервере 1, а с нечетным ID хранить на сервере 2

class Model
{
    private $id;

    public function afterSave()
    {
        if ($this->id % 2 === 0) {
            Yii::$app->redis1->set($this->id, $this);
        } else {
            Yii::$app->redis2->set($this->id, $this);
        }
    }
}

// А теперь, если мы удаляем с помощь soft delete (поставить флаг is_deleted = 1), то нужно удалить из редиса кеш

class Model
{
    private $id;

    public function afterSave()
    {
        if ($this->is_deleted === 1){
            if ($this->id % 2 === 0) {
                Yii::$app->redis1->delete($this->id);
            } else {
                Yii::$app->redis2->delete($this->id);
            }
        }
        if ($this->id % 2 === 0) {
            Yii::$app->redis1->set($this->id, $this);
        } else {
            Yii::$app->redis2->set($this->id, $this);
        }
    }
}
