# Правила кодирования для Yii2
Данная страница посвещена правилам кодирования для разработке на фреймворке Yii2.

Здесь будут описаны примеры как делать не нужно, и как можно сделать взамен неправильному

Стоит следовать этим правилам, если хотите не ломать основы архитектуры, писать чистый и тестируемый код.

## Сущности
Сущность `ActiveRecord` - это проекция таблицы на код. Поэтому не стоит делать из проекции что-то большее.

#### Правила валидации

Правила валидации должны затрагивать лишь правила сохранения в базу и не должны покрывать `use case`, 
в котором сущность используется.

В базе нет ограничений на минимальную и максимальную длины телефонного номера. 
А так же там нет ограничений на паттерн подходящих строк
```php
class User extends \yii\db\ActiveRecord 
{
    public function rules() 
    {
        return [
            [
                ['phone'], 
                'string', 
                'max' => self::PHONE_MAX_LENGTH, 
                'min' => self::PHONE_MIN_LENGTH,
            ],
            [
                ['phone'],
                'phone',
                'pattern' => self::PHONE_PATTERN,
            ],
            [['phone'], 'string', 'max' => 255, 'skipOnEmpty' => false],
        ];
    }
}
```
Вместо одной "смешанной" модели стоит сделать одну форму ...
```php
class ChangePhoneForm extends \yii\base\Model 
{
    public function rules() 
    {
        return [
            [
                ['phone'], 
                'string', 
                'max' => self::PHONE_MAX_LENGTH, 
                'min' => self::PHONE_MIN_LENGTH,
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
... и одну модель, которые не будут зависеть друг от друга.
```php
class User extends \yii\db\ActiveRecord 
{
    public function rules() 
    {
        return [
            [['phone'], 'string', 'max' => 255, 'skipOnEmpty' => false],
        ];
    }
}
```

## Формы

#### Наследование
При работе с формами запрещается наследоваться от основной модели `ActiveRecord` сущности:
```php
class CreateUserForm extends \app\models\User
```
Стоит наследоваться от базовой модели `\yii\base\Model`
```php
class CreateUserForm extends \yii\base\Model
```

#### Первичное заполнение

Иногда одна форма может обрабатывать 2 `use case`: создание и редактирование.

Это вполне нормально, когда поля и правила валидации не отличаются в этих юзкейсах.

В `action`-е создания у нас нет готовых данных, которыми мы можем заполнить форму. 
Поэтому форму можно создавать классическим способом:
```php
public function actionCreate() 
{
    $form = new CreateForm();
    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
        /* сохраняем */
    }

    return $this->render('create', [
        'form' => $form,
    ]);
}
```

В `action`-е редактирования появляются первичные данные, которые мы достаем из базы данных по ID.

##### Неверный путь
И обычно процесс заполнения выглядит как-то так ...
```php
public function actionUpdate() 
{
    $model = $this->getModel($id);
    $form = new CreateForm();
    $form->modelId = $model->id;
    $form->name = $model->name;
    $form->phone = $model->phone;
    /* обработка */
}
```
... или так ...
```php
public function actionUpdate() 
{
    $model = $this->getModel($id);
    $form = new CreateForm();
    $form->setAttributes($model->attributes);
    /* обработка */
}
```
Но это засоряет контроллер и не дает четкого понятия что и куда будет записано.

К тому же, если этот код будет использован в нескольких методах, то возможны расхождения в коде: 
в одном методе дополнительно добавили заполнение поля, а в другом забыли 

##### Правильный путь
В этом случае стоит создать создающий метод, на который можно делегировать процесс заполнения формы первичными данными этой формой

```php
public function actionUpdate() 
{
    $model = $this->getModel($id);
    $form = CreateForm::loadFromAr($model);
    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
        /* сохраняем */
    }

    return $this->render('create', [
        'form' => $form,
    ]);
}
```
```php
class CreateForm extends \yii\base\Model
{
    public $modelId;
    public $phone;

    public static function loadFromAr(User $user) 
    {
        $model = new self([
            'modelId' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
        ]);
        
        return $model;
    } 
}
```

#### Сценарии 

В случае, когда нужно разделить логику валидации одной формы, 
стоит создать новую форму и использовать в ней тот самый новый `use case`

Кейс: 
Есть форма добавления картинки в хранилище файлов.

Создание: 
- Имя (`name`) (не обязательное)
- Категория (`category`) (обязательное)
- Путь до файла (`url`) (обязательное)

Если оставить имя пустым, то будет сгенерировано случайное имя.

Редактирование: 
- Имя (`name`) (обязательное)
- Категория (`category`) (нельзя изменить)
- Путь до файла (`url`) (нельзя изменить)

##### Неверный путь
Изучив документацию, неопытный разработчик сразу начнет её копировать:

```php
class AddImageForm extends \yii\base\Model
{
    public $name;
    public $category;
    public $path;

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    public function rules() 
    {
        return [
            [['name'], 'string', 'skipOnEmpty' => true, 'on' => self::SCENARIO_CREATE],
            [['name'], 'string', 'skipOnEmpty' => false, 'on' => self::SCENARIO_UPDATE],
            [['path'], ValidPathValidator::class, 'on' => self::SCENARIO_CREATE],
            [
                ['category'],
                'exists',
                'targetClass' => Category::class,
                'targetAttribute' => ['id' => 'category'],
                'on' => self::SCENARIO_CREATE,
            ],
            [['name'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['category', 'path'], 'required', 'on' => self::SCENARIO_CREATE],
        ];
    }
}
```

А теперь представьте, что там 10 полей и 3 юзкейса...

##### Правильный путь
Проще всего разделить эту форму на 2 разных формы: форма создания и форма редактирования:
```php
class CreateImageForm extends \yii\base\Model
{
    public $name;
    public $category;
    public $path;

    public function rules() 
    {
        return [
            [['name'], 'string', 'skipOnEmpty' => true],
            [['path'], ValidPathValidator::class],
            [
                ['category'],
                'exists',
                'targetClass' => Category::class,
                'targetAttribute' => ['id' => 'category'],
            ],
            [['category', 'path'], 'required'],
        ];
    }
}
```
А так же можно почистить ненужные поля, если они не будут выводиться на `frontend`
```php
class EditImageForm extends \yii\base\Model
{
    public $name;

    public function rules() 
    {
        return [
            [['name'], 'string', 'skipOnEmpty' => false],
            [['name'], 'required'],
        ];
    }
}
```

## Yii::$app

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
Стоит такого избегать, и вот план к побегу:

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
    Теперь, когда контейнер приложения загрузит конфиг, он будет отдавать по алиасу полному имени интерфейса `FormatterInterface` 
    реализацию `YiiFormatter`

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

#### Работа с `Request`
Хорошим тоном стоит передавать объект `Request` в `action`, но в Yii2 этого не сделали.

Объект `Request` запрещается получать не в `controller` и `view` файлах.

Вместо этого стоит передавать `Request` в аргументе метода.

##### Пример: Определение OS устройства по заголовку **Authorization-Token**
В контроллере вызывается метод `OSHelper::getOS()`, в котором из `Yii::$app` берется объект `Request`

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
        $token = Yii::$app->request->getHeader('Authorization-Token');
        
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

Вместо этого нужно передать объект класса `\yii\web\Request` аргументом в метод `getOS` хелпера `OSHelper`.

А так же можно избавиться от статического метода. Создание хелпера можно поручить `Dependency Injection Container`, 
а можно и "по-старинке" - через `new`.

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

Теперь в метод `OSHelper::getOS` можно передать сколь угодно объектов `Request` и узнать **OS** каждого
