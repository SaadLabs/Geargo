let currentSlideIndex = 0;
let slideInterval;

function showSlide(index) {
  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.dot');

  if (index >= slides.length) {
    currentSlideIndex = 0;
  } else if (index < 0) {
    currentSlideIndex = slides.length - 1;
  } else {
    currentSlideIndex = index;
  }

  slides.forEach(slide => {
    slide.classList.remove('active');
  });

  dots.forEach(dot => {
    dot.classList.remove('active');
  });

  slides[currentSlideIndex].classList.add('active');
  dots[currentSlideIndex].classList.add('active');
}

function changeSlide(direction) {
  showSlide(currentSlideIndex + direction);
  resetAutoSlide();
}

function currentSlide(index) {
  showSlide(index);
  resetAutoSlide();
}

function autoSlide() {
  slideInterval = setInterval(() => {
    showSlide(currentSlideIndex + 1);
  }, 5000);
}

function resetAutoSlide() {
  clearInterval(slideInterval);
  autoSlide();
}

document.addEventListener('DOMContentLoaded', () => {
  showSlide(0);
  autoSlide();
});

window.changeSlide = changeSlide;
window.currentSlide = currentSlide;
