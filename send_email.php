<?php
session_start();
error_reporting(0);

// 1. PROTECCIÓN CSRF
if (empty($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Error de seguridad: token CSRF inválido.');
}

// 2. SANEAMIENTO con $_POST
$nombre     = htmlspecialchars(strip_tags(trim($_POST['nombre']     ?? '')));
$rut        = htmlspecialchars(strip_tags(trim($_POST['rut']        ?? '')));
$email      = htmlspecialchars(strip_tags(trim($_POST['email']      ?? '')));
$fecha      = htmlspecialchars(strip_tags(trim($_POST['fecha']      ?? '')));
$direccion  = htmlspecialchars(strip_tags(trim($_POST['direccion']  ?? '')));
$apoderado  = htmlspecialchars(strip_tags(trim($_POST['apoderado']  ?? '')));
$emergencia = htmlspecialchars(strip_tags(trim($_POST['emergencia'] ?? '')));
$enfermedad = htmlspecialchars(strip_tags(trim($_POST['enfermedad'] ?? '')));

// 3. VALIDACIÓN EN EL SERVIDOR
$errores = [];

if (empty($nombre))     $errores[] = "El nombre es obligatorio.";
if (empty($rut))        $errores[] = "El RUT es obligatorio.";
if (empty($fecha))      $errores[] = "La fecha de nacimiento es obligatoria.";
if (empty($direccion))  $errores[] = "La dirección es obligatoria.";
if (empty($emergencia)) $errores[] = "El número de emergencias es obligatorio.";

if (!empty($rut) && !preg_match('/^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]$/', $rut)) {
    $errores[] = "El RUT no tiene formato válido (Ej: 22.015.790-3).";
}

if (empty($email)) {
    $errores[] = "El correo electrónico es obligatorio.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo electrónico no es válido.";
}

// 4. SI HAY ERRORES, MOSTRAR Y VOLVER
if (!empty($errores)) {
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"></head><body>
    <header class="hero"><div class="hero-content"><h1>⚠️ Errores en el formulario</h1><ul>';
    foreach ($errores as $e) echo '<li>' . $e . '</li>';
    echo '</ul><a href="inscribete.php" class="btn">← Volver</a></div></header></body></html>';
    exit;
}

// 5. CONEXIÓN A LA BASE DE DATOS
$host     = "127.0.0.1";
$usuario  = "root";
$password = "";
$base     = "kungfu_db";

$conexion = new mysqli($host, $usuario, $password, $base);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// 6. INSERTAR DATOS EN LA TABLA con prepared statement (previene inyección SQL)
$sql = "INSERT INTO inscripciones (nombre, rut, email, fecha_nacimiento, direccion, apoderado, emergencia, enfermedad)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssss", $nombre, $rut, $email, $fecha, $direccion, $apoderado, $emergencia, $enfermedad);

$guardado = $stmt->execute();

$stmt->close();
$conexion->close();

// 7. ENVÍO DE CORREO CON mail()
$destinatario = "contacto@kungfuvalle.cl";
$asunto       = "Nueva Inscripción - Kung Fu Long Rinconada";

$cuerpo  = "Nueva solicitud de inscripción:\n\n";
$cuerpo .= "Nombre     : " . $nombre     . "\n";
$cuerpo .= "RUT        : " . $rut        . "\n";
$cuerpo .= "Email      : " . $email      . "\n";
$cuerpo .= "Nacimiento : " . $fecha      . "\n";
$cuerpo .= "Dirección  : " . $direccion  . "\n";
$cuerpo .= "Apoderado  : " . ($apoderado ?: "No indicado") . "\n";
$cuerpo .= "Emergencia : " . $emergencia . "\n";
$cuerpo .= "Enfermedad : " . ($enfermedad ?: "Ninguna") . "\n";

$cabeceras  = "From: formulario@kungfuvalle.cl\r\n";
$cabeceras .= "Reply-To: " . $email . "\r\n";
$cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail($destinatario, $asunto, $cuerpo, $cabeceras);

// Regeneramos token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscripción Enviada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <nav>
    <ul>
      <li><a href="index.html">Inicio</a></li>
      <li><a href="inscribete.php">Inscríbete</a></li>
      <li><a href="profesor.html">Profesor</a></li>
      <li><a href="tienda.html">Tiendas</a></li>
      <li><a href="ubicacion.html">Ubicación</a></li>
    </ul>
  </nav>
  <header class="hero">
    <div class="hero-content">
      <h1>✅ ¡Inscripción Enviada!</h1>
      <p class="lema">Hola <strong><?php echo $nombre; ?></strong>, tu solicitud fue recibida correctamente.</p>
      <p>El maestro Manuel Castro se pondrá en contacto contigo pronto.</p>
      <a href="index.html" class="btn">← Volver al inicio</a>
    </div>
  </header>
  <footer>
    <p>&copy; 2026 Escuela Kung Fu Valle Aconcagua</p>
  </footer>
</body>
</html>