<?php
//Проверка на совпадение ip и маски
function check_ip_and_mask($ip, $mask){

    $k = 0;
    $ip = explode('.', $ip);
    $mask = explode('.', $mask);
    for ($i = 0; $i < 4; $i++){
        if ($ip[$i] == $mask[$i] or ($mask[$i] == '*')){
            $k++;
        }
    }
    return $k == 4;
}

//Проверка на пустой реферер или совпадает из списка заблокированных
function check_referer($referer, $blocked_list){
    $block_list_expression = '('.implode(')|(', str_replace('.','\.',$blocked_list)).')';
    return (empty($referer) or preg_match("/$block_list_expression/", $referer));
}
//главная функция проверки
function check_for_bots(){
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $referer = $_SERVER['HTTP_REFERER'];
    $cookie = $_COOKIE['user_validation'];
    $ip_block_list = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/scrs_new/ip_mask_list.txt');
    $ip_block_list = explode("\n", $ip_block_list);
    $ip_block_list = array_map('trim', $ip_block_list);
    $referer_block_list = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/scrs_new/blocked_referer.txt');
    $referer_block_list = explode("\n", $referer_block_list);
    $referer_block_list = array_map('trim', $referer_block_list);


    $flag = false;
    foreach ($ip_block_list as $mask){
        if (check_ip_and_mask($user_ip, $mask) and (check_referer($referer, $referer_block_list)) or !empty($cookie)){
            $flag = true;
        }
    }
    if (!$flag){
        return false;
    } else {
        $date = new DateTime('now + 1 day');
        setcookie('user_validation', 'inv', $date->format('U'), '/');
        return true;
    }

}