// Get elements
const form = document.getElementById("registerForm");
const email = document.getElementById("email");
const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
const errorMsg = document.getElementById("errorMsg");

// Show / hide password
togglePassword.addEventListener("click", () => {
  const type = password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);

  if (type === "text") {
    togglePassword.classList.remove("fa-eye");
    togglePassword.classList.add("fa-eye-slash");
  } else {
    togglePassword.classList.remove("fa-eye-slash");
    togglePassword.classList.add("fa-eye");
  }
});

// Form submit validation
form.addEventListener("submit", (e) => {
  e.preventDefault(); // prevent form from submitting

  // Trim values
  const emailValue = email.value.trim();
  const passValue = password.value;

  // Check empty fields
  if (!emailValue || !passValue) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Email and password are required";
    return;
  }

  // Basic email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(emailValue)) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Enter a valid email address";
    return;
  }

  // Password length check
  if (passValue.length < 6) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Password must be at least 6 characters";
    return;
  }

  // Success (frontend only)
  errorMsg.style.color = "green";
  errorMsg.textContent = "Login successful (frontend only)";
});
