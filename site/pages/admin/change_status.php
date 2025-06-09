<?php
require_once '../../config.php';

if (!isAdmin()) {
  header('Location: /index.php');
  exit();
}

$bookingId = $_GET['id'] ?? null;

if (!$bookingId) {
  header('Location: /pages/admin/orders.php');
  exit();
}

// Получение текущей заявки
$stmt = $pdo->prepare("
    SELECT b.*, u.login, s.name as status_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN statuses s ON b.status_id = s.id
    WHERE b.id = ?
");
$stmt->execute([$bookingId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
  header('Location: orders.php');
  exit();
}

// Получение всех возможных статусов
$statuses = $pdo->query("SELECT * FROM statuses")->fetchAll(PDO::FETCH_ASSOC);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newStatusId = $_POST['status_id'];
  $comment = $_POST['comment'] ?? null;

  try {
    // Обновление статуса
    $stmt = $pdo->prepare("UPDATE bookings SET status_id = ? WHERE id = ?");
    $stmt->execute([$newStatusId, $bookingId]);

    // Логирование изменения (если нужно добавить таблицу booking_logs)
    // $logStmt = $pdo->prepare("INSERT INTO booking_logs (...) VALUES (...)");
    // $logStmt->execute([...]);

    $_SESSION['success_message'] = 'Статус заявки успешно обновлен!';
    header("Location: orders.php");
    exit();
  } catch (PDOException $e) {
    $error = "Ошибка при обновлении статуса: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <? require_once '../../headers.php'; ?>
  <title>Мои заявки - Я буду кушац</title>
</head>


<body>
  <?php include '../../components/header.php'; ?>
  <div class="container">
    <h2 class="my-4">Изменение статуса заявки #<?= htmlspecialchars($booking['id']) ?></h2>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card mb-4">
      <div class="card-header">
        <h5>Информация о заявке</h5>
      </div>
      <div class="card-body">
        <p><strong>Пользователь:</strong> <?= htmlspecialchars($booking['login']) ?></p>
        <p><strong>Дата:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
        <p><strong>Время:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>
        <p><strong>Текущий статус:</strong>
          <span class="badge <?= getStatusBadgeClass($booking['status_id']) ?>">
            <?= htmlspecialchars($booking['status_name']) ?>
          </span>
        </p>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Изменение статуса</h5>
      </div>
      <div class="card-body">
        <form method="POST">
          <div class="mb-3">
            <label for="status_id" class="form-label">Новый статус</label>
            <select class="form-select" id="status_id" name="status_id" required>
              <?php foreach ($statuses as $status): ?>
                <option value="<?= $status['id'] ?>" <?= $status['id'] == $booking['status_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($status['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="comment" class="form-label">Комментарий (необязательно)</label>
            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Обновить статус</button>
          <a href="orders.php" class="btn btn-secondary">Отмена</a>
        </form>
      </div>
    </div>
  </div>
</body>