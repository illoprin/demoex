<?
// Начало сессии
session_start();

if (isset($_SESSION['user_id']))
  $_SESSION = array();

header("Location: /pages/login.php");
exit;