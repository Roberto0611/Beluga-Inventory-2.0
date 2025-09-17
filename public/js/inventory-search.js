document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const expireButton = document.getElementById("expireButton");
    const expiredButton = document.getElementById("expiredButton");
    const allProductsButton = document.getElementById("allProductsButton");

    let filtros = {
        caducidadActivo: false,
        caducadosActivo: false,
        textoBusqueda: ''
    };

    // Función para normalizar fechas (solo año, mes, día) en UTC
    function normalizeDate(date) {
        if (!(date instanceof Date) || isNaN(date)) return null;
        return new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate()));
    }

    // Función para limpiar el texto de la fecha
    function cleanDateString(dateString) {
        // Eliminar íconos, espacios y caracteres no deseados
        return dateString.replace(/[^0-9-]/g, '').trim();
    }

    // Expose actualizarContadores globally
    window.actualizarContadores = function() {
        document.querySelectorAll(".accordion-item").forEach(accordion => {
            if (accordion.style.display === "none") return;
            
            const rows = accordion.querySelectorAll("tbody tr");
            let totalVisible = 0;
            let totalExpireSoon = 0;
            let totalExpired = 0;
            const today = normalizeDate(new Date());
            const limitDate = normalizeDate(new Date());
            limitDate.setDate(today.getDate() + 30);

            rows.forEach(row => {
                if (row.style.display !== "none") {
                    const quantity = parseInt(row.querySelector("td").textContent);
                    const dateText = cleanDateString(row.querySelector(".dateInfo").textContent);
                    
                    let fechaCaducidad = null;
                    if (dateText && dateText !== "null") {
                        fechaCaducidad = normalizeDate(new Date(dateText));
                    }

                    // Depuración
                    console.log(`Contadores - Fecha original: ${dateText}, Parseada: ${fechaCaducidad}, Hoy: ${today}, ¿Caducado?: ${fechaCaducidad && fechaCaducidad <= today}, ¿Pronto a caducar?: ${fechaCaducidad && fechaCaducidad <= limitDate && fechaCaducidad > today}`);

                    if (!isNaN(quantity)) {
                        totalVisible += quantity;
                        if (fechaCaducidad) {
                            if (fechaCaducidad <= limitDate && fechaCaducidad > today) {
                                totalExpireSoon += quantity;
                            }
                            if (fechaCaducidad <= today) {
                                totalExpired += quantity;
                            }
                        }
                    }
                }
            });

            // Encontrar o crear el contador-cápsula
            let counterBadge = accordion.querySelector(".counter-badge");
            if (!counterBadge) {
                counterBadge = document.createElement("span");
                counterBadge.className = "counter-badge";
                accordion.querySelector(".accordion-button").appendChild(counterBadge);
            }

            // Actualizar el contador con estilo de cápsula
            if (filtros.caducadosActivo) {
                counterBadge.innerHTML = `
                    <span class="badge bg-danger text-white">
                        <i class="bi bi-exclamation-triangle"></i> ${totalExpired} caducados
                    </span>
                `;
            } else if (filtros.caducidadActivo) {
                counterBadge.innerHTML = `
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock"></i> ${totalExpireSoon} pronto a caducar
                    </span>
                `;
            } else {
                counterBadge.innerHTML = `
                    <span class="badge bg-primary">
                        <i class="bi bi-box-seam"></i> ${totalVisible} en stock
                    </span>
                `;
            }
        });
    };

    function aplicarFiltros() {
        const today = normalizeDate(new Date());
        const limitDate = normalizeDate(new Date());
        limitDate.setDate(today.getDate() + 30);

        // Depuración: mostrar fecha actual
        console.log(`Fecha actual: ${today}, Límite (30 días): ${limitDate}`);

        document.querySelectorAll(".accordion-item").forEach(accordion => {
            const textoProducto = accordion.querySelector(".accordion-button").textContent.toLowerCase();
            const cumpleBusqueda = !filtros.textoBusqueda || textoProducto.includes(filtros.textoBusqueda);

            let tieneRegistrosValidos = false;
            const rows = accordion.querySelectorAll("tbody tr");
            
            rows.forEach(row => {
                const dateText = cleanDateString(row.querySelector(".dateInfo").textContent);
                let fechaCaducidad = null;
                if (dateText && dateText !== "null") {
                    fechaCaducidad = normalizeDate(new Date(dateText));
                }

                // Depuración
                console.log(`Filtro - Fecha original: ${dateText}, Parseada: ${fechaCaducidad}, Hoy: ${today}, ¿Caducado?: ${fechaCaducidad && fechaCaducidad <= today}, ¿Pronto a caducar?: ${fechaCaducidad && fechaCaducidad <= limitDate && fechaCaducidad > today}`);

                const cumpleCaducidad = (
                    (!filtros.caducidadActivo && !filtros.caducadosActivo) || // Sin filtros: mostrar todos, incluyendo null
                    (filtros.caducidadActivo && fechaCaducidad && fechaCaducidad <= limitDate && fechaCaducidad > today) || // Prontos a caducar
                    (filtros.caducadosActivo && fechaCaducidad && fechaCaducidad <= today) // Caducados
                );
                
                row.style.display = (cumpleCaducidad && cumpleBusqueda) ? "" : "none";
                
                if (cumpleCaducidad && cumpleBusqueda) {
                    tieneRegistrosValidos = true;
                }
            });

            accordion.style.display = (tieneRegistrosValidos && cumpleBusqueda) ? "" : "none";
        });

        // Actualizar contadores después de aplicar filtros
        window.actualizarContadores();
    }

    searchInput.addEventListener("input", function() {
        filtros.textoBusqueda = this.value.toLowerCase();
        aplicarFiltros();
    });

    expireButton.addEventListener("click", function() {
        filtros.caducidadActivo = !filtros.caducidadActivo;
        filtros.caducadosActivo = false; // Desactivar filtro de caducados
        
        if (filtros.caducidadActivo) {
            this.classList.replace("btn-primary", "btn-success");
            allProductsButton.classList.replace("btn-success", "btn-primary");
            expiredButton.classList.replace("btn-success", "btn-primary");
        } else {
            this.classList.replace("btn-success", "btn-primary");
            if (!filtros.caducadosActivo) {
                allProductsButton.classList.replace("btn-primary", "btn-success");
            }
        }
        
        aplicarFiltros();
    });

    expiredButton.addEventListener("click", function() {
        filtros.caducadosActivo = !filtros.caducadosActivo;
        filtros.caducidadActivo = false; // Desactivar filtro de prontos a caducar
        
        if (filtros.caducadosActivo) {
            this.classList.replace("btn-primary", "btn-success");
            allProductsButton.classList.replace("btn-success", "btn-primary");
            expireButton.classList.replace("btn-success", "btn-primary");
        } else {
            this.classList.replace("btn-success", "btn-primary");
            if (!filtros.caducidadActivo) {
                allProductsButton.classList.replace("btn-primary", "btn-success");
            }
        }
        
        aplicarFiltros();
    });

    allProductsButton.addEventListener("click", function() {
        if (filtros.caducidadActivo || filtros.caducadosActivo) {
            filtros.caducidadActivo = false;
            filtros.caducadosActivo = false;
            expireButton.classList.replace("btn-success", "btn-primary");
            expiredButton.classList.replace("btn-success", "btn-primary");
            this.classList.replace("btn-primary", "btn-success");
            aplicarFiltros();
        }
    });

    // Inicializar contadores al cargar
    window.actualizarContadores();
});