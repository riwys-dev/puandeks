document.addEventListener("DOMContentLoaded", function () {
	const params = new URLSearchParams(window.location.search);
	const query = params.get("q");

	if (!query) return;

	fetch(`/puandeks/backend/api/get-companies.php?q=${encodeURIComponent(query)}`)
		.then(res => res.json())
		.then(companies => {
			const resultsContainer = document.getElementById("company-results");
			if (!resultsContainer) return;

			resultsContainer.innerHTML = "";

			if (companies.length === 0) {
				resultsContainer.innerHTML = "<p>Sonuç bulunamadı.</p>";
				return;
			}

			companies.forEach(c => {
				const card = document.createElement("div");
				card.className = "company-card";
				card.innerHTML = `
					<a href="company-profile.html?id=${c.id}">
						<img src="img/placeholder/company-profile.png" alt="${c.name}">
					</a>
					<a href="company-profile.html?id=${c.id}"><h3>${c.name}</h3></a>
					<a href="#">${c.domain}</a><br>
					<span style="margin-bottom:10px;">${c.rating} | ${c.reviews} inceleme</span>
					<span class="rating">${renderStars(c.rating)}</span>
				`;
				resultsContainer.appendChild(card);
			});
		})
		.catch(() => {
			console.error("Şirketler alınırken hata oluştu.");
		});
});

// Basit yıldız render fonksiyonu
function renderStars(score) {
	const fullStars = Math.floor(score);
	let html = '';
	for (let i = 0; i < 5; i++) {
		html += `<img src="img/core/${i < fullStars ? 'star_5' : 'star_0'}.svg" class="custom-star">`;
	}
	return html;
}
