<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Admin adı ve bildirim sayısı
$admin_id = $_SESSION['admin_id'];
$admin_name = 'Admin';
$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    $admin_name = $admin['full_name'];
}
$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Blog Yönetimi</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">
 
  <!-- Quill CSS -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">


    <style>
      #toolbar {
        border: 1px solid #ccc;
        padding: 8px;
        margin-bottom: 5px;
        background: #f1f1f1;
      }
    
      #toolbar button {
        margin-right: 5px;
        padding: 5px 8px;
        font-size: 14px;
        border: none;
        background: #fff;
        cursor: pointer;
      }
    
      #editor {
        min-height: 200px;
        border: 1px solid #ccc;
        padding: 10px;
        background: #fff;
        font-size: 14px;
      }
    
      #editor:focus {
        outline: none;
        border-color: #007bff;
      }
    </style>


</head>
<body id="page-top">
<div id="wrapper">

<!-- Sidebar -->
<?php include('admin-sidebar.php'); ?>
<!-- Sidebar -->



<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

<?php include('includes/topbar.php'); ?>


<!-- =================================== -->

            <div id="content-wrapper" class="d-flex flex-column">
              <div id="content">
                <div class="container-fluid">
                  <h1 class="h3 mb-4 text-gray-800">Blog Yönetimi</h1>
            
                  <!-- Sekmeler -->
                  <ul class="nav nav-tabs mb-4" id="blogTabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab">Blog Listesi</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="add-tab" data-toggle="tab" href="#add" role="tab">Yeni Blog Ekle</a>
                    </li>
                  </ul>
            
                  <div class="tab-content" id="blogTabsContent">
            
                    <!-- Blog Listesi -->
                    <div class="tab-pane fade show active" id="list" role="tabpanel">
                      <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary">
                          <h6 class="m-0 font-weight-bold text-white">Blog Yazıları</h6>
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                              <thead class="thead-light">
                                <tr>
                                  <th>Başlık</th>
                                  <th>Tarih</th>
                                  <th style="width: 1%; white-space: nowrap;">İşlem</th>
                                </tr>
                              </thead>
                              <tbody id="blogTableBody">
                                
                                 <!-- DB ile doluyor -->

                              </tbody>
 
                            </table>
                            
                            <div id="pagination" class="mt-3 text-center"></div>
                          </div>
                        </div>
                      </div>
                    </div>
            
                    <!-- Blog Ekle -->
                    <div class="tab-pane fade" id="add" role="tabpanel">
                      <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success">
                          <h6 class="m-0 font-weight-bold text-white">Yeni Blog Yazısı</h6>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="api/blog-create.php" enctype="multipart/form-data">
                          <div class="form-group">
                            <label for="blogTitle">Başlık</label>
                            <input type="text" class="form-control" id="blogTitle" name="title" required>
                          </div>
                          <div class="form-group">
                            <label for="blogImage">Kapak Görseli (800x533)</label>
                            <input type="file" class="form-control-file" id="blogImage" name="image" accept="image/*" required>
                          </div>
                         <div class="form-group">
                          <label for="blogEditor">İçerik</label>
                          <div id="quill-editor" style="height: 250px;"></div>
                          <textarea id="blogContent" name="content" style="display:none;"></textarea>
                        </div>
                          <input type="hidden" name="status" value="1">
                          <button type="submit" class="btn btn-success">Yayınla</button>
                        </form>

                        </div>
                      </div>
                    </div>
            
                  </div>
                </div>
              </div>
            </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>© <?php echo date('Y'); ?> Puandeks</span>
                </div>
            </div>
        </footer>
    </div>
</div>


<!-- ==================== BLOG DÜZENLEME MODAL ==================== -->
<div class="modal fade" id="editBlogModal" tabindex="-1" role="dialog" aria-labelledby="editBlogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="editBlogForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="editBlogModalLabel">Blog Yazısını Düzenle</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="editBlogId">

          <div class="form-group">
            <label>Başlık</label>
            <input type="text" class="form-control" name="title" id="editBlogTitle" required>
          </div>

          <div class="form-group">
            <label>Mevcut Görsel</label><br>
            <img id="editBlogImagePreview" src="" alt="Görsel" style="max-height: 150px;">
          </div>

          <div class="form-group">
            <label>Yeni Görsel</label>
            <input type="file" class="form-control-file" name="image" id="editBlogImageInput" accept="image/*">
          </div>

          <div class="form-group">
            <label>İçerik</label>
            <div id="editQuill" style="height: 250px;"></div>
            <textarea name="content" id="editBlogContent" style="display:none;"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
          <button type="submit" class="btn btn-warning">Güncelle</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- ==================== /BLOG DÜZENLEME MODAL ==================== -->


