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
//-----------------------------------------------------------------

//  === Contact Form Validation ===


document.getElementById("contactForm").addEventListener("submit", function(e) {

    // Inputs
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let message = document.getElementById("message").value.trim();

    // Error fields
    let nameError = document.getElementById("nameError");
    let emailError = document.getElementById("emailError");
    let messageError = document.getElementById("messageError");

    // Reset errors
    nameError.textContent = "";
    emailError.textContent = "";
    messageError.textContent = "";

    let isValid = true;

    // Name validation
    if (name === "") {
        nameError.textContent = "Name is required";
        isValid = false;
    }

    // Email validation
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "") {
        emailError.textContent = "Email is required";
        isValid = false;
    } else if (!emailPattern.test(email)) {
        emailError.textContent = "Enter a valid email address";
        isValid = false;
    }

    // Message validation
    if (message === "") {
        messageError.textContent = "Message cannot be empty";
        isValid = false;
    } else if (message.length < 10) {
        messageError.textContent = "Message must be at least 10 characters";
        isValid = false;
    }


    if (!isValid) {
        e.preventDefault();
    }
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
          fetch(`contact.php?ajax_query=${query}`)
            .then(response => response.json())
            .then(data => {
              resultsBox.innerHTML = ''; // Clear old results

              if (data.length > 0) {
                resultsBox.style.display = 'block';

                data.forEach(product => {
                  let a = document.createElement('a');
                  a.href = `../product page/product.php?id=${product.product_id}`;
                  a.classList.add('suggestion-item');

                  let imgPath = product.image ? "../" + product.image : '../headphone1.png';

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
