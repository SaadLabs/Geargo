function openCart() {
    document.getElementById("cart-sidebar").classList.add("active");
    document.getElementById("cart-overlay").classList.add("active");
    document.body.classList.add('cart-open');
}

function closeCart() {
    document.getElementById("cart-sidebar").classList.remove("active");
    document.getElementById("cart-overlay").classList.remove("active");
    document.body.classList.remove('cart-open');
}

