# Laravel API Project

Проект представляет собой API на Laravel, предназначенный для управления маршрутами и местами. В проекте используются Docker-контейнеры для упрощения развертывания и Swagger для документации API.

## Структура проекта

- **Laravel Framework** — серверный код.
- **Docker** — для контейнеризации приложения и зависимостей.
- **Swagger** — генерация документации API.
- **MySql** — в качестве основной базы данных.
- **DBeaver** — для управления базой данных.

## Установка и настройка
Клонирование репозитория
```bash
git clone https://github.com/anastasikalen/laravel-api.git
cd laravel-api
```
### Создание .env файла

Скопируйте файл .env.example в .env и отредактируйте его:
cp .env.example .env
В этом файле можно настроить переменные окружения, такие как параметры базы данных, Redis, перемнные для Mailpit, добавить ключи для стороннего API, Swagger и другие.

### Билд Docker контейнеров
```bash
docker-compose build
```
Запуск Docker контейнеров
Выполните команду для запуска контейнеров:
```bash
docker-compose up -d
```
Это создаст и запустит контейнеры для Laravel приложения, базы данных PostgreSQL и других сервисов.

### Установка зависимостей

После успешного запуска контейнеров, подключитесь к контейнеру PHP и установите зависимости:
```bash
docker-compose exec app composer install
```
### Генерация ключа приложения
```bash
docker-compose exec app php artisan key:generate
```

### Запуск миграций и заполнение базы данных

Выполните миграции и наполните базу данных тестовыми данными:
```bash
docker-compose exec app php artisan migrate --seed
```
Это создаст таблицы users, routes, places и заполнит их тестовыми данными для первоначальной работы.

### Использование Swagger для документации API
Генерация документации Swagger

Для генерации файла документации Swagger выполните следующую команду:
```bash
docker-compose exec app ./vendor/bin/openapi --output public/swagger.json app/Http/Controllers
```

### Доступ к документации API

После генерации документации откройте её по адресу http://localhost:8080/swagger.json.

### Полезные команды
Просмотр контейнеров
```bash
docker ps
```
Остановка всех контейнеров
```bash
docker-compose down
```
Перезапуск контейнеров
```bash
docker-compose down && docker-compose up -d
```
Вход в контейнер
```bash
docker-compose exec app bash
```

### Пример запросов в Postman

1. Получение токена пользователя
Маршрут для авторизации:
```bash
URL: http://localhost:8080/api/login
Метод: POST
Тело запроса (JSON):
{
  "email": "admin@example.com",
  "password": "yourpassword"
}
Ответ:
{
  "token": "your_generated_token"
}
```
Примечание: Сохраните значение токена из ответа для использования в других запросах.

2. Список маршрутов API
2.1. Пользовательские маршруты
Регистрация пользователя
```bash
URL: http://localhost:8080/api/register
Метод: POST
Тело запроса (JSON):
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "yourpassword",
  "password_confirmation": "yourpassword"
}
```
Получение информации о текущем пользователе
```bash
URL: http://localhost:8080/api/user
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
```
2.2. Маршруты для маршрутов (routes)
Получение всех маршрутов
```bash
URL: http://localhost:8080/api/routes
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
```
Создание маршрута
```bash
URL: http://localhost:8080/api/routes
Метод: POST
Заголовок:
Authorization: Bearer your_generated_token
Тело запроса (JSON):
{
  "title": "New Route",
  "description": "Description of the new route"
}
```
Получение маршрута по ID
```bash
URL: http://localhost:8080/api/routes/{route_id}
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
```
2.3. Маршруты для мест (places)
Получение всех мест для определенного маршрута
```bash
URL: http://localhost:8080/api/routes/{route_id}/places
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
```
Добавление места к маршруту
```bash
URL: http://localhost:8080/api/routes/{route_id}/places
Метод: POST
Заголовок:
Authorization: Bearer your_generated_token
Тело запроса (JSON):
{
  "name": "New Place",
  "description": "Description of the place",
  "latitude": 48.8566,
  "longitude": 2.3522
}
```
Получение места по ID
```bash
URL: http://localhost:8080/api/places/{place_id}
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
```
3. Настройка Postman для тестирования API
Откройте Postman и создайте новую коллекцию для вашего API.
Добавьте запросы для каждого маршрута, используя localhost:8080 в качестве домена.
Во все запросы, требующие аутентификации, добавьте заголовок Authorization:
Authorization: Bearer your_generated_token (замените your_generated_token на полученный токен).

Для тестирования сторонних API в Postman, таких как получение данных о отелях и погоде, следуйте инструкциям ниже. Предварительно убедитесь, что у вас есть валидный токен авторизации, и добавьте его в заголовок Authorization.

1. Получение данных об отелях по координатам
Маршрут для получения данных об отелях:
```bash
URL: http://localhost:8080/api/routes/{route_id}/places/{place_id}/hotel
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
Пример запроса:
URL с параметрами: http://localhost:8080/api/routes/1/places/1/hotel
```
Описание:

Этот маршрут использует сторонний API Booking.com для поиска отелей по координатам места (latitude, longitude).
Данные о координатах берутся из модели Place, связанной с маршрутом Route.
Запрос автоматически кэшируется на час (3600 секунд) для оптимизации повторных обращений.
Пример ответа:
```bash
[
  {
    "name": "Hotel Name",
    "address": "City, Country",
    "price": "100 USD",
    "rating": 8.5,
    "reviewWord": "Very Good",
    "checkin": "14:00",
    "checkout": "11:00",
    "photo": "https://example.com/photo.jpg",
    "coordinates": {
      "latitude": 48.8566,
      "longitude": 2.3522
    },
    "badges": ["Eco-Friendly", "Family Friendly"],
    "class": "4 star"
  }
]
```
2. Получение данных о погоде для места
Маршрут для получения данных о погоде:
```bash
URL: http://localhost:8080/api/routes/{route_id}/places/{place_id}/weather
Метод: GET
Заголовок:
Authorization: Bearer your_generated_token
Пример запроса:
URL с параметрами: http://localhost:8080/api/routes/1/places/1/weather
```
Описание:

Этот маршрут использует API OpenWeather для получения текущих данных о погоде по координатам места (latitude, longitude).
Данные кэшируются на 1 час (3600 секунд) для оптимизации.
Пример ответа:
```bash
{
  "temperature": 18.5,
  "humidity": 60,
  "weather": "clear sky",
  "wind_speed": 3.5,
  "city_name": "Paris"
}
```
Настройка API-ключей и окружения
Перед тестированием убедитесь, что у вас правильно настроены API-ключи:

Примечания
Кэширование: Обратите внимание, что данные отелей и погоды кэшируются на час. Чтобы сбросить кэш и получить актуальные данные, можно изменить время кэширования или очистить кэш вручную 
```bash
php artisan cache:refresh-weather-hotel
```
Токен авторизации: Убедитесь, что в каждом запросе для защищенных маршрутов (/hotel и /weather) вы добавляете токен в заголовок Authorization.

При регистрации нового пользователя в API автоматически запускается задача по отправке приветственного письма на указанный email. 
Откройте Mailpit по адресу http://localhost:8025/, чтобы увидеть письмо, отправленное новому пользователю.