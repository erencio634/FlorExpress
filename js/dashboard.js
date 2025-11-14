// ----------------------
// FUNCIÓN DE SECCIONES (tu diseño intacto)
// ----------------------
function showDashboardSection(sectionId, el = null) {
    const sections = [
        'dashboard-main', 'catalogo', 'mis-pedidos', 'seguimiento',
        'carrito', 'pagos', 'direcciones', 'favoritos',
        'reseñas', 'notificaciones', 'perfil'
    ];

    sections.forEach(section => {
        const element = document.getElementById(section);
        if (element) element.classList.add('hidden');
    });

    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.classList.remove('hidden');

        // ✅ Cada que entras a la sección "carrito" se actualiza automáticamente
        if (sectionId === 'carrito') {
            recargarCarrito();
        }
    }

    const navButtons = document.querySelectorAll('.dashboard-nav-btn');
    navButtons.forEach(btn => btn.classList.remove('bg-white', 'bg-opacity-20'));

    if (el) el.classList.add('bg-white', 'bg-opacity-20');
}

// ----------------------
// FUNCIÓN: RECARGAR CARRITO (sin modificar diseño)
// ----------------------
async function recargarCarrito() {
    const contenedor = document.querySelector('#carrito .contenido-carrito');
    if (!contenedor) {
        console.warn("⚠️ No se encontró .contenido-carrito dentro del carrito");
        return;
    }

    try {
        const respuesta = await fetch('actions/listar_carrito.php');
        const html = await respuesta.text();
        contenedor.innerHTML = html; // solo reemplaza el interior, no el div padre
        console.log('🛒 Carrito actualizado sin alterar el diseño.');
    } catch (error) {
        console.error('❌ Error al actualizar carrito:', error);
        contenedor.innerHTML = '<div class="text-red-500 p-4">Error al cargar el carrito.</div>';
    }
}


// ----------------------
// DETECTAR CUANDO SE ELIMINA UN PRODUCTO DEL CARRITO
// ----------------------
document.addEventListener('click', async (e) => {
    const btnEliminar = e.target.closest('.btn-eliminar-carrito');
    if (!btnEliminar) return;

    e.preventDefault();
    const idArticulo = btnEliminar.dataset.id;
    if (!idArticulo) return;

    Swal.fire({
        title: '¿Eliminar producto?',
        text: 'Se eliminará del carrito',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C33A94',
        cancelButtonColor: '#C3D600',
        confirmButtonText: 'Sí, eliminar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('actions/eliminar_carrito.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_articulo=' + encodeURIComponent(idArticulo)
                });

                const txt = await res.text();
                if (txt.trim() === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El producto fue eliminado del carrito',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        // ✅ Reactualiza la vista del carrito al eliminar
                        recargarCarrito();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el producto.'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo contactar al servidor.'
                });
            }
        }
    });
});



function logout() {
    window.location.href = 'logout.php';
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('dashboard-content')) {
        showDashboardSection('catalogo');
    }
});

