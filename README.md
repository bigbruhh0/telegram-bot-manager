# Telegram Bot Manager

Веб-приложение для управления Telegram-ботами. Позволяет пользователям подключать своих ботов, управлять подписчиками и отправлять массовые рассылки. Проект построен на чистой архитектуре с использованием сервисного слоя и репозиториев.

#  Функциональность


- Авторизация — регистрация и вход через Laravel Breeze

- Подключение ботов — добавление ботов через токен от @BotFather

- Автоматические вебхуки — настройка вебхуков при добавлении бота

- Управление подписчиками — просмотр и удаление подписчиков

- Массовые рассылки — отправка сообщений всем подписчикам через очереди

- Команды бота — обработка /start и /ping

- AJAX — отправка форм без перезагрузки страницы

# 🛠 Технологический стек
- Backend	PHP 8.2, Laravel 11
- Frontend	Tailwind CSS, Alpine.js
- Database	MySQL 8.0
- Queue	Laravel Queues (database)
- Telegram API	defstudio/telegraph v1.70.1
- Container	Docker, Docker Compose
- Auth	Laravel Breeze

# 🚀 Быстрый старт
1. Клонирование репозитория

> git clone https://github.com/your-username/telegram-bot-manager.git
cd telegram-bot-manager

2. Настройка окружения
> cp .env.example .env

3. Запуск Docker контейнеров
> docker-compose up -d

4. Установка зависимостей

**Установка PHP зависимостей**
> docker-compose exec php composer install

**Установка Node.js зависимостей и сборка фронтенда**
> docker-compose exec php npm install
docker-compose exec php npm run build

5. Настройка приложения

**Генерация ключа**
> docker-compose exec php php artisan key:generate

**Запуск миграций**
> docker-compose exec php php artisan migrate

**Очистка кэша**
> docker-compose exec php php artisan optimize:clear

6. Запуск очередей (в отдельном терминале)

> docker-compose exec php php artisan queue:work

7. Доступ к приложению
	

- [x] Откройте браузер: http://localhost:8080

- [x] Регистрация: /register

- [x] Вход: /login

- [x] Dashboard: /dashboard

**Примечание:**
Для корректной работы вебхуков с telegram требуется включить туннелирование и указать домен в .env
