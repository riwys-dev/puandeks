document.addEventListener("DOMContentLoaded", function () {
    const searchInput  = document.getElementById("companySearchInput");
    const resultsBox   = document.getElementById("search-results");
    const warningBox   = document.getElementById("category-warning"); 


    // If search components don't exist, stop
    if (!searchInput || !resultsBox || !warningBox) return;

    // ------------------------------------------------------------
    // LIVE CATEGORY SEARCH (runs on every keystroke)
    // ------------------------------------------------------------
    searchInput.addEventListener("input", function () {
        const term = searchInput.value.trim();

        // Hide previous warning
        warningBox.style.display = "none";

        // Clear dropdown if term is too short
        if (term.length < 2) {
            resultsBox.style.display = "none";
            resultsBox.innerHTML = "";
            return;
        }

        // Fetch matching categories
        fetch(`/api/search-categories.php?term=${encodeURIComponent(term)}`)
            .then(res => res.json())
            .then(data => {
                resultsBox.innerHTML = "";

                // If no category found → close dropdown, but no warning yet
                if (!data || data.length === 0) {
                    resultsBox.style.display = "none";
                    return;
                }

                // Generate dropdown results
                data.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "search-item";
                    div.textContent = item.name;

                    // Click > redirect to category results
                    div.onclick = () => window.location.href = `company-search?category=${item.slug}`;


                    resultsBox.appendChild(div);
                });

                resultsBox.style.display = "block";
            });
    });

    // ------------------------------------------------------------
    // ENTER KEY BEHAVIOR
    //    - Do NOT redirect
    //    - Show a warning message instead
    // ------------------------------------------------------------
    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();

            const term = searchInput.value.trim();
            if (term.length < 2) return;

            // Check category match again
            fetch(`/api/search-categories.php?term=${encodeURIComponent(term)}`)
                .then(res => res.json())
                .then(data => {

                    // No category found > show warning
                    if (!data || data.length === 0) {
                        warningBox.textContent = `"${term}" ile eşleşen bir kategori bulunamadı.`;
                        warningBox.style.display = "block";
                        return;
                    }

                    // Category exists but user must choose manually
                    warningBox.textContent = `Bir kategori seçin.`;
                    warningBox.style.display = "block";
                });
        }
    });

    // ------------------------------------------------------------
    // CLICK OUTSIDE → CLOSE DROPDOWN
    // ------------------------------------------------------------
    document.addEventListener("click", function (e) {
        if (!resultsBox.contains(e.target) && e.target !== searchInput) {
            resultsBox.style.display = "none";
        }
    });
});
