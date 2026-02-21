<?php include 'conexion.php'; 

// LÓGICA DEL BUSCADOR
$busqueda = "";
$where = "";

if(isset($_POST['buscar'])){
    $busqueda = $_POST['busqueda'];
    // Busca por TAG, Usuario, Marca, Modelo o Serial
    $where = "WHERE codigo_qr LIKE '%$busqueda%' OR usuario LIKE '%$busqueda%' OR marca LIKE '%$busqueda%' OR service_tag LIKE '%$busqueda%'";
}

$sql = "SELECT * FROM equipos $where ORDER BY id DESC";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Activos - Vistas Golf</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e4d2b; --accent: #d4af37; --bg: #f0f2f5; --white: #ffffff; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg); margin: 0; padding: 20px; }
        
        /* HEADER Y BUSCADOR */
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .search-box { display: flex; gap: 10px; background: white; padding: 5px; border-radius: 8px; border: 1px solid #ddd; }
        .search-box input { border: none; outline: none; padding: 8px; width: 200px; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        
        /* TABLA */
        .card { background: var(--white); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; } /* Min-width evita que se rompa en celular */
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background-color: #f8f9fa; color: var(--primary); font-weight: 600; text-transform: uppercase; font-size: 12px; }
        tr:hover { background-color: #f1f1f1; }

        /* BADGES (ETIQUETAS DE ESTADO) */
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .st-operativo { background: #e6f4ea; color: #1e8e3e; }
        .st-revision { background: #fef7e0; color: #f9a825; }
        .st-baja { background: #fce8e6; color: #c5221f; }

        /* BOTONES DE ACCIÓN */
        .actions { display: flex; gap: 5px; }
        .btn-icon { text-decoration: none; padding: 6px 10px; border-radius: 4px; font-size: 14px; transition: 0.2s; }
        .btn-print { background: #eee; color: #333; } /* Ticket */
        .btn-view { background: var(--primary); color: white; } /* Ver Perfil */
        .btn-supply { background: #007bff; color: white; } /* Insumos */
        
        .btn-icon:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="top-bar">
        <div>
            <h1 style="color: var(--primary); margin:0;">📋 Inventario General</h1>
            <a href="index.php" style="text-decoration:none; color:#666; font-size:14px;">⬅ Volver al Dashboard</a>
        </div>

        <form method="post" class="search-box">
            <input type="text" name="busqueda" placeholder="Buscar por Tag, Usuario..." value="<?php echo $busqueda; ?>">
            <button type="submit" name="buscar" class="btn-search">🔍 Buscar</button>
            <?php if($busqueda != "") echo "<a href='inventario.php' style='padding:8px; color:red; text-decoration:none;'>✖</a>"; ?>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Tag / Tipo</th>
                    <th>Usuario / Ubicación</th>
                    <th>Equipo / Modelo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows > 0){
                    while($row = $res->fetch_assoc()){
                        // Colores del estado
                        $clase_estado = "st-operativo"; 
                        if($row['estado'] == 'En Revisión') $clase_estado = "st-revision";
                        if($row['estado'] == 'Dañado') $clase_estado = "st-baja";

                        echo "<tr>";
                        
                        // Columna 1: Tag
                        echo "<td>
                                <b style='color:#1e4d2b;'>" . $row['codigo_qr'] . "</b><br>
                                <small style='color:#888;'>" . $row['tipo_equipo'] . "</small>
                              </td>";
                        
                        // Columna 2: Usuario
                        echo "<td>" . $row['usuario'] . "<br><small>" . $row['departamento'] . "</small></td>";
                        
                        // Columna 3: Detalles Técnicos
                        if($row['tipo_equipo'] == 'Celular'){
                            echo "<td>" . $row['marca'] . " " . $row['modelo'] . "<br><small>Línea: " . $row['numero_linea'] . "</small></td>";
                        } else {
                            echo "<td>" . $row['marca'] . " " . $row['modelo'] . "<br><small>S/N: " . $row['service_tag'] . "</small></td>";
                        }

                        // Columna 4: Estado
                        echo "<td><span class='badge $clase_estado'>" . $row['estado'] . "</span></td>";
                        
                        // Columna 5: BOTONES DE ACCIÓN
                        echo "<td>
                                <div class='actions'>
                                    <a href='ticket.php?id=" . $row['id'] . "' target='_blank' class='btn-icon btn-print' title='Imprimir Etiqueta'>🖨️</a>
                                    <a href='ver.php?id=" . $row['id'] . "' class='btn-icon btn-view' title='Ver Detalles y Fallas'>👁️</a>
                                    <a href='entregar.php?id_equipo=" . $row['id'] . "' class='btn-icon btn-supply' title='Entregar Insumo'>🎁</a>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>No se encontraron equipos.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>