// ----------------------
// EVENTOS DINÁMICOS (carrito y favoritos)
// ----------------------
// Escuchamos clics solo dentro del área principal del dashboard,
// y detenemos la propagación para evitar que se oculte todo el contenido.
document.getElementById('dashboard-content')?.addEventListener('click', async function (e) {
    const btnAddCart = e.target.closest('.btn-agregar-carrito');
    const btnDelCart = e.target.closest('.btn-eliminar-carrito');
    const btnAddFav = e.target.closest('.btn-favorito');
    const btnDelFav = e.target.closest('.btn-eliminar-favorito');

    // --------------------------
    // AGREGAR AL CARRITO (versión robusta)
    // --------------------------
    if (btnAddCart) {
        e.preventDefault();
        e.stopPropagation(); // ← Evita que el clic afecte otras secciones
        const idArticulo = btnAddCart.dataset.id;
        if (!idArticulo) return;

        try {
            const res = await fetch('actions/agregar_carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_articulo=' + encodeURIComponent(idArticulo)
            });

            const text = await res.text().catch(() => '');
            let data = null;

            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                console.warn('Respuesta no válida del servidor:', text);
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la respuesta',
                    text: 'No se pudo procesar la respuesta del servidor.'
                });
                return;
            }

            if (!data || !data.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Respuesta vacía o incorrecta del servidor.'
                });
                return;
            }

            switch (data.status) {
                case 'ok':
                    Swal.fire({
                        icon: 'success',
                        title: 'Agregado',
                        text: data.message,
                        timer: 1200,
                        showConfirmButton: false
                    });
                    break;
                case 'updated':
                    Swal.fire({
                        icon: 'info',
                        title: 'Cantidad aumentada',
                        text: data.message,
                        timer: 1200,
                        showConfirmButton: false
                    });
                    break;
                case 'not_logged':
                    Swal.fire({
                        icon: 'warning',
                        title: 'Inicia sesión',
                        text: data.message
                    });
                    break;
                default:
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo agregar al carrito.'
                    });
            }

        } catch (err) {
            console.error('Error general al agregar al carrito:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor.'
            });
        }
    }

    // --------------------------
    // ELIMINAR DEL CARRITO
    // --------------------------
    if (btnDelCart) {
        e.preventDefault();
        e.stopPropagation();
        const idArticulo = btnDelCart.dataset.id;
        if (!idArticulo) return;

        Swal.fire({
            title: '¿Eliminar producto?',
            text: 'Se eliminará del carrito',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C33A94',
            cancelButtonColor: '#C3D600',
            confirmButtonText: 'Sí, eliminar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch('actions/eliminar_carrito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_articulo=' + encodeURIComponent(idArticulo)
                    });
                    const txt = await res.text();

                    if (txt.trim() === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El producto fue eliminado del carrito',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
    // Simplemente recargar los datos dinámicos si aplica
    if (document.getElementById('carrito')) {
        // Puedes recargar solo el carrito con AJAX si quieres,
        // pero para evitar errores solo re-mostramos la sección actual
        showDashboardSection('carrito');
    }
});

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar el producto.'
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo contactar al servidor.'
                    });
                }
            }
        });
    }

    // --------------------------
    // AGREGAR / QUITAR FAVORITO (desde catálogo)
    // --------------------------
    if (btnAddFav) {
        e.preventDefault();
        e.stopPropagation();
        const idArticulo = btnAddFav.dataset.id;
        const heart = btnAddFav.querySelector('svg.heart-icon');

        try {
            const res = await fetch('actions/agregar_favorito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_articulo=' + encodeURIComponent(idArticulo)
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch {
                console.error('Respuesta no válida:', text);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error del servidor.' });
                return;
            }

            if (data.status === 'added') {
                Swal.fire({ icon: 'success', title: 'Agregado a favoritos', timer: 1200, showConfirmButton: false });
                heart.setAttribute('fill', 'currentColor');
                heart.classList.remove('text-gray-400');
                heart.classList.add('text-red-500', 'animate-ping-once');
                setTimeout(() => heart.classList.remove('animate-ping-once'), 500);
            } else if (data.status === 'removed') {
                Swal.fire({ icon: 'info', title: 'Eliminado de favoritos', timer: 1200, showConfirmButton: false });
                heart.setAttribute('fill', 'none');
                heart.classList.remove('text-red-500');
                heart.classList.add('text-gray-400');
            } else if (data.status === 'not_logged') {
                Swal.fire({ icon: 'warning', title: 'Inicia sesión', text: data.message });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar la acción.' });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor.' });
        }
    }

    // --------------------------
    // ELIMINAR FAVORITO (desde "Mis Favoritos")
    // --------------------------
    if (btnDelFav) {
        e.preventDefault();
        e.stopPropagation();
        const idArticulo = btnDelFav.dataset.id;
        if (!idArticulo) return;

        Swal.fire({
            title: '¿Quitar de favoritos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#C33A94',
            cancelButtonColor: '#C3D600',
            confirmButtonText: 'Sí, quitar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch('actions/eliminar_favorito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_articulo=' + encodeURIComponent(idArticulo)
                    });
                    const txt = await res.text();

                    if (txt.trim() === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El producto se quitó de tus favoritos',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
    // Simplemente recargar los datos dinámicos si aplica
    if (document.getElementById('carrito')) {
        // Puedes recargar solo el carrito con AJAX si quieres,
        // pero para evitar errores solo re-mostramos la sección actual
        showDashboardSection('carrito');
    }
});

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar de favoritos.'
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo contactar al servidor.'
                    });
                }
            }
        });
    }
});


