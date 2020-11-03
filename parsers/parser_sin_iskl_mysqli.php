<?
unset($mas_cat);
unset($sin_arr);
unset($iskl_raz_arr);
global $mas_cat;
global $db;

$s_tovar_sho_L_M = $db->query("SELECT category, count(id) cnt FROM `exp_tovar_sho_L_M` GROUP BY category ORDER BY category ASC");
while($row = $s_tovar_sho_L_M->fetch_array(MYSQLI_ASSOC)) {
	$mas_cat[] = $row['category'];
}

$s_tovar_sho_L_W = $db->query("SELECT category, count(id) cnt FROM `exp_tovar_sho_L_W` GROUP BY category ORDER BY category ASC");
while($row = $s_tovar_sho_L_W->fetch_array(MYSQLI_ASSOC)) {
	$mas_cat[] = $row['category'];
}

$s_tovar_sho_S_M = $db->query("SELECT category, count(id) cnt FROM `exp_tovar_sho_S_M` GROUP BY category ORDER BY category ASC");
while($row = $s_tovar_sho_S_M->fetch_array(MYSQLI_ASSOC)) {
	$mas_cat[] = $row['category'];
}
$s_tovar_sho_S_W = $db->query("SELECT category, count(id) cnt FROM `exp_tovar_sho_S_W` GROUP BY category ORDER BY category ASC");
while($row = $s_tovar_sho_S_W->fetch_array(MYSQLI_ASSOC)) {
	$mas_cat[] = $row['category'];
}
$mas_cat=array_unique($mas_cat);

if($sex=='man') {

	
	//синонимы разбор(записываем массив)
	$sin_ii=0;
	$sinon_arr = explode("\n",file_get_contents('/var/www/html/parsers/txt/sinon_man.txt'));
	foreach($sinon_arr as $sin_val) {
		$sin_val=explode(',',$sin_val);
		$ferst_sin_val=trim($sin_val[0]);
		foreach($sin_val as $sin_val_sin) {
			$sin_arr[$sin_ii.'@'.$ferst_sin_val]=trim(mb_strtolower($sin_val_sin, 'UTF-8'));
			++$sin_ii;
		}
	}
	//конец	
	
	$iskl_cat_arr = explode(',',file_get_contents('/var/www/html/parsers/txt/exclusion_man.txt'));//в этом файле хранятся категории которые мы не парсим
	//находим размеры которые нужно парсить
	$result2_2 = $db->query("SELECT *  FROM `size` WHERE (`data`='sho_L_M' or `data`='sho_S_M')");
	$i_isk=0;
	while($row_is = $result2_2->fetch_array(MYSQLI_ASSOC)){
		$iskl_raz_arr[$i_isk."@".$row_is['data']]=$row_is['size'];
		++$i_isk;
	}
	//конец	
}

if($sex=='woomen') {
	//синонимы разбор(записываем массив)
	$sin_ii=0;
	$sinon_arr = explode("\n",file_get_contents('/var/www/html/parsers/txt/sinon.txt'));
	foreach($sinon_arr as $sin_val) {
		$sin_val=explode(',',$sin_val);
		$ferst_sin_val=trim($sin_val[0]);
		foreach($sin_val as $sin_val_sin) {
			$sin_arr[$sin_ii.'@'.$ferst_sin_val]=trim(mb_strtolower($sin_val_sin, 'UTF-8'));
			++$sin_ii;
		}
	}
	//конец	
	
	$iskl_cat_arr = explode(',',file_get_contents('/var/www/html/parsers/txt/exclusion.txt'));//в этом файле хранятся категории которые мы не парсим
	//находим размеры которые нужно парсить
	$result2_2 = $db->query("SELECT *  FROM `size` WHERE (`data`='sho_L_W' or `data`='sho_S_W')");
	$i_isk=0;
	while($row_is = $result2_2->fetch_array(MYSQLI_ASSOC)){
		$iskl_raz_arr[$i_isk."@".$row_is['data']]=$row_is['size'];
		++$i_isk;
	}
	//конец	
}
?>