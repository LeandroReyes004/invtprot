
<?php
session_start(); // Iniciamos las sesiones

// Saber en qué archivo estamos parados
$archivo_actual = basename($_SERVER['PHP_SELF']);

// EL GUARDIÁN: Si no hay sesión activa y NO estamos en el login... ¡Pa' fuera!
if(!isset($_SESSION['admin_activo']) && $archivo_actual != 'login.php'){
    header("Location: login.php");
    exit();
}

// --- TUS DATOS DE CONEXIÓN HOSTINGER AQUÍ ---
$host = "localhost";
$user = "u687815389_AdminVistas";
$pass = "Apolo9090###";   // Tu clave
$db   = "u687815389_InvtEquipo"; // Tu base de datos

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }
?>