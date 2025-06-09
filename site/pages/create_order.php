<?
require_once '../config.php';

if (!isLoggedIn()) {
  header('Location: /pages/login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_SESSION['user_id'];
  $bookingDate = $_POST['booking_date'];
  $bookingTime = $_POST['booking_time'];
  $guestsCount = $_POST['guests_count'];
  $contactPhone = $_POST['contact_phone'];
  $statusId = 1; // pending

  try {
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, guests_count, contact_phone, status_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $bookingDate, $bookingTime, $guestsCount, $contactPhone, $statusId]);

    header('Location: /pages/orders.php');
    exit();
  } catch (PDOException $e) {
    $error = "Ошибка при создании заявки: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <? require_once '../headers.php'; ?>
  <title>Создать заявку - Я буду кушац</title>
</head>

<body>
  <?php include '../components/header.php'; ?>

  <div class="container mt-4 border rounded-3 p-3">
    <h2 class="mb-4">Создать заявку на бронирование</h2>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form id="bookingForm" method="POST">
      <div class="mb-3">
        <label for="booking_date" class="form-label">Дата бронирования</label>
        <input type="date" class="form-control" id="booking_date" name="booking_date" required>
        <div class="invalid-feedback">Пожалуйста, выберите дату начиная с завтрашнего дня.</div>
      </div>

      <div class="mb-3">
        <label for="booking_time" class="form-label">Время бронирования</label>
        <input type="time" class="form-control" id="booking_time" name="booking_time" min="10:00" max="22:00" required>
        <div class="invalid-feedback">Ресторан работает с 10:00 до 22:00.</div>
      </div>

      <div class="mb-3">
        <label for="guests_count" class="form-label">Количество гостей</label>
        <input type="number" class="form-control" id="guests_count" name="guests_count" min="1" max="20" required>
        <div class="invalid-feedback">Пожалуйста, укажите от 1 до 20 гостей.</div>
      </div>

      <div class="mb-3">
        <label for="contact_phone" class="form-label">Контактный телефон</label>
        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" required>
        <div class="invalid-feedback">Пожалуйста, укажите контактный телефон.</div>
      </div>

      <button type="submit" class="btn btn-primary">Отправить заявку</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('bookingForm');
      const bookingDate = document.getElementById('booking_date');
      const bookingTime = document.getElementById('booking_time');

      // Установка минимальной даты (завтра)
      const today = new Date();
      const tomorrow = new Date(today);
      tomorrow.setDate(today.getDate() + 1);

      const minDate = tomorrow.toISOString().split('T')[0];
      bookingDate.setAttribute('min', minDate);

      // Валидация даты
      bookingDate.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        if (selectedDate < tomorrow) {
          this.setCustomValidity('Дата должна быть не раньше завтрашнего дня');
        } else {
          this.setCustomValidity('');
        }
      });

      // Валидация времени
      bookingTime.addEventListener('change', function() {
        const time = this.value;
        const [hours, minutes] = time.split(':').map(Number);

        if (hours < 10 || hours > 22 || (hours === 22 && minutes > 0)) {
          this.setCustomValidity('Ресторан работает с 10:00 до 22:00');
        } else {
          this.setCustomValidity('');
        }
      });

      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add('was-validated');
      }, false);
    });
  </script>
</body>