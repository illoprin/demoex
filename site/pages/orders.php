<?
require_once '../config.php';
if (!isLoggedIn()) {
  header('Location: login.php');
  exit();
}

$userId = $_SESSION['user_id'];

try {
  $stmt = $pdo->prepare("SELECT b.*, s.name as status_name 
    FROM bookings b
    JOIN statuses s ON b.status_id = s.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
  ");
  $stmt->execute([$userId]);
  $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Ошибка при получении заявок: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <? require_once '../headers.php'; ?>
  <title>Мои заявки - Я буду кушац</title>
</head>

<body>
  <?php include '../components/header.php'; ?>


  <div class="container mt-4 border rounded-3 p-3">

  <h2 class="mb-4 text-center">Мои заявки на бронирование</h2>
    <?php if (empty($bookings)): ?>
      <div class="alert alert-info">У вас пока нет заявок на бронирование.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th>Дата</th>
              <th>Время</th>
              <th>Количество гостей</th>
              <th>Контактный телефон</th>
              <th>Статус</th>
              <th>Дата создания</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $booking): ?>
              <tr>
                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                <td><?php echo htmlspecialchars($booking['guests_count']); ?></td>
                <td><?php echo htmlspecialchars($booking['contact_phone']); ?></td>
                <td>
                  <?php
                  $badgeClass = '';
                  switch ($booking['status_id']) {
                    case 1: // pending
                      $badgeClass = 'bg-warning text-dark';
                      break;
                    case 2: // confirmed
                      $badgeClass = 'bg-success';
                      break;
                    case 3: // rejected
                      $badgeClass = 'bg-danger';
                      break;
                    default:
                      $badgeClass = 'bg-secondary';
                  }
                  ?>
                  <span class="badge <?php echo $badgeClass; ?>">
                    <?php echo htmlspecialchars($booking['status_name']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>