// ----------------------
// ANIMACIÓN DE CORAZÓN (pulse breve)
// ----------------------
const style = document.createElement('style');
style.innerHTML = `
@keyframes ping-once {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.3); opacity: 0.8; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-ping-once {
  animation: ping-once 0.4s ease-in-out;
}
`;
document.head.appendChild(style);

// ==========================================================
// MÓDULO: DIRECCIONES
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const lista = document.getElementById("lista-direcciones");
  const modal = document.getElementById("modal-direccion");
  const form = document.getElementById("form-direccion");
  const btnNueva = document.getElementById("btn-nueva-direccion");
  const btnCancelar = document.getElementById("btn-cancelar-modal");
  const tituloModal = document.getElementById("titulo-modal");

  if (lista) cargarDirecciones();

  btnNueva?.addEventListener("click", () => {
    form.reset();
    document.getElementById("id_direccion").value = "";
    tituloModal.textContent = "Agregar dirección";
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  });

  btnCancelar?.addEventListener("click", () => {
    modal.classList.add("hidden");
  });

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const datos = new FormData(form);
    const id = datos.get("id_direccion");
    const archivo = id ? "editar_direccion.php" : "agregar_direccion.php";
    try {
      const res = await fetch("actions/" + archivo, { method: "POST", body: datos });
      const data = await res.json();
      if (data.status === "ok") {
        Swal.fire({ icon: "success", title: data.message, timer: 1200, showConfirmButton: false });
        modal.classList.add("hidden");
        cargarDirecciones();
      } else {
        Swal.fire({ icon: "error", title: "Error", text: data.message });
      }
    } catch (err) {
      Swal.fire({ icon: "error", title: "Error de conexión" });
    }
  });

  async function cargarDirecciones() {
    const res = await fetch("actions/listar_direcciones.php");
    const html = await res.text();
    lista.innerHTML = html;

   document.querySelectorAll(".btn-editar-direccion").forEach(btn => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;

    try {
      const res = await fetch("actions/obtener_direccion.php?id=" + id);
      const data = await res.json();

      if (data.status === "ok") {
        const d = data.data;
        document.getElementById("id_direccion").value = d.id_direccion;
        document.getElementById("nombre_receptor").value = d.nombre_receptor;
        document.getElementById("apellidos_receptor").value = d.apellidos_receptor;
        document.getElementById("telefono_receptor").value = d.telefono_receptor;
        document.getElementById("codigo_postal").value = d.codigo_postal;
        document.getElementById("estado").value = d.estado;
        document.getElementById("municipio").value = d.municipio;
        document.getElementById("colonia").value = d.colonia;
        document.getElementById("calle").value = d.calle;
        document.getElementById("referencias").value = d.referencias || "";

        // Cambiar título del modal y mostrar
        tituloModal.textContent = "Editar dirección";
        modal.classList.remove("hidden");
        modal.classList.add("flex");
      } else {
        Swal.fire({ icon: "error", title: "Error", text: data.message });
      }
    } catch (err) {
      Swal.fire({ icon: "error", title: "Error de conexión", text: "No se pudo obtener la dirección" });
    }
  });
});


    document.querySelectorAll(".btn-eliminar-direccion").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        Swal.fire({
          title: "¿Eliminar dirección?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar"
        }).then(async (r) => {
          if (r.isConfirmed) {
            const fd = new FormData();
            fd.append("id_direccion", id);
            const res = await fetch("actions/eliminar_direccion.php", { method: "POST", body: fd });
            const data = await res.json();
            if (data.status === "ok") {
              Swal.fire({ icon: "success", title: data.message, timer: 1200, showConfirmButton: false });
              cargarDirecciones();
            } else {
              Swal.fire({ icon: "error", title: "Error", text: data.message });
            }
          }
        });
      });
    });
  }
});

