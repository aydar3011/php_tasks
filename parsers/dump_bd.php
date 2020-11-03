<?
ini_set('memory_limit', '-1');
$dump_dir = '/var/www/admin/www/russia-fin.online/parser/dump'; // директория, куда будем сохранять резервную копию БД
$dump_name = "dump.sql"; //имя файла 
$insert_records = 255; //записей в одном INSERT
$gzip = true; 		//упаковать файл дампа
$stream = false;		//вывод файла в поток

$user = "banki";
$pass = "o8Q13a45r3I2R18E";
$dbname = "banki";
$dbhost = "localhost";
$db = mysql_connect("$dbhost","$user","$pass") or die ("Can't connect to mySQL server");
mysql_select_db ("$dbname") or die (mysql_error());
mysql_db_query ($dbname,"SET NAMES utf8",$db) or die (mysql_error());

mysql_query("INSERT INTO `cron_tbl`(`txt_cron`, `time_cron`) VALUES ('dump1',NOW())");


$res = mysql_query("SHOW TABLES FROM `banki` WHERE `Tables_in_banki` LIKE 'exp_tovar_sho_L_M' OR `Tables_in_banki` LIKE 'exp_tovar_sho_L_W' OR `Tables_in_banki` LIKE 'exp_tovar_sho_S_M' OR `Tables_in_banki` LIKE 'exp_tovar_sho_S_W' OR `Tables_in_banki` LIKE 'need_download_img'") or die( "Ошибка при выполнении запроса: ".mysql_error() );
$fp = fopen( $dump_dir."/".$dump_name, "w" );
$utf8_file='/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */; /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */; /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */; /*!40101 SET NAMES utf8 */;';
fwrite($fp, $utf8_file);

while( $table = mysql_fetch_row($res) )
{

$query="";
    if ($fp)
    {
		$res1 = mysql_query("SHOW CREATE TABLE ".$table[0]);
		$row1=mysql_fetch_row($res1);
		$query="\nDROP TABLE IF EXISTS `".$table[0]."`;\n".$row1[1].";\n";
        fwrite($fp, $query); $query="";
        $r_ins = mysql_query('SELECT * FROM `'.$table[0].'`') or die("Ошибка при выполнении запроса: ".mysql_error());
		if(mysql_num_rows($r_ins)>0){
		$query_ins = "\nINSERT INTO `".$table[0]."` VALUES ";
		fwrite($fp, $query_ins);
		$i=1;
        while( $row = mysql_fetch_row($r_ins) )
        { $query="";
            foreach ( $row as $field )
            {
                if ( is_null($field) )$field = "NULL";
                else $field = "'".mysql_escape_string( $field )."'";
                if ( $query == "" ) $query = $field;
                else $query = $query.', '.$field;
            }
			if($i>$insert_records){
							$query_ins = ";\nINSERT INTO `".$table[0]."` VALUES ";
							fwrite($fp, $query_ins);
							$i=1;
							}
            if($i==1){$q="(".$query.")";}else $q=",(".$query.")";
			fwrite($fp, $q); $i++;
        }
        fwrite($fp, ";\n");
	}
    }
	
} fclose ($fp);

if($gzip||$stream){ $data=file_get_contents($dump_dir."/".$dump_name);
$ofdot="";

if($gzip){
	$data = gzencode($data, 9);
	unlink($dump_dir."/".$dump_name);
	$ofdot=".gz";
}

if($stream){
		header('Content-Disposition: attachment; filename='.$dump_name.$ofdot);
		if($gzip) header('Content-type: application/x-gzip'); else header('Content-type: text/plain');
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		echo $data;
}else{
		$fp = fopen($dump_dir."/".$dump_name.$ofdot, "w");
		fwrite($fp, $data);
		fclose($fp);
	}
}

?>