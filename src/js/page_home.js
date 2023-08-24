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


// Create a listener for the `.extra_rise_2` element to fully load
document.getElementById("contact_spin_item").addEventListener("load", function () {
    const targetObject = document.getElementById("contact_delay_item");
    const originalSrc = targetObject.getAttribute("src");
    targetObject.setAttribute("style", "opacity: 0");

    setTimeout(function () {
        targetObject.removeAttribute("src");
        targetObject.setAttribute("src", originalSrc);
        targetObject.setAttribute("style", "opacity: 1");
    }, 900);
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

submitFormOnEvent('contactForm', 'msg_submit_email', 'msg_submit_content', 'formOutput', 'src/php/handler_contact_form.php');


function canvasDraw() {
    let resizeReset = function () {
        w = canvasBody.width = window.innerWidth;
        h = canvasBody.height = window.innerHeight;
    }

    const opts = {
        particleColor: "#B97B7B",
        lineColor: "rgb(100,0,14)",
        particleAmount: 25,
        defaultSpeed: 0.4,
        variantSpeed: 0.4,
        defaultRadius: 1,
        variantRadius: 2,
        linkRadius: 200,
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



