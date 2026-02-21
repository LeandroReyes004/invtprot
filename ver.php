<?php 
include 'conexion.php'; 

// 1. Validar ID
if(!isset($_GET['id'])){ die("Error: No se especificó ningún equipo."); }
$id = $_GET['id'];

// 2. Lógica para GUARDAR UN REPORTE DE FALLO (Si se envió el formulario)
if(isset($_POST['reportar'])){
    $falla = $_POST['falla'];
    $tecnico = "Soporte TI"; // Puedes cambiar esto después
    $conn->query("INSERT INTO historial_errores (equipo_id, fecha_reporte, falla_reportada, tecnico) VALUES ($id, NOW(), '$falla', '$tecnico')");
    echo "<script>alert('Fallo reportado correctamente');</script>";
}

// 3. Buscar datos del equipo
$sql = "SELECT * FROM equipos WHERE id = $id";

$res = $conn->query($sql);
if($res->num_rows == 0){ die("Equipo no encontrado."); }
$equipo = $res->fetch_assoc();


// 4. Buscar historial de fallos de ESTE equipo
$historial = $conn->query("SELECT * FROM historial_errores WHERE equipo_id = $id ORDER BY fecha_reporte DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Ficha Técnica - Vistas Golf</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e4d2b; --bg: #f4f7f6; --white: #fff; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; padding: 20px; color: #333; }
        
        .card { background: var(--white); border-radius: 15px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        
        h1 { font-size: 22px; color: var(--primary); margin: 0 0 5px 0; }
        .tag { background: var(--primary); color: white; padding: 5px 10px; border-radius: 5px; font-size: 14px; display: inline-block; margin-bottom: 15px; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
        .item { background: #f9f9f9; padding: 10px; border-radius: 8px; }
        .label { font-size: 11px; color: #888; text-transform: uppercase; font-weight: bold; }
        .value { font-size: 14px; font-weight: 600; color: #333; word-break: break-word; }

        .btn-report { width: 100%; background: #d9534f; color: white; border: none; padding: 15px; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px; box-sizing: border-box; }
        
        .history-item { border-left: 3px solid #d9534f; padding-left: 10px; margin-bottom: 10px; }
        .date { font-size: 11px; color: #999; }
    </style>
</head>
<body>

    <div class="card">
        <div class="tag"><?php echo $equipo['codigo_qr']; ?></div>
        <h1><?php echo $equipo['usuario']; ?></h1>
        <p style="margin:0; color:#666;"><?php echo $equipo['departamento']; ?></p>
        
        <hr style="border:0; border-top:1px solid #eee; margin: 15px 0;">

        <div class="grid">
            <div class="item">
                <div class="label">Modelo</div>
                <div class="value"><?php echo $equipo['marca'] ." ". $equipo['modelo']; ?></div>
            </div>
            <div class="item">
                <div class="label">Service Tag</div>
                <div class="value"><?php echo $equipo['service_tag']; ?></div>
            </div>
            <div class="item">
                <div class="label">Procesador</div>
                <div class="value"><?php echo $equipo['procesador']; ?></div>
            </div>
            <div class="item">
                <div class="label">RAM / Disco</div>
                <div class="value"><?php echo $equipo['memoria']; ?> / <?php echo $equipo['disco_tamano']; ?></div>
            </div>
        </div>

        <?php if($equipo['tiene_monitor']): ?>
        <div class="item" style="margin-top:10px; background: #e8f5e9;">
            <div class="label">🖥️ Monitor Asignado</div>
            <div class="value"><?php echo $equipo['monitor_datos']; ?></div>
        </div>
        <?php endif; ?>
    </div>
    <div style="margin-bottom: 20px;">
        <a href="entregar.php?id_equipo=<?php echo $id; ?>" style="text-decoration:none;">
            <div style="background: linear-gradient(45deg, #1e4d2b, #2e7d32); color: white; padding: 15px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div>
                    <span style="font-size: 18px; font-weight: bold;">🎁 Entregar Insumo</span>
                    <div style="font-size: 12px; opacity: 0.9;">Tóner, Papel, Repuestos...</div>
                </div>
                <span style="font-size: 24px;">➔</span>
            </div>
        </a>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">🔧 Historial de Fallas</h3>
        
        <?php 
        if($historial->num_rows > 0){
            while($row = $historial->fetch_assoc()){
                echo "<div class='history-item'>";
                echo "<div class='date'>" . date("d/m/Y", strtotime($row['fecha_reporte'])) . "</div>";
                echo "<div>" . $row['falla_reportada'] . "</div>";
                if($row['solucion_aplicada']){
                    echo "<div style='color:green; font-size:12px;'>✅ " . $row['solucion_aplicada'] . "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<p style='color:#999; font-size:13px;'>Este equipo no tiene fallas reportadas.</p>";
        }
        ?>

        <hr>
        <details>
            <summary style="cursor:pointer; color: #d9534f; font-weight:bold;">🚨 Reportar nueva falla</summary>
            <form method="post">
                <textarea name="falla" rows="3" placeholder="Describe el problema (Ej: No da video, hace ruido)..." required></textarea>
                <button type="submit" name="reportar" class="btn-report">Guardar Reporte</button>
            </form>
        </details>
    </div>

</body>
</html>