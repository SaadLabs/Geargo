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

