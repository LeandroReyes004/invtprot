<?php include 'conexion.php';
// 1. Recibimos el ID del equipo
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Buscamos solo el TAG para mostrarlo abajo del QR
    $sql = "SELECT codigo_qr FROM equipos WHERE id=$id";
    $res = $conn->query($sql);
    $equipo = $res->fetch_assoc();
    $codigo = $equipo['codigo_qr']; // Ej: INV-001
}

// 2. URL a la que llevará el QR
$urlQR = "https://olivedrab-hippopotamus-703494.hostingersite.com/ver.php?id=" . $id;
?>

<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4; /* Fondo gris claro para ver mejor la etiqueta blanca */
            text-align: center;
        }
        .etiqueta-container {
            /* Tamaño total de la etiqueta */
            width: 380px; 
            padding: 15px;
            border: 2px dashed #333; /* Borde punteado más visible */
            margin: 20px auto;
            background-color: white;
            box-sizing: border-box; /* Asegura que el padding no aumente el ancho total */
        }

        /* --- NUEVO: Contenedor flexible para la parte superior --- */
        .contenido-superior {
            display: flex;
            align-items: center; /* Centra verticalmente el texto con el QR */
            justify-content: center; /* Centra todo el bloque horizontalmente */
            margin-bottom: 15px;
            text-align: left; /* El texto dentro de este bloque va a la izquierda */
        }

        .qr-img {
            display: block;
            /* Aumenté un poco el tamaño (de 50 a 80) para que se balancee mejor con el texto al lado.
               Si lo quieres más pequeño, bájalo aquí. */
            width: 80px; 
            height: 80px;
            margin-right: 20px; /* Espacio entre el QR y el texto a su derecha */
        }

        /* --- NUEVO: Contenedor para el texto a la derecha --- */
        .texto-lateral {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            margin: 0;
            font-size: 18px; /* Un poco más grande */
            text-transform: uppercase;
            font-weight: 900;
            color: #1e4d2b; /* Usando el verde corporativo */
            line-height: 1.2;
        }
        p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
            font-weight: 600;
        }

        /* Estilo para el código inferior */
        .codigo-texto {
            font-size: 16px;
            font-weight: bold;
            text-align: center; /* Este sí lo mantenemos centrado abajo */
            letter-spacing: 3px;
            background: #eee;
            padding: 5px;
            border-radius: 4px;
        }

        /* Al imprimir, quitamos el botón y el fondo */
        @media print {
            body { background-color: white; padding: 0; }
            button { display: none; }
            .etiqueta-container { margin: 0; border: 1px solid #ddd; } 
        }
        
        /* Botón de volver */
        .btn-volver {
            padding: 10px 20px;
            background: #1e4d2b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="etiqueta-container">
        
        <div class="contenido-superior">
            <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($urlQR); ?>" />
            
            <div class="texto-lateral">
                <h2>VISTAS GOLF COUNTRY & CLUB</h2>
                <p>DEPARTAMENTO DE TI</p>
            </div>
        </div>
        
        <div class="codigo-texto"><?php echo $codigo; ?></div>
    </div>

    <br>
    <button onclick="window.print()" class="btn-volver" style="background: #d4af37; margin-right: 10px;">🖨️ Imprimir</button>
    <button onclick="window.history.back()" class="btn-volver">⬅️ Volver</button>

</body>
</html>