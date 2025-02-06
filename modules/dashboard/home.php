<?php
if(!defined('_CODE')){
    die('Access denied...');
}
$title = [
    'pageTitle' => 'DashBoard'
];
Layouts('header-admin',$title);

// Kiểm tra trạng thái đăng nhập
if(!isLogin()){
    redirect('?module=auth&action=login');
}

?>
<h1>DASHBOARD</h1>

<?php
Layouts('footer-admin',$title);

?>