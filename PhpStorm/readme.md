## Code Style

#### Основная идея

В проекте важно иметь один код стайл, которому будут следовать все разработчики проекта.
Хорошим стилем считается схема, которая не будет меняться из проекта в проект, 
не меняется по "хотению" одного из разработчиков
и идет в ногу со временем, используя стандарты PSR-1, PSR-2, PSR-12.

Предлагаю небольшую правки для схемы PHPStorm для разработки на PHP.
Приветствуются правки с пояснением зачем и чем лучше.

Файл скачать можно по ссылке [скачать](Modernized%20Code%20Style.xml)


## Plugins

Предлагаю вашему вниманию список плагинов для PHPStorm.

#### Общие плагины
- [Php Inspections ​(EA Extended)](https://plugins.jetbrains.com/plugin/7622-php-inspections-ea-extended-)

    Умеет очень многое и подсказывает каждую упущенную мелочь.     
    С полным функционалом можно ознакомиться на странице плагина.

    ##### **Must have** для разработки на  PHP.
    
- [Code Glance](https://plugins.jetbrains.com/plugin/7275-codeglance)

    Миникарта, которая располагается справа, рядом со скролл-панелью. 
    Такую карту можно наблюдать в Sublime Text
    
- [PHP Composer.json Support](https://plugins.jetbrains.com/plugin/7631-php-composer-json-support)

    Показывает подсказки при редактировании `composer.json` файла. 
    Подсказывает структура файла, доступные пакеты и их версии.

- [String Manipulation](https://plugins.jetbrains.com/plugin/2162-string-manipulation)

    Полезный плагин, позволяющий делать различные операциями со строками.
    
- [.ignore](https://plugins.jetbrains.com/plugin/7495--ignore)

    Полезный плагин для создания *.ignore файлов.
    
- [Key Promoter](https://plugins.jetbrains.com/plugin/4455-key-promoter)

    Если вы еще не используете горячие клавишы, то этот плагин поможет вам.
    
    Плагин подсказывает горячую клавишу, которой можно было бы заменить клик мыши, 
    и количество раз нажатых на ту или иную кнопку раз.
    
    
#### Плагины для разработки на Yii2

- [Yii2 Support](https://plugins.jetbrains.com/plugin/9388-yii2-support)

    Самый большой и популярный плагин для разработки на **Yii2**.
    Может очень много: помогает в навигации, подсказывает магические свойства и т.п.
    
    ##### **Must have** для разработки на Yii2 

- [Yii Inspections](https://plugins.jetbrains.com/plugin/9400-yii2-inspections)

    Довольно сомнительный плагин, который особо не помогает при разработке как таковой.
    На момент написания имеет функционал для подсказки упущенных переводов и `@property`
    
    
#### Плагины для разработки на Symfony

- [PHP Annotations](https://plugins.jetbrains.com/plugin/7320-php-annotations)

    Помогает с подсказкой полей и методов при использовании аннотаций для описания и конфигурирования классов.
    
- [Symfony Support](https://plugins.jetbrains.com/plugin/7219-symfony-support)

    Пожалуй, самый большой плагин для **Symfony**. Умеет очень многое. 
    
    ##### **Must have** для разработки на **Symfony**
    
    
#### Стандартные плагины, которые не имеют место быть при разработке на неменяющемся стеке технологий

Чтобы уменьшить "глюки" `PHPStorm`-а, можно отключить некоторые плагины, 
которыми вы никогда, возможно, не будете пользоваться.

Список расширений, которые можно отключить, если они вам не нужны:

- Angular and AngularJS

	Плагин для работы с JS библиотеками Angular и AngularJS соответственно.

- Apache config (.htaccess)

	Плагин для помощи в редактировании конфигурационного файла `.htaccess`.

	Плагин не имеет смысла, если в вашем проекте используется **Nginx**.

- ASP 

	Плагин для работы с [Active Server Pages](https://ru.wikipedia.org/wiki/ASP).

- Behat Support

	Плагин для работы с Behat

- Blade

	Плагин для поддержки шаблонизатора **Blade**.

- Codeception Framework

	Плагин для помощи запуска и создания тестов с помощью фреймворка для тестирования **Codeception**.

	Не нужен, если вы используется **PHPUnit** или не пользуетесь тестированием вовсе.

- CoffeScript

- Copyright

- Drupal Support

	Плагин для помощи разработки с использованием CMS **Drupal**

- Gherkin

- Gnu GetText files support (*.po)

- Haml


- Joomla! Support

	Плагин для помощи разработки с использованием CMS **Joomla**

- Mercurial

	Если в вашем проекте в качестве [CSV](https://ru.wikipedia.org/wiki/CVS) используется плагин **Git**, этот плагин вам не нужен.


- Perforce

- Perfomance Testing

- Phing

- PhpStorm Workshop

- ReStructuredText Support

- Subversion

- Task Management

- TextMate bundles

- Time Tracking

- tslint

- Twig Support

- UML

- Vagrant

- Vue.js

- WordPress Support
