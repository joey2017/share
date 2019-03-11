<?php
include 'init.php';
//if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
//    header('Location:' . $notwxlink);
//    exit();
//}

if (!(isWechat() && isMobile())) {
    header('Location:' . $notwxlink);
    exit();
}
$back_link = $back_link[mt_rand(0, count($back_link) - 1)];
$curr_host = parse_url($back_link);
$curr_host = $curr_host['host'];
if ($_SERVER['HTTP_HOST'] != $curr_host) {
    header('Location:' . $back_link);
    exit();
}
?>