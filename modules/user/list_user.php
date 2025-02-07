<?php
if(!defined('_CODE')){
    die('Access denied...');
}
$title = [
    'pageTitle' => 'USER'
];
Layouts('header-admin',$title);

// Kiểm tra trạng thái đăng nhập
if(!isLogin()){
    redirect('?module=auth&action=login');
}

?>


<div class="table_list">
    <div class="title" style="display: flex;">
        <h4>Active Projects</h4>
        <p style="display: flex;">
            <img style="width: 10px; height: 13px;" src="#" alt="">
            <span>Export Report</span>
        </p>
    </div>
    <table id="usersTable">
        <tr class="top-table">
            <th>Project Name <i></i></th>
            <th>Project Lead </th>
            <th>Progress</th>
            <th>Assignee</th>
            <th>Status </th>
            <th>Due Date</th>
        </tr>
        <!-- Dòng dữ liệu sẽ được thêm vào từ Ajax -->
        <tbody id="userData"></tbody>
    </table>
</div>


<script>
    // Đảm bảo rằng _WEB_HOST được truyền từ PHP sang JavaScript
    var _WEB_HOST = "<?php echo _WEB_HOST; ?>";

    // Gửi yêu cầu Ajax khi trang được tải
    window.onload = function() {
        // Lấy module và action từ URL
        var module = '<?php echo $module; ?>';

        // Xây dựng URL cho yêu cầu Ajax
        var url = _WEB_HOST + '/modules/' + module + '/' + 'get_users.php';

        // Sử dụng fetch API để gửi yêu cầu GET
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                // Thêm bất kỳ headers nào nếu cần
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Xử lý dữ liệu thành công và cập nhật bảng
                var userData = data.data; // Dữ liệu người dùng trả về
                var tableBody = document.getElementById('userData');
                
                // Clear các dòng hiện tại trong bảng
                tableBody.innerHTML = '';

                // Thêm các dòng mới vào bảng
                userData.forEach(function(user) {
                    var row = document.createElement('tr');
                    row.classList.add('item');

                    // Thêm các ô vào dòng
                    row.innerHTML = `
                        <td>${user.name}</td>
                        <td id="user-name">
                            <img src="img/avata.jpg" alt="">
                            ${user.name}
                        </td>
                        <td class="progress">
                            <progress class="progress-table" value="53" max="100" style="width: 65px;"></progress>
                        </td>
                        <td>
                            <ul class="list-user">
                                <li><img src="img/avata.jpg" alt=""></li>
                                <li><img src="img/user1.jpg" alt=""></li>
                                <li><img src="img/user2.png" alt=""></li>
                            </ul>
                        </td>
                        <td>
                            <button class="defalt">Inprogress</button>
                        </td>
                        <td>
                            <p>06 Sep 2021</p>
                        </td>
                    `;
                    
                    // Thêm dòng vào bảng
                    tableBody.appendChild(row);
                });
            } else {
                // Xử lý lỗi nếu không tìm thấy dữ liệu
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Request failed', error);
        });
    };
</script>


<?php
Layouts('footer-admin',$title);

?>