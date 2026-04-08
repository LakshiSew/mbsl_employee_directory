document.addEventListener("DOMContentLoaded", function () {

    const toggleBtn = document.getElementById("darkModeToggle");

    if (localStorage.getItem("darkMode") === "enabled") {
        document.body.classList.add("dark-mode");
        toggleIcon(true);
    }

    toggleBtn?.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("darkMode", "enabled");
            toggleIcon(true);
        } else {
            localStorage.setItem("darkMode", "disabled");
            toggleIcon(false);
        }

    });

    function toggleIcon(isDark) {
        const icon = toggleBtn.querySelector("i");

        if (isDark) {
            icon.classList.remove("bi-moon-fill");
            icon.classList.add("bi-sun-fill");
        } else {
            icon.classList.remove("bi-sun-fill");
            icon.classList.add("bi-moon-fill");
        }
    }

});