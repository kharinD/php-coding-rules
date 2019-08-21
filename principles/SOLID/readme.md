## [SOLID](https://ru.wikipedia.org/wiki/SOLID_(%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D0%BD%D0%BE-%D0%BE%D1%80%D0%B8%D0%B5%D0%BD%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D0%BE%D0%B5_%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5))

Это акроним, который расшировывается с помощью пяти других акронимов.

Начнем по порядку:

### S - SRP

##### Single Responsibility Principle
###### Принцип единой ответственности

> Модуль должен иметь одну и только одну причину для изменения.

Конечная правильная трактовка данного принципа звучит так

> Модуль должен отвечать за одного и только за одного актора.


Где актор (*actor*) - один или несколько лиц, которые что-то хотят от этого модуля.


Данная цитата на первый взгляд выглядит совершенно непонятно:
какая причина? почему только одна? а что будет, если их будет 2, 5 , 10?


Принцип заставляет не писать несколько ответственностей в одном модуле.
Принцип заставляет оставить одну и только одну причину для изменения этого модуля.

###### Пример

```php
class Payout 
{
    public function pay(Card $card) 
    {
        $amount = $this->calculateAmount($card->items);
        $response = $this->requestPay($amount);

        if ($reponse == true) {
            $this->savePay();
        }
    }

    public function calculateAmount(array $items): int
    {
        $price = 0;
        foreach ($items as $item) {
            $price += $item->price;
        }
        
        return $price;
    }

    public function requestPay(int $amount)
    {
        // HTTP POST http://money.com/pay?amount=$amount
    }

    public function savePay()
    {
        // INSERT INTO pays ...
    }
}
```

На этапе проектирования задача была такие: в этом класс должна происходить оплата.

Но в итоге получились такие ответственности: 
- Считает сумму товаров в корзине
- Класс выполняет HTTP запросы
- Класс сохраняет результат в базу данных

Т.к. SRP говорит, чтобы была одна и только одна ответственность в классе, 
этот класс придется разбить на 3 других.

### O - OCP

##### Open-Closed Principle

### L - Liskov Substitution Principle 

##### Single Responsibility Principle

### I - ISP

##### Interface Segregation Principle

### D - DIP

##### Dependency Inversion Principle