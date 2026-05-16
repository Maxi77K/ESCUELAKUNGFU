<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario de Inscripción - Escuela Kung Fu Valle Aconcagua</title>
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
      <h1>Formulario de Inscripción</h1>
      <p class="lema">El profesor Manuel Castro de Long Rinconada se pondrá en contacto contigo
        por teléfono o correo electrónico una vez enviado el formulario.</p>
 
      <form action="send_email.php" method="post" class="form-inscripcion">
 
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
 
        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre"
               placeholder="Ej: Juan Pérez González"
               required>
 
        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut"
               placeholder="Ej: 22.015.790-3"
               required>
 
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email"
               placeholder="Ej: juan.perez@gmail.com"
               required>
 
        <label for="fecha">Fecha de nacimiento:</label>
        <input type="date" id="fecha" name="fecha" required>
 
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion"
               placeholder="Ej: Calle Los Olivos 123, Rinconada"
               required>
 
        <label for="apoderado">Nombre de apoderado:</label>
        <input type="text" id="apoderado" name="apoderado"
               placeholder="Ej: María González">
 
        <label for="emergencia">Número de emergencias:</label>
        <input type="tel" id="emergencia" name="emergencia"
               placeholder="Ej: 987654321"
               required>
 
        <label for="enfermedad">¿Padece alguna enfermedad?</label>
        <textarea id="enfermedad" name="enfermedad" rows="3"
                  placeholder="Ej: Asma, hipertensión, ninguna"></textarea>
 
        <button type="submit" class="btn">Enviar Inscripción</button>
      </form>
 
    </div>
  </header>
 
  <footer>
    <p>&copy; 2026 Escuela Kung Fu Valle Aconcagua</p>
  </footer>
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>