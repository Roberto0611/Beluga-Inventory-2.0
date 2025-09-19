document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const imgUrl = this.getAttribute('data-imgurl');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-precio').value = price;
            document.getElementById('edit-imgUrl').value = imgUrl;

            const form = document.getElementById('editForm');
            form.action = `/updateCatalog/${id}`;
            });
        });ºº
    }); 