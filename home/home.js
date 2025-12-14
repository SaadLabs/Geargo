//                                 === Navbar Responsiveness ===


const menuIcon = document.getElementById("menuIcon");
const navContainer = document.getElementById("navContainer");

menuIcon.addEventListener("click", () => {
  navContainer.classList.toggle("active");
  menuIcon.textContent = navContainer.classList.contains("active") ? "✕" : "☰";
});

// Function to handle Search Input Logic
function setupSearchLogic(inputId, clearBtnId) {
  const input = document.getElementById(inputId);
  const clearBtn = document.getElementById(clearBtnId);

  // Check if elements exist to prevent errors
  if (!input || !clearBtn) return;

  // Show/Hide "X" button when typing
  input.addEventListener("input", () => {
    if (input.value.length > 0) {
      clearBtn.style.display = "block";
    } else {
      clearBtn.style.display = "none";
    }
  });

  //  Clear text when "X" is clicked
  clearBtn.addEventListener("click", () => {
    input.value = "";              // Clear text
    clearBtn.style.display = "none";
    input.focus();
  });
}

setupSearchLogic("searchInput", "clearBtn");
setupSearchLogic("mobileSearchInput", "mobileClearBtn");

// Listen for screen resize to reset mobile state on desktop
const mobileSearchBarvis = document.querySelector('.mobile-search-bar');
const breakpoint = 1100; // Use the same breakpoint as your CSS media query

window.addEventListener('resize', () => {
  if (window.innerWidth > breakpoint) {
    mobileSearchBarvis.style.display = 'none';
  }
});



// Toggle mobile search bar
const mobileSearchIcon = document.querySelector('.mobile-search-icon');
const mobileSearchBar = document.querySelector('.mobile-search-bar');

mobileSearchIcon.addEventListener('click', () => {
  if (mobileSearchBar.style.display === 'block') {
    mobileSearchBar.style.display = 'none';
  } else {
    mobileSearchBar.style.display = 'block';
  }
});

//---------------------------------------------------------------------------------------------------------



//                                        === Slideshow ===



const slides = document.querySelectorAll(".hero-container");
const dots = document.querySelectorAll(".dot");
let current = 0;

// === Show specific slide and update dots ===
function showSlide(index) {
  slides.forEach(slide => slide.classList.remove("exit-left"));

  // Remove active from all slides and dots
  slides.forEach(slide => slide.classList.remove("active"));
  dots.forEach(dot => dot.classList.remove("active"));
  slides[current].classList.add('exit-left');

  // Add active to the current one
  slides[index].classList.add("active");
  dots[index].classList.add("active");

  current = index;
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
// const prevBtn = document.querySelector(".prev");
// const nextBtn = document.querySelector(".next");

// if (prevBtn && nextBtn) {
//   prevBtn.addEventListener("click", () => {
//     showPrevSlide();
//     resetInterval();
//   });

//   nextBtn.addEventListener("click", () => {
//     showNextSlide();
//     resetInterval();
//   });
// }

// === Dots Click Handling ===
dots.forEach((dot, index) => {
  dot.addEventListener("click", () => {
    showSlide(index);
    resetInterval();
  });
});
