## Расчет стоимости путешествия с учетом скидок

1.  Creating Symfony Applications
```
composer create-project symfony/skeleton:"6.4.*" ./
symfony server:start
```
2. Установим необходимые пакеты. Создадим контроллер, dto c правилами валидации полей и сервисный слой для расчета итоговой стоимости путешествия
```
composer require serializer validator
```