// ==========================================================
// MÓDULO: MÉTODOS DE PAGO
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const lista = document.getElementById("lista-pagos");
  const modal = document.getElementById("modal-pago");
  const form = document.getElementById("form-pago");
  const btnNuevo = document.getElementById("btn-nuevo-pago");
  const btnCancelar = document.getElementById("btn-cancelar-pago");
  const titulo = document.getElementById("titulo-modal-pago");

  if (lista) cargarPagos();

  btnNuevo?.addEventListener("click", () => {
    form.reset();
    document.getElementById("id_metodo").value = "";
    titulo.textContent = "Agregar Método de Pago";
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  });

  btnCancelar?.addEventListener("click", () => {
    modal.classList.add("hidden");
  });

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const datos = new FormData(form);
    const id = datos.get("id_metodo");
    const archivo = id ? "editar_metodo_pago.php" : "agregar_metodo_pago.php";
    const res = await fetch("actions/" + archivo, { method: "POST", body: datos });
    const data = await res.json();
    if (data.status === "ok") {
      Swal.fire({ icon: "success", title: data.message, timer: 1200, showConfirmButton: false });
      modal.classList.add("hidden");
      cargarPagos();
    } else {
      Swal.fire({ icon: "error", title: "Error", text: data.message });
    }
  });

  async function cargarPagos() {
    const res = await fetch("actions/listar_metodos_pago.php");
    const html = await res.text();
    lista.innerHTML = html;

    document.querySelectorAll(".btn-editar-pago").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        const res = await fetch("actions/obtener_pago.php?id=" + id);
        const data = await res.json();
        if (data.status === "ok") {
          const p = data.data;
          document.getElementById("id_metodo").value = p.id_metodo;
          document.getElementById("tipo").value = p.tipo;
          document.getElementById("alias").value = p.alias;
          document.getElementById("titular").value = p.titular;
          document.getElementById("ultimos4").value = p.ultimos4;
          document.getElementById("expiracion").value = p.expiracion;
          document.getElementById("es_principal").checked = p.es_principal == 1;
          titulo.textContent = "Editar Método de Pago";
          modal.classList.remove("hidden");
          modal.classList.add("flex");
        } else {
          Swal.fire({ icon: "error", title: "Error", text: data.message });
        }
      });
    });

    document.querySelectorAll(".btn-eliminar-pago").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        Swal.fire({
          title: "¿Eliminar método?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar"
        }).then(async (r) => {
          if (r.isConfirmed) {
            const fd = new FormData();
            fd.append("id_metodo", id);
            const res = await fetch("actions/eliminar_pago.php", { method: "POST", body: fd });
            const data = await res.json();
            if (data.status === "ok") {
              Swal.fire({ icon: "success", title: data.message, timer: 1200, showConfirmButton: false });
              cargarPagos();
            } else {
              Swal.fire({ icon: "error", title: "Error", text: data.message });
            }
          }
        });
      });
    });
  }
});

