document.addEventListener("DOMContentLoaded", function() {
    let navbarToggler = document.querySelector(".navbar-toggler");
    let navbarCollapse = document.querySelector("#navbarNav");
    let navSeparator = document.querySelector("#navSeparator");

    navbarToggler.addEventListener("click", function() {
        if (navbarCollapse.classList.contains("show")) {
            navSeparator.style.opacity = "1";
        } else {
            navSeparator.style.opacity = "0";
        }
    });

    navbarCollapse.addEventListener("hidden.bs.collapse", function () {
        navSeparator.style.opacity = "1";
    });

    navbarCollapse.addEventListener("shown.bs.collapse", function () {
        navSeparator.style.opacity = "0";
    });
});

document.addEventListener("wheel", (event) => {
    if (event.deltaY > 0) { // If scrolling down
        let sections = document.querySelectorAll(".hero-content, .display-business, .demo-class1, .demo-class2");
        let currentScrollPosition = window.scrollY;
        
        for (let i = 0; i < sections.length; i++) {
            let rect = sections[i].getBoundingClientRect();
            let sectionTop = rect.top + window.scrollY;

            if (sectionTop > currentScrollPosition) {
                sections[i].scrollIntoView({ behavior: "smooth", block: "start" });
                break;
            }
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    let navbar = document.querySelector(".navbar");
    let logo = document.querySelector(".navbar-brand");
    let navLinks = document.querySelectorAll(".nav-link");
    let buttons = document.querySelectorAll(".button, .button-get-started");
    let navSeparator = document.querySelector(".navbar-separator");

    // For demo-class1
    let demoClass1 = document.querySelector(".demo-class1");
    let glassDiv = document.querySelector(".glass-div");

    // Apply default styles for navbar and elements on page load
    function setDefaultStyles() {
        logo.style.color = "#023047";
        navSeparator.style.backgroundColor = "#023047";
        navLinks.forEach((link) => {
            link.style.color = "#023047";
            link.addEventListener("mouseover", function () {
                this.style.color = "#ffffff"; // Hover color
            });
            link.addEventListener("mouseout", function () {
                this.style.color = "#023047"; // Back to default
            });
        });
        buttons.forEach((btn) => {
            btn.style.backgroundColor = "#023047";
            btn.style.color = "#ffffff";
        });

        // Set initial styles for glass-div
        glassDiv.style.background = "rgba(255, 255, 255, 0.1)"; // Default
        glassDiv.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08)";
    }

    setDefaultStyles(); // Apply styles on page load

    // Observe demo-class1 for color changes
    let observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // Change elements color when demo-class1 is in view
                    glassDiv.style.background = "rgba(255, 255, 255, 0.2)"; // Example color change
                    glassDiv.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.15)"; // Example shadow

                    // If you want to adjust navbar colors as well when demo-class1 is in view
                    logo.style.color = "#ffffff";
                    navSeparator.style.backgroundColor = "#ffffff";

                    navLinks.forEach((link) => {
                        link.style.color = "#ffffff";
                        link.addEventListener("mouseover", function () {
                            this.style.color = "#023047"; // Hover color
                        });
                        link.addEventListener("mouseout", function () {
                            this.style.color = "#ffffff"; // Back to white
                        });
                    });

                    buttons.forEach((btn) => {
                        btn.style.backgroundColor = "#ffffff";
                        btn.style.color = "#023047";
                    });
                } else {
                    // Reset styles when demo-class1 is out of view
                    setDefaultStyles();
                }
            });
        },
        { threshold: 0.5 } // Adjusts when the color change happens
    );

    observer.observe(demoClass1);

    // Now set up the IntersectionObserver for display-business
    let displayBusiness = document.querySelector(".display-business");
    let businessObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    logo.style.color = "#ffffff";
                    navSeparator.style.backgroundColor = "#ffffff";

                    navLinks.forEach((link) => {
                        link.style.color = "#ffffff";
                        link.addEventListener("mouseover", function () {
                            this.style.color = "#023047"; // Hover color
                        });
                        link.addEventListener("mouseout", function () {
                            this.style.color = "#ffffff"; // Back to white
                        });
                    });

                    buttons.forEach((btn) => {
                        btn.style.backgroundColor = "#ffffff";
                        btn.style.color = "#023047";
                    });
                } else {
                    setDefaultStyles();
                }
            });
        },
        { threshold: 0.5 }
    );

    businessObserver.observe(displayBusiness);
});

document.addEventListener("DOMContentLoaded", function () {
    const businesses = document.querySelectorAll(".business");

    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    observer.unobserve(entry.target); 
                }
            });
        },
        { threshold: 0.7 } 
    );

    businesses.forEach(business => {
        observer.observe(business);
    });
});

