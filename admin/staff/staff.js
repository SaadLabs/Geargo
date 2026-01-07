// Navigation: Switches between sections

function showSection(id, element) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.add('hidden');
    });
    
    // Show the selected section
    document.getElementById(id).classList.remove('hidden');

    // Update active class in sidebar
    document.querySelectorAll('.sidebar ul li').forEach(li => {
        li.classList.remove('active');
    });
    element.classList.add('active');
}

/* Search */
function searchProduct() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    
    // Select the table body rows only
    let rows = document.querySelectorAll("#productTable tbody tr");

    rows.forEach((row) => {
        let name = row.cells[1].innerText.toLowerCase(); 
        
        let id = row.cells[0].innerText.toLowerCase();

        if (name.includes(input) || id.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

//Order Search
function searchOrders() {
    let input = document.getElementById('searchOrders').value.toLowerCase();
    let rows = document.querySelectorAll('#ordersTable tr');

    rows.forEach((row, index) => {
        if (index === 0) return;
        let customer = row.cells[1].innerText.toLowerCase();
        let orderId = row.cells[0].innerText.toLowerCase();
        
        row.style.display = (customer.includes(input) || orderId.includes(input)) ? '' : 'none';
    });
}

//Operational Functions
function markShipped(orderId) {
    alert("Order " + orderId + " marked as Shipped. Customer will be notified.");
}
// Update Stock

function updateStock(btn) {
    let row = btn.closest("tr");
    let stockCell = row.querySelector(".stock");
    let statusCell = row.querySelector(".status");

    let newStock = prompt("Enter new stock quantity:");

    if (newStock === null || newStock === "" || isNaN(newStock)) {
        alert("Invalid stock value");
        return;
    }

    newStock = parseInt(newStock);
    stockCell.innerText = newStock;

    // Update status
    if (newStock === 0) {
        statusCell.innerHTML = "<span style='color:red;'>Out of Stock</span>";
    } else if (newStock < 20) {
        statusCell.innerHTML = "<span style='color:orange;'>Low Stock</span>";
    } else {
        statusCell.innerHTML = "<span style='color:green;'>In Stock</span>";
    }

    alert("Stock updated successfully (frontend)");
}

// Highlight low stock rows
function checkLowStock() {
    document.querySelectorAll("#productTable tr").forEach((row, index) => {
        if (index === 0) return;

        let stock = parseInt(row.querySelector(".stock").innerText);
        if (stock < 20) {
            row.style.background = "#fff3cd"; // warning color
        }
    });
}

// Run on load
checkLowStock();

//mark order as shipped
function markShipped(btn) {
    let row = btn.closest("tr");
    btn.innerText = "Shipped";
    btn.disabled = true;
    btn.style.background = "#6c757d";

    alert("Order marked as shipped. Customer notified (simulation).");
}

// Dashboard Stats Update
function updateDashboardStats() {
    let lowStock = 0;

    document.querySelectorAll("#productTable tr").forEach((row, index) => {
        if (index === 0) return;
        let stock = parseInt(row.querySelector(".stock").innerText);
        if (stock < 20) lowStock++;
    });

    document.getElementById("lowStockCount").innerText = lowStock;
}

updateDashboardStats();

// Open / Close Password Modal
function openPasswordModal() {
    document.getElementById("passwordModal").style.display = "flex";
}

function closePasswordModal() {
    document.getElementById("passwordModal").style.display = "none";

    // Clear inputs
    document.getElementById("currentPassword").value = "";
    document.getElementById("newPassword").value = "";
    document.getElementById("confirmPassword").value = "";
}

// Change Password Logic (Frontend Simulation)
function changePassword() {
    const current = document.getElementById("currentPassword").value.trim();
    const newPass = document.getElementById("newPassword").value.trim();
    const confirm = document.getElementById("confirmPassword").value.trim();

    // Dummy stored password (simulation)
    const storedPassword = "123456";

    if (!current || !newPass || !confirm) {
        alert("All fields are required");
        return;
    }

    if (current !== storedPassword) {
        alert("Current password is incorrect");
        return;
    }

    if (newPass.length < 6) {
        alert("New password must be at least 6 characters");
        return;
    }

    if (newPass !== confirm) {
        alert("Passwords do not match");
        return;
    }

    alert("Password updated successfully (frontend simulation)");
    closePasswordModal();
}
