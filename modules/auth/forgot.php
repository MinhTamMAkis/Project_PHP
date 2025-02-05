<?php

if(!defined('_CODE')){
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Forgot'
];

// Kiểm tra trạng thái đăng nhập
if(isLogin()){
    redirect('?module=home&action=dashboard');
}

if(isPost()){
    $filerAll = filter();
    if(!empty($filerAll['email'])){
        $email = $filerAll['email'];
        $queryUser = oneRaw("SELECT id FROM user WHERE email='$email'");
        if(!empty($queryUser)){
            $userID = $queryUser['id'];
            // Tạo forgot Token
            $forgotToken = sha1(uniqid().time());
            $dataUpdate =[
                'forgotToken' => $forgotToken
            ];
                $updateStatus = update('user',$dataUpdate,"id = $userID");
                if($updateStatus){
                    $linkReset = _WEB_HOST.'?module=auth&action=reset&token='.$forgotToken;

                    $subject = 'Yêu cầu khôi phục mật khẩu';
                    $content = 'Chào bạn.<br>';
                    $content .= 'Chúng tôi nhận được yêu cầu khôi phục mật khẩu từ bạn .<br> Vui lòng click và link sau để đổi lại mật khẩu ';
                    $content .= $linkReset.'<br>';
                    $content .='Trân Trọng';

                    $sendMail = sendMail($email,$subject,$content);
                    if($sendMail){
                        setFlashData('smg','Please check your email to reset your password');
                        setFlashData('smg_type','success');
                    }else{
                        setFlashData('smg','System error please try again(email)');
                        setFlashData('smg_type','danger');
                    }
                }else{
                    setFlashData('smg','System error please try again');
                    setFlashData('smg_type','danger');
                }
        }else{
            setFlashData('smg','Your Email not isset');
            setFlashData('smg_type','danger');
        }

    }else{
        setFlashData('smg','Please enter your Email');
        setFlashData('smg_type','danger');
    }
    redirect('?module=auth&action=forgot');
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
    <h2 class="text-center text-uppercase">Forgot Password</h2>
        <form action="" method="post" >
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Please enter your email">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Send</button>
            <p class="text-center"><a href="?module=auth&action=login">Login</a></p>
            <p class="text-center"><a href="?module=auth&action=register">Register</a></p>

        </form>
    </div>
</div>
<?php
Layouts('footer');
