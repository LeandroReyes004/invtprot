<?php 
include 'conexion.php'; 

// Obtenemos quién está usando el sistema ahora mismo
$rol_actual = $_SESSION['rol'] ?? 'Tecnico';
$id_actual = $_SESSION['id_usuario'];

// --- LÓGICA: CREAR USUARIO (SOLO ADMIN) ---
if(isset($_POST['crear_usuario']) && $rol_actual == 'Admin'){
    $nombre = $_POST['nombre'];
    $user = $_POST['usuario'];
    $pass = md5($_POST['clave']); // Encriptar
    $rol_nuevo = $_POST['rol'];

    $check = $conn->query("SELECT * FROM usuarios WHERE usuario = '$user'");
    if($check->num_rows > 0){
        $error = "❌ El usuario '$user' ya existe.";
    } else {
        $sql = "INSERT INTO usuarios (usuario, password, nombre, rol) VALUES ('$user', '$pass', '$nombre', '$rol_nuevo')";
        if($conn->query($sql)) $msg = "✅ Usuario '$nombre' creado con éxito.";
        else $error = "❌ Error: " . $conn->error;
    }
}

// --- LÓGICA: CAMBIAR CONTRASEÑA ---
if(isset($_POST['cambiar_clave'])){
    $id_modificar = $_POST['id_usuario_modificar'];
    $nueva_clave = md5($_POST['nueva_clave']);

    // SEGURIDAD: Solo el Admin puede cambiar cualquier clave. El Técnico solo puede cambiar la suya.
    if($rol_actual == 'Admin' || $id_actual == $id_modificar){
        $conn->query("UPDATE usuarios SET password = '$nueva_clave' WHERE id = $id_modificar");
        $msg = "🔑 Contraseña actualizada correctamente.";
    } else {
        $error = "🚨 No tienes permisos para cambiar esta contraseña.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e4d2b; --accent: #d4af37; --bg: #f0f2f5; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
        
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: fit-content;}
        h2, h3 { color: var(--primary); margin-top: 0; }
        
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-save { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle;}
        th { background: #f8f9fa; color: var(--primary); }
        
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .alert-ok { background: #d4edda; color: #155724; }
        .alert-err { background: #f8d7da; color: #721c24; }
        .badge-admin { background: #1e4d2b; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-tec { background: #6c757d; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

    <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; max-width: 900px; margin: 0 auto 20px auto;">
        <h1 style="color:var(--primary); margin:0;">👥 Gestión de Accesos</h1>
        <a href="index.php" style="text-decoration:none; color:#666; font-weight: 600;">⬅ Volver al Dashboard</a>
    </header>

    <div class="container">
        
        <div class="card">
            <?php if($rol_actual == 'Admin'): ?>
                <h3>➕ Nuevo Usuario</h3>
                
                <?php if(isset($msg)) echo "<div class='alert alert-ok'>$msg</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert alert-err'>$error</div>"; ?>

                <form method="post">
                    <label style="font-size: 12px; font-weight: bold; color: #555;">Nombre Completo:</label>
                    <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required>

                    <label style="font-size: 12px; font-weight: bold; color: #555;">Usuario (Login):</label>
                    <input type="text" name="usuario" placeholder="Ej: jperez" required>

                    <label style="font-size: 12px; font-weight: bold; color: #555;">Contraseña Inicial:</label>
                    <input type="password" name="clave" placeholder="Asigna una clave" required>

                    <label style="font-size: 12px; font-weight: bold; color: #555;">Rol en el sistema:</label>
                    <select name="rol">
                        <option value="Tecnico">Técnico (Limitado)</option>
                        <option value="Admin">Administrador (Total)</option>
                    </select>

                    <button type="submit" name="crear_usuario" class="btn-save">Guardar Usuario</button>
                </form>
            <?php else: ?>
                <div style="text-align:center; padding: 20px;">
                    <h1 style="font-size: 40px; margin:0;">🛡️</h1>
                    <h3 style="color:#555;">Acceso Restringido</h3>
                    <p style="font-size: 13px; color:#888;">Solo los Administradores pueden crear o eliminar cuentas. <br><br> Utiliza la tabla de la derecha para cambiar tu propia contraseña si lo necesitas.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" style="overflow-x: auto;">
            <h3>Lista del Personal IT</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT id, nombre, usuario, rol FROM usuarios ORDER BY id ASC");
                    while($row = $res->fetch_assoc()){
                        
                        $badge = ($row['rol'] == 'Admin') ? "<span class='badge-admin'>Admin</span>" : "<span class='badge-tec'>Técnico</span>";
                        
                        echo "<tr>";
                        echo "<td><strong>" . $row['nombre'] . "</strong><br><small style='color:#888;'>@" . $row['usuario'] . "</small></td>";
                        echo "<td>$badge</td>";
                        echo "<td>";

                        // SEGURIDAD EN INTERFAZ: Mostramos el botón de cambiar clave SOLO si es Admin o si es la cuenta del mismo usuario
                        if($rol_actual == 'Admin' || $row['id'] == $id_actual){
                            echo "
                            <details>
                                <summary style='cursor:pointer; color:#d4af37; font-weight:bold; font-size:13px; outline:none;'>🔑 Editar Clave</summary>
                                <form method='post' style='margin-top:8px; display:flex; gap:5px;'>
                                    <input type='hidden' name='id_usuario_modificar' value='".$row['id']."'>
                                    <input type='password' name='nueva_clave' placeholder='Nueva...' required style='padding:6px; margin:0; width:100px;'>
                                    <button type='submit' name='cambiar_clave' style='padding:6px 10px; background:#1e4d2b; color:white; border:none; border-radius:4px; cursor:pointer;'>OK</button>
                                </form>
                            </details>
                            ";
                        } else {
                            echo "<span style='font-size:12px; color:#aaa;'>Sin permisos</span>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>