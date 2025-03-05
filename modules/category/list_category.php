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
        
        <button id="showAddForm" onclick="openAddCategoryPopup()">+ Thêm Manga</button>
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
        
       
    </div>

    <table id="mangasTable">
        
        <thead>
            <tr>
              
            </tr>
        </thead>
        <tbody>
            <!-- Dữ liệu sẽ được cập nhật bởi TablePagination -->
        </tbody>
    </table>
    <div id="paginationContainer"></div>

</div>
    <!-- Form Thêm Manga -->
<div id="addCategoryPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closeAddCategoryPopup()">&times;</span>
            <h3>Add New Category</h3>
            <form id="addForm">
                <input type="text" id="name_category" name="name_category" placeholder="Name Category" required>
                <input type="text" id="slug_category" name="slug_category" placeholder="Slug Category" required>
                <div>
                    <select id="status" name="status" required>
                        <option value="0">Active</option>
                        <option value="1">Inactive</option>
                    </select>
                </div>
                <textarea   id="note_category" name="note_category" rows="4" cols="50" required lang="vi" dir="ltr"></textarea>

                <button type="submit"  class="submit_popup">Add more category</button>
                <button type="button" id="cancelAdd" class="cancel_popup" onclick="closeAddCategoryPopup()">Cancel</button>
            </form>
        </div>
    </div>
    <div id="categoryDetailPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2>Category Detail</h2>

            <p><strong>Name:</strong>
                <span id="popupCategoryName"></span>
                <input type="text" id="editCategoryName" style="display: none;">
            </p>

            <p><strong>Slug:</strong>
                <span id="popupCategorySlug"></span>
                <input type="text" id="editCategorySlug" style="display: none;">
            </p>
            <div>
                    <select id="status" name="status" required>
                        <option value="0">Active</option>
                        <option value="1">Inactive</option>
                    </select>
                </div>
            <textarea   id="editNoteCategory"  rows="4" cols="50" required lang="vi" dir="ltr"></textarea>
            <div class="btn-action">
                <button class="submit-btn" id="updateCategoryBtn" onclick="toggleEditMode()">Cập Nhật</button>
                <button class="delete-btn" id="deleteCategoryBtn">Xóa</button>
            </div>
          
        </div>
    </div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var _WEB_HOST = "<?php echo _WEB_HOST; ?>";
var module = '<?php echo $module; ?>';


let tablePaginationInstance; // Biến toàn cục để lưu instance của TablePagination

function fetchMangas(searchKey = '') {
    console.log('fetchMangas called with searchKey:', searchKey); // Debug

    let url = _WEB_HOST + '/modules/' + module + '/api_category.php';

    // Tạo instance của TablePagination và lưu vào biến toàn cục
    tablePaginationInstance = new TablePagination({
        url: url,
        tableId: '#mangasTable',
        paginationId: '#paginationContainer',
        currentPage: 1,
        buttonupdate: true,
        buttondelete: true,
        enableSearch: true, // Kích hoạt tính năng tìm kiếm
        columns: [
            {
                key: 'name_category',
                label: 'Name Category',
                sortable: true,
                width: '20%'
            },
            {
                key: 'slug_category',
                label: 'Slug Category',
                sortable: true,
                width: '20%'
            },
            {
                key: 'status',
                label: 'Status',
                width: '10%',
                sortable: false
            },
            {
                key: 'note_category',
                label: 'Note Category',
                width: '0%',
                sortable: false,
                toggleable: true,
                columnClass: 'note-column',
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
function showTooltip() {
        // Hiển thị tooltip
        document.getElementById("tooltip").style.display = "block";
    }

    function hideTooltip() {
        // Ẩn tooltip khi người dùng rời khỏi ô input
        document.getElementById("tooltip").style.display = "none";
    }
    

// Gọi fetchMangas() khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    fetchMangas(); // Gọi dữ liệu mặc định khi trang tải xong
    new SlugGenerator("#name_category", "#slug_category");
    new SlugGenerator("#editCategoryName", "#editCategorySlug");
    
});

function showAlert(message) {
    $("#alertBox").text(message).fadeIn();

    // Ẩn thông báo sau 3 giây
    setTimeout(() => {
        $("#alertBox").fadeOut();
    }, 3000);
}

$(document).on('click', '.view-btn', function() {
    // Lấy dữ liệu từ hàng (row) được chọn
    const id = $(this).data('id'); // Lấy ID từ thuộc tính data-id
    const name = $(this).closest('tr').find('td:nth-child(1)').text().trim(); // Lấy tên từ cột đầu tiên
    const slug = $(this).closest('tr').find('td:nth-child(2)').text().trim(); // Lấy slug từ cột thứ hai
    const status = $(this).closest('tr').find('td:nth-child(3)').text().trim(); // Lấy trạng thái từ cột thứ ba
    const note = $(this).closest('tr').find('td:nth-child(4)').text().trim(); // Lấy ghi chú từ cột thứ tư

    // Hiển thị dữ liệu lên popup
    $('#popupCategoryName').text(name); // Hiển thị tên
    $('#popupCategorySlug').text(slug); // Hiển thị slug
    $('#status').val(status === "Active" ? "0" : "1"); // Đặt giá trị cho dropdown trạng thái
    $('#editNoteCategory').val(note); // Đặt giá trị cho textarea ghi chú

    // Lưu ID vào popup để sử dụng khi cập nhật
    $('#categoryDetailPopup').data('id', id);

    // Hiển thị popup
    $('#categoryDetailPopup').show();
});

function openAddCategoryPopup() {
    document.getElementById("addCategoryPopup").style.display = "block";
}

function closeAddCategoryPopup() {
    document.getElementById("addCategoryPopup").style.display = "none";
}
function openFilePopup() {
    document.getElementById("filePopup").style.display = "block";
}
function closePopupFile() {
    document.getElementById("filePopup").style.display = "none";
}
function closePopup() {
    resetEditMode();
    $('#categoryDetailPopup').hide();
}
function resetEditMode() {
    // Hiển thị lại các trường văn bản
    $('#popupCategoryName').show();
    $('#popupCategorySlug').show();
    // Ẩn các trường nhập liệu
    $('#editCategoryName').hide();
    $('#editCategorySlug').hide();
    // Đặt lại nút "Cập Nhật" về trạng thái ban đầu
    $('#updateCategoryBtn').text("Cập Nhật");
}
// Xử lý Thêm Manga

$("#addForm").submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: _WEB_HOST + '/modules/' + module + '/api_category.php',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("Response từ server:", response);
            alert(response.message);
            fetchMangas();
            closeAddCategoryPopup();
        }
    });
});

