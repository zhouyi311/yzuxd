// on demand iframe

window.onload = () => {
    const wrappers = document.querySelectorAll('.iframe_wrapper.on_demand');

    wrappers.forEach(wrapper => {
        const button = wrapper.querySelector('.load_iframe');
        const iframe = wrapper.querySelector('iframe');

        if (button) {
            button.addEventListener('click', () => {
                wrapper.style.minHeight = wrapper.dataset.targetHeight;
                wrapper.classList.remove('on_demand');
                iframe.src = wrapper.dataset.iframeSrc;
                button.classList.add('disabled');
                button.style.zIndex = -1;
                button.innerHTML = "<span class='spinner-border spinner-border-sm me-2' aria-hidden='true'></span><span role='status'>Loading...</span>";
                iframe.addEventListener('load', () => {
                    button.classList.add('hidden');
                });
            });
        }
    });
};


// parallax

window.addEventListener('scroll', function () {
    var windowHeight = window.innerHeight;
    document.querySelectorAll('.bg_attach').forEach(function (wrapper) {
        var element = wrapper.querySelector('.parallax');
        if (!element) return;
        var elementTop = element.getBoundingClientRect().top;
        var elementHeight = element.offsetHeight;
        var centerPosition = windowHeight / 2 - elementHeight / 2;
        var positionDiff = elementTop - centerPosition;

        // Range from -50% to 50% when the element goes from the top to the bottom of the viewport
        var backgroundPositionY = (- positionDiff / (windowHeight + elementHeight) * 100) + '%';

        element.style.backgroundPosition = 'center ' + backgroundPositionY;
    });

    document.querySelectorAll('.image_wrapper').forEach(function (wrapper) {
        var image = wrapper.querySelector('.parallax');

        if (!image) return; // If no image inside the wrapper, skip to the next iteration.
        var wrapperTop = wrapper.getBoundingClientRect().top;
        // var wrapperHeight = wrapper.offsetHeight;
        // var imageHeight = image.offsetHeight;

        var moveDistance = wrapperTop * -0.3;
        // image.offsetHeight / 10;

        image.style.transform = 'translateY(' + moveDistance + 'px)';

        // console.log("wrapperTop:", wrapperTop);
        // console.log("wrapperHeight:", wrapperHeight);
        // console.log("imageHeight:", imageHeight);
        // console.log("moveDistance:", moveDistance);
    });
});



// autoplay videos 
document.addEventListener('DOMContentLoaded', function () {

    const videosWithoutControls = document.querySelectorAll('video:not([controls].article_video)');
    videosWithoutControls.forEach(video => {
        video.addEventListener('click', function () {
            if (video.paused) {
                video.play();
                video.muted = false;
                video.style.opacity = 1;
            } else {
                video.pause();
                video.style.opacity = 0.8;
            }
        });
    });

    let videosToAutoplay = document.querySelectorAll('[data-autoplay-on-scroll].article_video');
    const playVideosOnScroll = function () {
        videosToAutoplay.forEach(video => {
            video.play();
        });

        window.removeEventListener('scroll', playVideosOnScroll);
    }
    window.addEventListener('scroll', playVideosOnScroll);

});

// light box
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
    const lightboxReset = document.getElementById('lightboxReset');
    const zoomInButton = document.getElementById('lightboxZoomIn');
    const zoomOutButton = document.getElementById('lightboxZoomOut');

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
        window.addEventListener('mouseup', function () {
            if (state.isDragging) {
                lightboxImage.style.transitionProperty = 'left, top, transform';
            }
            state.isDragging = false
        });

        zoomInButton.addEventListener('click', function (event) {
            event.stopPropagation(); // Prevent the click event from reaching any parent elements
            zoomByAmount(0.3); // Zoom in by 10%
        });
        zoomOutButton.addEventListener('click', function (event) {
            event.stopPropagation(); // Prevent the click event from reaching any parent elements
            zoomByAmount(-0.3); // Zoom out by 10%
        });

        // lightboxOverlay.addEventListener('click', closeLightbox);
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxReset.addEventListener('click', resetLightbox);
        lightboxImage.addEventListener('click', event => event.stopPropagation());

        lightboxImage.addEventListener('dragstart', preventDefaultImageDrag);
    }

    function openLightbox(img) {
        document.body.style.overflow = 'hidden';
        lightboxOverlay.style.display = 'flex';
        void lightboxOverlay.offsetWidth;
        lightboxOverlay.style.opacity = '1';
        lightboxImage.src = 'src/img/local_icons/circle-loading.gif'; // Clear the current image

        lightboxOverlay.addEventListener('transitionend', function onEnd() {
            lightboxOverlay.removeEventListener('transitionend', onEnd);
        })

        const defaultSrc = img.src;
        const largerImageFilename = img.getAttribute('data-larger-src');

        // If the data-larger-src attribute is "original" or doesn't exist, just use the default source
        if (!largerImageFilename || largerImageFilename === "original") {
            lightboxImage.src = defaultSrc;
            // console.log('default')
            return;
        }

        // Extract the path from defaultSrc and append the largerImageFilename
        const pathWithoutFilename = defaultSrc.substring(0, defaultSrc.lastIndexOf('/') + 1);
        console.log(pathWithoutFilename);
        const fullLargerImageSrc = pathWithoutFilename + largerImageFilename;

        // Try to fetch the larger image
        fetch(fullLargerImageSrc)
            .then(response => {
                if (response.ok) {
                    lightboxImage.src = fullLargerImageSrc;
                    lightboxImage.alt = ''; // Clear the loading text
                    // console.log('found')
                } else {
                    lightboxImage.src = defaultSrc;
                    // console.log('not found')
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
    function resetLightbox() {
        state.scale = 1;
        lightboxImage.style.setProperty('--img-scale', state.scale);
        lightboxImage.style.left = '0px';
        lightboxImage.style.top = '0px';
    }


    function zoomByAmount(amount) {
        state.scale += amount;
        // Set a minimum and maximum scale level
        state.scale = Math.min(Math.max(state.scale, 0.5), 3);
        lightboxImage.style.setProperty('--img-scale', state.scale);
    }

    function zoomImage(event) {

        // event.preventDefault();

        // // Get mouse position relative to the image
        // const offsetX = event.clientX / 2;
        // const offsetY = event.clientY / 2;

        // // Set transform origin to the mouse position
        // lightboxImage.style.transformOrigin = `${offsetX}px ${offsetY}px`;

        if (event.type === 'wheel') {

            zoomByAmount(event.deltaY < 0 ? 0.1 : -0.1);

        } else if (event.touches.length === 2) {
            const dx = event.touches[0].clientX - event.touches[1].clientX;
            const dy = event.touches[0].clientY - event.touches[1].clientY;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (state.initialPinchDistance === null) {
                state.initialPinchDistance = distance;
            }

            const difference = distance - state.initialPinchDistance;
            zoomByAmount(difference * 0.01);
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
        lightboxImage.style.transitionProperty = 'transform';
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


// // image handler

// window.addEventListener('DOMContentLoaded', () => {
//     const images = document.querySelectorAll('img.compact_size');

//     images.forEach((image) => {
//         const naturalWidth = image.naturalWidth;
//         const naturalHeight = image.naturalHeight;

//         image.style.width = (naturalWidth / 2) + 'px';
//         image.style.height = (naturalHeight / 2) + 'px';
//     });
// });
