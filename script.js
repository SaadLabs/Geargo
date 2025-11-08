// === Navbar Responsiveness ===
const menuIcon = document.getElementById("menuIcon");
const navContainer = document.getElementById("navContainer");

menuIcon.addEventListener("click", () => {
  navContainer.classList.toggle("active");
  menuIcon.textContent = navContainer.classList.contains("active") ? "✕" : "☰";
});


// === Slideshow ===
const slides = document.querySelectorAll(".hero-container");
const dots = document.querySelectorAll(".dot");
let current = 0;

// === Show specific slide and update dots ===
function showSlide(index) {
  // Remove active from all slides and dots
  slides.forEach(slide => slide.classList.remove("active"));
  dots.forEach(dot => dot.classList.remove("active"));
  slides[current].classList.add('exit-left');

  // Add active to the current one
  slides[index].classList.add("active");
  dots[index].classList.add("active");

  current = index;
  slides[current].classList.remove('exit-left');
}

// === Next & Previous Functions ===
function showNextSlide() {
  let next = (current + 1) % slides.length;
  showSlide(next);
}

function showPrevSlide() {
  let prev = (current - 1 + slides.length) % slides.length;
  showSlide(prev);
}

// === Interval for auto-sliding ===
let slideInterval = setInterval(showNextSlide, 4000);

// === Reset interval when user interacts ===
function resetInterval() {
  clearInterval(slideInterval);
  slideInterval = setInterval(showNextSlide, 4000);
}

// === Buttons ===
const prevBtn = document.querySelector(".prev");
const nextBtn = document.querySelector(".next");

if (prevBtn && nextBtn) {
  prevBtn.addEventListener("click", () => {
    showPrevSlide();
    resetInterval();
  });

  nextBtn.addEventListener("click", () => {
    showNextSlide();
    resetInterval();
  });
}

// === Dots Click Handling ===
dots.forEach((dot, index) => {
  dot.addEventListener("click", () => {
    showSlide(index);
    resetInterval();
  });
});
