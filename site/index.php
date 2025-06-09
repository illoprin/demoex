<?
// Подключаем конфигурацию и функции
require_once 'config.php';

// Проверяем авторизацию и перенаправляем
if (isLoggedIn()) {
    // Если пользователь авторизован
    if (isAdmin()) {
        // Если админ - на админ-панель
        header('Location: pages/admin/orders.php');
        exit;
    } else {
        // Если обычный пользователь - в личный кабинет
        header('Location: pages/orders.php');
        exit;
    }
} else {
    // Если не авторизован - на страницу входа
    header('Location: pages/login.php');
    exit;
}