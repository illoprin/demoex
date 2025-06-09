# 🍽️ Учебный проект "Бронирование столиков"

Простое веб-приложение для бронирования столиков в ресторане (учебный проект для экзамена)

## 🚀 Быстрый старт

1. Убедитесь, что установлены Docker и Docker Compose
2. Клонируйте репозиторий
3. Выполните команду:

```bash
docker compose up -d
```

4. Откройте в браузере: [http://localhost](http://localhost)

## 🐳 Docker-окружение

Сервисы:

- **nginx**: Веб-сервер (порт 80)
- **php**: Обработка PHP-кода
- **mysql**: База данных (порт 3306)

## 📌 Cтраницы

1. **Авторизация** (`/pages/login.php`)

   - Проверка логина/пароля
   - Запись в сессию

2. **Создание заявки** (`/pages/create_order.php`)

   - POST-запрос с валидацией
   - Привязка к текущему пользователю

3. **Мои заявки** (`/pages/orders.php`)

   - Показывает только заявки текущего пользователя

4. **Админ-панель** (`/pages/admin/orders.php`)
   - Просмотр всех заявок
   - Изменение статусов (`/pages/admin/change_status.php`)

## 📊 Схема базы данных

### 1. Таблица `roles` (Роли пользователей)

| id  | name  |
| --- | ----- |
| 1   | user  |
| 2   | admin |

- Определяет права доступа (обычный пользователь или администратор)

### 2. Таблица `users` (Пользователи)

| id  | role_id | first_name | last_name | phone        | email             | login  | password (hash) |
| --- | ------- | ---------- | --------- | ------------ | ----------------- | ------ | --------------- |
| 1   | 1       | Иван       | Иванов    | +79991112233 | ivan@example.com  | ivanov | $2y$10$...      |
| 2   | 2       | Админ      | Админов   | +79994445566 | admin@example.com | admin  | $2y$10$...      |

- Содержит данные пользователей и их роли
- Поля `email` и `login` уникальны

### 3. Таблица `statuses` (Статусы заявок)

| id  | name                     |
| --- | ------------------------ |
| 1   | pending (ожидание)       |
| 2   | confirmed (подтверждено) |
| 3   | rejected (отклонено)     |

### 4. Таблица `bookings` (Заявки на бронирование)

| id  | user_id | booking_date | booking_time | guests_count | contact_phone | status_id | created_at          |
| --- | ------- | ------------ | ------------ | ------------ | ------------- | --------- | ------------------- |
| 1   | 1       | 2023-12-25   | 19:30:00     | 4            | +79991112233  | 1         | 2023-12-20 10:00:00 |

- Связана с пользователями и статусами внешними ключами

## 🔐 Система авторизации (псевдокод)

```php
// Примерная логика авторизации
session_start();

function login($login, $password) {
    // 1. Находим пользователя в БД
    $user = db_query("SELECT * FROM users WHERE login = ?", [$login]);

    // 2. Проверяем пароль
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        return true;
    }
    return false;
}
```

## 🛡️ Безопасность

1. **Защита от XSS**:

```php
// Все выводы данных экранируются
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

2. **Валидация заявок**:

```php
// Проверка данных перед сохранением
function validateBooking($data) {
    $errors = [];
    if (empty($data['booking_date'])) $errors[] = "Укажите дату";
    if ($data['guests_count'] < 1) $errors[] = "Неверное количество гостей";
    // ... другие проверки
    return $errors;
}
```

3. **PDO для работы с БД**:

```php
// Безопасные запросы с параметрами
$stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
$stmt->execute([$login]);
```
Настройки БД (из `docker-compose.yml`):

- Логин: `appuser`
- Пароль: `apppassword`
- База данных: `appdb`
