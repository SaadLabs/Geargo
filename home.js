//  === Navbar Responsiveness ===


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


document.addEventListener("DOMContentLoaded", function () {

  // Reusable function for both Desktop and Mobile search
  function setupSearch(inputId, resultsId) {
    const searchInput = document.getElementById(inputId);
    const resultsBox = document.getElementById(resultsId);

    if (searchInput && resultsBox) {
      searchInput.addEventListener('keyup', function () {
        let query = searchInput.value.trim();

        if (query.length > 1) {
          // Fetch data from index.php
          fetch(`index.php?ajax_query=${query}`)
            .then(response => response.json())
            .then(data => {
              resultsBox.innerHTML = ''; // Clear old results

              if (data.length > 0) {
                resultsBox.style.display = 'block';

                data.forEach(product => {
                  let a = document.createElement('a');
                  a.href = `product page/product.php?id=${product.product_id}`;
                  a.classList.add('suggestion-item');

                  let imgPath = product.image ? product.image : 'headphone1.png';

                  a.innerHTML = `
                      <img src="${imgPath}" alt="${product.title}">
                      <div class="suggestion-info">
                          <h4>${product.title}</h4>
                          <p>Rs. ${parseInt(product.price).toLocaleString()}</p>
                      </div>
                  `;
                  resultsBox.appendChild(a);
                });
              } else {
                resultsBox.style.display = 'none';
              }
            })
            .catch(error => console.error('Error:', error));
        } else {
          resultsBox.style.display = 'none';
        }
      });

      // Close list if clicking outside
      document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
          resultsBox.style.display = 'none';
        }
      });
    }
  }

  // Initialize Desktop Search
  setupSearch('searchInput', 'searchResultsList');

  // Initialize Mobile Search
  setupSearch('mobileSearchInput', 'mobileSearchResultsList');
});

document.addEventListener("DOMContentLoaded", function() {
    // Check if the URL has "?open_cart=1"
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('open_cart')) {
        // 1. Open the cart immediately
        openCart(); // Call your existing function that opens the sidebar

        // 2. Clean the URL (remove "?open_cart=1") so it doesn't keep opening on refresh
        urlParams.delete('open_cart');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState(null, '', newUrl);
    }
});