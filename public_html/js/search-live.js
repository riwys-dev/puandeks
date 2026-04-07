document.addEventListener("DOMContentLoaded", function () {
	const searchInput = document.getElementById("companySearchInput");
	const resultsBox = document.getElementById("search-results");

	if (!searchInput || !resultsBox) return;

	// Enter hit ->
	searchInput.addEventListener("keydown", function (e) {
		if (e.key === "Enter") {
			e.preventDefault();
			const query = searchInput.value.trim();
			if (query.length > 1) {
				window.location.href = `company-search?q=${encodeURIComponent(query)}`;
			}
		}
	});

	// Live search dropdown
	searchInput.addEventListener("input", function () {
		const query = searchInput.value.trim();

		if (query.length < 2) {
			resultsBox.style.display = "none";
			resultsBox.innerHTML = "";
			return;
		}

		fetch(`/api/search-company.php?query=${encodeURIComponent(query)}`)
			.then(res => res.json())
			.then(data => {
				resultsBox.innerHTML = "";

				if (data.length === 0) {
					resultsBox.style.display = "none";
					return;
				}
               
                resultsBox.style.display = "block";

				data.forEach(item => {
					const div = document.createElement("div");
					div.classList.add("search-result-item");

					if (item.type === "company") {
						div.textContent = item.name;
						div.onclick = () => window.location.href = `/company/${item.slug}`;
					}

					if (item.type === "category") {
						div.textContent = item.name + " (Kategori)";
						div.onclick = () => window.location.href = `/company-search?category=${item.slug}`;
					}

					resultsBox.appendChild(div);
				});
			})   
			.catch(err => {
				console.error("Arama hatası:", err);
				resultsBox.style.display = "none";
			});
	});

	// Search icon hit ->
	const searchIcon = document.querySelector(".input-group-text");
	if (searchIcon) {
		searchIcon.addEventListener("click", function () {
			const query = searchInput.value.trim();
			if (query.length > 1) {
				window.location.href = `company-search?q=${encodeURIComponent(query)}`;
			}
		});
	}

	// click outside
	document.addEventListener("click", function (e) {
		if (!resultsBox.contains(e.target) && e.target !== searchInput) {
			resultsBox.style.display = "none";
		}
	});
});
