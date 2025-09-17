document.addEventListener('DOMContentLoaded', function () {
    // Select all forms with class 'reduce-form'
    const forms = document.querySelectorAll('.reduce-form');

    // Get CSRF token
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        console.error('CSRF token meta tag not found. Ensure <meta name="csrf-token" content="{{ csrf_token() }}"> is in the layout.');
        alert('Error: No se encontró el token CSRF. Contacta al administrador.');
        return;
    }
    const csrfToken = csrfMeta.getAttribute('content');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Prompt user for quantity
            let cantidad = prompt("¿Cuántas unidades deseas reducir?");

            if (cantidad === null || cantidad === "") {
                return; // User canceled or left empty
            }

            cantidad = Number(cantidad);

            // Validate input: must be a positive integer
            if (isNaN(cantidad) || cantidad <= 0 || !Number.isInteger(cantidad)) {
                alert("Introduce una cantidad válida (entero positivo).");
                return;
            }

            // Set the reduction_quantity input value
            const reductionInput = form.querySelector('input[name="reduction_quantity"]');
            reductionInput.value = cantidad;

            // Get form data
            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');

            // Send AJAX request
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the UI
                    const row = form.closest('tr');
                    if (data.deleted) {
                        // Remove the row if item was deleted
                        row.remove();
                    } else {
                        // Update quantity in the table
                        row.querySelector('td:first-child').textContent = data.new_quantity;
                        form.querySelector('input[name="current_quantity"]').value = data.new_quantity;
                    }
                    // Call actualizarContadores to update badges
                    if (typeof window.actualizarContadores === 'function') {
                        window.actualizarContadores();
                    } else {
                        console.warn('actualizarContadores function not found.');
                    }
                    alert('Producto actualizado correctamente.');
                } else {
                    alert('Error: ' + (data.message || 'No se pudo procesar la solicitud.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud.');
            });
        });
    });
});