<?php include 'conexion.php'; 

if(isset($_POST['guardar'])){
    // 1. Recibir datos comunes
    $tipo = $_POST['tipo_equipo'];
    $usuario = $_POST['usuario'];
    $dpto = $_POST['dpto'];
    $estado = $_POST['estado'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ip = $_POST['ip_address']; // Nuevo campo
    
    // Recibir datos específicos
    $imei = $_POST['imei'];       
    $linea = $_POST['numero_linea']; 
    $stag = $_POST['stag'];       
    $cpu = $_POST['cpu'];         
    $ram = $_POST['ram'];
    $anydesk = $_POST['anydesk']; // Nuevo campo

    // --- CEREBRO DE AUTO-TAGGING ---
    // Definir el prefijo según tu nomenclatura
    $prefijo = "";
    if($tipo == "PC") $prefijo = "CMP";
    elseif($tipo == "Celular") $prefijo = "FLS";
    elseif($tipo == "Tablet") $prefijo = "TBL";
    elseif($tipo == "Impresora") $prefijo = "IMPR";
    else $prefijo = "OTRO";

    // Contar cuántos hay de este tipo actualmente para saber el número siguiente
    $sql_count = "SELECT COUNT(*) as total FROM equipos WHERE tipo_equipo = '$tipo'";
    $conteo = $conn->query($sql_count)->fetch_assoc()['total'];
    $siguiente = $conteo + 1;

    // Generar el código (Ej: CMP-005)
    // str_pad agrega ceros a la izquierda (3 dígitos)
    $qr_auto = $prefijo . "-" . str_pad($siguiente, 3, "0", STR_PAD_LEFT);

    // --- GUARDAR EN BASE DE DATOS ---
    $sql = "INSERT INTO equipos (tipo_equipo, codigo_qr, usuario, departamento, estado, marca, modelo, imei, numero_linea, service_tag, procesador, memoria, anydesk_id, ip_address) 
            VALUES ('$tipo', '$qr_auto', '$usuario', '$dpto', '$estado', '$marca', '$modelo', '$imei', '$linea', '$stag', '$cpu', '$ram', '$anydesk', '$ip')";
    
    if($conn->query($sql)){
        // Redirigir al ticket para ver el código generado
        $id_nuevo = $conn->insert_id;
        header("Location: ticket.php?id=$id_nuevo"); 
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ingreso</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .card { background: white; max-width: 700px; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { margin-top:0; color:#1e4d2b; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: span 2; }
        
        label { font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        input:focus { border-color: #1e4d2b; outline: none; }

        .btn-save { width: 100%; padding: 15px; background: #1e4d2b; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; }
        .btn-save:hover { background: #14361e; }
        
        .section-box { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; margin-top: 10px; }
        .hidden { display: none; }
        
        .auto-tag-msg { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; border: 1px dashed #2e7d32; }
    </style>
    
    <script>
        function cambiarFormulario() {
            var tipo = document.getElementById("tipoSelect").value;
            
            // Secciones
            var secPC = document.getElementById("seccionPC");
            var secCel = document.getElementById("seccionCelular");
            var secImp = document.getElementById("seccionImpresora");

            // Ocultar todo primero
            secPC.style.display = "none";
            secCel.style.display = "none";
            secImp.style.display = "none";

            // Mostrar según selección
            if (tipo === "PC") {
                secPC.style.display = "block";
            } else if (tipo === "Celular" || tipo === "Tablet") {
                secCel.style.display = "block";
            } else if (tipo === "Impresora") {
                secImp.style.display = "block";
            }
        }
    </script>
</head>
<body>

    <div class="card">
        <h2>⛳ Nuevo Activo IT</h2>
        
        <div class="auto-tag-msg">
            🤖 <strong>Sistema Inteligente:</strong> El código (Tag) se generará automáticamente al guardar (Ej: <em>CMP-005</em>).
        </div>

        <form method="post">
            <div class="form-grid">
                <div class="full-width">
                    <label>Tipo de Equipo:</label>
                    <select name="tipo_equipo" id="tipoSelect" onchange="cambiarFormulario()" required>
                        <option value="">-- Seleccione --</option>
                        <option value="PC">🖥️ Computadora / Laptop (Genera CMP-XX)</option>
                        <option value="Celular">📱 Flota (Genera FLS-XX)</option>
                        <option value="Tablet">📟 Tablet (Genera TBL-XX)</option>
                        <option value="Impresora">🖨️ Impresora (Genera IMPR-XX)</option>
                    </select>
                </div>

                <div>
                    <label>Usuario Asignado / Ubicación:</label>
                    <input type="text" name="usuario" placeholder="Ej: Recepción Lobby" required>
                </div>
                <div>
                    <label>Departamento:</label>
                    <input type="text" name="dpto" placeholder="Ej: Administración">
                </div>
                
                <div>
                    <label>Marca:</label>
                    <input type="text" name="marca" placeholder="Ej: HP, Dell, Samsung">
                </div>
                <div>
                    <label>Modelo:</label>
                    <input type="text" name="modelo" placeholder="Ej: Laserjet P1102">
                </div>

                <div>
                    <label>Estado Actual:</label>
                    <select name="estado">
                        <option value="Operativo">✅ Operativo</option>
                        <option value="En Revisión">⚠️ En Revisión</option>
                        <option value="Dañado">❌ Dañado</option>
                    </select>
                </div>
            </div>

            <div id="seccionPC" class="section-box hidden">
                <h4 style="margin:0 0 10px 0; color:#1e4d2b;">Detalles de Computadora</h4>
                <div class="form-grid">
                    <div>
                        <label>AnyDesk ID: *</label>
                        <input type="text" name="anydesk" placeholder="Ej: 999 555 123">
                    </div>
                    <div>
                        <label>Service Tag / Serial:</label>
                        <input type="text" name="stag">
                    </div>
                    <div>
                        <label>Procesador:</label>
                        <input type="text" name="cpu" placeholder="i5, i7, Ryzen 5">
                    </div>
                    <div>
                        <label>Memoria RAM:</label>
                        <input type="text" name="ram" placeholder="8GB, 16GB">
                    </div>
                    <div class="full-width">
                        <label>Dirección IP (Opcional):</label>
                        <input type="text" name="ip_address" placeholder="Ej: 192.168.1.50">
                    </div>
                </div>
            </div>

            <div id="seccionCelular" class="section-box hidden">
                <h4 style="margin:0 0 10px 0; color:#1e4d2b;">Detalles Móviles</h4>
                <div class="form-grid">
                    <div>
                        <label>IMEI:</label>
                        <input type="text" name="imei" placeholder="Serial único">
                    </div>
                    <div>
                        <label>Número de Línea:</label>
                        <input type="text" name="numero_linea" placeholder="809-...">
                    </div>
                </div>
            </div>

            <div id="seccionImpresora" class="section-box hidden">
                <h4 style="margin:0 0 10px 0; color:#1e4d2b;">Configuración de Red Impresora</h4>
                <div class="form-grid">
                    <div class="full-width">
                        <label>Dirección IP Estática: *</label>
                        <input type="text" name="ip_address" placeholder="Ej: 192.168.1.200">
                    </div>
                    <div class="full-width">
                        <label>Serial / Service Tag:</label>
                        <input type="text" name="stag">
                    </div>
                </div>
            </div>

            <button type="submit" name="guardar" class="btn-save">💾 Guardar y Generar Etiqueta</button>
            <br><br>
            <center><a href="index.php" style="color:#666; text-decoration:none;">Cancelar</a></center>
        </form>
    </div>

</body>
</html>