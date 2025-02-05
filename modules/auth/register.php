
<?php
if(!defined('_CODE')){
    die('Access denied...');
}
$title = [
    'pageTitle' => 'Register'
];

if(isPost()){
    $filterAll = filter();
    $error = [];

    //Validate fullname : bat buoc nhap, > 5 ky tu
    if(empty($filterAll['fullname'])){
        $error['fullname']['requireds'] = 'Please enter your FullName';
    }else{
        if(strlen($filterAll['fullname']) < 5){
            $error['fullname']['min'] = 'FullName more than 5 word';   
        }
    }
    //Validate Email : bat buoc nhap, kiem tra email da ton tai chua
    if(empty($filterAll['email'])){
        $error['email']['requireds'] = 'Please enter your Email';
    }else{
        $email = $filterAll['email'];
        $sql = "SELECT id FROM users WHERE email = '$email'";
        if(getRows($sql) > 0){
            $error['email']['unique'] = 'Email had isset';
        }

    }

    //Validate Phone : bat buoc nhap, kiem tra email da ton tai chua
    if(empty($filterAll['phone'])){
        $error['phone']['requireds'] = 'Please enter your Phone';
    }else{
        if(!isPhone($filterAll['phone'])){
            $error['phone']['validata'] = 'Your phone not correct';   
        }

    }
    
    if(empty($filterAll['password'])){
        $error['password']['requireds'] = 'Please enter your Password';
    }else{
        if(strlen($filterAll['password']) < 8){
            $error['password']['min'] = 'Password more than 8 word';   
        }

    }

    //Validate Password : bat buoc nhap, kiem tra email da ton tai chua
    if(empty($filterAll['repassword'])){
        $error['repassword']['requireds'] = 'Please enter your Password Confirm';
    }else{
        if($filterAll['password'] != $filterAll['repassword'] ){
            $error['repassword']['match'] = 'Password Confirm not like password';   
        }
    }


    if(empty($error)){
        $activeToken =  sha1(uniqid().time());
        $dataInsert =[
            //key giong voi cai truong trong database
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'password' => password_hash($filterAll['password'],PASSWORD_DEFAULT),
            'activeToken' => $activeToken,
            'create_at' => date('Y-m-d H:i:s'),
        ];

        $insertStatus = insert('user',$dataInsert);
        if($insertStatus){
            setFlashData('smg','Register Success');
            setFlashData('smg_type','success');

            // Tạo link kích hoạt
            $linkActive = _WEB_HOST . '?module=auth&action=active&token='.$activeToken;

            // Thiet lap mail gui
            $subject = $filterAll['fullname'].'vui long kích hoạt tài khoản';
            $content = 'Chào'.$filterAll['fullname'].'.</br>';
            $content .= 'Vui long click vào link bên dưới để kích hoạt tài khoản :</br>';
            $content .= $linkActive .'</br>';
            $content .= 'Trân trọng cảm ơn';

            // Tiến hành gửi mail
            $sendMail = sendMail($filterAll['email'],$subject,$content);
            if($sendMail){
                setFlashData('smg','Đăng ký thành công vui lòng kiếm tra email để kích hoạt tài khoản ');
                setFlashData('smg_type','success');
            }else{
                setFlashData('smg','Hệ thông đang lỗi, vui lòng thử lại sau');
                setFlashData('smg_type','danger');
            }
        }else{
            setFlashData('smg','Đăng ký không thành công');
            setFlashData('smg_type','danger');
        }
        
        redirect('?module=auth&action=login');

    }else{
        setFlashData('smg','Please check the data again');
        setFlashData('smg_type','danger');
        setFlashData('error',$error);
        setFlashData('olddata',$filterAll);
        redirect('?module=auth&action=register');
    }
    
}



layouts('header',$title);
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
$errors = getFlashData('error');
$old_data = getFlashData('olddata');

?>




<div class="row">  
    <div class="col-6" style="margin: 0 auto;">
    <h2 class="text-center text-uppercase">Register</h2>
    <?php
        if(!empty($smg)){
            getSmg($smg,$smg_type);
        }
    ?>
    <script>
        // JavaScript to remove the message div after 10 seconds
        setTimeout(function(){
            var element = document.getElementById("flash-message");
            element.parentNode.removeChild(element);
        }, 5000); // 10 seconds
    </script>
        <form action="" method="POST" >
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Please enter your email" value="<?php echo old_data('email',$old_data);?>">
                <?php
                        echo form_mess_error('email','<span class="erorr">','</span>',$errors);
                ?>
            </div>
            <div class="form-group">
                <label for="fullname">FullName</label>
                <input type="text" class="form-control" name="fullname" placeholder="Please enter your FullName "  value="<?php echo old_data('fullname',$old_data);?>">
                <?php
                        echo form_mess_error('fullname','<span class="erorr">','</span>',$errors);
                ?>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" name="phone" placeholder="Please enter your Phone" value="<?php echo old_data('phone',$old_data);?>">
                <?php
                        echo form_mess_error('phone','<span class="erorr">','</span>',$errors);
                ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Please enter your password" >
                <?php
                        echo form_mess_error('password','<span class="erorr">','</span>',$errors);
                ?>
            </div>
            <div class="form-group">
                <label for="repassword">Password Confirm</label>
                <input type="password" class="form-control" name="repassword" placeholder="Please enter your password" >
                <?php
                        echo form_mess_error('repassword','<span class="erorr">','</span>',$errors);
                ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <p class="text-center"><a href="?module=auth&action=login">Login</a></p>

        </form>
    </div>
</div>
<?php

layouts('footer');

