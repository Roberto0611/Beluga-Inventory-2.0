console.log('javascript activo')

$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
});

// select for sells
$(document).ready(function() {
    $('#sell_select').select2({
        width: '100%',          // Ancho fijo del campo
        theme: "bootstrap-5",   // Para Bootstrap 5
        dropdownAutoWidth: true, // Dropdown no se ajusta al texto
    });
});

$(document).ready(function() {
    $("#product_select").select2({
        dropdownParent: $('#exampleModal'),
        width: '100%',          // Ancho fijo del campo
        theme: "bootstrap-5",   // Para Bootstrap 5
        dropdownAutoWidth: true, // Dropdown no se ajusta al texto
    });
});
