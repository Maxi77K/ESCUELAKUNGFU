// Espera que el HTML cargue completo antes de ejecutar
document.addEventListener("DOMContentLoaded", () => {
  console.log("Sitio cargado correctamente.");

  // Busca el formulario en la página
  const form = document.getElementById("formInscripcion");

  // Solo corre si el formulario existe
  if (form) {

    // Revisa que el RUT tenga el formato correcto XX.XXX.XXX-X
    function validarRut(rut) {
      const formato = /^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]$/;
      return formato.test(rut);
    }

    // Agarra el input del RUT y el span de error
    const rutInput = document.getElementById("rut");
    const mensajeRut = document.getElementById("rut-error");

    // Bloquea letras y muestra aviso por 2 segundos
    rutInput.addEventListener("keypress", function(e) {
      const char = String.fromCharCode(e.charCode);
      const permitidos = /[0-9Kk]/;
      if (!permitidos.test(char)) {
        e.preventDefault(); // no deja escribir la letra
        mensajeRut.textContent = "⚠️ Solo se permiten números.";
        mensajeRut.style.color = "orange";
        setTimeout(() => {
          mensajeRut.textContent = ""; // borra el aviso a los 2 seg
        }, 2000);
      }
    });

    // Formatea el RUT automáticamente mientras se escribe
    rutInput.addEventListener("input", function() {
      let valor = this.value.replace(/[^0-9Kk]/g, ''); // saca todo excepto números y K

      if (valor.length > 9) valor = valor.slice(0, 9); // máximo 9 dígitos

      // Agrega puntos y guión según cuántos dígitos hay
      if (valor.length > 7) {
        valor = valor.slice(0, 2) + '.' + valor.slice(2, 5) + '.' + valor.slice(5, 8) + '-' + valor.slice(8);
      } else if (valor.length > 5) {
        valor = valor.slice(0, 2) + '.' + valor.slice(2, 5) + '.' + valor.slice(5);
      } else if (valor.length > 2) {
        valor = valor.slice(0, 2) + '.' + valor.slice(2);
      }

      this.value = valor; // muestra el RUT formateado en el input

      // Cuando el RUT está completo (12 chars), valida el formato
      if (valor.length === 12) {
        if (!validarRut(valor)) {
          mensajeRut.textContent = "RUT inválido. Verifica el formato (Ej: 22.015.790-3).";
          mensajeRut.style.color = "red";
        } else {
          mensajeRut.textContent = "RUT válido ✅";
          mensajeRut.style.color = "green";
        }
      } else {
        mensajeRut.textContent = ""; // limpia el mensaje si aún no está completo
      }
    });

    // Maneja el envío del formulario
    form.addEventListener("submit", function(e) {
      e.preventDefault(); // evita que la página se recargue

      let confirmacion = document.getElementById("mensajeConfirmacion");

      // Valida el RUT antes de enviar
      if (!validarRut(rutInput.value)) {
        mensajeRut.textContent = "RUT inválido. Verifica el formato (Ej: 22.015.790-3).";
        mensajeRut.style.color = "red";
        rutInput.focus(); // lleva el cursor al input del RUT
        return; // corta el envío
      } else {
        mensajeRut.textContent = ""; // limpia el error si el RUT está bien
      }

      // Muestra el mensaje de éxito y oculta el formulario
      confirmacion.style.display = "block";
      this.style.display = "none";
    });

  }
});