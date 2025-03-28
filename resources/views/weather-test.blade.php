<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
	<h1>Animated Weather Wann WohinCard</h1>
</header>
<div class="container mt-5">
	<div class="card">
		<div class="card-body">
			<div class="backgroundNight"></div>
			<div class="background"></div>
			<div class="temperature cardInfo"><span id="temperature">21</span>°C</div>
			<div class="weatherType cardInfo"><span id="weatherType">Sunny</span></div>
			<div class="currentDay cardInfo"><span id="currentDay">Today</span></div>
			<div id="thunderstorm">
				<div id="lightning"></div>
			</div>
			<div class="sun"></div>
			<div class="moon"></div>
			<div id="cloud"> </div>
			<canvas id="rain"></canvas>
			<div id="snow"> </div>
			<div class="hours-container">
				<div class="hours">
				</div>
			</div>
		</div>
	</div>
</div>
<span id="sunrise" data-hour="{{ \Carbon\Carbon::parse($weather_data['sunrise'])->format('H') }}"></span>
<span id="sunset" data-hour="{{ \Carbon\Carbon::parse($weather_data['sunset'])->format('H') }}"></span>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,300,0,0" rel="stylesheet"/>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>



    <style>
        @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap");

body {
	background: #f5f5f5;
	font-family: "Plus Jakarta Sans", sans-serif;
	font-weight: 300;
}

header {
	padding: 16px;
	text-align: center;
}

h1,
h2,
h3,
h4,
h5 {
	font-weight: 700;
}

.card {
	position: relative;
	overflow: hidden; /* Ensure vertical scrolling */
	height: 566px;
	width: 350px;
	margin: auto;
	border: none;
	border-radius: 32px;
}

.sun,
.moon {
	position: absolute;

	left: 50%;
	width: 80px;
	height: 80px;
	border-radius: 50%;
	transform-origin: 0px 220px;
	transition: all 1s;
	transform: rotate(-90deg); /* Initial rotation of -90 degrees */
}

.sun {
	bottom: 300px;
	background: #fceabb;
	box-shadow: 0px 0px 40px 15px #fceabb;
	opacity: 1;
}

.moon {
	bottom: 250px;
	background: url("https://drive.google.com/thumbnail?id=1nbYvuzW1fU3iiXoxKeMSb4TvP7rkryuy&sz=w1000");
	background-size: contain;
	box-shadow: 0px 0px 20px 5px #ffffff;
	opacity: 0;
}

@keyframes rise-set {
	0% {
		transform: rotate(-90deg);
	}
	100% {
		transform: rotate(90deg);
	}
}

.hours-container {
	position: absolute;
	bottom: 0;
	left: 0;
	width: 100%;
	overflow-x: scroll;
	white-space: nowrap;
	backdrop-filter: blur(20px);
}

.hours {
	display: flex;
	padding: 8px;
}

.hours-container {
	scroll-behavior: smooth;
}

.hour {
	padding: 5px 10px;
	cursor: pointer;
	margin: 2px;
	transition: background-color 0.3s ease;
	border-radius: 16px;
	height: 107px;
	min-width: 80px;
	text-align: center;
}

.hour:hover {
	background-color: rgba(255, 255, 255, 0.3);
}

.active {
	background-color: rgba(255, 255, 255, 0.7);
}

.last {
	min-width: 150px;
}

