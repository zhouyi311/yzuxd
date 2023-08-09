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
            projectNavName.hidden = false;;
        });
    } else {
        pageNavbar.classList.remove('bg-light');
        listenElements.forEach((navElement) => {
            navElement.classList.add('navbar-dark');
        });
        projectNavName.forEach((projectNavName) => {
            projectNavName.hidden = true;;
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
        console.log('contact form set')
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
        console.log('contact form not set')
    }
}

submitFormOnEvent('contactForm', 'msg_submit_email', 'msg_submit_content', 'formOutput', 'src/php/handler_contact_form.php');


// lightbox

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
                openLightbox(img.src);
            });
        });

        lightboxImage.addEventListener('wheel', zoomImage);
        lightboxOverlay.addEventListener('touchmove', zoomImage);
        lightboxOverlay.addEventListener('touchend', () => state.initialPinchDistance = null);

        lightboxImage.addEventListener('mousedown', startDrag);
        window.addEventListener('mousemove', dragImage);
        window.addEventListener('mouseup', () => state.isDragging = false);

        lightboxOverlay.addEventListener('click', closeLightbox);
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxImage.addEventListener('click', event => event.stopPropagation());

        lightboxImage.addEventListener('dragstart', preventDefaultImageDrag);
    }

    function openLightbox(src) {
        // Check for a larger version of the image
        const baseSrc = src.replace(/(\.[a-z]+)$/, ''); // Remove the extension
        const largerImageSrc = `${baseSrc}_original$1`; // Add _original and the extension back

        // Create a new Image object to test if the larger image exists
        const testImage = new Image();
        testImage.onload = function () {
            // If the larger image loads successfully, set it as the source for the lightbox image
            lightboxImage.src = largerImageSrc;
        };
        testImage.onerror = function () {
            // If there's an error (e.g., the image doesn't exist), use the original source
            lightboxImage.src = src;
        };

        // Start loading the larger image
        testImage.src = largerImageSrc;

        lightboxOverlay.style.display = 'flex';
    }


    function closeLightbox() {
        lightboxOverlay.style.display = 'none';
        state.scale = 1;
        lightboxImage.style.setProperty('--img-scale', state.scale);
        lightboxImage.style.left = '0px';
        lightboxImage.style.top = '0px';
    }

    function zoomImage(event) {
        event.preventDefault();

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





// document.addEventListener('DOMContentLoaded', function () {
//     const images = document.querySelectorAll('.lightbox-enabled');
//     const lightboxOverlay = document.getElementById('lightboxOverlay');
//     const lightboxImage = document.getElementById('lightboxImage');
//     const lightboxClose = document.getElementById('lightboxClose');

//     images.forEach(img => {
//         img.addEventListener('click', function () {
//             lightboxImage.src = img.src;
//             lightboxOverlay.style.display = 'flex';
//         });
//     });

//     let scale = 1;
//     const scaleIncrement = 0.1;

//     lightboxImage.addEventListener('wheel', function (event) {
//         event.preventDefault();

//         if (event.deltaY < 0) {
//             // Zoom in
//             scale += scaleIncrement;
//         } else {
//             // Zoom out
//             scale -= scaleIncrement;
//         }

//         // Set a minimum and maximum scale level
//         scale = Math.min(Math.max(scale, 0.5), 3);

//         lightboxImage.style.setProperty('--img-scale', scale);
//     });

//     let initialPinchDistance = null;

//     lightboxOverlay.addEventListener('touchmove', function (event) {
//         if (event.touches.length === 2) {
//             const dx = event.touches[0].clientX - event.touches[1].clientX;
//             const dy = event.touches[0].clientY - event.touches[1].clientY;
//             const distance = Math.sqrt(dx * dx + dy * dy);

//             if (initialPinchDistance === null) {
//                 initialPinchDistance = distance;
//             }

//             const difference = distance - initialPinchDistance;
//             scale += difference * 0.01;

//             // Set a minimum and maximum scale level
//             scale = Math.min(Math.max(scale, 0.5), 3);

//             lightboxImage.style.setProperty('--img-scale', scale);
//             initialPinchDistance = distance;
//         }
//     });

//     lightboxOverlay.addEventListener('touchend', function () {
//         initialPinchDistance = null;
//     });

//     //more features

//     let isDragging = false;
//     let startX = 0;
//     let startY = 0;
//     let initialOffsetX = 0;
//     let initialOffsetY = 0;

//     lightboxImage.addEventListener('mousedown', function (event) {
//         isDragging = true;
//         startX = event.clientX;
//         startY = event.clientY;
//         initialOffsetX = parseFloat(lightboxImage.style.left || 0);
//         initialOffsetY = parseFloat(lightboxImage.style.top || 0);
//     });

//     window.addEventListener('mousemove', function (event) {
//         if (isDragging) {
//             const dx = event.clientX - startX;
//             const dy = event.clientY - startY;
//             lightboxImage.style.left = `${initialOffsetX + dx}px`;
//             lightboxImage.style.top = `${initialOffsetY + dy}px`;
//         }
//     });

//     window.addEventListener('mouseup', function () {
//         isDragging = false;
//     });

//     // Reset scale and position when closing the lightbox
//     function closeLightbox() {
//         lightboxOverlay.style.display = 'none';
//         scale = 1;
//         lightboxImage.style.setProperty('--img-scale', scale);
//         lightboxImage.style.left = '0px';
//         lightboxImage.style.top = '0px';
//     }

//     lightboxOverlay.addEventListener('click', closeLightbox);
//     lightboxClose.addEventListener('click', closeLightbox);
//     // lightboxImage.addEventListener('click', closeLightbox);

//     // Prevent the lightbox from closing when the image is clicked
//     lightboxImage.addEventListener('click', function (event) {
//         event.stopPropagation();
//     });

// });

// lightboxImage.addEventListener('dragstart', function (event) {
//     event.preventDefault();
// });


// function openLightbox(imageSrc) {
//     // Check for a larger version of the image
//     const largerImageSrc = imageSrc.replace('.jpg', '_original.jpg');

//     // Create a new Image object to test if the larger image exists
//     const testImage = new Image();
//     testImage.onload = function () {
//         // If the larger image loads successfully, set it as the source for the lightbox image
//         lightboxImage.src = largerImageSrc;
//     };
//     testImage.onerror = function () {
//         // If there's an error (e.g., the image doesn't exist), use the original source
//         lightboxImage.src = imageSrc;
//     };

//     // Start loading the larger image
//     testImage.src = largerImageSrc;

//     // Display the lightbox
//     lightboxOverlay.style.display = 'block';
// }
