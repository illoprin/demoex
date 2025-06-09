<?
require_once '../config.php';

$errors = [];

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Получаем данные из формы
  $login = trim($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';

  // Валидация полей
  if (empty($login)) {
    $errors[] = "Логин обязателен для заполнения";
  }

  if (empty($password)) {
    $errors[] = "Пароль обязателен для заполнения";
  }

  // Если нет ошибок - проверяем авторизацию
  if (empty($errors)) {
    try {
      $stmt = $pdo->prepare("SELECT id, login, password, role_id FROM users WHERE login = ?");
      $stmt->execute([$login]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
        // Успешная авторизация
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['login'] = $user['login'];

        // Перенаправляем в зависимости от роли
        if (isAdmin()) {
          header('Location: /pages/admin/orders.php');
          exit();
        } else {
          header('Location: orders.php');
          exit();
        }
      } else {
        $errors[] = "Неверный логин или пароль";
      }
    } catch (PDOException $e) {
      $errors[] = "Ошибка авторизации. Попробуйте позже.";
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
  <title>Вход - Я буду кушац</title>
</head>

<body>
  <div class="login-container">
    <div class="card login-card">
      <div class="card-body p-4">
        <h1 class="card-title text-center mb-4">Вход в систему</h1>

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

        <form method="POST" action="">
          <div class="mb-3">
            <label for="login" class="form-label">Логин</label>
            <input type="text" class="form-control" id="login" name="login"
              value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>

          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">Войти</button>
          </div>
        </form>

        <div class="text-center">
          <a href="register.php" class="text-decoration-none">Нет аккаунта? Зарегистрироваться</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>