// Scrolling Nav BG change
function handleScrollForNavigation() {
    const homeNavbar = document.getElementById('page_navbar');
    const navElements = document.querySelectorAll('.nav_listen_target');

    if (window.scrollY > 200) {
        homeNavbar.classList.add('bg-light');
        navElements.forEach((navElement) => {
            navElement.classList.remove('navbar-dark');
        });

    } else {
        homeNavbar.classList.remove('bg-light');
        navElements.forEach((navElement) => {
            navElement.classList.add('navbar-dark');
        });
    }
}
document.addEventListener('DOMContentLoaded', handleScrollForNavigation);
window.addEventListener('scroll', handleScrollForNavigation);

// main margin setup
function offsetHeight() {
    const sourceElement = document.getElementById('page_navbar');
    const targetElement = document.getElementById('project_main');
    const sourceHeight = sourceElement.offsetHeight;
    // Set the top margin
    if (targetElement) {
        console.log('Element is set.');
        targetElement.style.marginTop = '${sourceHeight}px';
    }
}
document.addEventListener('DOMContentLoaded', offsetHeight);


// interactive decoration
function applyInteractiveDecoration(e) {
    const scale = 80; // movement scale, larger -> smaller movement
    const maxRotation = 15; // maximum rotation in degrees

    const moveable = document.getElementById('hero_moveable');
    const moveable2 = document.getElementById('contact_moveable');

    if (window.innerWidth > 992) {
        let xAxis = (window.innerWidth / 2 - e.clientX) / scale;
        let yAxis = (window.innerHeight / 2 - e.clientY) / scale;

        xAxis = Math.min(Math.max(xAxis, -maxRotation), maxRotation);
        yAxis = Math.min(Math.max(yAxis, -maxRotation), maxRotation);

        moveable.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
        moveable2.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
    } else {
        moveable.style.transform = `rotateY(0deg) rotateX(0deg)`;
        moveable2.style.transform = `rotateY(0deg) rotateX(0deg)`;
    }
}
document.addEventListener('mousemove', applyInteractiveDecoration);


// check summary size with blur fade out
function applyGradientForLargeHeight() {
    const cardInfoSummaries = document.querySelectorAll('.card_info_summary');
    const desiredHeight = 3*24-1; // Set the desired height

    cardInfoSummaries.forEach((cardInfoSummary) => {
        const actualHeight = cardInfoSummary.scrollHeight;

        if (actualHeight > desiredHeight) {
            cardInfoSummary.classList.add('large-height');
        } else {
            cardInfoSummary.classList.remove('large-height');
        }
    });
}
document.addEventListener('DOMContentLoaded', applyGradientForLargeHeight);
window.addEventListener('resize', applyGradientForLargeHeight);


// site load run
// document.addEventListener('DOMContentLoaded', function () {
//     handleScrollForNavigation();
//     applyGradientForLargeHeight();
// });



// Submit Message
function submitFormOnEvent(formID, emailInputID, messageInputID, outputElementID, handlerURLfromRoot) {
    const form = document.getElementById(formID);
    const emailInput = document.getElementById(emailInputID);
    const messageInput = document.getElementById(messageInputID);
    const outputElement = document.getElementById(outputElementID);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const fromEmail = emailInput.value;
        const message = messageInput.value;

        const formData = new FormData();
        formData.append('fromEmail', fromEmail);
        formData.append('message', message);

        fetch(handlerURLfromRoot, {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => outputElement.innerHTML = data)
            .catch((error) => console.error('Error:', error));
    });
}

submitFormOnEvent('contactForm', 'msg_submit_email', 'msg_submit_content', 'formOutput', 'src/php/handler_contact_form.php');