.background {
	position: absolute;
	width: 100%;
	height: 100%;
	background-image: linear-gradient(0deg, #fefefe 0%, #00a4e4 74%);
	z-index: 0;
	transition: all 2s linear;
}

.backgroundNight {
	position: absolute;
	width: 100%;
	height: 100%;
	background-image: linear-gradient(0deg, #4c5177 0%, #051428 74%);
	z-index: -1;
	transition: all 2s linear;
}

.card,
.card-body {
	background: transparent !important;
	padding: 0;
}

.rain {
	width: 100%;
	height: 100%;
	opacity: 0;
}

.drop {
	background: -webkit-gradient(
		linear,
		0% 0%,
		0% 100%,
		to(rgba(255, 255, 255, 0.6)),
		from(rgba(255, 255, 255, 0))
	);
	background: -moz-linear-gradient(
		top,
		rgba(255, 255, 255, 0.6) 0%,
		rgba(255, 255, 255, 0) 100%
	);
	width: 1px;
	height: 30px;
	position: absolute;
	bottom: 0px;
	-webkit-animation: fall 0.3s linear infinite;
	-moz-animation: fall 0.3s linear infinite;
}

/* animate the drops*/
@-webkit-keyframes fall {
	to {
		margin-top: 500px;
	}
}
@-moz-keyframes fall {
	to {
		margin-top: 500px;
	}
}

.temperature {
	position: absolute;
	z-index: 2;
	right: 24px;
	top: 24px;
	text-align: right;
	font-size: 40px;
	font-weight: 600;
}

.weatherType {
	position: absolute;
	z-index: 2;
	right: 24px;
	top: 72px;
	text-align: right;
	font-size: 16px;
	line-height: 34px;
	text-transform: capitalize;
}

.currentDay {
	position: absolute;
	z-index: 2;
	left: 8px;
	bottom: 144px;
	text-align: left;
	font-size: 16px;
	line-height: 34px;
}

.hour span {
	position: relative;
	display: flex;
	text-align: center;
	color: black;
}

.hour .timeSpan {
	font-size: 14px;
	font-weight: 300;
}
.hour .tempSpan {
	font-size: 14px;
	font-weight: 700;
}

.hour:last-child::after {
	width: 300px;
	height: 1px;
	content: "";
}

.hour .material-symbols-rounded {
	font-size: 32px;
	line-height: 40px;
}

.hour[data-weather="sunny"] .material-symbols-rounded::after {
	content: "sunny";
}

.hour[data-weather="clear-night"] .material-symbols-rounded::after {
	content: "clear_night";
}

.hour[data-weather="partly-cloudy"] .material-symbols-rounded::after {
	content: "partly_cloudy_day";
}

.hour[data-weather="partly-cloudy-night"] .material-symbols-rounded::after {
	content: "partly_cloudy_night";
}

.hour[data-weather="cloudy"] .material-symbols-rounded::after {
	content: "cloud";
}

.hour[data-weather="foggy"] .material-symbols-rounded::after {
	content: "foggy";
}

.hour[data-weather="rainy"] .material-symbols-rounded::after {
	content: "rainy";
}

.hour[data-weather="snowy"] .material-symbols-rounded::after {
	content: "ac_unit";
}

.hour[data-weather="thunderstorm"] .material-symbols-rounded::after {
	content: "thunderstorm";
}

.card {
	color: black;
	box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px,
		rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
	border: none;
}

.credit {
	box-shadow: none;
	border-radius: 0;
}

a {
	color: black;
}

#cloud {
	position: absolute;
	z-index: 0;
	width: 100%;
	height: 100%;
	background-image: url("");
	background-repeat: no-repeat;
	background-size: cover;
	background-position: 50% 50%;
	filter: brightness(200%) drop-shadow(0 0 10px rgba(255, 255, 255, 01));
	top: 0;
	transition: all 2s;
}

#snow {
	opacity: 0;
	top: 0;
	position: absolute;
	pointer-events: none;
	z-index: 0;
	width: 100%;
	height: 100%;
	transition: all 2s;
}

#rain {
	width: 100%;
	height: 100%;
	position: absolute;
	top: 0;
	transition: all 2s;
}

#lightning {
	position: absolute;
	top: -200px;
	left: 0;
	width: 100%;
	height: 150%;
	background: radial-gradient(
		closest-side,
		rgba(255, 255, 255, 1),
		rgba(255, 255, 255, 0.5)
	);
	opacity: 0;
	pointer-events: none;
	animation: lightningFlash var(--lightning-duration) linear infinite;
}

@keyframes lightningFlash {
	0%,
	100% {
		opacity: 0;
	}
	24% {
		opacity: 0;
	}
	25% {
		opacity: 1;
	}
	26% {
		opacity: 0;
	}
	28% {
		opacity: 1;
	}
	29% {
		opacity: 0;
	}
}

    </style>