$(document).on("click", ".delete-btn, #deleteCategoryBtn", function() {
    let id = $(this).data("id") || $(".view-btn").data("id");
    if (!id) {
        alert("ID không hợp lệ!");
        return;
    }

    if (confirm("Bạn có chắc chắn muốn xóa manga này?")) {
        $.ajax({
            url: _WEB_HOST + "/modules/" + module + "/api_category.php?id=" + id,
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


// Update
// Toggle Edit Mode
function toggleEditMode() {
    let button = $('#updateCategoryBtn');
    let isEditing = button.text() === "Lưu";

    if (isEditing) {
        // Lưu dữ liệu
        let updatedName = $('#editCategoryName').val()?.trim();
        let updatedSlug = $('#editCategorySlug').val()?.trim();
        let updatedStatus = $('#status').val();
        let updatedNote = $('#editNoteCategory').val()?.trim();

        console.log("Dữ liệu khi lưu:", {
            updatedName,
            updatedSlug,
            updatedStatus,
            updatedNote
        });

        if (!updatedName || !updatedSlug) {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }

        // Hiển thị lại dữ liệu
        $('#popupCategoryName').text(updatedName).show();
        $('#popupCategorySlug').text(updatedSlug).show();
        $('#editCategoryName').hide();
        $('#editCategorySlug').hide();
        button.text("Cập Nhật");

        updateCategory(updatedName, updatedSlug, updatedStatus, updatedNote);
    } else {
        // Chuyển sang chế độ chỉnh sửa
        let currentName = $('#popupCategoryName').text().trim();
        let currentSlug = $('#popupCategorySlug').text().trim();
        let currentStatus = $('#status').val();
        let currentNote = $('#editNoteCategory').val()?.trim();

        console.log("Chuyển sang chế độ chỉnh sửa:", {
            currentName,
            currentSlug,
            currentStatus,
            currentNote
        });

        if (!currentName || !currentSlug) {
            alert("Lỗi: Không tìm thấy dữ liệu!");
            return;
        }

        $('#editCategoryName').val(currentName).show();
        $('#editCategorySlug').val(currentSlug).show();
        $('#popupCategoryName').hide();
        $('#popupCategorySlug').hide();
        $('#status').val(currentStatus);
        $('#editNoteCategory').val(currentNote);
        button.text("Lưu");
    }
}

// Hàm cập nhật danh mục
function updateCategory(name, slug, status, note) {
    let id = $('#categoryDetailPopup').data('id');

    console.log("Dữ liệu gửi đi:", {
        id,
        name,
        slug,
        status,
        note
    });

    if (!id || !name || !slug) {
        alert("Dữ liệu không hợp lệ!");
        return;
    }

    let formData = new FormData();
    formData.append('id', id);
    formData.append('name', name);
    formData.append('slug', slug);
    formData.append('status', status);
    formData.append('note', note);

    $.ajax({
        url: _WEB_HOST + '/modules/' + module + '/api_category.php', // Thay đổi URL tương ứng
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("Phản hồi từ server:", response);
            if (response.success) {
                alert("Cập nhật thành công!");
                fetchCategories(); // Gọi lại hàm để cập nhật danh sách danh mục
                $('#categoryDetailPopup').hide();
            } else {
                alert(response.error || "Lỗi khi cập nhật!");
            }
        },
        error: function() {
            alert("Lỗi khi cập nhật danh mục!");
        }
    });
}

</script>
<?php
Layouts('footer-admin', $title);
?>