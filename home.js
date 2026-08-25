document.addEventListener("DOMContentLoaded", function () {
    const themeButton = document.querySelector(".theme-btn");
    const heroArt = document.querySelector(".hero-art");

    if (themeButton) {
        themeButton.addEventListener("click", function () {
            document.body.classList.toggle("light-preview");
            themeButton.textContent =
                document.body.classList.contains("light-preview") ? "☾" : "☀";
        });
    }

    if (heroArt && window.matchMedia("(min-width: 900px)").matches) {
        heroArt.addEventListener("mousemove", function (e) {
            const rect = heroArt.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            heroArt.style.transform =
                `translate(${x * 6}px, ${y * 4}px)`;
        });

        heroArt.addEventListener("mouseleave", function () {
            heroArt.style.transform = "";
        });
    }

    const cards = document.querySelectorAll(".product-card, .category-pills a");
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show-card");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    cards.forEach((card, i) => {
        card.style.transitionDelay = `${Math.min(i * 60, 240)}ms`;
        observer.observe(card);
    });
});
