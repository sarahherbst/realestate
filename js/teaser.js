// State area for hammerJS
const swipeElement = document.getElementById("teaser-swipe-area");
const hammertime = new Hammer(swipeElement);

// State position of continer
let positionPercent = 0;
const cardWidth = 180; // count in the width + margin
const positioning = document.getElementById("position-container");

// State elements as Array
const cardArr = document.getElementsByClassName("teaser-perspective");

// Counter for keeping track of current Position
let numCounter = 0;

// Instantiate Function
window.onload = () => {
	applyClasses(numCounter);

	if (numCounter < cardArr.length - 1) {
		applyPerspective("left");
		numCounter++;
		applyClasses(numCounter);
	}

	if (numCounter > 0) {
		applyPerspective("right");
		numCounter--;
		applyClasses(numCounter);
	}
};

// Function for application of current position and classes
const applyClasses = numCounter => {
	// Apply to left side
	for (var i = numCounter; i >= 0; i--) {
		cardArr[i].classList.remove("left");
		cardArr[i].classList.remove("right");
		cardArr[i].classList.remove("center");
		cardArr[i].classList.add("left");
		cardArr[i].style.zIndex = i;
	}

	// Apply to center
	cardArr[numCounter].classList.remove("right");
	cardArr[numCounter].classList.remove("left");
	cardArr[numCounter].classList.remove("center");
	cardArr[numCounter].classList.add("center");

	// Apply to right side
	for (var i = numCounter + 1; i < cardArr.length; i++) {
		cardArr[i].classList.remove("left");
		cardArr[i].classList.remove("right");
		cardArr[i].classList.remove("center");
		cardArr[i].classList.add("right");
		cardArr[i].style.zIndex = Math.abs(i) * -1;
	}
};

hammertime.on("swipeleft swiperight", ev => {
	if (ev.type == "swipeleft") {
		if (numCounter < cardArr.length - 1) {
			applyPerspective("left");
			numCounter++;
			applyClasses(numCounter);
		}
	} else if (ev.type == "swiperight") {
		if (numCounter > 0) {
			applyPerspective("right");
			numCounter--;
			applyClasses(numCounter);
		}
	}
});

const applyPerspective = direction => {
	if (direction == "left") {
		positionPercent -= cardWidth;
		positioning.style.transform = `translateX(${positionPercent}px)`;
	} else if (direction == "right") {
		positionPercent += cardWidth;
		positioning.style.transform = `translateX(${positionPercent}px)`;
	}
};