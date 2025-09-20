document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Obtener datos del botón
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const imgUrl = this.getAttribute('data-imgurl');
            const idealAlmacen = this.getAttribute('data-ideal-almacen');
            const idealAuto = this.getAttribute('data-ideal-auto');

            // Rellenar el formulario del modal
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-precio').value = price;
            document.getElementById('edit-imgUrl').value = imgUrl;
            document.getElementById('edit-ideal-almacen').value = idealAlmacen;
            document.getElementById('edit-ideal-auto').value = idealAuto;

            const form = document.getElementById('editForm');
            form.action = `/updateCatalog/${id}`;
            });
        });ºº
    }); 