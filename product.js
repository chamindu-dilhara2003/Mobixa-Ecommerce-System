/* =====================================================
   MOBIXA PRODUCTS JAVASCRIPT
   ===================================================== */

document.addEventListener("DOMContentLoaded", function () {


    /* =================================================
       ELEMENTS
       ================================================= */

    const searchInput =
        document.getElementById("searchInput");

    const searchButton =
        document.getElementById("searchButton");

    const categories =
        document.querySelectorAll(".category");

    const products =
        document.querySelectorAll(".product-card");

    const noResults =
        document.getElementById("noResults");

    const themeToggle =
        document.getElementById("themeToggle");

    const cartCount =
        document.getElementById("cartCount");


    /* =================================================
       CURRENT CATEGORY
       ================================================= */

    let currentCategory = "All";


    /* =================================================
       SEARCH + FILTER FUNCTION
       ================================================= */

    function filterProducts() {

        const searchValue =
            searchInput.value
                .trim()
                .toLowerCase();


        let visibleProducts = 0;


        products.forEach(function (product) {

            const name =
                product.dataset.name
                    .toLowerCase();

            const category =
                product.dataset.category;


            const matchesSearch =
                name.includes(searchValue);


            const matchesCategory =
                currentCategory === "All" ||
                category === currentCategory;


            if (
                matchesSearch &&
                matchesCategory
            ) {

                product.style.display =
                    "flex";

                visibleProducts++;

            } else {

                product.style.display =
                    "none";
            }

        });


        /* NO RESULTS */

        if (visibleProducts === 0) {

            noResults.classList.add("show");

        } else {

            noResults.classList.remove("show");
        }
    }


    /* =================================================
       SEARCH BUTTON
       ================================================= */

    searchButton.addEventListener(
        "click",
        function () {

            filterProducts();
        }
    );


    /* =================================================
       SEARCH WHILE TYPING
       ================================================= */

    searchInput.addEventListener(
        "input",
        function () {

            filterProducts();
        }
    );


    /* =================================================
       CATEGORY FILTER
       ================================================= */

    categories.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {


                /* Remove active */

                categories.forEach(
                    function (btn) {

                        btn.classList.remove(
                            "active"
                        );

                    }
                );


                /* Add active */

                button.classList.add(
                    "active"
                );


                /* Set category */

                currentCategory =
                    button.dataset.category;


                /* Filter */

                filterProducts();

            }
        );

    });


    /* =================================================
       DARK MODE
       ================================================= */

    const savedTheme =
        localStorage.getItem(
            "mobixa-theme"
        );


    if (savedTheme === "dark") {

        document.body.classList.add(
            "dark-mode"
        );

        themeToggle.textContent = "☀";

    } else {

        document.body.classList.remove(
            "dark-mode"
        );

        themeToggle.textContent = "☾";
    }


    /* =================================================
       THEME TOGGLE
       ================================================= */

    themeToggle.addEventListener(
        "click",
        function () {


            document.body.classList.toggle(
                "dark-mode"
            );


            const darkMode =
                document.body.classList.contains(
                    "dark-mode"
                );


            if (darkMode) {

                themeToggle.textContent =
                    "☀";

                localStorage.setItem(
                    "mobixa-theme",
                    "dark"
                );

            } else {

                themeToggle.textContent =
                    "☾";

                localStorage.setItem(
                    "mobixa-theme",
                    "light"
                );
            }

        }
    );
/* =========================================================
   PRODUCT SORTING
   ========================================================= */

const sortSelect = document.getElementById("sortSelect");
const productGrid = document.getElementById("productGrid");
const productCount = document.getElementById("productCount");


if (sortSelect) {

    sortSelect.addEventListener("change", function () {

        const sortType = this.value;

        const products = Array.from(
            productGrid.querySelectorAll(".product-card")
        );

        /* SORT PRODUCTS */

        if (sortType === "price-low") {

            products.sort(function (a, b) {

                return (
                    parseFloat(a.dataset.price) -
                    parseFloat(b.dataset.price)
                );

            });

        }


        else if (sortType === "price-high") {

            products.sort(function (a, b) {

                return (
                    parseFloat(b.dataset.price) -
                    parseFloat(a.dataset.price)
                );

            });

        }


        else if (sortType === "name-az") {

            products.sort(function (a, b) {

                return a.dataset.name
                    .localeCompare(b.dataset.name);

            });

        }


        else if (sortType === "name-za") {

            products.sort(function (a, b) {

                return b.dataset.name
                    .localeCompare(a.dataset.name);

            });

        }


        /* PUT PRODUCTS BACK */

        products.forEach(function (product) {

            productGrid.appendChild(product);

        });


        /* UPDATE COUNT */

        updateProductCount();

    });

}


/* =========================================================
   PRODUCT COUNT
   ========================================================= */

function updateProductCount() {

    const visibleProducts =
        productGrid.querySelectorAll(
            ".product-card:not([style*='display: none'])"
        );

    if (productCount) {

        productCount.textContent =
            visibleProducts.length +
            (visibleProducts.length === 1
                ? " Product"
                : " Products");

    }

}


updateProductCount();

    /* =================================================
       CART COUNT
       ================================================= */

    function updateCartCount() {

        const cart =
            JSON.parse(
                localStorage.getItem(
                    "mobixa-cart"
                )
            ) || [];


        let count = 0;


        cart.forEach(function (item) {

            count +=
                Number(item.quantity) || 0;

        });


        cartCount.textContent =
            count;
    }


    updateCartCount();


    /* =================================================
       PRODUCT CARD ANIMATION
       ================================================= */

    const observer =
        new IntersectionObserver(
            function (entries) {

                entries.forEach(
                    function (entry) {

                        if (
                            entry.isIntersecting
                        ) {

                            entry.target.style.opacity =
                                "1";

                            entry.target.style.transform =
                                "translateY(0)";

                            observer.unobserve(
                                entry.target
                            );
                        }

                    }
                );

            },
            {
                threshold: 0.1
            }
        );


    products.forEach(
        function (product, index) {

            product.style.opacity =
                "0";

            product.style.transform =
                "translateY(20px)";

            product.style.transition =
                `opacity .5s ease ${index * 0.08}s,
                 transform .5s ease ${index * 0.08}s`;

            observer.observe(product);
        }
    );

});