<?php
if(!defined('_CODE')){
    die('Access denied...');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/style.css?ver=<?php echo rand();?>">
    <title><?php echo !empty($title['pageTitle']) ? $title['pageTitle'] : 'Mangaer User'; ?></title>
</head>
<body>
    
