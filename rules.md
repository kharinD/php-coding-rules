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
class User extends \yii\db\ActiveRecord 
{
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
class ChangePhoneForm extends \yii\base\Model 
{
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
class User extends \yii\db\ActiveRecord 
{
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

### Yii::$app

#### <span style="color: red">Запрещается использовать вне `controller` и `view`-файлов</span>

#### Работа с сервисами

##### Синглтоны

Хорошим тоном считается избегание паттерна `Singleton`. 
Но если все же вам предстоит это делать, стоит воспользоваться `Dependency Injection Container`
и сконфигурировать синглтон с помощью метода `Yii::$app->container->setSingletons()`

##### Стандартные сервисы
Вместо прямого обращения к любому сервису из `Yii::$app` ...
```php
Yii::$app->someService->someMethod($data);
```
... стоит писать интерфейс сервиса и его реализацию, которая будет вызывать этот же сервис

##### Кастомные сервисы

При создании своих сервисов не нужно их помещать в `Yii::$app` с помощью конфигурирования файла `main.php` в папке `config`

Для "заменяемых" сервисов стоит написать общий `Interface` и сконфигурировать "прокидывание" конкретной реализации под этот интерфейс

Если не предполагается писать другие реализации для вашего сервиса,
можно просто прокидывать нужный сервис через конструктор или с помощью `Yii::$app->container`



##### Пример: Formatter
Обычно сервисы используется "напрямую" там, где это нужно
```php
class PriceFormatter
{
    public function format(int $price)
    {
        return Yii::$app->formatter->asPrice($price);
    }
}
```
стоит такого избегать, и вот план к побегу:
1. Сначала нужно создать `Interface` сервиса, который предстоит абстрагивать
    ```php
    interface FormatterInterface
    {
        public function asPrice(int $price): string;
    }
    ```
2. Затем стоит создать реализацию и унаследовать созданный в предыдущем шаге интерфейс
    ```php
    class YiiFormatter implements FormatterInterface
    {
        public function asPrice(int $price): string
        {
            return Yii::$app->formatter->asPrice($price);
        }
    }
    ```
3.  Теперь стоит сконфигурировать `Dependency Injection Container`, 
    подставив реализацию `YiiFormatter` под интерфейс `FormatterInterface`
    
    В файле `bootstrap.php` или любом классе, реализующего интерфейс `\yii\base\BootstrapInterface` нужно прописать следующий код:
    ```php
    Yii::$container->setDefenitions([
        FormatterInterface::class => YiiFormatter::class,
    ]);
    ```
    <span color="grey">Теперь контейнер приложения, когда загрузит конфиг будет отдавать по алиасу полному имени интерфейса `FormatterInterface` 
    реализацию `YiiFormatter`</span>

4. Наконец, теперь нужно:
    - "прокидывать" в конструктор интерфейс `FormatterInterface`,
    - присвоить объект в приватное свойство
    - заменить обращение `Yii::$app->formatter` на обращение к приватному свойству `$this->formatter`
    ```php
    class PriceFormatter
    {
        private $formatter;
    
        public function __construct(FormatterInterface $formatter)
        {
            $this->formatter = $formatter;
        }
    
        public function format(int $price)
        {
            return $formatter->asPrice($price);
        }
    }
    ```

##### Работа `Request`
```php
class ApiController extends Controller
{
    public function actionIndex() 
    {
        $os = OSHelper::getOS();
        
        return $this->render('index', [
            'os' => $os
        ]);
    }
}
```
```php
class OSHelper
{
    public static function getOS(): int
    {
        $token = Yii::$app->request->getHeader('Authorization Token');
        
        if ($token === self::TOKEN_ANDROID) {
            return self::OS_ANDROID;
        } elseif ($token === self::TOKEN_IOS) {
            return self::OS_IOS;
        } elseif ($token === self::TOKEN_MOBILE) {
            return self::OS_MOBILE;
        } else {
            return self::OS_DESKTOP;
        }
    }
}
```

Вместо это стоит передать объект класса `\yii\web\Request` параметров в этот хелпер

```php
class ApiController extends Controller
{
    public function actionIndex() 
    {
        $requst = Yii::$app->request;
        $osHelper = new OSHelper();

        $os = $osHelper->getOS($request);
        
        return $this->render('index', [
            'os' => $os
        ]);
    }
}
```
```php
use yii\web\Request;

class OSHelper
{
    public function getOS(Request $request): int
    {
        $token = $request->getHeader('Authorization Token');
        
        if ($token === self::TOKEN_ANDROID) {
            return self::OS_ANDROID;
        } elseif ($token === self::TOKEN_IOS) {
            return self::OS_IOS;
        } elseif ($token === self::TOKEN_MOBILE) {
            return self::OS_MOBILE;
        } else {
            return self::OS_DESKTOP;
        }
    }
}
```
