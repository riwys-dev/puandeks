document.addEventListener("DOMContentLoaded", function () {
	const searchForm = document.querySelector(".search-bar");
	if (!searchForm) return;

	searchForm.addEventListener("submit", function (e) {
		e.preventDefault();

		const input = searchForm.querySelector("input");
		if (!input) return;

		const query = input.value.trim();
		if (!query) return;

		fetch(`/puandeks/backend/api/search-company?q=${encodeURIComponent(query)}`)
			.then(res => res.json())
			.then(data => {
				if (data.status === "found") {
					window.location.href = data.redirect;
				} else {
					window.location.href = "company-search?q=" + encodeURIComponent(query);
				}
			})
			.catch(() => {
				alert("Arama sırasında bir hata oluştu.");
			});
	});
});