<script>
    $(document).ready(function () {
        const hoursData = @json($hourly_weather);

	// Iterate over the data and create the HTML dynamically
	hoursData.forEach((data) => {
		const hourDiv = $("<div>", {
			class: "hour d-flex flex-column align-items-center",
			"data-day": data.day,
			"data-hour": data.hour,
			"data-weather": data.weather,
			"data-temp": data.temp
		});

		const timeSpan = $("<span class='timeSpan'>").text(data.time);
		const iconSpan = $("<span>", { class: "material-symbols-rounded" });
		const tempSpan = $("<span class='tempSpan'>").text(data.temp + "°C");

		hourDiv.append(timeSpan, iconSpan, tempSpan);
		$(".hours").append(hourDiv);
	});
	const background = $(".background");
	const backgroundNight = $(".backgroundNight");
	const sun = $(".sun");
	const moon = $(".moon");
	const hoursContainer = $(".hours-container");
	const hours = $(".hour");
	const rain = $("#rain");
	const cloud = $("#cloud");
	const snow = $("#snow");
	const thunderstorm = $("#thunderstorm");
	const temperatureDisplay = $("#temperature");
	const weatherTypeDisplay = $("#weatherType");
	const currentDay = $("#currentDay");
    const initialHour = 22;

    function toggleSunMoon(hour) {
    const sunrise = parseInt($("#sunrise").data("hour")); // Sonnenaufgangsstunde aus dem DOM holen
    const sunset = parseInt($("#sunset").data("hour"));   // Sonnenuntergangsstunde aus dem DOM holen

    if (hour >= sunrise && hour < sunset) {
        const rotation = -90 + ((hour - sunrise) * (180 / (sunset - sunrise)));
        sun.css("transform", "rotate(" + rotation + "deg)");
        sun.css("opacity", "1");
        moon.css("opacity", "0");
        background.css("opacity", "1");
        backgroundNight.css("opacity", "0");
        $(".hour").css("filter", "invert(0%)");
        $(".cardInfo").css("filter", "invert(0%)");
        moon.css("transition", "all 0s");

        setTimeout(function () {
            sun.css("transition", "all 1s");
        }, 10);

        cloud.css("filter", "brightness(200%) drop-shadow(0 0 10px rgba(255, 255, 255, 1))");
        cloud.css("mix-blend-mode", "normal");
        rain.css("mix-blend-mode", "normal");
    } else {
        const adjustedHour = hour < sunrise ? hour + 24 : hour;
        const rotation = -90 + ((adjustedHour - sunset) * (180 / ((sunrise + 24) - sunset)));

        moon.css("opacity", "1");
        sun.css("opacity", "0");
        moon.css("transform", "rotate(" + rotation + "deg)");
        background.css("opacity", "0");
        backgroundNight.css("opacity", "1");
        $(".hour").css("filter", "invert(100%)");
        $(".cardInfo").css("filter", "invert(100%)");
        sun.css("transition", "all 0s");

        setTimeout(function () {
            moon.css("transition", "all 1s");
        }, 10);

        cloud.css("filter", "brightness(0%) drop-shadow(0 0 10px rgba(255, 255, 255, 1))");
        cloud.css("mix-blend-mode", "multiply");
        rain.css("mix-blend-mode", "soft-light");
    }
}


	// Function to handle scroll and wheel events
	function handleScrollEvent() {
		const sl = hoursContainer.scrollLeft();
		const hourIndex = Math.round(sl / hours.outerWidth());
		const currentHour = hours.eq(hourIndex);

		toggleSunMoon(parseInt(currentHour.data("hour")));
		highlightHour(hourIndex);
		updateWeatherAndTemperature(currentHour);
	}

	// Function to highlight the selected hour
	function highlightHour(index) {
		hours.removeClass("active"); // Remove active class from all hours
		hours.eq(index).addClass("active"); // Add active class to the selected hour
	}

	function updateWeatherAndTemperature(currentHour) {
		const temperature = currentHour.data("temp");
		const weather = currentHour.data("weather");
		const day = currentHour.data("day");

		temperatureDisplay.text(temperature);
		weatherTypeDisplay.text(weather.replace(/-/g, " "));

		// Reset elements to default state
		rain.css("opacity", "0");
		snow.css("opacity", "0");
		cloud.css("opacity", "0");
		thunderstorm.css("opacity", "0");
		background.css("filter", "none");
		sun.css("filter", "none");
		moon.css("filter", "none");

		// Handle weather visibility and background filters
		if (weather === "rainy") {
			rain.css("opacity", "1");
			cloud.css("opacity", "0.8");
			background.css("filter", "grayscale(0.5) brightness(0.5)");
			moon.css("filter", "brightness(0.8)");
		} else if (weather === "snowy") {
			snow.css("opacity", "1");
			cloud.css("opacity", "0");
			background.css("filter", "grayscale(0.5) opacity(0.4)");
			sun.css("filter", "grayscale(0.9)");
		} else if (weather === "cloudy") {
			cloud.css("opacity", "0.9");
			background.css("filter", "grayscale(0.5) brightness(0.5)");
			moon.css("filter", "brightness(0.8)");
		} else if (weather === "thunderstorm") {
			cloud.css("opacity", "0.8");
			thunderstorm.css("opacity", "1");
			background.css("filter", "grayscale(1) brightness(0.1)");
			sun.css("filter", "grayscale(0.9)");
		} else if (weather === "partly-cloudy" || weather === "partly-cloudy-night") {
			cloud.css("opacity", "0.5");
		}

		// Handle day text update
		if (day === "tom") {
			currentDay.text("Tomorrow");
		} else {
			currentDay.text("Today");
		}
	}

	// Initial setup for the first hour
	function init() {
		toggleSunMoon(initialHour); // Toggle sun/moon for initial position (07:00)
		highlightHour(initialHour); // Highlight the first hour initially
		updateWeatherAndTemperature(hours.eq(2));
	}

	// Function to generate drops
	function createRain() {
		const nbDrop = 800;
		for (let i = 1; i <= nbDrop; i++) {
			const dropLeft = randRange(0, 1600);
			const dropTop = randRange(-1000, 1400);

			rain.append('<div class="drop" id="drop' + i + '"></div>');
			$("#drop" + i).css({ left: dropLeft, top: dropTop });
		}
	}

	// Function to generate a random number range
	function randRange(minNum, maxNum) {
		return Math.floor(Math.random() * (maxNum - minNum + 1)) + minNum;
	}

	// Event listeners
	hoursContainer.on("scroll", handleScrollEvent);
	hoursContainer.on("wheel", function (event) {
		event.preventDefault(); // Prevent default scrolling behavior
		hoursContainer.scrollLeft(
			hoursContainer.scrollLeft() + event.originalEvent.deltaY
		);
		handleScrollEvent();
	});
	hours.on("click", function () {
		const hour = parseInt($(this).data("hour"));
		toggleSunMoon(hour);
		highlightHour(hours.index(this));
		updateWeatherAndTemperature($(this));
	});

	// Make it rain
	createRain();
	init();
	hoursContainer.scrollLeft(81 * initialHour);
});

