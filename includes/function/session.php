<?php
if(!defined('_CODE')){
    die('Access denied...');
}


function setSession($key,$value){
    return $_SESSION[$key] = $value;
}


function getSession($key = ''){
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Đảm bảo session đã được bắt đầu
    }

    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    return null; // Trả về null nếu không có key
}


function removeSession($key = ''){
    if(empty($key)){
        session_destroy();
        return true;
    }else{
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
            return true;
        }
    }
}

// lấy dữ liệu xong xóa Session đi 

function setFlashData($key, $value){
    $key = 'flash_' .$key;
    return setSession($key,$value);
}

function getFlashData($key){
    $key = 'flash_' . $key;
    $data = getSession($key); 
    removeSession($key);
    return $data;
}
