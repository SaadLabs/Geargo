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
const breakpoint = 1100;

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