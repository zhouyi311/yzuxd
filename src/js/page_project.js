// autoplay videos 
document.addEventListener('DOMContentLoaded', function () {

    const videosWithoutControls = document.querySelectorAll('video:not([controls].article_video)');
    videosWithoutControls.forEach(video => {
        video.addEventListener('click', function () {
            if (video.paused) {
                video.play();
                video.muted = false;
            } else {
                video.pause();
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