// ==========================================================
// MÓDULO: PROCESAR PAGO (FINALIZAR COMPRA)
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const btnProcesar = document.getElementById("btn-procesar-pago");

  if (!btnProcesar) return;

  btnProcesar.addEventListener("click", async () => {
    // Obtener IDs seleccionados desde inputs del carrito
    const id_direccion = document.querySelector('input[name="direccion_predeterminada"]:checked')?.value;
    const id_metodo = document.querySelector('input[name="metodo_predeterminado"]:checked')?.value;

    if (!id_direccion || !id_metodo) {
      Swal.fire({
        icon: "warning",
        title: "Faltan datos",
        text: "Selecciona una dirección y un método de pago antes de continuar.",
      });
      return;
    }

    // Confirmar acción
    const confirm = await Swal.fire({
      title: "¿Confirmar compra?",
      text: "Se generará tu pedido y se procederá al pago.",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#C33A94",
      cancelButtonColor: "#C3D600",
      confirmButtonText: "Sí, confirmar pedido"
    });

    if (!confirm.isConfirmed) return;

    // Mostrar loader de procesamiento
    Swal.fire({
      title: "Procesando pedido...",
      text: "Por favor espera un momento",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Enviar datos
    const datos = new FormData();
    datos.append("id_direccion", id_direccion);
    datos.append("id_metodo", id_metodo);

    try {
      const res = await fetch("actions/procesar_pago.php", { method: "POST", body: datos });
      const data = await res.json();

      if (data.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Pedido confirmado",
          text: "Tu pedido se ha generado correctamente.",
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          // Redirigir a página de seguimiento o pedidos
          window.location.href = "dashboard_cliente.php#mis-pedidos";
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: data.message });
      }

    } catch (error) {
      console.error(error);
      Swal.fire({ icon: "error", title: "Error de conexión", text: "No se pudo contactar con el servidor." });
    }
  });
});

// ==========================================================
// PERFIL: ACTUALIZAR DATOS
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const formPerfil = document.getElementById("form-actualizar-perfil");
  if (formPerfil) {
    formPerfil.addEventListener("submit", async e => {
      e.preventDefault();
      const formData = new FormData(formPerfil);
      Swal.fire({ title: "Actualizando perfil...", didOpen: () => Swal.showLoading(), allowOutsideClick: false });
      try {
        const res = await fetch("actions/actualizar_perfil.php", { method: "POST", body: formData });
        const data = await res.json();
        if (data.status === "ok") {
          Swal.fire({ icon: "success", title: "Perfil actualizado", timer: 2000, showConfirmButton: false })
           .then(() => {
    // Simplemente recargar los datos dinámicos si aplica
    if (document.getElementById('carrito')) {
        // Puedes recargar solo el carrito con AJAX si quieres,
        // pero para evitar errores solo re-mostramos la sección actual
        showDashboardSection('carrito');
    }
});

        } else {
          Swal.fire({ icon: "error", title: "Error", text: data.message });
        }
      } catch {
        Swal.fire({ icon: "error", title: "Error de conexión", text: "No se pudo conectar con el servidor." });
      }
    });
  }
});

// ==========================================================
// CAMBIAR CONTRASEÑA
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const formPass = document.getElementById("form-cambiar-pass");
  if (formPass) {
    formPass.addEventListener("submit", async e => {
      e.preventDefault();
      const datos = new FormData(formPass);
      Swal.fire({ title: "Actualizando...", didOpen: () => Swal.showLoading(), allowOutsideClick: false });
      try {
        const res = await fetch("actions/cambiar_contrasena.php", { method: "POST", body: datos });
        const data = await res.json();
        if (data.status === "ok") {
          Swal.fire({ icon: "success", title: "Contraseña actualizada", timer: 2000, showConfirmButton: false });
          formPass.reset();
        } else {
          Swal.fire({ icon: "error", title: "Error", text: data.message });
        }
      } catch {
        Swal.fire({ icon: "error", title: "Error de conexión", text: "No se pudo contactar al servidor." });
      }
    });
  }
});

