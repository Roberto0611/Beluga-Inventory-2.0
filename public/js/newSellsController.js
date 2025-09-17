let sales = [];
let subtotal = 0;
let initialInventory = {}; // Store initial inventory quantities

// Add items
function addItem() {
    const itemList = document.getElementById('sell_select');
    const selectedItem = itemList.options[itemList.selectedIndex];
    const itemName = selectedItem.getAttribute('data-name');
    const itemPrice = parseFloat(selectedItem.getAttribute('data-price'));
    const itemQuantity = parseInt(document.getElementById('itemQuantity').value);
    const itemID = selectedItem.value;
    const isService = selectedItem.getAttribute('data-service') == '1';

    if (itemName && !isNaN(itemPrice) && !isNaN(itemQuantity) && itemQuantity > 0) {
        if (isService) {
            const totalPrice = itemPrice * itemQuantity;
            sales.push({ name: itemName, quantity: itemQuantity, unitPrice: itemPrice, totalPrice: totalPrice, id: itemID });
            subtotal += totalPrice;
            updateSalesTable();
            updateTotals();
            clearInputs();
        } else {
            // Verificar que initialInventory[itemID] esté definido
            if (typeof initialInventory[itemID] === 'undefined') {
                alert('Error: El inventario para este producto no está cargado.');
                return;
            }
            const availableQuantity = initialInventory[itemID] - getUsedQuantity(itemID);
            if (itemQuantity <= availableQuantity) {
                const totalPrice = itemPrice * itemQuantity;
                sales.push({ name: itemName, quantity: itemQuantity, unitPrice: itemPrice, totalPrice: totalPrice, id: itemID });
                subtotal += totalPrice;
                updateSalesTable();
                updateTotals();
                updateInventoryDisplay(itemID);
                clearInputs();
            } else {
                alert('Cantidad seleccionada excede la cantidad en inventario.');
            }
        }
    }
}

// Get used quantity on the sales array
function getUsedQuantity(productId) {
    let usedQuantity = 0;
    sales.forEach(sale => {
        if (sale.id == productId) {
            usedQuantity += sale.quantity;
        }
    });
    return usedQuantity;
}

// Update sales table
function updateSalesTable() {
    const salesTable = document.getElementById('salesTable');
    salesTable.innerHTML = '';
    sales.forEach((sale, index) => {
        const row = `<tr>
                        <td>${sale.name}</td>
                        <td>${sale.quantity}</td>
                        <td>$${sale.unitPrice.toFixed(2)}</td>
                        <td>$${sale.totalPrice.toFixed(2)}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="removeItem(${index})">Eliminar</button></td>
                    </tr>`;
        salesTable.insertAdjacentHTML('beforeend', row);
    });
}

// Delete items from the sales array and table
function removeItem(index) {
    const sale = sales[index];
    subtotal -= sale.totalPrice;
    sales.splice(index, 1);
    updateSalesTable();
    updateTotals();
    updateInventoryDisplay(sale.id);
}

// Update totals
function updateTotals() {
    const discountPercent = parseFloat(document.getElementById('discount').value) || 0;
    const discount = subtotal * (discountPercent / 100);
    const total = subtotal - discount;

    document.getElementById('subtotal').innerText = `$${subtotal.toFixed(2)}`;
    document.getElementById('total').innerText = `$${total.toFixed(2)}`;

    document.getElementById('pagoMonto1').value = total.toFixed(2);

    document.getElementById('formSubtotal').value = subtotal.toFixed(2);
    document.getElementById('formTotal').value = total.toFixed(2);
    document.getElementById('formDescuento').value = discountPercent.toFixed(2);
    document.getElementById('formNotas').value = document.getElementById('notes').value;
    document.getElementById('formCartItems').value = JSON.stringify(sales);
}

