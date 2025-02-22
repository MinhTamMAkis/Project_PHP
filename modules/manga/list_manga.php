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
    <div class="option_table" style="display: flex; justify-content: space-between;">
        <h4>Danh sách Manga</h4>
        <div>
        <form id="importForm" enctype="multipart/form-data">
            <input type="file" id="csvFile" name="csvFile" accept=".csv">
            <button type="submit">Import CSV</button>
        </form>
        <button id="exportBtn">Export CSV</button>
    </div>
        <div class="dropdown">
        <p>show more table </p>
        <button class="dropbtn">Select Show</button>
        <div class="dropdown-content">
        <input type="checkbox" id="actionsColumn" > Action
        <input type="checkbox" id="statusColumn" > Status
        <input type="checkbox" id="dueDateColumn" > Due Date
        </div>
    </div>
        <button id="showAddForm">+ Thêm Manga</button>
    </div>

    <!-- Form Thêm Manga -->
    <div id="addMangaForm" style="display: none;">
        <h3>Thêm Manga Mới</h3>
        <form id="addForm">
            <input type="text" id="nam_m" name="nam_m" placeholder="Tên Manga" required>
            <input type="text" id="tac_gia" name="tac_gia" placeholder="Tác Giả" required>
            <input type="file" id="image" name="image" accept="image/*" required>
            <button type="submit">Thêm Manga</button>
            <button type="button" id="cancelAdd">Hủy</button>
        </form>
    </div>

    <!-- Form Cập Nhật Manga -->
    <div id="updateMangaForm" style="display: none;">
        <h3>Cập Nhật Manga</h3>
        <form id="updateForm">
            <input type="hidden" id="update_id" name="id">
            <input type="text" id="update_nam_m" name="nam_m" placeholder="Tên Manga" required>
            <input type="text" id="update_tac_gia" name="tac_gia" placeholder="Tác Giả" required>
            <input type="file" id="update_image" name="image" accept="image/*">
            <button type="submit">Cập Nhật</button>
            <button type="button" id="cancelUpdate">Hủy</button>
        </form>
    </div>

    <table id="mangasTable">
        <thead>
            <tr>
                <th>Tên Manga</th>
                <th>Tác Giả</th>
                <th>Ảnh Bìa</th>
                
                <th  class="actions-column" style="display: none;" >Hành Động</th>
                
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    var _WEB_HOST = "<?php echo _WEB_HOST; ?>";
    var module = '<?php echo $module; ?>';

    function fetchMangas() {
    new TablePagination({
        url: _WEB_HOST + '/modules/' + module + '/get_manga.php',
        tableId: '#mangasTable',
        paginationId: '#pagination',
        currentPage: 1,
        buttonupdate: true,
        buttondelete: true,
        columns: [
            { key: 'nam_m' },
            { key: 'tac_gia' },
            { key: 'image' },
            { key: 'actions', columnClass: 'actions-column', checkboxId: 'actionsColumn' }
        ]
    });
}
function showAlert(message) {
    $("#alertBox").text(message).fadeIn();

    // Ẩn thông báo sau 3 giây
    setTimeout(() => {
        $("#alertBox").fadeOut();
    }, 3000);
}
// Gọi fetchMangas() khi trang tải xong
document.addEventListener('DOMContentLoaded', fetchMangas);





    // Hiển thị Form Thêm
    $("#showAddForm").click(() => {
        $("#addMangaForm").show();
    });
    $("#cancelAdd").click(() => {
        $("#addMangaForm").hide();
    });
    $("#cancelUpdate").click(() => {
        $("#updateMangaForm").hide();
    });

    // Xử lý Thêm Manga
    $("#addForm").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: _WEB_HOST + '/modules/' + module + '/get_manga.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log("Response từ server:", response); // Kiểm tra dữ liệu trả về
                alert(response.message);
                fetchMangas();
                $("#addMangaForm").hide();

            }
        });
    });

    // Xử lý Cập Nhật Manga
    $(document).on('click', '.update-btn', function () {
        const id = $(this).data('id');
    const name = $(this).data('name');
    const author = $(this).data('author');
    const image = $(this).data('image');

    console.log("ID được lấy:", id); // Debug xem id có đúng không

    if (!id) {
        alert("Không tìm thấy ID, vui lòng thử lại!");
        return;
    }

    // Hiển thị form cập nhật và điền dữ liệu
    $("#updateMangaForm").show();
    $("#update_id").val(id);
    $("#update_nam_m").val(name);
    $("#update_tac_gia").val(author);

    // Nếu có ảnh, hiển thị ảnh hiện tại
    if (image) {
        $("#update_image_preview").attr("src", _WEB_HOST + "/uploads/" + image).show();
    } else {
        $("#update_image_preview").hide();
    }
});
$("#updateForm").submit(function (e) {
    e.preventDefault();
    
    let id = $("#update_id").val();
    if (!id) {
        alert("ID Manga không hợp lệ, vui lòng thử lại!");
        return;
    }

    let formData = new FormData(this);
    $.ajax({
        url: _WEB_HOST + '/modules/' + module + '/get_manga.php',
        type: "POST", // Không phải PUT vì FormData không hỗ trợ PUT
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            alert(response.message);
            fetchMangas();
            $("#updateMangaForm").hide();
        }
    });
});

    // Xóa Manga
    $(document).on("click", ".delete-btn", function () {
    let id = $(this).data("id");
    console.log("ID được lấy:", id); // Kiểm tra ID

    if (!id) {
        alert("ID không hợp lệ!");
        return;
    }

    if (confirm("Bạn có chắc chắn muốn xóa manga này?")) {
        $.ajax({
    url: _WEB_HOST + "/modules/" + module + "/get_manga.php?id=" + id,
    type: "DELETE",
    success: function (response) {
        console.log("Response từ server:", response); // In response để kiểm tra

        if (response.success) {
            alert(response.message || "Xóa thành công!");
            fetchMangas();
        } else {
            alert(response.error || "Lỗi khi xóa manga!");
        }
    },
    error: function (xhr, status, error) {
        console.error("Lỗi AJAX:", status, error, "Response:", xhr.responseText);
        alert("Lỗi khi xóa manga! Kiểm tra console.");
    }
});

    }
});




</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Xử lý Import
    document.getElementById('importForm').addEventListener('submit', function (e) {
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
    document.getElementById('exportBtn').addEventListener('click', function () {
        window.location.href = _WEB_HOST + '/modules/' + module + '/export.php';
    });
});

</script>
<?php
Layouts('footer-admin', $title);
?>
