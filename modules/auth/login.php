<?php

if(!defined('_CODE')){
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Login'
];

// Kiểm tra trạng thái đăng nhập
if(isLogin()){
    redirect('?module=dashboard&action=home');
}

if(isPost()){
    $filterAll = filter();
    $error = [];
    if(!empty(trim($filterAll['email'])) && !empty(trim($filterAll['password']))){
        // Kiểm tra đăng nhập
        $email = $filterAll['email'];
        $password = $filterAll['password']; 
    
        // Truy vấn thông tin user theo mail
        $QueryUser = oneRaw("SELECT password ,id FROM users WHERE email = '$email'");
        if(!empty($QueryUser)){
            $passwordHash = $QueryUser['password'];
            $userId= $QueryUser['id'];
            if(password_verify($password,$passwordHash)){
                
                // Tạo Token Login
                $tokenLogin = sha1(uniqid().time());
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                // Insert vào bảng loginToken trong database
                $dataInsertToken =[
                    'user_Id' =>$userId,
                    'token' => $tokenLogin,
                    'create_at' => date('Y-m-d H:i:s')
                ];

                $insertStatus = insert('logintoken',$dataInsertToken);
                var_dump($insertStatus);

                if($insertStatus){
                    //Insert thành công

                    //Lưu cái tokenLogin vào trong sesssion 
                    setSession('logintoken',$tokenLogin);
                    redirect('?module=dashboard&action=home');
                    
                    
                }else{
                    setFlashData('smg','Can not login,please try a again');
                    setFlashData('smg_type','danger');
                }

            }else{
                    setFlashData('smg','Password not correct');
                    setFlashData('smg_type','danger');
            }
        }else{
            setFlashData('smg','Email not isset');
            setFlashData('smg_type','danger');
        }

    }else {
        setFlashData('smg','Please enter your email and password');
        setFlashData('smg_type','danger');
    }
    redirect('?module=auth&action=login');
}

Layouts('header',$title);
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
?>

<div class="row">
    <div class="col-6" style="margin: 0 auto;">
    <?php
        if(!empty($smg)){
            getSmg($smg,$smg_type);
        }
    ?>
    <h2 class="text-center text-uppercase">Login</h2>
        <form action="" method="post" >
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Please enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Please enter your password" autocomplete="off">
                </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <p class="text-center"><a href="?module=auth&action=forgot">Forgot Password</a></p>
            <p class="text-center"><a href="?module=auth&action=register">Register</a></p>

        </form>
    </div>
</div>
<?php
Layouts('footer');
