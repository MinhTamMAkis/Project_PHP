<!-- Các hằng số của project -->

<?php

const _MODULE = 'dashboard';
const _ACTION = 'home';
const _CODE = true;

// thiết lập host
define('_WEB_HOST','http://'.$_SERVER['HTTP_HOST'].'/Project_PHP');
// define('_WEB_HOST', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/Project_PHP');

define('_WEB_HOST_TEMPLATES', _WEB_HOST.'/views');


// thiết lập path 
define('_WEB_PATH',__DIR__);
define('_WEB_PATH_TEMPLATES',_WEB_PATH.'/views');

//Infomation connect database
const _HOST = 'localhost';
const _USER = 'root';
const _PASS = '';
const _DB = 'maki';