// ==========================================================
// PROCESO DE COMPRA CON RESUMEN
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const btnIrPago = document.getElementById("btn-ir-pago");
  const modal = document.getElementById("modal-confirmar-pedido");
  const resumen = document.getElementById("resumen-pedido");
  const btnCancelar = document.getElementById("btn-cancelar-pedido");
  const btnConfirmar = document.getElementById("btn-confirmar-pedido");

  if (!btnIrPago) return;

  // Abrir resumen
  btnIrPago.addEventListener("click", async () => {
    Swal.fire({
      title: "Cargando resumen...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const res = await fetch("actions/generar_resumen_pedido.php");
      const text = await res.text();
      Swal.close();

      resumen.innerHTML = text;
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    } catch (err) {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error de conexión",
        text: "No se pudo obtener el resumen del pedido."
      });
    }
  });

  // Cerrar modal
  btnCancelar?.addEventListener("click", () => {
    modal.classList.add("hidden");
  });

  // Confirmar pedido
  btnConfirmar?.addEventListener("click", async () => {
    const direccion = document.querySelector('input[name="direccion_predeterminada"]:checked');
    const metodo = document.querySelector('input[name="metodo_predeterminado"]:checked');

    if (!direccion || !metodo) {
      Swal.fire({
        icon: "warning",
        title: "Faltan datos",
        text: "Selecciona dirección y método de pago antes de confirmar."
      });
      return;
    }

    Swal.fire({
      title: "Procesando pedido...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const formData = new FormData();
      formData.append("id_direccion", direccion.value);
      formData.append("id_metodo", metodo.value);

      const res = await fetch("actions/procesar_pago.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();
      Swal.close();

      if (data.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Pedido confirmado",
          text: "Tu pedido se ha registrado correctamente.",
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          modal.classList.add("hidden");
          window.location.href = "dashboard_cliente.php#mis-pedidos";
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: data.message });
      }
    } catch (err) {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error de conexión",
        text: "No se pudo contactar con el servidor."
      });
    }
  });
});



// ==========================================================
// BÚSQUEDA EN TIEMPO REAL DE PEDIDO
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("numero_pedido_live");
  const resultado = document.getElementById("resultado-pedido");
  if (!input) return;

  let timeout = null;

  input.addEventListener("input", () => {
    const valor = input.value.trim();

    // si no hay nada, limpiamos
    if (valor === "") {
      resultado.innerHTML = "";
      return;
    }

    // solo cuando escriba algo tipo FE- o número
    if (!/^FE-\d*$/i.test(valor)) {
      resultado.innerHTML = `<div class="text-gray-500 mt-2">Formato: FE- seguido del número de pedido</div>`;
      return;
    }

    clearTimeout(timeout);
    timeout = setTimeout(async () => {
      try {
        const res = await fetch("actions/buscar_pedido.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "numero_pedido=" + encodeURIComponent(valor)
        });

        const text = await res.text();
        let data;
        try {
          data = JSON.parse(text);
        } catch {
          resultado.innerHTML = `<div class="text-red-600 mt-2">Error al interpretar la respuesta.</div>`;
          return;
        }

        if (data.status === "ok") {
          resultado.innerHTML = `
            <div class="bg-gray-50 border rounded-lg p-4 mt-4">
              <p><strong>ID Pedido:</strong> #FE-${data.id_pedido}</p>
              <p><strong>Estado:</strong> ${data.estado}</p>
              <p><strong>Rastreo:</strong> ${data.estado_rastreo}</p>
              <p><strong>Fecha:</strong> ${data.fecha_pedido}</p>
              <p><strong>Total:</strong> $${data.total} MXN</p>
            </div>
          `;
        } else {
          resultado.innerHTML = `<div class="text-red-500 mt-3">${data.message || "Pedido no encontrado."}</div>`;
        }
      } catch {
        resultado.innerHTML = `<div class="text-red-500 mt-3">Error de conexión con el servidor.</div>`;
      }
    }, 400); // ← espera 400 ms después de dejar de escribir
  });
});

