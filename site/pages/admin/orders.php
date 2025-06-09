<?php
require_once '../../config.php';

if (!isAdmin()) {
  header('Location: /index.php');
  exit();
}

// Пагинация
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Получение общего количества заявок
$totalStmt = $pdo->query("SELECT COUNT(*) FROM bookings");
$total = $totalStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Получение заявок с пользователями
$stmt = $pdo->prepare("
    SELECT b.*, u.login, s.name as status_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN statuses s ON b.status_id = s.id
    ORDER BY b.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2 class="my-4 text-center">Все заявки пользователей</h2>

    <!-- Пагинация -->
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">Назад</a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">Вперед</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="row">
      <?php foreach ($bookings as $booking): ?>
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
              <span>Заявка #<?= htmlspecialchars($booking['id']) ?></span>
              <span class="badge <?= getStatusBadgeClass($booking['status_id']) ?>">
                <?= htmlspecialchars($booking['status_name']) ?>
              </span>
            </div>
            <div class="card-body">
              <h5 class="card-title">Пользователь: <?= htmlspecialchars($booking['login']) ?></h5>
              <p class="card-text">
                <strong>Дата:</strong> <?= htmlspecialchars($booking['booking_date']) ?><br>
                <strong>Время:</strong> <?= htmlspecialchars($booking['booking_time']) ?><br>
                <strong>Гостей:</strong> <?= htmlspecialchars($booking['guests_count']) ?><br>
                <strong>Телефон:</strong> <?= htmlspecialchars($booking['contact_phone']) ?><br>
                <strong>Создано:</strong> <?= htmlspecialchars($booking['created_at']) ?>
              </p>
              <a href="change_status.php?id=<?= $booking['id'] ?>" class="btn btn-primary">
                Изменить статус
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>