<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
  

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
  const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'İçeriği buraya yazın...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link']
      ]
    }
  });

  document.querySelector('form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const html = quill.root.innerHTML;
    document.getElementById('blogContent').value = html;

    const formData = new FormData(this);
    try {
      const response = await fetch('api/blog-create.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();

      console.log(result); 

      if (result.success) {
        alert(' Blog başarıyla eklendi.');
        this.reset();
        quill.root.innerHTML = '';
      } else {
        alert('❌ Hata: ' + result.message + '\n\n' + JSON.stringify(result, null, 2));
      }
    } catch (error) {
      alert('❌ Sistem hatası: ' + error.message);
    }
  });
</script>


<!-- BLOG LİSTELEME  -->
<script>
function loadBlogs(page = 1) {
  fetch(`api/blog-list.php?page=${page}`)
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert('Blog listesi alınamadı.');
        return;
      }

      const tbody = document.getElementById('blogTableBody');
      tbody.innerHTML = '';

      data.blogs.forEach(blog => {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${blog.title}</td>
    <td>${blog.created_at}</td>
    <td style="white-space: nowrap;">
      <button class="btn btn-sm btn-primary" onclick="editBlog(${blog.id})">Düzenle</button>
      <button class="btn btn-sm btn-danger" onclick="confirmDelete(${blog.id})">Sil</button>
      <a href="/blog-post.php?id=${blog.id}" target="_blank" class="btn btn-sm btn-info">Oku</a>
    </td>
  `;
  tbody.appendChild(row); 
});


      renderPagination(data.total, page);
    })
    .catch(error => {
      console.error(error);
      alert('Hata oluştu.');
    });
}

// SAYFALAMA 
function renderPagination(total, currentPage) {
  const pageSize = 10;
  const totalPages = Math.ceil(total / pageSize);
  const container = document.getElementById('pagination');
  container.innerHTML = '';

  if (totalPages <= 1) return;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement('button');
    btn.textContent = i;
    btn.className = 'btn btn-sm mx-1 ' + (i === currentPage ? 'btn-primary' : 'btn-outline-primary');
    btn.onclick = () => loadBlogs(i);
    container.appendChild(btn);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadBlogs(1);
});

// SİLME
function confirmDelete(blogId) {
  const confirmed = confirm("Bu blog yazısın silmek üzeresiniz!\n\n Bu işlem geri alınamaz ve blog kalıcı olarak silinir.\n\nEmin misiniz?");
  if (!confirmed) return;

  fetch(`api/blog-delete.php?id=${blogId}`, {
    method: 'GET'
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      alert("✅ Blog başarıyla silindi.");
      loadBlogs(1); // yeniden yükle
    } else {
      alert("❌ Hata: " + result.message);
    }
  })
  .catch(error => {
    console.error(error);
    alert("❌ Sistem hatası.");
  });
}

</script>
<!-- BLOG LİSTELEME -->

<!-- BLOG DÜZENLEME JS  -->
<script>
let editQuill;

document.addEventListener('DOMContentLoaded', () => {
  editQuill = new Quill('#editQuill', {
    theme: 'snow',
    placeholder: 'İçeriği buraya yazın...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link']
      ]
    }
  });
});

function editBlog(id) {
  fetch(`api/blog-get.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert("Blog verisi alınamadı.");
        return;
      }

      const blog = data.blog;

      document.getElementById('editBlogId').value = blog.id;
      document.getElementById('editBlogTitle').value = blog.title;
      document.getElementById('editBlogImagePreview').src = blog.image || '';
      editQuill.root.innerHTML = blog.content || '';

      $('#editBlogModal').modal('show');
    })
    .catch(err => {
      console.error(err);
      alert("Veri alınırken hata oluştu.");
    });
}
</script>
<!-- BLOG DÜZENLEME JS  -->

<!-- BLOG GÜNCELLEME JS  -->
<script>
document.getElementById('editBlogForm').addEventListener('submit', function (e) {
  e.preventDefault();

  // Quill ieriğini textarea’ya kopyala
  const contentHTML = editQuill.root.innerHTML;
  document.getElementById('editBlogContent').value = contentHTML;

  const formData = new FormData(this);

  fetch('api/blog-update.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert("✅ Blog başarıyla güncellendi.");
        $('#editBlogModal').modal('hide');
        loadBlogs(1); // tabloyu yeniden yükle
      } else {
        alert("❌ Hata: " + result.message);
      }
    })
    .catch(error => {
      console.error(error);
      alert(" Sistem hatası.");
    });
});
</script>
<!-- BLOG GÜNCELLEME JS  -->


 

</body>
</html>
