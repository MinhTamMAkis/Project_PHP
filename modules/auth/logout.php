<?php

if(!defined('_CODE')){
    die('Access denied...');
}

if(isLogin()){
    $token = getSession('logintoken');
    delete('logintoken',"token='$token'");
    removeSession('logintoken');
    redirect('?module=auth&action=login');
}