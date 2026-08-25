document.addEventListener("DOMContentLoaded", function () {
    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("search");
    const priceRange = document.getElementById("priceRange");
    const priceValue = document.getElementById("priceValue");
    const products = document.querySelectorAll(".Product");
    const productCountElement = document.getElementById("productCount");
    
    function filterProducts() {
        const searchText = searchInput.value.toLowerCase();
        const selectedPrice = parseFloat(priceRange.value);
        priceValue.textContent = `$0.25 - $${selectedPrice}`; // Show selected price range

        let visibleCount = 0;

        products.forEach((product) => {
            const productName = product.querySelector(".text-muted").textContent.toLowerCase();
            const productPrice = parseFloat(
                product.querySelector(".text-success").textContent.replace("$", "")
            );

            if ((productName.includes(searchText) || searchText === "") && productPrice <= selectedPrice) {
                product.style.display = "block";
                visibleCount++;
            } else {
                product.style.display = "none";
            }
        });

        productCountElement.textContent = `Showing 1-${visibleCount}`;
    }

    // Search function on form submit
    searchForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent page reload
        filterProducts();
    });

    // Live search as the user types
    searchInput.addEventListener("input", filterProducts);

    // Filter products by price
    priceRange.addEventListener("input", filterProducts);

    // Initialize product count display
    filterProducts();
});
