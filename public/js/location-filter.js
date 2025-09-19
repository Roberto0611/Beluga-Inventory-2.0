document.addEventListener('DOMContentLoaded', () => {
    const selects = document.querySelectorAll('.product-location-filter');

    selects.forEach(select => {
        select.addEventListener('change', (e) => {
            const productId = select.getAttribute('data-product-id');
            const value = select.value.trim(); // nombre de la ubicación o '' para todas

            // Bloques completos de ubicación
            const allBlocks = document.querySelectorAll(`.location-block[data-product-id='${productId}']`);
            if (value === '') {
                allBlocks.forEach(b => b.style.display = '');
            } else {
                allBlocks.forEach(b => {
                    const loc = b.getAttribute('data-location-block');
                    b.style.display = (loc === value) ? '' : 'none';
                });
            }

            // Recalcular resumen (Unidades / Ubicaciones / Primera caducidad) solo con visibles
            if(typeof window.updateFilteredUnitsForProduct === 'function'){
                window.updateFilteredUnitsForProduct(productId);
            }
        });
    });
});
