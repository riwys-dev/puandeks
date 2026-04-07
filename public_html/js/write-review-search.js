document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("companySearchInput");
    const resultsBox = document.getElementById("search-results");

    if (!searchInput || !resultsBox) return;

    // Enter yönlendirme
    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length > 1) {
                window.location.href = `company-search?q=${encodeURIComponent(query)}`;
            }
        }
    });

    // Canlı arama (only company)
    searchInput.addEventListener("input", function () {
        const query = searchInput.value.trim();

        if (query.length < 2) {
            resultsBox.style.display = "none";
            resultsBox.innerHTML = "";
            return;
        }

        fetch(`/api/search-company.php?query=${encodeURIComponent(query)}&type=business`)
            .then(res => res.json())
            .then(data => {
                resultsBox.innerHTML = "";

                if (data.length === 0) {
                    resultsBox.style.display = "none";
                    return;
                }

                data.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "search-item";
                    div.textContent = item.name;
                    div.onclick = () => {
                        window.location.href = `company-profile?id=${item.id}`;
                    };
                    resultsBox.appendChild(div);
                });

                resultsBox.style.display = "block";
            })
            .catch(() => {
                resultsBox.style.display = "none";
            });
    });
});
