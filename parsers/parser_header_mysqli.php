<?php
//include_once 'db_connect.php';
//include_once '/var/www/admin/www/russia-fin.online/scrs/connect_db_mysqli.php';
//include_once '/var/www/admin/www/russia-fin.online/parser/simple_html_dom.php';
//include_once '/var/www/admin/www/russia-fin.online/parser/funcs_mysqli.php';
//include_once '/var/www/admin/www/russia-fin.online/parser/Snoopy.class.php';
//include_once '/var/www/admin/www/russia-fin.online/parser/phpQuery.php';


include_once '/var/www/html/parsers/connect_db_mysqli.php';
include_once '/var/www/html/parsers/simple_html_dom.php';
include_once '/var/www/html/parsers/funcs_mysqli.php';
include_once '/var/www/html/parsers/Snoopy.class.php';
include_once '/var/www/html/parsers/phpQuery.php';


$snoopy = new Snoopy;
$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows XP)";
$snoopy->rawheaders["Pragma"] = "no-cache";
$snoopy->maxredirs = 0;
$snoopy->offsiteok = false;
$snoopy->expandlinks = false;
