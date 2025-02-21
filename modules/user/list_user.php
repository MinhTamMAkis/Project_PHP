<?php
if(!defined('_CODE')){
    die('Access denied...');
}

$title = [
    'pageTitle' => 'USER'
];
Layouts('header-admin',$title);

// Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
    redirect('?module=auth&action=login');
}
$sql = "SELECT name, email, id FROM users";
$result = getRows($sql); 
?>


<div class="table_list">
    <div class="option_table" style="display: flex;">
        <h4>Active Projects</h4>
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
        <input type="checkbox" id="assigneeColumn" > Assignee
        <input type="checkbox" id="statusColumn" > Status
        <input type="checkbox" id="dueDateColumn" > Due Date
        </div>
    </div>
    </div>

    <div class="option_for_table">
        <!-- <div class="inputBox">
        <input required="" type="text">
        <span>First name</span>
        </div> -->

         <!-- Dropdown để chọn cột -->
    
    </div>
   

    <table id="usersTable">
    <thead>
    <tr class="top-table">
        <th class="name-column">Project Name <i></i></th>
        <th class="email-column">Project Lead</th>
        <th class="assignee-column" style="display: none;">Assignee</th>
        <th class="status-column" style="display: none;">Status</th>
        <th class="dueDate-column" style="display: none;">Due Date</th>
    </tr>
</thead>
<tbody id="userData">
    <!-- Dữ liệu sẽ được cập nhật bởi AJAX -->
</tbody>

    <tbody id="userData">
        <!-- Dữ liệu sẽ được cập nhật bởi AJAX -->
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
document.addEventListener('DOMContentLoaded', () => {
    new TablePagination({
        url: _WEB_HOST + '/modules/<?php echo $module; ?>/get_users.php',  // URL API
        tableId: '#usersTable',  // ID bảng cần phân trang
        paginationId: '#pagination',  // ID phân trang
        currentPage: 1,  // Trang đầu tiên
        columns: [
            { key: 'name' },  // Cột name luôn hiển thị
            { key: 'email' },  // Cột email luôn hiển thị
            { key: 'assignee', columnClass: 'assignee-column', checkboxId: 'assigneeColumn' },
            { key: 'status', columnClass: 'status-column', checkboxId: 'statusColumn' },
            { key: 'dueDate', columnClass: 'dueDate-column', checkboxId: 'dueDateColumn' }
        ]  // Cấu hình các cột có thể ẩn hiện
    });
});


</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Xử lý Import
    document.getElementById('importForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch(_WEB_HOST + '/modules/' + module + '/import_user.php', {
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
        window.location.href = _WEB_HOST + '/modules/' + module + '/export_user.php';
    });
});

</script>

<?php
Layouts('footer-admin',$title);
?>