<?
session_start();

$host = 'mysql';
$dbname = 'appdb';
$username = 'appuser';
$password = 'apppassword';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Ошибка подключения: " . $e->getMessage());
}

// Простая функция проверки авторизации
function isLoggedIn()
{
  return isset($_SESSION['user_id']);
}

// Проверка админа (user_id = 1)
function isAdmin()
{
  return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2;
}

function getStatusBadgeClass($statusId)
{
  switch ($statusId) {
    case 1:
      return 'bg-warning text-dark'; // pending
    case 2:
      return 'bg-success'; // confirmed
    case 3:
      return 'bg-danger'; // rejected
    default:
      return 'bg-secondary';
  }
}
