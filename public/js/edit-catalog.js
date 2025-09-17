document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const imgUrl = this.getAttribute('data-imgurl');
            const ideal = this.getAttribute('data-ideal');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-precio').value = price;
            document.getElementById('edit-imgUrl').value = imgUrl;
            document.getElementById('edit-ideal').value = ideal;

            const form = document.getElementById('editForm');
            form.action = `/updateCatalog/${id}`;
            });
        });ºº
    }); 