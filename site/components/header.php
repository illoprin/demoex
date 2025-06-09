<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Я буду кушац</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item">
            <a class="nav-link" href="/pages/orders.php">Мои заявки</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/create_order.php">Создать заявку</a>
          </li>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
          <li class="nav-item">
            <a class="nav-link" href="/pages/orders.php">Заявки пользователей</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/pages/change_status.php">Изменить статус заявки</a>
          </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item">
            <a class="nav-link" href="/logout.php">Выйти</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/pages/login.php">Войти</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/register.php">Регистрация</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>