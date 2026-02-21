<?php 
include 'conexion.php'; 

if(isset($_SESSION['admin_activo'])){
    header("Location: index.php");
    exit();
}

$error = "";

if(isset($_POST['ingresar'])){
    $usuario = $_POST['usuario'];
    $clave = MD5($_POST['clave']); 

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$clave'";
    $res = $conn->query($sql);

    if($res->num_rows > 0){
        $datos = $res->fetch_assoc();
        $_SESSION['admin_activo'] = true;
        $_SESSION['id_usuario'] = $datos['id'];       // NUEVO: Guardamos su ID
        $_SESSION['nombre_admin'] = $datos['nombre'];
        $_SESSION['rol'] = $datos['rol'];             // NUEVO: Guardamos su Rol (Admin o Tecnico)
        
        header("Location: index.php"); 
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vistas Golf IT</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #1e4d2b; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 350px; text-align: center; }
        h2 { color: #1e4d2b; margin-top: 0; }
        input { width: 100%; padding: 12px; margin: 10px 0 20px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 12px; background: #d4af37; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-login:hover { background: #b5952f; }
        .error-msg { color: #d9534f; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>⛳ Vistas Golf</h2>
        <p style="color:#666; font-size:14px; margin-bottom:20px;">Gestión de Activos IT</p>
        <?php if($error != "") echo "<div class='error-msg'>$error</div>"; ?>
        <form method="post">
            <input type="text" name="usuario" placeholder="Usuario" required autofocus>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit" name="ingresar" class="btn-login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>