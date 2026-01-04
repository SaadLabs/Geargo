document.addEventListener("DOMContentLoaded", function() {

    // 1. SEARCH LOGIC (Kept same, just wrapped safely)
    function setupSearch(inputId, resultsId) {
        const searchInput = document.getElementById(inputId);
        const resultsBox = document.getElementById(resultsId);

        if (searchInput && resultsBox) {
            searchInput.addEventListener('keyup', function() {
                let query = searchInput.value.trim();
                if (query.length > 1) {
                    fetch(`?ajax_query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsBox.innerHTML = '';
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
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }
    }

    setupSearch('searchInput', 'searchResultsList');
    setupSearch('mobileSearchInput', 'mobileSearchResultsList');


    // 2. TOGGLE CARD DETAILS
    const paymentMethod = document.getElementById("paymentMethod");
    const cardDetails = document.getElementById("cardDetails");
    const placeOrderBtn = document.getElementById("placeOrderBtn"); // Ensure your button has this ID

    if (paymentMethod) {
        paymentMethod.addEventListener("change", function() {
            if (this.value === "Card") {
                cardDetails.style.display = "block";
                
                // Check if the inputs exist (meaning user HAS cards)
                // If 'cardName' is missing, it means PHP showed the "No Payment Methods" alert instead
                if (!document.getElementById("cardName")) {
                    // Disable button if no cards are available
                    if(placeOrderBtn) {
                        placeOrderBtn.disabled = true;
                        placeOrderBtn.style.opacity = "0.5";
                        placeOrderBtn.style.cursor = "not-allowed";
                    }
                }
            } else {
                cardDetails.style.display = "none";
                // Re-enable button for COD
                if(placeOrderBtn) {
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.style.opacity = "1";
                    placeOrderBtn.style.cursor = "pointer";
                }
                
                // Clear errors
                document.querySelectorAll("#cardDetails .error").forEach(e => e.innerText = "");
                document.querySelectorAll("#cardDetails input").forEach(i => i.classList.remove("error-border"));
            }
        });
    }

    // 3. FORM VALIDATION
    const checkoutForm = document.getElementById("checkoutForm");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function(e) {
            
            // ================= Validation =================
            let isValid = true;

            // Clear previous errors
            document.querySelectorAll(".error").forEach(span => span.innerText = "");
            document.querySelectorAll("input, textarea, select").forEach(el => el.classList.remove("error-border"));

            // --- Helper Function ---
            function setError(elementId, message) {
                const el = document.getElementById(elementId);
                if (el) { // Only set error if element exists
                    el.classList.add("error-border");
                    // Assuming error span ID is {id}Error
                    const errorSpan = document.getElementById(elementId + "Error") || el.nextElementSibling; 
                    if(errorSpan && errorSpan.classList.contains("error")) {
                         errorSpan.innerText = message;
                    } else {
                        // Fallback for specific IDs you used
                         const specificSpan = document.getElementById(elementId + "Error");
                         if(specificSpan) specificSpan.innerText = message;
                    }
                }
                isValid = false;
            }
            
            // --- Basic Fields ---
            const fullName = document.getElementById("fullName");
            if (fullName && fullName.value.trim() === "") setError("fullName", "Full Name is required");

            const email = document.getElementById("email");
            if (email && email.value.trim() !== "") {
                const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
                if (!emailPattern.test(email.value.trim())) setError("email", "Enter a valid email");
            }

            const phone = document.getElementById("phone");
            if (phone && phone.value.trim() === "") {
                setError("phone", "Phone number is required");
            } else if (phone && !/^\d{10,15}$/.test(phone.value.trim())) {
                setError("phone", "Enter a valid phone number");
            }

            const address = document.getElementById("address");
            if (address && address.value.trim() === "") setError("address", "Address is required");

            const payment = document.getElementById("paymentMethod");
            if (payment && payment.value === "") setError("paymentMethod", "Select a payment method");


            // --- Card Validation (Only if Card is selected) ---
            if (payment && payment.value === "Card") {
                const cardName = document.querySelector("input[name='card_name']"); // Using name selector is safer here
                const cardNumber = document.querySelector("input[name='card_number']");
                const cardExpiry = document.querySelector("input[name='card_expiry']");
                const cardCVV = document.querySelector("input[name='card_cvv']");

                // CRITICAL CHECK: Do the inputs exist? 
                // If they don't, it means the user has no saved cards.
                if (!cardName) {
                    alert("Please add a card to your profile first.");
                    e.preventDefault();
                    return;
                }

                if (cardName.value.trim() === "") {
                    // Manually highlighting since they might not have IDs in your HTML loop sometimes
                    cardName.classList.add("error-border");
                    isValid = false;
                    alert("Card Name is required"); // Fallback alert if span missing
                }

                // Allow spaces in regex: [\d\s]
                const cleanNum = cardNumber.value.replace(/\s/g, '');
                if (!/^\d{13,19}$/.test(cleanNum)) {
                    cardNumber.classList.add("error-border");
                    isValid = false;
                    alert("Enter a valid card number");
                }

                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry.value.trim())) {
                    cardExpiry.classList.add("error-border");
                    isValid = false;
                    alert("Enter expiry (MM/YY)");
                }

                if (!/^\d{3,4}$/.test(cardCVV.value.trim())) {
                    cardCVV.classList.add("error-border");
                    isValid = false;
                    alert("Enter valid CVV");
                }
            }

            if (!isValid) {
                e.preventDefault(); // Stop submission if invalid
            } else {
                // Form is valid, let it submit naturally to process_checkout.php
                // We DO NOT use e.preventDefault() here.
            }
        });
    }
});