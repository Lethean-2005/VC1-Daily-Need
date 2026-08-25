// <--------------// ----------------- nav links ---------------- //
document.addEventListener("DOMContentLoaded", function () {
    // Select all navbar links
    const navLinks = document.querySelectorAll(".nav-link");

    // Function to remove active class from all links
    function removeActiveClass() {
        navLinks.forEach(link => link.classList.remove("active"));
    }

    // Add click event to each link
    navLinks.forEach(link => {
        link.addEventListener("click", function () {
            removeActiveClass();  // Remove active class from all links
            this.classList.add("active");  // Add active class to the clicked link
        });
    });

    // Set active class based on current URL (for page reloads)
    const currentPath = window.location.pathname;
    navLinks.forEach(link => {
        if (link.getAttribute("href") === currentPath) {
            removeActiveClass();
            link.classList.add("active");
        }
    });
});

// ------------- link page on view detail -------------------->
function viewDetails(productId) {
    console.log(`/product_detail?productId=${productId}`);
    window.location.href = `/product_detail?productId=${productId}`;
    }

// ----------------- rate filter -------------------->
const stats = document.querySelectorAll('.stat');
stats.forEach(stat => {
    stat.addEventListener('mouseenter', () => {
        stat.classList.add('hovered');
        stat.querySelector('h3').classList.add('text-warning');
        stat.querySelector('h6').classList.add('text-success');
        const img = stat.querySelector('.icon-overlay img');
        if (img) {
            img.style.transform = 'scale(1.2) rotate(10deg)';
        }
    });
    stat.addEventListener('mouseleave', () => {
        stat.classList.remove('hovered');
        stat.querySelector('h3').classList.remove('text-warning');
        stat.querySelector('h6').classList.remove('text-success');
        const img = stat.querySelector('.icon-overlay img');
        if (img) {
            img.style.transform = 'scale(1) rotate(0deg)';
        }
    });
});

// ------------------ Search Functionality (by Product Name Only) ------------------>
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('search');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            filterProducts();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
    }

    function filterProducts() {
        const searchQuery = searchInput.value.toLowerCase();
        const products = document.querySelectorAll('#productList > div');

        products.forEach(product => {
            const title = product.querySelector('.card-title').textContent.toLowerCase();

            // Check if the title includes the search query
            const matchesSearch = title.includes(searchQuery);

            if (matchesSearch) {
                product.style.display = 'block';  // Show the product
            } else {
                product.style.display = 'none';  // Hide the product
            }
        });
    }



// -------------------- Rating Functionality -------------------->
    function setRating(productId, rating) {
        const stars = document.querySelectorAll(`[data-product-id="${productId}"] .star`);
        stars.forEach(star => {
            const starValue = parseInt(star.getAttribute('data-star'));
            if (starValue <= rating) {
                star.classList.add('filled');
            } else {
                star.classList.remove('filled');
            }
        });
        const ratingValueElement = document.querySelector(`[data-rating-id="${productId}"]`);
        ratingValueElement.textContent = `(${rating})`;
    }

    // Favorite Toggle
    function toggleFavorite(productId) {
        const heartIcon = document.querySelector(`[data-heart-id="${productId}"]`);
        if (!heartIcon) return;
        if (heartIcon.classList.contains('ti-heart')) {
            heartIcon.classList.toggle('active');
        } else if (heartIcon.classList.contains('bi-heart')) {
            heartIcon.classList.remove('bi-heart');
            heartIcon.classList.add('bi-heart-fill');
        } else {
            heartIcon.classList.remove('bi-heart-fill');
            heartIcon.classList.add('bi-heart');
        }
    }
    window.setRating = setRating;
    window.toggleFavorite = toggleFavorite;


// <--------------------- rate filter -------------------->
// rate filter
const priceRangeInput = document.getElementById("priceRange");
if (priceRangeInput) {
    priceRangeInput.addEventListener("input", function () {
        let selectedPrice = parseInt(this.value);
        const priceValueEl = document.getElementById("priceValue");
        if (priceValueEl) priceValueEl.innerText = `$1 - $${selectedPrice}`;

        document.querySelectorAll(".col-md-4").forEach(product => {
            const priceEl = product.querySelector(".price");
            if (!priceEl) return;
            let productPrice = parseFloat(priceEl.innerText.replace(/[^0-9.]/g, ""));

            if (productPrice <= selectedPrice) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }
        });
    });
}



   




        

        