<!-- kích hoạt  -->
<?php

if(!defined('_CODE')){
    die('Access denied...');
}
layouts('header');
$token  = filter()['token'];
if(!empty($token)){
    
    $QueryToken = oneRaw("SELECT id FROM users WHERE activeToken = '$token'");

    if(!empty($QueryToken)){
        $userId = $QueryToken['id'];
        $dataUpdate =[
            'status' => 1,
            'activeToken' => null
        ];
        
        $updateStatus = update('user', $dataUpdate ,"id=$userId");
        if($updateStatus){
            setFlashData('smg','Kích hoạt tài khoản thành công');
            setFlashData('smg_type','success');
        }else{
            setFlashData('smg','Kích hoạt tài khoản không thành công');
            setFlashData('smg_type','danger');
        }
        redirect('?module=auth&action=login');
    }else{
        getSmg('Liên kết không tồn tại hoặc đã hết hạn','danger');
    }
}else{
    getSmg('Lien ket không tồn tại hoạc đã hhết hạn');
}
?>

<?php

layouts('footer');