function setRandomLightningDuration() {
	const lightning = document.getElementById("thunderstorm");
	const minDuration = 1; // minimum duration in seconds
	const maxDuration = 4; // maximum duration in seconds
	const randomDuration =
		Math.random() * (maxDuration - minDuration) + minDuration;
	thunderstorm.style.setProperty("--lightning-duration", `${randomDuration}s`);
}

// Set an initial random duration
setRandomLightningDuration();

// Change the duration periodically
setInterval(setRandomLightningDuration, 5000); // Change every 5 seconds

particlesJS("cloud", {
	particles: {
		number: { value: 5, density: { enable: true, value_area: 100 } },
		color: { value: "#ffffff" },
		shape: {
			type: "image",
			stroke: { width: 2, color: "#00ffff" },
			polygon: { nb_sides: 5 },
			image: {
				src: "https://i.ibb.co/vzP35N4/fluffyvloud.png",
				width: 100,
				height: 100
			}
		},
		opacity: {
			value: 1,
			random: true,
			anim: {
				enable: true,
				speed: 10,
				opacity_min: 0.0081,
				sync: false
			}
		},
		size: {
			value: 800,
			random: false,
			anim: { enable: true, speed: 10, size_min: 2, sync: false }
		},
		line_linked: {
			enable: false,
			distance: 0,
			color: "#ffffff",
			opacity: 0.4,
			width: 1
		},
		move: {
			enable: true,
			speed: 6,
			direction: "left",
			random: true,
			straight: true,
			out_mode: "out",
			bounce: false,
			attract: { enable: false, rotateX: 60, rotateY: 120 }
		}
	},
	interactivity: {
		detect_on: "canvas",
		events: {
			onhover: { enable: false, mode: "bubble" },
			onclick: { enable: false, mode: "push" },
			resize: true
		},
		modes: {
			grab: { distance: 0, line_linked: { opacity: 1 } },
			bubble: {
				distance: 0,
				size: 2,
				duration: 2,
				opacity: 8,
				speed: 3
			},
			repulse: { distance: 200, duration: 0.4 },
			push: { particles_nb: 4 },
			remove: { particles_nb: 2 }
		}
	},
	retina_detect: true
});

