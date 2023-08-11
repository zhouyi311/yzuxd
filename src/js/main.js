// Scrolling Nav BG change
function handleScrollForNavigation() {
    const pageNavbar = document.getElementById('page_navbar');
    const listenElements = document.querySelectorAll('.nav_listen_target');
    const projectNavName = document.querySelectorAll('.project_name');

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

// interactive decoration
function applyInteractiveDecoration(e) {
    const scale = 80; // movement scale, larger -> smaller movement
    const maxRotation = 15; // maximum rotation in degrees

    const moveable = document.getElementById('hero_moveable');
    const moveable2 = document.getElementById('contact_moveable');

    if (moveable && moveable2) {
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
}

document.addEventListener('mousemove', applyInteractiveDecoration);


// check summary size with blur fade out
function applyGradientForLargeHeight() {
    const cardInfoSummaries = document.querySelectorAll('.card_info_summary');
    const desiredHeight = 3 * 24 - 1; // Set the desired height

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


// Submit Message
function submitFormOnEvent(formID, emailInputID, messageInputID, outputElementID, handlerURLfromRoot) {
    const form = document.getElementById(formID);
    const emailInput = document.getElementById(emailInputID);
    const messageInput = document.getElementById(messageInputID);
    const outputElement = document.getElementById(outputElementID);

    if (form) {
        console.log('Send me email if you have any questions.')
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

    } else {
        console.log('Thank you for checking here :)')
    }
}

submitFormOnEvent('contactForm', 'msg_submit_email', 'msg_submit_content', 'formOutput', 'src/php/handler_contact_form.php');


// lightbox ////////////////////////////////////////////////////////////////////////////////////////

document.addEventListener('DOMContentLoaded', function () {
    const state = {
        isDragging: false,
        startX: 0,
        startY: 0,
        initialOffsetX: 0,
        initialOffsetY: 0,
        scale: 1,
        initialPinchDistance: null
    };

    const lightboxOverlay = document.getElementById('lightboxOverlay');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxClose = document.getElementById('lightboxClose');

    function initializeLightbox() {
        const images = document.querySelectorAll('.lightbox-enabled');
        images.forEach(img => {
            img.addEventListener('click', function () {
                openLightbox(img);
            });
        });

        lightboxImage.addEventListener('wheel', zoomImage, { passive: true });
        lightboxOverlay.addEventListener('touchmove', zoomImage, { passive: true });
        lightboxOverlay.addEventListener('touchend', () => state.initialPinchDistance = null);

        lightboxImage.addEventListener('mousedown', startDrag);
        window.addEventListener('mousemove', dragImage);
        window.addEventListener('mouseup', () => state.isDragging = false);

        lightboxOverlay.addEventListener('click', closeLightbox);
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxImage.addEventListener('click', event => event.stopPropagation());

        lightboxImage.addEventListener('dragstart', preventDefaultImageDrag);
    }

    function openLightbox(img) {
        document.body.style.overflow = 'hidden';
        lightboxOverlay.style.display = 'flex';
        void lightboxOverlay.offsetWidth;
        lightboxOverlay.style.opacity = '1';
        lightboxImage.src = 'src/img/local_icons/circle-loading.gif'; // Clear the current image
        lightboxImage.alt = 'Loading...'; // You can also add a spinner or loading graphic here

        lightboxOverlay.addEventListener('transitionend', function onEnd() {
            lightboxOverlay.removeEventListener('transitionend', onEnd);
            
        })

        const defaultSrc = img.src;
        const largerImageFilename = img.getAttribute('data-larger-src');

        // If there's no data-larger-src attribute, just use the default source
        if (!largerImageFilename) {
            lightboxImage.src = defaultSrc;
            console.log('default')
            return;
        }

        // Extract the path from defaultSrc and append the largerImageFilename
        const pathWithoutFilename = defaultSrc.substring(0, defaultSrc.lastIndexOf('/') + 1);
        const fullLargerImageSrc = pathWithoutFilename + largerImageFilename;

        // Try to fetch the larger image
        fetch(fullLargerImageSrc)
            .then(response => {
                if (response.ok) {
                    lightboxImage.src = fullLargerImageSrc;
                    lightboxImage.alt = ''; // Clear the loading text
                    console.log('found')
                } else {
                    lightboxImage.src = defaultSrc;
                    console.log('not found')
                }
            })
            .catch(error => {
                lightboxImage.src = defaultSrc;
                console.log('error throw')
            });
    }


    function closeLightbox() {
        lightboxOverlay.style.opacity = '0';
        lightboxOverlay.addEventListener('transitionend', function onEnd() {
            lightboxOverlay.style.display = 'none';
            lightboxOverlay.removeEventListener('transitionend', onEnd);
            state.scale = 1;
            lightboxImage.style.setProperty('--img-scale', state.scale);
            lightboxImage.style.left = '0px';
            lightboxImage.style.top = '0px';
            document.body.style.overflow = '';
        });
    }

    function zoomImage(event) {
        // event.preventDefault();

        if (event.type === 'wheel') {
            if (event.deltaY < 0) {
                // Zoom in
                state.scale += 0.1;
            } else {
                // Zoom out
                state.scale -= 0.1;
            }
        } else if (event.touches.length === 2) {
            const dx = event.touches[0].clientX - event.touches[1].clientX;
            const dy = event.touches[0].clientY - event.touches[1].clientY;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (state.initialPinchDistance === null) {
                state.initialPinchDistance = distance;
            }

            const difference = distance - state.initialPinchDistance;
            state.scale += difference * 0.01;
            state.initialPinchDistance = distance;
        }

        // Set a minimum and maximum scale level
        state.scale = Math.min(Math.max(state.scale, 0.5), 3);
        lightboxImage.style.setProperty('--img-scale', state.scale);
    }

    function startDrag(event) {
        state.isDragging = true;
        state.startX = event.clientX;
        state.startY = event.clientY;
        state.initialOffsetX = parseFloat(lightboxImage.style.left || 0);
        state.initialOffsetY = parseFloat(lightboxImage.style.top || 0);
    }

    function dragImage(event) {
        if (state.isDragging) {
            const dx = event.clientX - state.startX;
            const dy = event.clientY - state.startY;
            lightboxImage.style.left = `${state.initialOffsetX + dx}px`;
            lightboxImage.style.top = `${state.initialOffsetY + dy}px`;
        }
    }

    function preventDefaultImageDrag(event) {
        event.preventDefault();
    }

    initializeLightbox();
});
