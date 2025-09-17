document.addEventListener('DOMContentLoaded', function() {
    const productItems = document.querySelectorAll('.product-item');
    
    productItems.forEach(item => {
        const idealText = item.querySelector('p:nth-child(2)').textContent;
        const currentText = item.querySelector('p:nth-child(3)').textContent;
        const faltanteElement = item.querySelector('.faltante');
        
        const ideal = parseInt(idealText.replace(/\D/g, '')) || 0;
        const current = parseInt(currentText.replace(/\D/g, '')) || 0;
        const missing = ideal - current;
        
        // Calcular porcentaje faltante (protección contra división por cero)
        const porcentaje = ideal > 0 ? missing / ideal : 0;
        
        // Eliminar clases anteriores
        faltanteElement.classList.remove(
            'faltante-cero', 'faltante-bajo', 'faltante-medio', 'faltante-alto', 'faltante-critico'
        );
        
        // Aplicar nueva clase según porcentaje
        if (missing <= 0) {
            faltanteElement.classList.add('faltante-cero');
        } else if (porcentaje <= 0.2) {
            faltanteElement.classList.add('faltante-bajo');
        } else if (porcentaje <= 0.5) {
            faltanteElement.classList.add('faltante-medio');
        } else if (porcentaje <= 0.75) {
            faltanteElement.classList.add('faltante-alto');
        } else {
            faltanteElement.classList.add('faltante-critico');
        }
    });
});