// Adjust initial positions of the particles
function adjustInitialPositions() {
	const particlesArray = window.pJSDom[0].pJS.particles.array;
	particlesArray.forEach((p) => {
		p.x = Math.random() * window.innerWidth;
		p.y = Math.random() * window.innerHeight;
	});
}

// Wait until particles are initialized and then adjust positions
setTimeout(adjustInitialPositions, 1000);

particlesJS("snow", {
	particles: {
		number: {
			value: 2000,
			density: {
				enable: true,
				value_area: 800
			}
		},
		color: {
			value: "#fff"
		},
		shape: {
			type: "circle",
			stroke: {
				width: 0,
				color: "#000000"
			},
			polygon: {
				nb_sides: 5
			}
		},
		opacity: {
			value: 1,
			random: false,
			anim: {
				enable: false,
				speed: 1,
				opacity_min: 0.1,
				sync: false
			}
		},
		size: {
			value: 2,
			random: true,
			anim: {
				enable: false
			}
		},
		line_linked: {
			enable: false
		},
		move: {
			enable: true,
			speed: 3,
			direction: "bottom",
			random: false,
			straight: false,
			out_mode: "out",
			bounce: false,
			attract: {
				enable: false,
				rotateX: 600,
				rotateY: 1200
			}
		}
	},
	retina_detect: true
});

var canvas = $("#rain")[0];

if (canvas.getContext) {
	var ctx = canvas.getContext("2d");
	var w = canvas.width;
	var h = canvas.height;
	ctx.strokeStyle = "rgba(255,255,255,0.5)";
	ctx.lineWidth = 1;
	ctx.lineCap = "round";

	var init = [];
	var maxParts = 300;
	for (var a = 0; a < maxParts; a++) {
		init.push({
			x: Math.random() * w,
			y: Math.random() * h,
			l: Math.random() * 1,
			xs: -4 + Math.random() * 4 + 2,
			ys: Math.random() * 10 + 10
		});
	}

	var particles = [];
	for (var b = 0; b < maxParts; b++) {
		particles[b] = init[b];
	}

	function draw() {
		ctx.clearRect(0, 0, w, h);
		for (var c = 0; c < particles.length; c++) {
			var p = particles[c];
			ctx.beginPath();
			ctx.moveTo(p.x, p.y);
			ctx.lineTo(p.x + p.l * p.xs, p.y + p.l * p.ys);
			ctx.stroke();
		}
		move();
	}

	function move() {
		for (var b = 0; b < particles.length; b++) {
			var p = particles[b];
			p.x += p.xs;
			p.y += p.ys;
			if (p.x > w || p.y > h) {
				p.x = Math.random() * w;
				p.y = -20;
			}
		}
	}

	setInterval(draw, 3);
}

</script>
