<?php include 'conexion.php'; 

// --- CONTADORES PARA EL DASHBOARD ---
$total = $conn->query("SELECT COUNT(*) as c FROM equipos")->fetch_assoc()['c'];
$operativos = $conn->query("SELECT COUNT(*) as c FROM equipos WHERE estado='Operativo'")->fetch_assoc()['c'];
$revision = $conn->query("SELECT COUNT(*) as c FROM equipos WHERE estado='En Revisión'")->fetch_assoc()['c'];
$celulares = $conn->query("SELECT COUNT(*) as c FROM equipos WHERE tipo_equipo='Celular'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vistas Golf IT</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e4d2b; --accent: #d4af37; --bg: #f0f2f5; --white: #ffffff; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg); margin: 0; padding: 20px; }
        
        /* TARJETAS KPI (INDICADORES) */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .kpi-card { background: var(--white); padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .kpi-num { font-size: 32px; font-weight: 700; color: var(--primary); margin: 0; }
        .kpi-label { font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 1px; }

        /* ACCIONES RÁPIDAS */
        .action-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn { padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; flex: 1; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: white; color: var(--primary); border: 2px solid var(--primary); }

        /* TABLA RESUMEN */
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #333; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { color: #888; font-weight: 500; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .st-operativo { background: #e6f4ea; color: #1e8e3e; }
        .st-revision { background: #fef7e0; color: #f9a825; }
        .st-baja { background: #fce8e6; color: #c5221f; }
    </style>
</head>
<body>

   <header style="text-align: center; margin-bottom: 30px; position: relative;">
        <a href="logout.php" style="position: absolute; right: 0; top: 0; background: #d9534f; color: white; text-decoration: none; padding: 5px 15px; border-radius: 5px; font-size: 12px; font-weight: bold;">🚪 Cerrar Sesión</a>
        
        <h1 style="color: var(--primary); margin:0;">⛳ Vistas Golf IT</h1>
        <p style="margin:5px 0; color:#666;">Panel de Control de Activos</p>
        
        <div style="font-size: 13px; color: #1e4d2b; font-weight: bold; margin-top: 10px;">
            Hola, <?php echo $_SESSION['nombre_admin']; ?> 👋
        </div>
    </header>

    <div class="kpi-grid">
        <div class="kpi-card">
            <p class="kpi-num"><?php echo $total; ?></p>
            <p class="kpi-label">Total Activos</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-num" style="color: #1e8e3e;"><?php echo $operativos; ?></p>
            <p class="kpi-label">Operativos</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-num" style="color: #f9a825;"><?php echo $revision; ?></p>
            <p class="kpi-label">En Revisión</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-num" style="color: #1976d2;"><?php echo $celulares; ?></p>
            <p class="kpi-label">Flotas/Tablets</p>
        </div>
    </div>
    <?php
    $alerta = $conn->query("SELECT * FROM insumos WHERE stock_actual <= stock_minimo");
    if($alerta->num_rows > 0){
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;'>";
        echo "<strong>⚠️ ATENCIÓN COMPRAS:</strong> Se están agotando los siguientes insumos: ";
        while($row = $alerta->fetch_assoc()){
            echo " • " . $row['nombre'] . " (Quedan " . $row['stock_actual'] . ")";
        }
        echo "</div>";
    }
    ?>

   <div class="action-bar">
        <a href="agregar.php" class="btn btn-primary">➕ Nuevo Equipo</a>
        <a href="inventario.php" class="btn btn-outline">📋 Lista Completa</a>
        <a href="insumos.php" class="btn" style="background-color: #d4af37; color: white;">📦 Suministros</a>
        
        <a href="usuarios.php" class="btn" style="background-color: #6c757d; color: white;">👥 Usuarios</a>
    </div>
    <div class="table-container">
        <h2>📌 Últimos Movimientos</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Equipo / Usuario</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostramos solo los últimos 5
                $res = $conn->query("SELECT * FROM equipos ORDER BY id DESC LIMIT 5");
                while($row = $res->fetch_assoc()){
                    // Definir color del estado
                    $clase_estado = "st-operativo"; 
                    if($row['estado'] == 'En Revisión') $clase_estado = "st-revision";
                    if($row['estado'] == 'Dañado') $clase_estado = "st-baja";

                    echo "<tr>";
                    echo "<td>" . $row['tipo_equipo'] . "</td>";
                    echo "<td><b>" . $row['codigo_qr'] . "</b><br><small>" . $row['usuario'] . "</small></td>";
                    echo "<td><span class='badge $clase_estado'>" . $row['estado'] . "</span></td>";
                    echo "<td><a href='ticket.php?id=".$row['id']."'>🖨️</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>