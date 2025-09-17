document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", function () {
        const searchTerm = searchInput.value.toLowerCase();
        const tableElements = document.querySelectorAll(".tableElements");

        tableElements.forEach(function (tableElement) { 
            const headerText = tableElement.querySelector(".product-name").textContent.toLowerCase();

            if (headerText.includes(searchTerm)) {
                tableElement.style.setProperty('display', 'flex', 'important');     
            } else {
                tableElement.style.setProperty('display', 'none', 'important');
            }
        });
    });
});