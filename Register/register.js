const form = document.getElementById("registerForm");
const password = document.getElementById("password");
const errorMsg = document.getElementById("errorMsg");

form.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const mobile = document.getElementById("mobile").value.trim();
  const pass = password.value;
  const confirm = document.getElementById("confirmPassword").value;

  if (!name || !email || !mobile || !pass || !confirm) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "All fields are required";
    return;
  }

  const mobileRegex = /^[0-9]{10,15}$/;
  if (!mobileRegex.test(mobile)) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Enter a valid mobile number";
    return;
  }

  if (pass.length < 6) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Password must be at least 6 characters";
    return;
  }

  if (pass !== confirm) {
    errorMsg.style.color = "red";
    errorMsg.textContent = "Passwords do not match";
    return;
  }

  errorMsg.style.color = "green";
  errorMsg.textContent = "Registration successful (frontend only)";
});

// show password
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", () => {
  // Toggle the type attribute
  const type = password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);

  // Toggle icon
  if (type === "text") {
    togglePassword.classList.remove("fa-eye");
    togglePassword.classList.add("fa-eye-slash");
  } else {
    togglePassword.classList.remove("fa-eye-slash");
    togglePassword.classList.add("fa-eye");
  }
});
