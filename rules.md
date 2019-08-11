## Правила кодирования для Yii2
Данная страница посвещена правилам кодирования для разработке на фреймворке Yii2.

Здесь будут описаны примеры как делать не нужно, и как можно сделать взамен неправильному

Стоит следовать этим правилам, если хотите не ломать основы архитектуры, писать чистый и тестируемый код.

### Сущности
Сущность `ActiveRecord` - это проекция таблицы на код. Поэтому не стоит делать из проекции что-то большее.

##### Правила валидации

Правила валидации должны затрагивать лишь правила сохранения в базу и не должны покрывать `use case`, 
в котором сущность используется.

В базе нет ограничений на минимальную и максимальную длины телефонного номера. А так же там нет ограничений на паттерн подходящих строк
```php
class User extends \yii\db\ActiveRecord {
    public function rules() {
        return [
            [
                ['phone'], 
                'string', 
                'max' => self::PHONE_MAX_LENGTH, 
                'min' => self::PHONE_MIN_LENGTH],
            ],
            [
                ['phone'],
                'phone',
                'pattern' => self::PHONE_PATTERN,
            ],
        ];
    }
}
```
Вместо одной модели стоит сделать одну форму...
```php
class ChangePhoneForm extends \yii\base\Model {
    public function rules() {
        return [
            [
                ['phone'], 
                'string', 
                'max' => self::PHONE_MAX_LENGTH, 
                'min' => self::PHONE_MIN_LENGTH],
            ],
            [
                ['phone'],
                'phone',
                'pattern' => self::PHONE_PATTERN,
            ],
        ];
    }
}
```

...и одну модель, которые не будут зависеть друг от друга.
```php
class User extends \yii\db\ActiveRecord {
    public function rules() {
        return [
            [['phone'], 'string', 'max' => 255, 'skipOnEmpty' => false],
        ];
    }
}
```


### Формы

##### Наследование
При работе с формами запрещается наследоваться от основной модели `ActiveRecord` сущности:
```php
class CreateUserForm extends \app\models\User
```
Стоит наследоваться от базовой модели `\yii\base\Model`
```php
class CreateUserForm extends \yii\base\Model
```
