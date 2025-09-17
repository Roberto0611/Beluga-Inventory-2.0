document.addEventListener('DOMContentLoaded', function() {
    const fechaActual = new Date();
    
    const fechaElements = document.querySelectorAll('.dateInfo');
    
    // Aplicar colores según la proximidad a caducar
    fechaElements.forEach(element => {
        try {
            const fechaCaducidad = new Date(element.textContent.trim());
            
            // Calcular diferencia en días
            const diffTime = fechaCaducidad - fechaActual;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Aplicar estilos
            if (diffDays < 0) {
                // Producto caducado
                element.style.color = '#dc3545'; // Rojo
                element.style.fontWeight = 'bold';
                element.insertAdjacentHTML('beforeend', ' ⌛');
            } else if (diffDays <= 7) {
                // Caduca en 1 semana
                element.style.color = '#ffc107'; // Amarillo
                element.style.fontWeight = 'bold';
            } else if (diffDays <= 30) {
                // Caduca en 1 mes
                element.style.color = '#fd7e14'; // Naranja
            } else {
                // Buen estado
                element.style.color = '#28a745'; // Verde
            }
            
            //Mostrar días restantes como tooltip
            element.title = `${Math.abs(diffDays)} días ${diffDays < 0 ? 'caducado' : 'restantes'}`;
            
        } catch (error) {
            console.error('Error procesando fecha:', element.textContent, error);
        }
    });
});