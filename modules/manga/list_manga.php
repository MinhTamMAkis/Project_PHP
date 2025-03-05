<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Manga List'
];
Layouts('header-admin', $title);

// Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
    redirect('?module=auth&action=login');
}
?>

<div class="table_list">
    <div  class="page_title">
        <h4>Danh sách Manga</h4>
    </div>
    <div class="option_table" style="display: flex;">
        
        <button id="showAddForm" onclick="openAddMangaPopup()">+ Thêm Manga</button>
        <div id="filePopup" class="popup" style="display: none;">
            <div class="popup-content " style="display: flex;">
                <span class="close-btn" onclick="closePopupFile()">&times;</span>
                <h3>Import/Export</h3>
                <form id="importForm" enctype="multipart/form-data" style="display: flex;">
                    <input type="file" id="csvFile" name="csvFile" accept=".csv">
                    <button type="submit">Import </button>
                </form>
                <button id="exportBtn">Export </button>

            </div>
        </div>
        <button id="showFilePopup" onclick="openFilePopup()">File</button>
        <div class="search-container">
        <div id="tooltip" class="tooltip">Vui lòng nhập từ khóa bạn muốn tìm kiếm vào ô này.</div>

        <input type="text" id="searchInput1" placeholder="Nhập từ khóa 1"  onfocus="showTooltip()" onblur="hideTooltip()">
        <button id="searchBtn" onclick="searchManga()">Tìm kiếm</button>
                    
        </div>
       
    </div>

    <table id="mangasTable">
        
        <thead>
            <tr>
                <th style="width: 25%;">Tên Manga</th>
                <th style="width: 25%;">Tác Giả</th>
                <th style="width: 0%;">Ảnh Bìa</th>
                <th class="actions-column" style="display: none;">Hành Động</th>
                
            </tr>
        </thead>
        <tbody>
            <!-- Dữ liệu sẽ được cập nhật bởi TablePagination -->
        </tbody>
    </table>
    

    <div id="pagination">
        <button id="prevPage">Previous</button>
        <span id="pageInfo"></span>
        <button id="nextPage">Next</button>
    </div>
</div>
    <!-- Form Thêm Manga -->
<div id="addMangaPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closeAddMangaPopup()">&times;</span>
            <h3>Add New Manga</h3>
            <form id="addForm">
                <input type="text" id="nam_m" name="nam_m" placeholder="Tên Manga" required>
                <input type="text" id="tac_gia" name="tac_gia" placeholder="Tác Giả" required>
                <input type="file" id="image" name="image" accept="image/*" required>
                <button type="submit" class="submit_popup">Thêm Manga</button>
                <button type="button" id="cancelAdd" class="cancel_popup" onclick="closeAddMangaPopup()">Hủy</button>
            </form>
        </div>
    </div>
    <div id="mangaDetailPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2>Chi tiết Manga</h2>

            <p><strong>Tên:</strong>
                <span id="popupMangaName"></span>
                <input type="text" id="editMangaName" style="display: none;">
            </p>

            <p><strong>Tác giả:</strong>
                <span id="popupMangaAuthor"></span>
                <input type="text" id="editMangaAuthor" style="display: none;">
            </p>

            <img id="popupMangaImage" src="" alt="Ảnh bìa" style="max-width: 200px;">
            <input type="file" id="editMangaImage" style="display: none;">
            <div class="btn-action">
                <button class="submit-btn" id="updateMangaBtn" onclick="toggleEditMode()">Cập Nhật</button>
                <button class="delete-btn" id="deleteMangaBtn">Xóa</button>
            </div>
          
        </div>
    </div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var _WEB_HOST = "<?php echo _WEB_HOST; ?>";
var module = '<?php echo $module; ?>';

function showTooltip() {
        // Hiển thị tooltip
        document.getElementById("tooltip").style.display = "block";
    }

    function hideTooltip() {
        // Ẩn tooltip khi người dùng rời khỏi ô input
        document.getElementById("tooltip").style.display = "none";
    }
function searchManga() {
    searchKey = document.getElementById('searchInput1').value.trim();
    fetchMangas(searchKey);
}

function fetchMangas(searchKey='') {
    let url = _WEB_HOST + '/modules/' + module + '/get_manga.php';
    if (searchKey.trim() !== '') {
        url += `?searchkey=${encodeURIComponent(searchKey)}`;
    }
    new TablePagination({
        url: url,
        tableId: '#mangasTable',
        paginationId: '#pagination',
        currentPage: 1,
        buttonupdate: true,
        buttondelete: true,
        columns: [
    {
        key: 'nam_m',
        label: 'Tên Manga',
        sortable: true,
        width: '20%'
    },
    {
        key: 'tac_gia',
        label: 'Tác Giả',
        sortable: true,
        width: '20%'
    },
    {
        key: 'image',
        label: 'Ảnh',
        width: '0%',
        sortable: false
    },
    {
        key: 'actions',
        label: 'Actions',
        columnClass: 'actions-column',
        toggleable: true,
        width: '10%'
    },
    {
        key: 'status',
        label: 'Status',
        columnClass: 'status-column',
        toggleable: true,
        width: '10%'
    }
],
        actionButtons: (item) => {
            const readBtn = document.createElement('button');
            readBtn.textContent = 'Add Chapter';
            readBtn.classList.add('addchapter-btn');
            readBtn.dataset.id = item.id;

            readBtn.addEventListener('click', () => {
                alert(`Đọc manga có ID: ${item.id}`);
            });

            return [readBtn];
        }
    });
}

