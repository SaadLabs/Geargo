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
          fetch(`search.php?ajax_query=${query}`)
            .then(response => response.json())
            .then(data => {
              resultsBox.innerHTML = ''; // Clear old results

              if (data.length > 0) {
                resultsBox.style.display = 'block';

                data.forEach(product => {
                  let a = document.createElement('a');
                  a.href = `product page/product.php?id=${product.product_id}`;
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