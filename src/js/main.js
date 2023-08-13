// Scrolling Nav BG change
function handleScrollForNavigation() {
    const pageNavbar = document.getElementById('page_navbar');
    const projectNavName = document.querySelectorAll('.project_name');
    const listenElements = document.querySelectorAll('.nav_listen_target');

    if (window.scrollY > 200) {
        pageNavbar.classList.add('bg-light');
        listenElements.forEach((navElement) => {
            navElement.classList.remove('navbar-dark');
        });
        projectNavName.forEach((projectNavName) => {
            projectNavName.classList.remove('slideout');
        });
    } else {
        pageNavbar.classList.remove('bg-light');
        listenElements.forEach((navElement) => {
            navElement.classList.add('navbar-dark');
        });
        projectNavName.forEach((projectNavName) => {
            projectNavName.classList.add('slideout');
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
        // console.log('offset - no target');
        return;
    }
}
document.addEventListener('DOMContentLoaded', offsetHeight);







