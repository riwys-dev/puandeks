/* =========================================================
   INIT
========================================================= */
document.addEventListener("DOMContentLoaded", function () {

  // Review sekmeleri yükle
  ["pending", "approved", "rejected"].forEach(tab => fetchReviews(tab, 1));

  // Kullanıcı şikayetlerini yükle
  fetchReviewReports();

  // İşletme şikayetlerini yükle
  fetchBusinessReports();

  // Tab değişim event
  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener("shown.bs.tab", function (e) {
      const target = e.target.getAttribute("href").substring(1);

      if (["pending", "approved", "rejected"].includes(target)) {
        fetchReviews(target, 1);
      }

      if (target === "reports") {
        fetchReviewReports();
      }

      if (target === "business-reports") {
        fetchBusinessReports();
      }
    });
  });

});


/* =========================================================
   REVIEWS TAB
========================================================= */
function fetchReviews(tabType, page = 1) {

  const params = new URLSearchParams({ status: tabType, page });

  fetch("api/get-reviews.php?" + params.toString())
    .then(res => res.json())
    .then(res => {

      const data = res.data || [];
      data.forEach(review => {
      if (!review.media) {
        review.media = [];
      } else if (typeof review.media === "string") {
        try {
          review.media = JSON.parse(review.media);
        } catch (e) {
          review.media = [];
        }
      } else if (!Array.isArray(review.media)) {
        review.media = [];
      }
    });
      if (!window.allReviews) window.allReviews = {};
      window.allReviews[tabType] = data;
      const total = res.total || 0;
      const perPage = res.perPage || 10;

      const tableBody = document.querySelector(`#${tabType} table tbody`);
      const pagination = document.querySelector(`#pagination-${tabType}`);

      if (!tableBody || !pagination) return;

      tableBody.innerHTML = "";
      pagination.innerHTML = "";

      if (data.length === 0) {
        const emptyRow = document.createElement("tr");
        emptyRow.innerHTML = `<td colspan="6" class="text-center">Bu sekmede yorum yok.</td>`;
        tableBody.appendChild(emptyRow);
        return;
      }

      data.forEach(review => {

        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${review.created_at}</td>
          <td>${review.company_name}</td>
          <td>${review.user_name}</td>

          
         <td>
            ${review.comment}
            ${Array.isArray(review.media) && review.media.length > 0 ? `
              <hr>
              <div style="font-size:12px; color:#666; margin-bottom:6px;">
                Eklenen Dosyalar (${review.media.length})
              </div>
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                ${review.media.map((m, i) => `
                  ${m.type === 'image'
                    ? `<a href="${m.url}" target="_blank">
                          <img src="${m.url}"
                              style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                      </a>`
                    : `<a href="${m.url}" target="_blank">
                          <video src="${m.url}"
                                style="width:60px; height:60px; object-fit:cover; border-radius:6px;"></video>
                      </a>`
                  }
                `).join('')}
              </div>
            ` : ""}
          </td>


          <td>${review.rating}</td>
          ${tabType === "pending"
            ? `<td>
                <button class="btn btn-sm btn-primary" data-action="approve" data-id="${review.id}">Onayla</button>
                <button class="btn btn-sm btn-danger" data-action="reject" data-id="${review.id}">Reddet</button>
              </td>`
            : tabType === "rejected"
            ? `<td>
                <button class="btn btn-sm btn-warning" data-action="restore" data-id="${review.id}">Geri Al</button>
                <button class="btn btn-sm btn-secondary" data-action="delete" data-id="${review.id}">Sil</button>
              </td>`
            : ""}
        `;

        tableBody.appendChild(row);

        if (tabType === "pending") {

          row.querySelector('[data-action="approve"]').onclick = () => {
            if (confirm("Bu yorumu onaylamak istiyor musunuz?"))
              updateReviewStatus(review.id, 1);
          };

          row.querySelector('[data-action="reject"]').onclick = () => {
            if (confirm("Bu yorumu reddetmek istiyor musunuz?"))
              updateReviewStatus(review.id, 2);
          };
        }

        if (tabType === "rejected") {

          row.querySelector('[data-action="restore"]').onclick = () => {
            if (confirm("Bu yorumu geri almak istiyor musunuz?"))
              updateReviewStatus(review.id, 0);
          };

          row.querySelector('[data-action="delete"]').onclick = () => {
            if (confirm("Bu yorumu silmek istediğinize emin misiniz?"))
              deleteReview(review.id);
          };
        }

      });

      const totalPages = Math.ceil(total / perPage);

      if (totalPages > 1) {

        const btnGroup = document.createElement("div");
        btnGroup.className = "btn-group";

        for (let i = 1; i <= totalPages; i++) {
          const btn = document.createElement("button");
          btn.className = `btn btn-sm ${i === page ? "btn-primary" : "btn-outline-primary"}`;
          btn.textContent = i;
          btn.onclick = () => fetchReviews(tabType, i);
          btnGroup.appendChild(btn);
        }

        pagination.appendChild(btnGroup);
      }

    })
    .catch(err => console.error("Review API Hatası:", err));
}


/* =========================================================
   UPDATE / DELETE REVIEW
========================================================= */
function updateReviewStatus(reviewId, newStatus) {

  fetch("api/review-update-status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ id: reviewId, status: newStatus })
  })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        fetchReviews("pending", 1);
        alert("İşlem başarıyla tamamlandı.");
      } else {
        alert("İşlem başarısız.");
      }

    })
    .catch(() => alert("Sunucu hatası oluştu."));
}


function deleteReview(reviewId) {

  fetch("api/delete-review.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ id: reviewId })
  })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        fetchReviews("rejected", 1);
        alert("Yorum silindi.");
      } else {
        alert("Silme işlemi başarısız.");
      }

    })
    .catch(() => alert("Silme sırasında sunucu hatası."));
}


/* =========================================================
   USER REPORTS TAB
========================================================= */
function fetchReviewReports() {

  fetch("api/get-review-reports.php")
    .then(res => res.json())
    .then(res => {

      const tableBody = document.getElementById("reports-body");
      if (!tableBody) return;

      tableBody.innerHTML = "";

      const data = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);

      if (!res.success || data.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center">Şikayet bulunamadı.</td>
          </tr>`;
        return;
      }

      data.forEach(report => {

        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${report.created_at}</td>
          <td>${report.company_name}</td>
          <td>${report.review_user_name}</td>
          <td>${report.reported_by_name}</td>
          <td>${report.reason}</td>
          <td>
            <button class="btn btn-sm btn-danger"
              onclick="deleteReviewReport(${report.id})">
              Sil
            </button>
          </td>
        `;

        tableBody.appendChild(row);

      });

    })
    .catch(err => console.error("User Report API Hatası:", err));
}


function deleteReviewReport(id) {

  if (!confirm("Bu şikayeti silmek istediğinize emin misiniz?")) return;

  fetch("api/delete-review-report.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ id })
  })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        fetchReviewReports();
        alert("Şikayet silindi.");
      } else {
        alert("Silme işlemi başarısız.");
      }

    })
    .catch(() => alert("Sunucu hatası."));
}


/* =========================================================
   BUSINESS REPORTS TAB
========================================================= */
function fetchBusinessReports() {

  fetch("api/get-business-reports.php")
    .then(res => res.json())
    .then(res => {

      const tableBody = document.getElementById("business-reports-body");
      if (!tableBody) return;

      tableBody.innerHTML = "";

      const data = res.data || [];

      if (!res.success || data.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center">İşletme şikayeti bulunamadı.</td>
          </tr>`;
        return;
      }

      data.forEach(report => {

        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${report.created_at}</td>
          <td>${report.company_name}</td>
          <td>${report.review_user_name}</td>
          <td>${report.reason}</td>
          <td>${report.status}</td>
          <td>
            <button class="btn btn-sm btn-danger"
              onclick="deleteBusinessReport(${report.id})">
              Sil
            </button>
          </td>
        `;

        tableBody.appendChild(row);

      });

    })
    .catch(err => console.error("Business Report API Hatası:", err));
}


function deleteBusinessReport(id) {

  if (!confirm("Bu şikayeti silmek istediğinize emin misiniz?")) return;

  fetch("api/delete-business-report.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ id })
  })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        fetchBusinessReports();
        alert("Şikayet silindi.");
      } else {
        alert("Silme işlemi başarısız.");
      }

    })
    .catch(() => alert("Sunucu hatası."));
}



