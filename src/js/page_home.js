// Function to monitor elements and apply changes to target elements
document.addEventListener("DOMContentLoaded", function () {
    const homeTarget = document.getElementById('home_target');
    const projectContainer = document.getElementById('projects_container');
    const projectChildren = projectContainer.querySelectorAll('.project_card_wrapper');
    const contactTarget = document.getElementById('contact_target');
    const projectsElement = document.getElementById('projects');

    let lastScrollPosition = window.scrollY;

    function isProjectsTopInViewport() {
        const rect = projectsElement.getBoundingClientRect();
        return (rect.top <= 0);
    }

    function isInViewport(element, extraHeight = 0) {
        const rect = element.getBoundingClientRect();
        return (rect.top <= window.innerHeight && rect.bottom + extraHeight >= 0);
    }


    function handleVisibility() {
        const currentScrollPosition = window.scrollY;
        const scrollingDown = currentScrollPosition > lastScrollPosition;
        lastScrollPosition = currentScrollPosition;

        if (!isProjectsTopInViewport()) {
            homeTarget.classList.remove('hidden_soft');
            homeTarget.classList.add('fadein');
        } else {
            homeTarget.classList.add('hidden_soft');
            homeTarget.classList.remove('fadein');
        }

        // Inside your handleVisibility function
        projectChildren.forEach(child => {
            if (isInViewport(child, 600)) { // 200px extra virtual height
                child.classList.remove('hidden_soft');
                child.classList.add('movein');
            } else {
                child.classList.add('hidden_soft');
                child.classList.remove('movein');
            }
        });


        if (isInViewport(contactTarget, 200)) {
            contactTarget.classList.remove('hidden_soft');
            contactTarget.classList.add('movein');
        } else {
            contactTarget.classList.add('hidden_soft');
            contactTarget.classList.remove('movein');
        }
    }

    // Check on load
    handleVisibility();

    // Check on scroll
    window.addEventListener('scroll', handleVisibility);
});


