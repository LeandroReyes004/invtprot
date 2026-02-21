<?php include 'conexion.php'; 

// --- LÓGICA: REGISTRAR NUEVO INSUMO O AUMENTAR STOCK ---
if(isset($_POST['crear_insumo'])){
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $compat = $_POST['modelo'];
    $min = $_POST['minimo'];
    
    // Evitar guardar vacíos si le das al botón sin querer
    if(!empty($nombre)){
        $conn->query("INSERT INTO insumos (nombre, tipo, stock_actual, stock_minimo, modelo_compatible) VALUES ('$nombre', '$tipo', 0, '$min', '$compat')");
    }
}

if(isset($_POST['agregar_stock'])){
    $id_insumo = $_POST['id_insumo'];
    $cantidad = $_POST['cantidad'];
    
    // 1. Aumentar Stock
    $conn->query("UPDATE insumos SET stock_actual = stock_actual + $cantidad WHERE id = $id_insumo");
    // 2. Registrar el Movimiento (ENTRADA)
    $conn->query("INSERT INTO movimientos_insumos (insumo_id, tipo_movimiento, cantidad, usuario_registro) VALUES ($id_insumo, 'ENTRADA', $cantidad, 'Admin')");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacén - Vistas Golf</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e4d2b; --accent: #d4af37; --bg: #f0f2f5; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 600px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .stock-ok { color: green; font-weight: bold; }
        .stock-low { color: red; font-weight: bold; background: #ffe6e6; padding: 4px 8px; border-radius: 4px; display: inline-block; }
        
        .btn { padding: 8px 15px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; transition: 0.2s; }
        .btn:hover { opacity: 0.9; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>

    <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h1 style="color:var(--primary); margin:0;">📦 Almacén de Suministros</h1>
        <a href="index.php" style="text-decoration:none; color:#666; font-weight: 600;">⬅ Volver al Dashboard</a>
    </header>

    <div class="card">
        <h3 style="color:#333; margin-top:0;">Inventario Disponible</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Insumo</th>
                    <th style="width: 25%;">Compatibilidad</th>
                    <th style="width: 15%;">Stock Actual</th>
                    <th style="width: 30%;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM insumos ORDER BY id DESC");
                while($row = $res->fetch_assoc()){
                    $class = ($row['stock_actual'] <= $row['stock_minimo']) ? 'stock-low' : 'stock-ok';
                    
                    echo "<tr>";
                    
                    // COLUMNA 1: Nombre del producto (Aquí estaba el error antes)
                    echo "<td>
                            <strong>" . $row['nombre'] . "</strong><br>
                            <small style='color:#777;'>" . $row['tipo'] . "</small>
                          </td>";
                    
                    // COLUMNA 2: Modelo compatible
                    echo "<td>" . $row['modelo_compatible'] . "</td>";
                    
                    // COLUMNA 3: Cantidad en almacén
                    echo "<td><span class='$class'>" . $row['stock_actual'] . " Unids.</span></td>";
                    
                    // COLUMNA 4: Botones de Editar y Comprar (Solo aquí deben ir)
                    echo "<td>
                            <div style='display:flex; align-items:center; gap:10px;'>
                                <a href='editar_insumo.php?id=".$row['id']."' style='text-decoration:none; font-size:18px; padding: 5px;' title='Editar Item'>✏️</a>
                                
                                <form method='post' style='display:flex; gap:5px; margin:0;'>
                                    <input type='hidden' name='id_insumo' value='".$row['id']."'>
                                    <input type='number' name='cantidad' placeholder='+' style='width:50px; padding:6px; text-align:center;' required>
                                    <button type='submit' name='agregar_stock' class='btn' style='font-size:12px; padding:6px 12px;'>Comprar</button>
                                </form>
                            </div>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="background: #e8f5e9; border: 1px solid #c8e6c9;">
        <h3 style="color:#1e4d2b; margin-top:0;">✨ Registrar Nuevo Tipo de Insumo</h3>
        <form method="post" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; align-items:end;">
            
            <div>
                <label style="font-size:12px; font-weight:bold; color:#1e4d2b;">Nombre del Producto</label>
                <input type="text" name="nombre" placeholder="Ej: Cartucho HP 85A" style="width:100%;" required>
            </div>
            
            <div>
                <label style="font-size:12px; font-weight:bold; color:#1e4d2b;">Tipo</label>
                <select name="tipo" style="width:100%;">
                    <option value="Toner">Toner / Tinta</option>
                    <option value="Papel">Papel / Rollo</option>
                    <option value="Repuesto">Repuesto / Pieza</option>
                    <option value="Accesorio">Accesorio (Cables, Mouse)</option>
                    <option value="Bateria">Batería</option>
                    <option value="Pantalla">Pantalla</option>
                </select>
            </div>

            <div>
                <label style="font-size:12px; font-weight:bold; color:#1e4d2b;">Compatible con (Modelo)</label>
                <input type="text" name="modelo" placeholder="Ej: HP P1102 / Universal" style="width:100%;" required>
            </div>
            
            <div>
                <label style="font-size:12px; font-weight:bold; color:#1e4d2b;">Alerta Stock Mínimo</label>
                <input type="number" name="minimo" placeholder="Ej: 2" value="2" style="width:100%;">
            </div>

            <button type="submit" name="crear_insumo" class="btn" style="background:var(--accent); color:white; font-weight:bold; height: 38px;">Guardar Nuevo Item</button>
        </form>
    </div>

</body>
</html>