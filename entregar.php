<?php include 'conexion.php';

// Validar ID de equipo
if(!isset($_GET['id_equipo'])){ die("Seleccione un equipo primero."); }
$id_equipo = $_GET['id_equipo'];

// Obtener datos del equipo
$equipo = $conn->query("SELECT * FROM equipos WHERE id = $id_equipo")->fetch_assoc();

// --- LÓGICA: PROCESAR ENTREGA (SALIDA) ---
if(isset($_POST['entregar'])){
    $id_insumo = $_POST['insumo'];
    $cantidad = $_POST['cantidad'];
    $usuario = $_POST['usuario'];

    // 1. Verificar si hay stock suficiente
    $check = $conn->query("SELECT stock_actual, nombre FROM insumos WHERE id=$id_insumo")->fetch_assoc();
    
    if($check['stock_actual'] >= $cantidad){
        // 2. Descontar Stock
        $conn->query("UPDATE insumos SET stock_actual = stock_actual - $cantidad WHERE id = $id_insumo");
        // 3. Registrar Movimiento (SALIDA) vinculado a este equipo
        $conn->query("INSERT INTO movimientos_insumos (insumo_id, equipo_id, tipo_movimiento, cantidad, usuario_registro) VALUES ($id_insumo, $id_equipo, 'SALIDA', $cantidad, '$usuario')");
        
        $msg = "✅ Se entregaron $cantidad de " . $check['nombre'] . " correctamente.";
    } else {
        $error = "❌ Error: Solo quedan " . $check['stock_actual'] . " en stock.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregar Insumo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; padding: 20px; display:flex; justify-content:center; }
        .card { background: white; width: 100%; max-width: 500px; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #1e4d2b; margin-top: 0; }
        .info-eq { background: #e8f5e9; padding: 10px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #1e4d2b; }
        select, input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;}
        button { width: 100%; padding: 15px; background: #1e4d2b; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        .msg { padding: 10px; margin-bottom: 10px; border-radius: 6px; text-align: center; }
        .cancel-btn { background:none; border:none; color:#666; text-decoration:underline; cursor:pointer; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>🎁 Entregar Suministro</h2>
        
        <?php if(isset($msg)) echo "<div class='msg' style='background:#d4edda; color:#155724;'>$msg</div>"; ?>
        <?php if(isset($error)) echo "<div class='msg' style='background:#f8d7da; color:#721c24;'>$error</div>"; ?>

        <div class="info-eq">
            <strong>Equipo:</strong> <?php echo $equipo['tipo_equipo']; ?> <br>
            <strong>Modelo:</strong> <?php echo $equipo['marca'] . " " . $equipo['modelo']; ?><br>
            <strong>Ubicación:</strong> <?php echo $equipo['departamento']; ?>
        </div>

        <form method="post">
            <label>Selecciona el Insumo Compatible:</label>
            <select name="insumo" required>
                <option value="">-- Seleccione --</option>
                <?php
                // --- CEREBRO DE FILTRADO INTELIGENTE ---
                $tipo_eq = $equipo['tipo_equipo']; 
                $modelo_eq = $equipo['modelo'];
                
                // 1. Definir qué TIPOS de insumos permite este equipo
                $tipos_permitidos = "";
                
                if($tipo_eq == 'PC' || $tipo_eq == 'Laptop'){
                    // Las PC usan repuestos, hardware, periféricos
                    $tipos_permitidos = "'Repuesto', 'Hardware', 'Periferico'";
                } 
                elseif($tipo_eq == 'Celular' || $tipo_eq == 'Tablet'){
                    // Los celulares usan cargadores, pantallas, baterías
                    $tipos_permitidos = "'Accesorio', 'Bateria', 'Pantalla', 'Repuesto'";
                } 
                else {
                    // Asumimos que "Otro" o "Impresora" usa consumibles
                    $tipos_permitidos = "'Toner', 'Papel', 'Tinta', 'Cartucho'";
                }

                // 2. Consulta SQL Blindada
                // Busca insumos que coincidan con el TIPO permitido
                // Y ADEMÁS coincidan con el modelo o sean Universal
                $sql_insumos = "SELECT * FROM insumos 
                                WHERE tipo IN ($tipos_permitidos) 
                                AND (modelo_compatible = '$modelo_eq' OR modelo_compatible = 'Universal')";
                
                $res_ins = $conn->query($sql_insumos);
                
                if($res_ins->num_rows > 0){
                    while($row = $res_ins->fetch_assoc()){
                        echo "<option value='".$row['id']."'>".$row['nombre']." (Quedan: ".$row['stock_actual'].")</option>";
                    }
                } else {
                    echo "<option disabled>❌ No hay insumos compatibles para este tipo de equipo</option>";
                }
                ?>
            </select>

            <label>Cantidad a entregar:</label>
            <input type="number" name="cantidad" value="1" min="1" required>

            <label>Entregado por:</label>
            <input type="text" name="usuario" placeholder="Tu nombre" required>

            <button type="submit" name="entregar">Confirmar Entrega</button>
        </form>
        
        <center>
            <button onclick="window.history.back()" class="cancel-btn">⬅ Cancelar y Volver</button>
        </center>
    </div>

</body>
</html>