// tilt project card
document.addEventListener("DOMContentLoaded", function () {
    let lastTime = 0; // To store the last time the mousemove event was handled
    const delay = 100; // Minimum delay between each mousemove event in milliseconds
    const body = document.querySelector("body"); // Cache the body element for reusability
    const cards = document.querySelectorAll("#projects .project_card"); // Select all the project cards

    function updateCard(e, card, wrapper) {
        const currentTime = Date.now();
        if (currentTime - lastTime < delay) {
            return; // Skip if the delay has not been met
        }
        lastTime = currentTime;

        const ratio = 3;
        const maxTilt = 0.4;
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const deltaX = x - centerX;
        const deltaY = y - centerY;

        let tiltX = (deltaY / centerY) * ratio;
        let tiltY = -(deltaX / centerX) * ratio;

        tiltX = Math.min(Math.max(tiltX, -maxTilt), maxTilt);
        tiltY = Math.min(Math.max(tiltY, -maxTilt), maxTilt);

        const currentTheme = body.getAttribute("data-bs-theme");
        if (currentTheme === 'dark') {
            if (wrapper) {
                wrapper.style.perspective = '1000px';
            }
            card.style.transform = `rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;

            const flare = card.querySelector('.card_flare');
            if (flare) {
                flare.style.opacity = '0.05';
                const flareRect = flare.getBoundingClientRect();
                const flareHalfWidth = flareRect.width / 2;
                const flareHalfHeight = flareRect.height / 2;
                flare.style.left = `${x - flareHalfWidth}px`;
                flare.style.top = `${y - flareHalfHeight}px`;
            }
        }
    }

    cards.forEach(function (card) {
        const wrapper = card.closest('.project_card_wrapper');

        card.addEventListener("mousemove", function (e) {
            requestAnimationFrame(() => updateCard(e, card, wrapper));
        });

        card.addEventListener("mouseout", function () {
            const currentTheme = body.getAttribute("data-bs-theme");
            card.style.transform = currentTheme === 'dark' ? 'rotateX(0deg) rotateY(0deg)' : '';

            if (wrapper) {
                wrapper.style.perspective = '';
            }

            const flare = card.querySelector('.card_flare');
            if (flare) {
                flare.style.opacity = '0';
            }
        });
    });
});



// interactive decoration
function applyInteractiveDecoration(e) {
    let lastTime = 0; // To store the last time the mousemove event was handled
    const delay = 100; // Minimum delay between each mousemove event in milliseconds
    const currentTime = Date.now();
    if (currentTime - lastTime < delay) {
        return; // Skip if the delay has not been met
    }
    lastTime = currentTime;

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

document.addEventListener('mousemove', function (e) {
    requestAnimationFrame(() => applyInteractiveDecoration(e));
});



// check card summary size with gradient blur fade out
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
submitFormOnEvent('contactForm', 'msg_submit_email', 'msg_submit_content', 'formOutput', 'src/handler_contact_form.php');


function canvasDraw() {
    let resizeReset = function () {
        w = canvasBody.width = window.innerWidth;
        h = canvasBody.height = window.innerHeight;
    }

    const opts = {
        particleColor: "#750023",
        lineColor: "rgb(40,40,40)",
        particleAmount: 30,
        defaultSpeed: 0.2,
        variantSpeed: 0.3,
        defaultRadius: 1,
        variantRadius: 1,
        linkRadius: 100,
    };

    window.addEventListener("resize", function () {
        deBouncer();
    });

    let deBouncer = function () {
        clearTimeout(tid);
        tid = setTimeout(function () {
            resizeReset();
        }, delay);
    };

    let checkDistance = function (x1, y1, x2, y2) {
        return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    };

    let linkPoints = function (point1, hubs) {
        for (let i = 0; i < hubs.length; i++) {
            let distance = checkDistance(point1.x, point1.y, hubs[i].x, hubs[i].y);
            let opacity = 1 - distance / opts.linkRadius;
            if (opacity > 0) {
                drawArea.lineWidth = 0.5;
                drawArea.strokeStyle = `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${opacity})`;
                drawArea.beginPath();
                drawArea.moveTo(point1.x, point1.y);
                drawArea.lineTo(hubs[i].x, hubs[i].y);
                drawArea.closePath();
                drawArea.stroke();
            }
        }
    }

    Particle = function (xPos, yPos) {
        this.x = Math.random() * w;
        this.y = Math.random() * h;
        this.speed = opts.defaultSpeed + Math.random() * opts.variantSpeed;
        this.directionAngle = Math.floor(Math.random() * 360);
        this.color = opts.particleColor;
        this.radius = opts.defaultRadius + Math.random() * opts.variantRadius;
        this.vector = {
            x: Math.cos(this.directionAngle) * this.speed,
            y: Math.sin(this.directionAngle) * this.speed
        };
        this.update = function () {
            this.border();
            this.x += this.vector.x;
            this.y += this.vector.y;
        };
        this.border = function () {
            if (this.x >= w || this.x <= 0) {
                this.vector.x *= -1;
            }
            if (this.y >= h || this.y <= 0) {
                this.vector.y *= -1;
            }
            if (this.x > w) this.x = w;
            if (this.y > h) this.y = h;
            if (this.x < 0) this.x = 0;
            if (this.y < 0) this.y = 0;
        };
        this.draw = function () {
            drawArea.beginPath();
            drawArea.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            drawArea.closePath();
            drawArea.fillStyle = this.color;
            drawArea.fill();
        };
    };

    function setup() {
        particles = [];
        resizeReset();
        for (let i = 0; i < opts.particleAmount; i++) {
            particles.push(new Particle());
        }
        window.requestAnimationFrame(loop);
    }

    function loop() {
        window.requestAnimationFrame(loop);
        drawArea.clearRect(0, 0, w, h);
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
        }
        for (let i = 0; i < particles.length; i++) {
            linkPoints(particles[i], particles);
        }
    }

    const canvasBody = document.getElementById("canvas-bg"),
        drawArea = canvasBody ? canvasBody.getContext("2d") : null;
    let delay = 200, tid,
        rgb = opts.lineColor.match(/\d+/g);
    resizeReset();
    setup();
}

const canvasTarget = document.getElementById("canvas-bg");
if (canvasTarget) {
    canvasDraw();
}


// Create a listener for the `.extra_rise_2` element to fully load
// document.getElementById("contact_spin_item").addEventListener("load", function () {
//     const targetObject = document.querySelectorAll("contact_delay_item");
//     const originalSrc = targetObject.getAttribute("src");
//     targetObject.setAttribute("style", "opacity: 0");

//     setTimeout(function () {
//         targetObject.removeAttribute("src");
//         targetObject.setAttribute("src", originalSrc);
//         targetObject.setAttribute("style", "opacity: 1");
//     }, 1300);
// });
