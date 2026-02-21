
<?php include 'conexion.php'; 

$id = $_GET['id'];
$row = $conn->query("SELECT * FROM insumos WHERE id=$id")->fetch_assoc();

if(isset($_POST['actualizar'])){
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $minimo = $_POST['minimo'];
    $stock = $_POST['stock']; // Ajuste manual
    $modelo = $_POST['modelo'];

    $conn->query("UPDATE insumos SET nombre='$nombre', tipo='$tipo', stock_minimo='$minimo', stock_actual='$stock', modelo_compatible='$modelo' WHERE id=$id");
    
    header("Location: insumos.php"); // Nos devuelve al almacén
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Insumo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; padding: 20px; display:flex; justify-content:center; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        label { font-size: 13px; font-weight: bold; color: #555; }
        .btn { width: 100%; padding: 12px; background: #1e4d2b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .btn-cancel { background: #ccc; color: #333; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="card">
        <h2 style="color:#1e4d2b; margin-top:0;">✏️ Editar Suministro</h2>
        
        <form method="post">
            <label>Nombre del Producto:</label>
            <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>

            <label>Tipo:</label>
            <select name="tipo">
                <option value="Toner" <?php if($row['tipo']=='Toner') echo 'selected'; ?>>Toner / Tinta</option>
                <option value="Papel" <?php if($row['tipo']=='Papel') echo 'selected'; ?>>Papel / Rollo</option>
                <option value="Repuesto" <?php if($row['tipo']=='Repuesto') echo 'selected'; ?>>Repuesto</option>
            </select>

            <label>Modelo Compatible:</label>
            <input type="text" name="modelo" value="<?php echo $row['modelo_compatible']; ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label>Stock Mínimo (Alerta):</label>
                    <input type="number" name="minimo" value="<?php echo $row['stock_minimo']; ?>">
                </div>
                <div>
                    <label>Stock REAL (Ajuste):</label>
                    <input type="number" name="stock" value="<?php echo $row['stock_actual']; ?>" style="background:#e8f5e9; font-weight:bold;">
                </div>
            </div>

            <button type="submit" name="actualizar" class="btn">Guardar Cambios</button>
            <a href="insumos.php"><button type="button" class="btn btn-cancel">Cancelar</button></a>
        </form>
    </div>

</body>
</html>