// Gọi fetchMangas() khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    fetchMangas(); // Gọi dữ liệu mặc định khi trang tải xong

    // Lắng nghe sự kiện Enter trong cả hai ô tìm kiếm
    document.getElementById('searchInput1').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            searchManga();
        }
    });

    
});

function showAlert(message) {
    $("#alertBox").text(message).fadeIn();

    // Ẩn thông báo sau 3 giây
    setTimeout(() => {
        $("#alertBox").fadeOut();
    }, 3000);
}

$(document).on('click', '.view-btn', function() {
    const id = $(this).data('id');
    const name = $(this).closest('tr').find('td:nth-child(1)').text().trim();
    const author = $(this).closest('tr').find('td:nth-child(2)').text().trim();
    const imageSrc = $(this).closest('tr').find('img').attr('src');

    $('#popupMangaName').text(name);
    $('#popupMangaAuthor').text(author);
    $('#popupMangaImage').attr('src', imageSrc);

    $('#mangaDetailPopup').data('id', id); // 🟢 Lưu ID vào popup
    $('#mangaDetailPopup').show();
});

function openAddMangaPopup() {
    document.getElementById("addMangaPopup").style.display = "block";
}

function closeAddMangaPopup() {
    document.getElementById("addMangaPopup").style.display = "none";
}
function openFilePopup() {
    document.getElementById("filePopup").style.display = "block";
}
function closePopupFile() {
    document.getElementById("filePopup").style.display = "none";
}
function closePopup() {
    $('#mangaDetailPopup').hide();
}


function toggleEditMode() {
    let button = $('#updateMangaBtn');
    let isEditing = button.text() === "Lưu";

    if (isEditing) {
        // Lưu dữ liệu
        let updatedName = $('#editMangaName').val()?.trim();
        let updatedAuthor = $('#editMangaAuthor').val()?.trim();
        let updatedImage = $('#editMangaImage')[0]?.files[0];

        console.log("Dữ liệu khi lưu:", {
            updatedName,
            updatedAuthor,
            updatedImage
        });

        if (!updatedName || !updatedAuthor) {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }

        // Hiển thị lại dữ liệu
        $('#popupMangaName').text(updatedName).show();
        $('#popupMangaAuthor').text(updatedAuthor).show();
        $('#editMangaName').hide();
        $('#editMangaAuthor').hide();
        $('#editMangaImage').hide();
        button.text("Cập Nhật");

        updateManga(updatedImage);
    } else {
        // Chuyển sang chế độ chỉnh sửa
        let currentName = $('#popupMangaName').text().trim();
        let currentAuthor = $('#popupMangaAuthor').text().trim();

        console.log("Chuyển sang chế độ chỉnh sửa:", {
            currentName,
            currentAuthor
        });

        if (!currentName || !currentAuthor) {
            alert("Lỗi: Không tìm thấy dữ liệu!");
            return;
        }

        $('#editMangaName').val(currentName).show();
        $('#editMangaAuthor').val(currentAuthor).show();
        $('#popupMangaName').hide();
        $('#popupMangaAuthor').hide();
        $('#editMangaImage').show();
        button.text("Lưu");
    }
}

function updateManga(imageFile) {
    let id = $('#mangaDetailPopup').data('id');
    let nameInput = $('#editMangaName').val()?.trim();
    let authorInput = $('#editMangaAuthor').val()?.trim();

    console.log("Dữ liệu gửi đi:", {
        id,
        nameInput,
        authorInput,
        imageFile
    });

    if (!id || !nameInput || !authorInput) {
        alert("Dữ liệu không hợp lệ!");
        return;
    }

    let formData = new FormData();
    formData.append('id', id);
    formData.append('nam_m', nameInput);
    formData.append('tac_gia', authorInput);
    if (imageFile) {
        formData.append('image', imageFile);
    }

    $.ajax({
        url: _WEB_HOST + '/modules/' + module + '/get_manga.php',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("Phản hồi từ server:", response);
            if (response.success) {
                alert("Cập nhật thành công!");
                fetchMangas();
                $('#mangaDetailPopup').hide();
            } else {
                alert(response.error || "Lỗi khi cập nhật!");
            }
        },
        error: function() {
            alert("Lỗi khi cập nhật manga!");
        }
    });
}


$(document).on("click", ".delete-btn, #deleteMangaBtn", function() {
    let id = $(this).data("id") || $(".view-btn").data("id");
    if (!id) {
        alert("ID không hợp lệ!");
        return;
    }

    if (confirm("Bạn có chắc chắn muốn xóa manga này?")) {
        $.ajax({
            url: _WEB_HOST + "/modules/" + module + "/get_manga.php?id=" + id,
            type: "DELETE",
            success: function(response) {
                if (response.success) {
                    alert(response.message || "Xóa thành công!");
                    fetchMangas();
                    closePopup();
                } else {
                    alert(response.error || "Lỗi khi xóa manga!");
                }
            },
            error: function(xhr, status, error) {
                alert("Lỗi khi xóa manga! Kiểm tra console.");
            }
        });
    }
});






// Xử lý Thêm Manga

$("#addForm").submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: _WEB_HOST + '/modules/' + module + '/get_manga.php',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("Response từ server:", response);
            alert(response.message);
            fetchMangas();
            closeAddMangaPopup();
        }
    });
});

// Xử lý Cập Nhật Manga
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Xử lý Import
    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch(_WEB_HOST + '/modules/' + module + '/import.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // Load lại bảng sau khi import thành công
            })
            .catch(error => console.error('Lỗi:', error));
    });

    // Xử lý Export
    document.getElementById('exportBtn').addEventListener('click', function() {
        window.location.href = _WEB_HOST + '/modules/' + module + '/export.php';
    });
});
</script>
<?php
Layouts('footer-admin', $title);
?>