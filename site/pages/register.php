<?
require_once '../config.php';

$errors = [];
$success = false;

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Получаем данные из формы
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $login = trim($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';

  // Валидация полей
  if (empty($first_name)) {
    $errors[] = "Имя обязательно для заполнения";
  }

  if (empty($last_name)) {
    $errors[] = "Фамилия обязательна для заполнения";
  }

  if (empty($phone)) {
    $errors[] = "Телефон обязателен для заполнения";
  }

  if (empty($email)) {
    $errors[] = "Email обязателен для заполнения";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный формат email";
  }

  if (empty($login)) {
    $errors[] = "Логин обязателен для заполнения";
  } elseif (strlen($login) < 3) {
    $errors[] = "Логин должен содержать минимум 3 символа";
  }

  if (empty($password)) {
    $errors[] = "Пароль обязателен для заполнения";
  } elseif (strlen($password) < 6) {
    $errors[] = "Пароль должен содержать минимум 6 символов";
  }

  // Проверка уникальности email и логина
  if (empty($errors)) {
    try {
      // Проверяем уникальность email
      $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким email уже существует";
      }

      // Проверяем уникальность логина
      $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
      $stmt->execute([$login]);
      if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким логином уже существует";
      }
    } catch (PDOException $e) {
      $errors[] = "Ошибка проверки данных";
    }
  }

  // Если нет ошибок - регистрируем пользователя
  if (empty($errors)) {
    try {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, phone, email, login, password, role_id) VALUES (?, ?, ?, ?, ?, ?, 1)");
      $stmt->execute([$first_name, $last_name, $phone, $email, $login, $hashed_password]);

      $success = true;
    } catch (PDOException $e) {
      $errors[] = "Ошибка регистрации. Попробуйте позже.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <? require_once '../headers.php'; ?>
  <title>Регистрация - Я буду кушац</title>
</head>

<body>
  <div class="register-container">
    <div class="card register-card">
      <div class="card-body p-4">
        <h1 class="card-title text-center mb-4">Регистрация</h1>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <strong>Ошибки:</strong>
            <ul class="mb-0 mt-2">
              <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success text-center">
            <strong>Регистрация успешно завершена!</strong><br>
            Теперь вы можете <a href="login.php" class="alert-link">войти в систему</a>
          </div>
        <?php else: ?>
          <form method="POST" action="">
            <div class="mb-3">
              <label for="first_name" class="form-label">Имя *</label>
              <input type="text" class="form-control" id="first_name" name="first_name"
                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label for="last_name" class="form-label">Фамилия *</label>
              <input type="text" class="form-control" id="last_name" name="last_name"
                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Телефон *</label>
              <input type="tel" class="form-control" id="phone" name="phone"
                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                placeholder="+7 (999) 123-45-67" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label for="login" class="form-label">Логин *</label>
              <input type="text" class="form-control" id="login" name="login"
                value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Пароль *</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg">Зарегистрироваться</button>
            </div>
          </form>
        <?php endif; ?>

        <div class="text-center">
          <a href="login.php" class="text-decoration-none">Уже есть аккаунт? Войти</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>