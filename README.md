# TO-DO List - Laravel + Vue/Nuxt

Данное веб-приложение позволяет просматривать, создавать, редактировать и удалять задачи. Реализовано с использованием фреймворка Laravel для серверной части и фреймворка Nuxt для клиентской части, в качестве базы данных используется Postgres.

За основу фронтенд части взял официальный starter kit от Laravel, но избавился от Inertia и разделил проект на 2 отдельных проекта - `backend` (Laravel) и `frontend` (Nuxt).

### ❗Для запуска необходимы Docker и make. Можно и без него, если запускать Docker команды вручную (ниже описано как), но я использую make для удобства❗

## Первичный запуск проекта

При первичном запуске нужно инициализировать проект командой `make init` (подтвердите действие с помощью ввода "y" и нажатием на Enter)<br />
Окружение:<br />
- `dev` - разработка<br />
- `prodhttp` - продакшн без домена (запуск с IP адресом)<br />
- `prod` - продакшн<br />

По дефолту выбирается `dev`

## Основные команды
#### ! Все команды указаны в `Makefile` в корне, а так же индивидуально в папках `backend` и `frontend` (запуск с префиксом `backend-` и `frontend-` соответственно)
#### Первичная инициализация проекта
```bash
make init  // подтвердите действие с помощью ввода "y" и нажатием на Enter 
```

#### Запуск проекта
```bash
make up
```

#### Остановка проекта
```bash
make down
```
#### ⚠️ Остановка проекта с очисткой данных (файлов хранилища, базы данных и т.д.)
```bash
make down-clear
```

#### Перезапуск проекта
```bash
make restart
```

#### Пересборка проекта
```bash
make build
```

## Запуск без `make` (используя Docker напрямую)
Если у вас не установлен `make`, вы можете запускать команды вручную через Docker Compose.

### 1. Инициализация проекта
Выполните по порядку:
```bash
# Копирование файлов окружения
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Создание сетей
docker network create app
docker network create traefik_public

# Сборка и запуск контейнеров
docker compose up -d --build --remove-orphans

# Установка зависимостей и настройка backend
docker compose run --rm --no-deps php-fpm composer install
docker compose run --rm php-fpm php artisan key:generate
docker compose run --rm php-fpm php artisan migrate --force
docker compose run --rm php-fpm php artisan db:seed
docker compose run --rm php-fpm php artisan storage:link
docker compose run --rm --no-deps php-fpm chmod 777 -R storage bootstrap/cache
```

### 2. Основные операции
- **Запуск:** `docker compose up -d`
- **Остановка:** `docker compose down`
- **Остановка с очисткой:** `docker compose down -v`
- **Пересборка:** `docker compose up -d --build`

## Тестовые пользователи
После запуска сидов (`php artisan db:seed`) или при инициализации проекта с командой `make init` в системе доступны 50 рандомных задач и два пользователя:

1. **Администратор:**
   - Email: `admin@example.com`
   - Пароль: `password`
2. **Обычный пользователь:**
   - Email: `user@example.com`
   - Пароль: `password`

Проект доступен по адресу: [http://localhost](http://localhost)

API документация: [http://app.localhost/docs/api](http://app.localhost/docs/api)

## Аутентификация
В приложении используется Laravel Sanctum с подходом Bearer Token.

- При успешном логине через `/api/v1/auth/login` сервер возвращает API токен (`token`).
- Этот токен необходимо передавать в каждом запросе к защищённым роутам в заголовке `Authorization`:
  ```http
  Authorization: Bearer <токен>
  ```
- Для Nuxt фронтенда управление токеном происходит автоматически через плагины и хранилище Pinia.
