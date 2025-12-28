document.getElementById("checkoutForm").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent form submission for validation


//   ================= Validation =================

    let isValid = true;

    // Clear previous errors
    document.querySelectorAll(".error").forEach(span => span.innerText = "");
    document.querySelectorAll("input, textarea, select").forEach(el => el.classList.remove("error-border"));

    // Full Name
    const fullName = document.getElementById("fullName");
    if (fullName.value.trim() === "") {
        isValid = false;
        fullName.classList.add("error-border");
        document.getElementById("fullNameError").innerText = "Full Name is required";
    }

    // Email (optional but validate if filled)
    const email = document.getElementById("email");
    if (email.value.trim() !== "") {
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!emailPattern.test(email.value.trim())) {
            isValid = false;
            email.classList.add("error-border");
            document.getElementById("emailError").innerText = "Enter a valid email";
        }
    }

    // Phone
    const phone = document.getElementById("phone");
    if (phone.value.trim() === "") {
        isValid = false;
        phone.classList.add("error-border");
        document.getElementById("phoneError").innerText = "Phone number is required";
    } else if (!/^\d{10,15}$/.test(phone.value.trim())) {
        isValid = false;
        phone.classList.add("error-border");
        document.getElementById("phoneError").innerText = "Enter a valid phone number";
    }

    // Address
    const address = document.getElementById("address");
    if (address.value.trim() === "") {
        isValid = false;
        address.classList.add("error-border");
        document.getElementById("addressError").innerText = "Address is required";
    }

    // City
    const city = document.getElementById("city");
    if (city.value.trim() === "") {
        isValid = false;
        city.classList.add("error-border");
        document.getElementById("cityError").innerText = "City is required";
    }

    // Postal Code
    const postalCode = document.getElementById("postalCode");
    if (postalCode.value.trim() === "") {
        isValid = false;
        postalCode.classList.add("error-border");
        document.getElementById("postalCodeError").innerText = "Postal Code is required";
    }

    // Country
    const country = document.getElementById("country");
    if (country.value.trim() === "") {
        isValid = false;
        country.classList.add("error-border");
        document.getElementById("countryError").innerText = "Country is required";
    }

    // Payment Method
    const payment = document.getElementById("paymentMethod");
    if (payment.value === "") {
        isValid = false;
        payment.classList.add("error-border");
        document.getElementById("paymentError").innerText = "Select a payment method";
    }

    // If all valid, submit the form (for frontend testing, we can just alert)
    if (isValid) {
        alert("Form is valid! You can submit to backend.");
        // this.submit(); // uncomment when backend is ready
    }
});


//   ================= card details show/hide =================
const paymentMethod = document.getElementById("paymentMethod");
const cardDetails = document.getElementById("cardDetails");

paymentMethod.addEventListener("change", function() {
    if (this.value === "Card") {
        cardDetails.style.display = "block";
    } else {
        cardDetails.style.display = "none";
        // Clear card errors
        document.querySelectorAll("#cardDetails .error").forEach(e => e.innerText = "");
        document.querySelectorAll("#cardDetails input").forEach(i => i.classList.remove("error-border"));
    }
});


//   ================= card valications =================

if (paymentMethod.value === "Card") {
    const cardName = document.getElementById("cardName");
    const cardNumber = document.getElementById("cardNumber");
    const cardExpiry = document.getElementById("cardExpiry");
    const cardCVV = document.getElementById("cardCVV");

    // Name on card
    if (cardName.value.trim() === "") {
        isValid = false;
        cardName.classList.add("error-border");
        document.getElementById("cardNameError").innerText = "Name on card is required";
    }

    // Card number
    if (!/^\d{16}$/.test(cardNumber.value.trim())) {
        isValid = false;
        cardNumber.classList.add("error-border");
        document.getElementById("cardNumberError").innerText = "Enter a valid 16-digit card number";
    }

    // Expiry MM/YY
    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry.value.trim())) {
        isValid = false;
        cardExpiry.classList.add("error-border");
        document.getElementById("cardExpiryError").innerText = "Enter expiry in MM/YY";
    }

    // CVV
    if (!/^\d{3,4}$/.test(cardCVV.value.trim())) {
        isValid = false;
        cardCVV.classList.add("error-border");
        document.getElementById("cardCVVError").innerText = "Enter 3 or 4 digit CVV";
    }
}
