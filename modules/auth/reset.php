
<?php

if(!defined('_CODE')){
    die('Access denied...');
}
$token = filter()['token'];
layouts('header');

if(!empty($token)){
    //truy vấn database kiểm tra token
    $tokenQuery = oneRaw("SELECT id, fullname, email FROM user WHERE forgotToken ='$token'");

    if(!empty($tokenQuery)){
        if(isPost()){
            $filterAll = filter();
            $userID = $tokenQuery['id'];
            $error =[];
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
                $passwordHash = password_hash($filterAll['password'],PASSWORD_DEFAULT);
                $dataUpdate =[
                    'password' => $passwordHash,
                    'forgotToken' => null,
                    'update_at' => date('Y-m-d H:i:')
                ];
                $updateStatus = update('user',$dataUpdate,"id ='$userID'");
                if($updateStatus){
                    setFlashData('smg','Change password successfully');
                    setFlashData('smg_type','success');
                    redirect('?module=auth&action=login');
                }else{
                    setFlashData('smg','Change password failed');
                    setFlashData('smg_type','danger');
                }
            }else{
                setFlashData('smg','Please check the data again');
                setFlashData('smg_type','danger');
                setFlashData('error',$error);
                redirect('?module=auth&action=reset&token='.$token);
            }


        }
        $smg = getFlashData('smg');
        $smg_type = getFlashData('smg_type');
        $errors = getFlashData('error');
        ?>
        <!-- Form đặt mật khẩu -->
        <div class="row">  
            <div class="col-6" style="margin: 0 auto;">
            <h2 class="text-center text-uppercase">Change Password</h2>
            <?php
                if(!empty($smg)){
                    getSmg($smg,$smg_type);
                }
            ?>
                <form action="" method="POST" >
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
                    <input type="hidden" name="token" value="<?php echo $token ?> ">
                    <button type="submit" class="btn btn-primary btn-block">Send</button>
                    <p class="text-center"><a href="?module=auth&action=login">Login</a></p>

                </form>
            </div>
        </div>

        <?php
    }else{
        getSmg('Liên kết không tôn tại hoặc đã hết hạn','danger');
    }

}else{
    getSmg('Liên kết không tôn tại hoặc đã hết hạn','danger');
}



?>

<?php
layouts('footer');

?>