// main margin top offest
function offsetHeight() {
    const sourceElement = document.getElementById('page_navbar');
    const targetElement = document.getElementById('home');
    const sourceHeight = sourceElement.offsetHeight;

    // Set the top margin
    if (targetElement) {
        targetElement.style.paddingTop = `${sourceHeight + 8}px`; // Use backticks (`) here
        // console.log('offsetHeight - Element is set.');
    } else {

        return;
    }
}
document.addEventListener('DOMContentLoaded', offsetHeight);

// Markdown library -> credit: https://marked.js.org/
window.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.markdown');

    elements.forEach((element) => {
        const markdownText = element.innerHTML;
        element.innerHTML = marked.parse(markdownText); // Use marked.parse here
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const lightTheme = "light";
    const darkTheme = "dark";

    const body = document.querySelector("body");
    const lightSwitch = document.getElementById("light_switch");

    if (lightSwitch) {
        lightSwitch.addEventListener("click", () => {
            const icon = lightSwitch.querySelector(".bi");
            const currentTheme = body.getAttribute("data-bs-theme");

            if (currentTheme === lightTheme) {
                body.setAttribute("data-bs-theme", darkTheme);
                icon.classList.add("bi-moon-stars-fill");
                icon.classList.remove("bi-sun-fill");
            } else {
                body.setAttribute("data-bs-theme", lightTheme);
                icon.classList.add("bi-sun-fill");
                icon.classList.remove("bi-moon-stars-fill");
            }
            handleScrollForNavigation();
        });
    }
});

//nav bar background and shadow change
function handleScrollForNavigation() {
    const pageNavbar = document.getElementById('page_navbar');
    const homeNavList = document.querySelectorAll('.home_page_navbar');
    // const pageFooter = document.getElementById('page_footer');
    const homeBody = document.getElementById('home_page_root');
    var isDarkTheme;
    if (homeBody) {
        isDarkTheme = homeBody.hasAttribute("data-bs-theme") && homeBody.getAttribute("data-bs-theme") === "dark";
    }

    // console.log(homeNavList)
    const navSubheads = pageNavbar.querySelectorAll('.project_name');
    const lightSwitch = document.getElementById("light_switch");

    // Check if pageNavbar exists
    if (!pageNavbar) {
        console.warn('page_navbar element not found!');
        return;
    }

    if (window.scrollY > 200) {
        pageNavbar.classList.add('shadow-sm');
        pageNavbar.classList.add('nav_bg');
        if (lightSwitch) {
            lightSwitch.classList.remove('hidden');
        }
        // console.log(isDarkTheme);
        if (isDarkTheme) {
            homeNavList.forEach(homeNav => {
                homeNav.setAttribute('data-bs-theme', 'dark');
                // console.log(homeNav)
            });
        } else {
            homeNavList.forEach(homeNav => {
                homeNav.setAttribute('data-bs-theme', 'light');
                // console.log(homeNav)
            });
        }
        navSubheads.forEach((subhead) => {
            subhead.classList.remove('slideout');
        });

        // console.log('offset - no target');
    } else {
        pageNavbar.classList.remove('shadow-sm');
        pageNavbar.classList.remove('nav_bg');
        if (lightSwitch) {
            lightSwitch.classList.add('hidden');
        }
        homeNavList.forEach(homeNav => {
            homeNav.setAttribute('data-bs-theme', 'dark');
        });

        navSubheads.forEach((subhead) => {
            subhead.classList.add('slideout');
        });
    }
}

document.addEventListener('DOMContentLoaded', handleScrollForNavigation);
window.addEventListener('scroll', handleScrollForNavigation);

// offcanvas transition
let scrollTimeout;
function handleDrawer() {
    const offcanvasBody = document.getElementById('offcanvasNavbar');

    // console.log(offcanvasBody);

    offcanvasBody.style.transition = '0.6s';
    clearTimeout(scrollTimeout);

    scrollTimeout = setTimeout(() => {
        offcanvasBody.style.transition = '';
    }, 100);
}
document.addEventListener('scroll', handleDrawer);

//scrolling behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const navbar = document.getElementById('page_navbar');
        const navOffset = navbar.offsetHeight;

        const href = this.getAttribute('href');
        const targetElement = document.querySelector(href);

        if (targetElement) {
            const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY - navOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});


