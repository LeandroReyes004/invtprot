<?php
session_start();
session_destroy(); // Destruye todo
header("Location: login.php"); // Te manda al login
exit();
?>