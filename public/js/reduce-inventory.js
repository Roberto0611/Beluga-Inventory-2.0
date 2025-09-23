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
                    const locationBlock = row.closest('.location-block');
                    const productAccordionBody = row.closest('.accordion-body');
                    const productId = row.getAttribute('data-product-id');
                    const locationName = row.getAttribute('data-location');

                    if (data.deleted) {
                        // Remove the row if item was deleted
                        row.remove();
                    } else {
                        // Update quantity cell (first td)
                        row.querySelector('td:first-child').textContent = data.new_quantity;
                        form.querySelector('input[name="current_quantity"]').value = data.new_quantity;
                    }

                    // Recalculate metrics for that location
                    recalcLocationMetrics(locationBlock);
                    // Recalculate product summary
                    recalcProductSummary(productAccordionBody, {considerVisibility:true});
                    // If filtering by location is active, make sure summary respects it
                    if(typeof window.updateFilteredUnitsForProduct === 'function'){
                        window.updateFilteredUnitsForProduct(productId);
                    }
                    // Update select options counts
                    updateSelectOptions(productAccordionBody, productId);

                    // If location block now has no rows, remove it and update again
                    if (locationBlock && locationBlock.querySelectorAll('tbody tr').length === 0) {
                        locationBlock.remove();
                        // If the removed block was currently selected in the dropdown, reset to all
                        const select = productAccordionBody.querySelector(`select.product-location-filter[data-product-id='${productId}']`);
                        if(select && select.value === locationName){
                            select.value = '';
                            // Reveal any hidden blocks (were hidden by location filter)
                            productAccordionBody.querySelectorAll('.location-block').forEach(b => b.style.display='');
                        }
                        recalcProductSummary(productAccordionBody, {considerVisibility:true});
                        updateSelectOptions(productAccordionBody, productId);

                        // Si ya no quedan bloques de ubicación, eliminar todo el accordion del producto
                        const remainingBlocks = productAccordionBody.querySelectorAll('.location-block').length;
                        if(remainingBlocks === 0){
                            const accordionItem = productAccordionBody.closest('.accordion-item');
                            if(accordionItem){ accordionItem.remove(); }
                        }
                    }

                    // Re-run external counters if present
                    if (typeof window.actualizarContadores === 'function') {
                        try { window.actualizarContadores(); } catch(e){ console.warn(e); }
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

// ---- Helper Functions for dynamic UI updates ---- //

// Centraliza reglas de estado: devuelve {status, icon, className}
function classifyExpiration(dateStr){
    if(!dateStr) return null;
    const diff = dateDiffDays(dateStr); // positivo => pasado (caducado), negativo => futuro
    if(diff > 0){
        return {status:'expired', icon:'⚠️', className:'badge-expired'};
    } else if(diff >= -15){
        return {status:'soon', icon:'⏳', className:'badge-soon'};
    } else {
        return {status:'fresh', icon:'✅', className:'badge-fresh'};
    }
}

// Extrae YYYY-MM-DD de un texto que puede contener iconos/emojis
function cleanDateStr(text){
    if(!text) return '';
    const match = text.match(/\d{4}-\d{2}-\d{2}/);
    return match ? match[0] : '';
}

function recalcLocationMetrics(locationBlock){
    if(!locationBlock) return;
    const rows = locationBlock.querySelectorAll('tbody tr');
    let sum = 0; let earliest = null;
    rows.forEach(r => {
        const qtyCell = r.querySelector('td:first-child');
        const dateCell = r.querySelector('.dateInfo');
        if(qtyCell){
            const val = parseInt(qtyCell.textContent.trim(),10); if(!isNaN(val)) sum += val;
        }
        if(dateCell){
            const raw = cleanDateStr(dateCell.textContent.trim());
            if(raw){
                if(!earliest || raw < earliest){ earliest = raw; }
            }
        }
    });
    // Update badge units
    const unitBadge = locationBlock.querySelector('.loc-metric');
    if(unitBadge) unitBadge.textContent = sum + ' uds';
    // Update expiration badge
    const expBadge = locationBlock.querySelector('.loc-exp');
    if(expBadge){
        if(!earliest){
            expBadge.remove();
        } else {
            const info = classifyExpiration(earliest);
            expBadge.className = 'loc-exp badge ' + info.className;
            expBadge.textContent = `${info.icon} ${earliest}`;
            if(info.status==='expired'){ expBadge.style.background='#dc3545'; expBadge.style.color='#fff'; }
            else if(info.status==='soon'){ expBadge.style.background='#ffc107'; expBadge.style.color='#000'; }
            else { expBadge.style.background='#198754'; expBadge.style.color='#fff'; }
        }
    } else if(earliest){
        const header = locationBlock.querySelector('.location-block-header');
        if(header){
            const span = document.createElement('span');
            const info = classifyExpiration(earliest);
            span.className = 'loc-exp badge ' + info.className;
            span.textContent = `${info.icon} ${earliest}`;
            if(info.status==='expired'){ span.style.background='#dc3545'; span.style.color='#fff'; }
            else if(info.status==='soon'){ span.style.background='#ffc107'; span.style.color='#000'; }
            else { span.style.background='#198754'; span.style.color='#fff'; }
            header.appendChild(span);
        }
    }
}

function recalcProductSummary(accordionBody, opts={}){
    if(!accordionBody) return;
    const summaryBar = accordionBody.querySelector('.inventory-summary-bar');
    if(!summaryBar) return;
    const considerVisibility = opts.considerVisibility === true; // when filtering we only count visible rows
    // Collect all item rows inside this product's body
    const allRows = accordionBody.querySelectorAll('.location-block tbody tr');
    let totalUnits = 0; let earliest = null; let locations = 0;
    const locationBlocks = accordionBody.querySelectorAll('.location-block');
    locations = Array.from(locationBlocks).filter(b => !considerVisibility || b.style.display !== 'none').length;
    allRows.forEach(r => {
        if(considerVisibility && (r.style.display === 'none' || r.closest('.location-block')?.style.display === 'none')){
            return; // skip hidden when filtering view
        }
        const qtyCell = r.querySelector('td:first-child');
        const dateCell = r.querySelector('.dateInfo');
        if(qtyCell){
            const val = parseInt(qtyCell.textContent.trim(),10); if(!isNaN(val)) totalUnits += val;
        }
        if(dateCell){
            const raw = cleanDateStr(dateCell.textContent.trim());
            if(raw){ if(!earliest || raw < earliest) earliest = raw; }
        }
    });
    // Update units chip
    const unitChip = summaryBar.querySelector('.summary-chip:nth-child(1) .chip-value');
    if(unitChip) unitChip.textContent = totalUnits;
    // Update locations chip
    const locChip = summaryBar.querySelector('.summary-chip:nth-child(2) .chip-value');
    if(locChip) locChip.textContent = locations;
    // Update earliest & status
    let earliestChipWrapper = summaryBar.querySelector('.summary-chip:nth-child(3)');
    let statusBadge = summaryBar.querySelector('.summary-status');
    if(!earliest){
        if(earliestChipWrapper) earliestChipWrapper.remove();
        if(statusBadge) statusBadge.remove();
    } else {
        if(!earliestChipWrapper){
            // Rebuild earliest chip if removed previously
            const chip = document.createElement('div');
            chip.className = 'summary-chip';
            chip.innerHTML = '<span class="chip-label">Primera caducidad</span><span class="chip-value"></span>';
            summaryBar.insertBefore(chip, summaryBar.children[2] || null);
            earliestChipWrapper = chip;
        }
        earliestChipWrapper.querySelector('.chip-value').textContent = earliest;
        const info = classifyExpiration(earliest);
        if(!statusBadge){
            statusBadge = document.createElement('div');
            statusBadge.className = 'summary-status';
            summaryBar.appendChild(statusBadge);
        }
        statusBadge.className = 'summary-status ' + (info.status==='expired' ? 'inv-badge-expired' : info.status==='soon' ? 'inv-badge-soon' : 'inv-badge-fresh');
        statusBadge.textContent = info.status==='expired' ? 'Caducado' : (info.status==='soon' ? 'Pronto a caducar' : 'Fresco');
        // Inline color update (en caso de que CSS no lo cubra o se haya cambiado dinamicamente)
        if(statusBadge.classList.contains('inv-badge-expired')){ statusBadge.style.background='#dc3545'; statusBadge.style.color='#fff'; }
        else if(statusBadge.classList.contains('inv-badge-soon')){ statusBadge.style.background='#ffc107'; statusBadge.style.color='#000'; }
        else { statusBadge.style.background='#198754'; statusBadge.style.color='#fff'; }
    }
}

// Helper accessible globally to refresh only units/earliest for a given product after filtering
window.updateFilteredUnitsForProduct = function(productId){
    const body = document.querySelector(`#collapse${productId} .accordion-body`);
    if(body){
        recalcProductSummary(body, {considerVisibility:true});
    }
}

// Recalculate summaries for all products (considering current visibility/filter state)
window.updateAllProductSummaries = function(){
    document.querySelectorAll('#inventoryAccordion .accordion-body').forEach(body => {
        recalcProductSummary(body, {considerVisibility:true});
    });
}

function updateSelectOptions(accordionBody, productId){
    if(!accordionBody) return; if(!productId) return;
    const select = accordionBody.querySelector(`select.product-location-filter[data-product-id='${productId}']`);
    if(!select) return;
    const blocks = Array.from(accordionBody.querySelectorAll('.location-block'));
    let selectedValue = select.value;
    // Detect if selected location block was removed
    if(selectedValue && !blocks.some(b => b.getAttribute('data-location-block') === selectedValue)){
        // Reset filter: show all blocks again
        selectedValue = '';
        select.value = '';
        blocks.forEach(b => { if(b.style.display === 'none') b.style.display=''; });
    }
    // Build counts for all existing blocks (even if currently hidden by location filter)
    const counts = {};
    blocks.forEach(block => {
        const name = block.getAttribute('data-location-block');
        let sum = 0;
        block.querySelectorAll('tbody tr').forEach(r => {
            // Si la fila está oculta por otros filtros (search / caducidad) no se suma.
            if(r.style.display === 'none') return;
            const qtyCell = r.querySelector('td:first-child');
            if(qtyCell){ const v = parseInt(qtyCell.textContent.trim(),10); if(!isNaN(v)) sum += v; }
        });
        counts[name] = sum;
    });
    // Rebuild select
    select.innerHTML = '';
    const allOpt = document.createElement('option');
    allOpt.value = '';
    allOpt.textContent = `Todas las ubicaciones (${blocks.length})`;
    if(selectedValue === '') allOpt.selected = true;
    select.appendChild(allOpt);
    Object.entries(counts).forEach(([name,sum]) => {
        const opt = document.createElement('option');
        opt.value = name;
        opt.textContent = `${name} (${sum})`;
        if(name === selectedValue) opt.selected = true;
        select.appendChild(opt);
    });
}
// Exponer para otros scripts (filtros globales)
window.updateSelectOptionsForProduct = function(productId){
    const body = document.querySelector(`#collapse${productId} .accordion-body`);
    if(body){ updateSelectOptions(body, productId); }
}

function dateDiffDays(dateStr){
    // dateStr in format YYYY-MM-DD
    const d = new Date(dateStr + 'T00:00:00');
    const today = new Date();
    const ms = d.getTime() - new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
    return Math.floor(ms / 86400000) * -1; // Convert difference (target - today); positive if past
}