document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const navbar = document.getElementById('page_navbar');

        const href = this.getAttribute('href');
        const targetElement = document.querySelector(href);

        if (targetElement) {
            const offset = navbar.offsetHeight; // Set your desired offset in pixels
            const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY - offset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});



function handleScrollForNavigation() {
    const pageNavbar = document.getElementById('page_navbar');
    const homeNavList = document.querySelectorAll('.home_page_navbar');

    // Check if pageNavbar exists
    if (!pageNavbar) {
        console.warn('page_navbar element not found!');
        return;
    }

    const navSubheads = pageNavbar.querySelectorAll('.project_name');

    if (window.scrollY > 200) {
        pageNavbar.classList.add('bg-light', 'shadow-sm');

        homeNavList.forEach(homeNav => {
            homeNav.removeAttribute('data-bs-theme');
        });

        navSubheads.forEach((subhead) => {
            subhead.classList.remove('slideout');
        });

        // console.log('offset - no target');
    } else {
        pageNavbar.classList.remove('bg-light', 'shadow-sm');

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

// main margin setup
function offsetHeight() {
    const sourceElement = document.getElementById('page_navbar');
    const targetElement = document.getElementById('page_home');
    const sourceHeight = sourceElement.offsetHeight;

    // Set the top margin
    if (targetElement) {
        targetElement.style.paddingTop = `${sourceHeight + 16}px`; // Use backticks (`) here
        // console.log('offsetHeight - Element is set.');
    } else {
        return;
    }

}
document.addEventListener('DOMContentLoaded', offsetHeight);


let scrollTimeout; 

function handleDrawer() {
    const offcanvasBody = document.getElementById('offcanvasNavbar');

    console.log(offcanvasBody);

    offcanvasBody.style.transition = '0.6s'; 
    clearTimeout(scrollTimeout);

    scrollTimeout = setTimeout(() => {
        offcanvasBody.style.transition = '';  
    }, 100); 
}

document.addEventListener('scroll', handleDrawer);





