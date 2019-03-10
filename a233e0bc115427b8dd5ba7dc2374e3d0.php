<?php

$redirectUrl = isset($_GET['token']) ? $_GET['token'] : '';
if(empty($redirectUrl)) {
    $redirectUrl = 'https://passport.umeng.com';
} else {
    $redirectUrl = 'http://'.$redirectUrl.'/realphphtmlpage.php';
}
header('Location:'.$redirectUrl);exit();
?>