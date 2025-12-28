function showSection(id, element) {
    document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.add('hidden');
    });
    document.getElementById(id).classList.remove('hidden');

    document.querySelectorAll('.sidebar ul li').forEach(li => {
        li.classList.remove('active');
    });
    element.classList.add('active');
}

// Dashboard Data
const dashboardData = {
    today: { orders: 5, sales: 12000 },
    week: { orders: 42, sales: 98000 },
    month: { orders: 180, sales: 420000 },
    year: { orders: 1450, sales: 5100000 }
};

function updateDashboard() {
    const filter = document.getElementById("dashboardFilter").value;

    document.getElementById("orderCount").innerText =
        dashboardData[filter].orders;

    document.getElementById("salesAmount").innerText =
        "Rs. " + dashboardData[filter].sales.toLocaleString();
}

// Load default
updateDashboard();


/* Modal */
function openModal() {
    document.getElementById('productModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

/* Search */
function searchProduct() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#productTable tr");

    rows.forEach((row, index) => {
        if (index === 0) return;
        let name = row.cells[1].innerText.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}

// Search Orders
function searchOrders() {
    let input = document.getElementById('searchOrders').value.toLowerCase();
    let rows = document.querySelectorAll('#ordersTable tr');

    rows.forEach((row, index) => {
        if (index === 0) return; // skip header
        let orderID = row.cells[0].innerText.toLowerCase();
        let userName = row.cells[1].innerText.toLowerCase();
        let status = row.cells[3].innerText.toLowerCase();

        row.style.display = (orderID.includes(input) || userName.includes(input) || status.includes(input)) ? '' : 'none';
    });
}

// Search Users
function searchUsers() {
    let input = document.getElementById('searchUsers').value.toLowerCase();
    let rows = document.querySelectorAll('#usersTable tr');

    rows.forEach((row, index) => {
        if (index === 0) return; // skip header
        let userName = row.cells[1].innerText.toLowerCase();
        let email = row.cells[2].innerText.toLowerCase();
        let status = row.cells[3].innerText.toLowerCase();

        row.style.display = (userName.includes(input) || email.includes(input) || status.includes(input)) ? '' : 'none';
    });
}

/* Staff Modal */
function openStaffModal() {
    document.getElementById('staffModal').style.display = 'flex';
}

function closeStaffModal() {
    document.getElementById('staffModal').style.display = 'none';
}

/* Validation */
function validateStaffForm() {
    let name = document.getElementById('staffName').value.trim();
    let email = document.getElementById('staffEmail').value.trim();
    let role = document.getElementById('staffRole').value;

    if (name === "" || email === "" || role === "") {
        alert("All staff fields are required");
        return false;
    }

    if (!email.includes("@")) {
        alert("Enter a valid email address");
        return false;
    }

    closeStaffModal();
    alert("Staff added (frontend only)");
    return false; // prevent real submit
}

// confirmation for delete buttons
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm("Are you sure you want to delete?")) return;
        alert("Deleted (frontend only)");
    });
});

// Block/Unblock Users
function toggleUser(btn) {
    if (btn.innerText === "Block") {
        btn.innerText = "Unblock";
        btn.closest("tr").cells[3].innerText = "Blocked";
    } else {
        btn.innerText = "Block";
        btn.closest("tr").cells[3].innerText = "Active";
    }
}

// Highlight low stock products
document.querySelectorAll("#productTable tr").forEach((row, index) => {
    if (index === 0) return;
    let stock = parseInt(row.cells[3].innerText);

    if (stock < 20) {
        row.style.background = "#fff3cd"; // warning color
    }
});