// Clear inputs
function clearInputs() {
    // Limpiar select2 seleccionando la opción con value=""
    $('#sell_select').val('').trigger('change.select2', { skipInventoryUpdate: true });
    document.getElementById('itemQuantity').value = 1;
    // Forzar la actualización del texto de inventario
    document.getElementById('inventarioCantidad').innerText = 'Selecciona un producto.';
}
// Update inventory display
function updateInventoryDisplay(productoID) {
    if (typeof initialInventory[productoID] === 'undefined') {
        document.getElementById('inventarioCantidad').innerText = 'Selecciona un producto.';
        return;
    }
    const availableQuantity = initialInventory[productoID] - getUsedQuantity(productoID);
    document.getElementById('inventarioCantidad').innerText = `Cantidad en inventario: ${availableQuantity}`;
}

// Checkout
function checkout() {
    let valid = true;

    sales.forEach(sale => {
        const itemID = sale.id;
        const isService = document.querySelector(`#sell_select option[value="${itemID}"]`).getAttribute('data-service') === '1';
        const quantity = sale.quantity;
    });

    const totalVenta = parseFloat(document.getElementById('total').innerText.split('$')[1]);
    const metodo1 = document.getElementById('pagoMetodo1').value;
    const monto1 = parseFloat(document.getElementById('pagoMonto1').value) || 0;
    const metodo2 = document.getElementById('pagoMetodo2').value;
    const monto2 = parseFloat(document.getElementById('pagoMonto2').value) || 0;

    const sumaPagos = monto1 + monto2;

    if (!metodo1 || monto1 <= 0) {
        alert("Debes ingresar al menos un método de pago válido.");
        return;
    }

    if (metodo2 && metodo1 === metodo2) {
        alert("Los métodos de pago no pueden ser iguales.");
        return;
    }

    if (Math.abs(sumaPagos - totalVenta) > 0.01) {
        alert("La suma de los pagos no coincide con el total.");
        return;
    }

    if(monto2 > 0 && metodo2 == 'Ninguno'){
        alert('seleccione un metodo de pago para el monto 2!!')
        return;
    }

    if (valid) {
        document.getElementById('formSubtotal').value = subtotal.toFixed(2);
        document.getElementById('formTotal').value = totalVenta.toFixed(2);
        document.getElementById('formDescuento').value = document.getElementById('discount').value;
        document.getElementById('formNotas').value = document.getElementById('notes').value;
        document.getElementById('formPagoMetodo1').value = metodo1;
        document.getElementById('formPagoMonto1').value = monto1.toFixed(2);
        document.getElementById('formPagoMetodo2').value = metodo2;
        document.getElementById('formPagoMonto2').value = metodo2 ? monto2.toFixed(2) : "0.00";

        document.getElementById('checkoutForm').submit();
    }
}

// Load inventory and initialize Select2
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar Select2 con un placeholder
    $('#sell_select').select2({
        placeholder: "Busca el producto...", // Debe coincidir con el texto de la opción por defecto
        width: '100%',
        theme: "bootstrap-5",
        dropdownAutoWidth: true,
        allowClear: true // Permite limpiar la selección
    });

    // Manejar evento select2:select
    $('#sell_select').on('select2:select', function (e) {
        const productoID = e.target.value;
        if (productoID) {
            fetch(`/inventario/${productoID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('inventarioCantidad').innerText = 'Error al obtener cantidad en inventario.';
                    } else {
                        initialInventory[productoID] = data.cantidad;
                        updateInventoryDisplay(productoID);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('inventarioCantidad').innerText = 'Error de conexión.';
                });
        } else {
            document.getElementById('inventarioCantidad').innerText = 'Selecciona un producto.';
        }
    });

    // Manejar evento change.select2 para la limpieza
    $('#sell_select').on('change.select2', function (e, data) {
        if (data && data.skipInventoryUpdate) {
            document.getElementById('inventarioCantidad').innerText = 'Selecciona un producto.';
            return;
        }
    });

    // Establecer el estado inicial del select
    $('#sell_select').val('').trigger('change.select2', { skipInventoryUpdate: true });
});