// ==========================================================
// MÓDULO: NOTIFICACIONES
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const listaNotif = document.getElementById("lista-notificaciones");
  if (!listaNotif) return;

  async function cargarNotificaciones() {
    try {
      const res = await fetch("actions/listar_notificaciones.php");
      const html = await res.text();
      listaNotif.innerHTML = html;
    } catch (err) {
      console.error("Error al cargar notificaciones:", err);
    }
  }

  cargarNotificaciones();
  setInterval(cargarNotificaciones, 60000); // refrescar cada minuto
});


// ==========================================================
// MÓDULO: RESEÑAS
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {
  const pendientesDiv = document.getElementById("pendientes-reseña");
  const listaResenasDiv = document.getElementById("lista-reseñas");
  const modal = document.getElementById("modal-reseña");
  const form = document.getElementById("form-reseña");
  const btnCancelar = document.getElementById("btn-cancelar-reseña");

  if (!pendientesDiv) return;

  async function cargarPendientes() {
    const res = await fetch("actions/listar_pedidos_pendientes_reseña.php");
    pendientesDiv.innerHTML = await res.text();
  }

  async function cargarResenas() {
    const res = await fetch("actions/listar_reseñas.php");
    listaResenasDiv.innerHTML = await res.text();
  }

  // abrir modal al presionar "Agregar reseña"
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("btn-agregar-reseña")) {
      const idPedido = e.target.dataset.idpedido;
      const idArticulo = e.target.dataset.idarticulo;
      document.getElementById("id_pedido_reseña").value = idPedido;
      document.getElementById("id_articulo_reseña").value = idArticulo;
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    }
  });

  // cerrar modal
  btnCancelar.addEventListener("click", () => {
    modal.classList.add("hidden");
    form.reset();
  });

  // enviar reseña
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    const res = await fetch("actions/agregar_reseña.php", {
      method: "POST",
      body: data,
    });
    const txt = await res.text();
    if (txt.includes("success")) {
      Swal.fire("Gracias por tu reseña", "Tu opinión fue registrada.", "success");
      modal.classList.add("hidden");
      form.reset();
      cargarPendientes();
      cargarResenas();
    } else {
      Swal.fire("Error", "No se pudo guardar la reseña.", "error");
    }
  });

  cargarPendientes();
  cargarResenas();
});

document.addEventListener("click", function(e) {
    // Si se presiona el botón "Proceder al Pago"
    if (e.target && e.target.id === "btn-ir-pago") {
        e.preventDefault();
        const modal = document.getElementById("modal-confirmar-pedido");
        if (modal) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }
    }

    // Si se presiona el botón "Cancelar"
    if (e.target && e.target.id === "btn-cancelar-pedido") {
        const modal = document.getElementById("modal-confirmar-pedido");
        if (modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    }
});

// Abre/cierra modal usando delegación (sirve aunque #btn-ir-pago venga de listar_carrito.php)
document.addEventListener('click', async (e) => {
  const abrir = e.target.closest('#btn-ir-pago');
  const cerrar = e.target.closest('#btn-cancelar-pedido');
  const confirmar = e.target.closest('#btn-confirmar-pedido');

  if (abrir) {
    e.preventDefault();
    const modal = document.getElementById('modal-confirmar-pedido');
    // (Opcional) cargar resumen por AJAX antes de abrir
    try {
      const res = await fetch('actions/generar_resumen_pedido.php');
      document.getElementById('resumen-pedido').innerHTML = await res.text();
    } catch { /* si falla, igual abrimos y puedes mostrar un mensaje dentro */ }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  if (cerrar) {
    const modal = document.getElementById('modal-confirmar-pedido');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  if (confirmar) {
    // aquí tu lógica actual de procesar pago (POST a actions/procesar_pago.php, etc.)
    // al finalizar, puedes cerrar el modal igual que en "cerrar"
  }
});

