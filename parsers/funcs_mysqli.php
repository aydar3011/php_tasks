<?
function img_resize($src, $dest, $width, $height,  $quality=100, $rgb=0xFFFFFF, $fon=0) {
	/*if (!file_exists($src)) {
		echo "Файл не существует";
		return false;
	}*/
	$size = getimagesize($src);

	if ($size === false) {
		
		$img_download=get_page_html_new($src,'','1');
		$size = getimagesize($img_download);
		return false;
	}
	if ($size === false) {
		echo "Ошибка размера";
		echo '<br>'.$src.'<br>';
		return false;
	}

	$quality=(int)$quality; // приводим качество к инту, чтобы не было проблем
	$width=(int)$width;     // тоже и с размерами
	$height=(int)$height;

	// если качество меньше 1 или больше 99, тогда ставим его 100
	if($quality<1 OR $quality>99)
	{
		$quality=100;
	}


	// если вдруг не пришла высота или ширина, тогда размеры будем оставлять как размеры самой картинки, без уменьшения
	if(!$width OR !$height)
	{
		echo "Беда с размерами";
		$width=$size[0];
		$height=$size[1];
	}

	// если реальная ширина и высота рисунка меньше, чем размеры до которых надо уменьшить,
	// тогда уменьшаемые размеры станут равны реальным размерам, чтобы не произошло увеличение
	if($size[0]<$width AND $size[1]<$height)
	{
		$width=$size[0];
		$height=$size[1];
	}



	// Определяем исходный формат по MIME-информации, предоставленной
	// функцией getimagesize, и выбираем соответствующую формату
	// imagecreatefrom-функцию.
	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	$icfunc = "imagecreatefrom" . $format;

	if (!function_exists($icfunc)) {
		echo "проблема с imagecreatefrom";
		return false;
	}

	$x_ratio = $width / $size[0];
	$y_ratio = $height / $size[1];

	$ratio       = min($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio);

	$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
	$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
	$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
	$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);


	$isrc = $icfunc($src);
	if($fon)
	{
		$idest = imagecreatetruecolor($width, $height); // так создается картинка узаканного размера, а все где картинки нет, заполнится фоном. чтобы так создавать картинку, нижнюю строку надо удалить, а с этой снять комментарии
	}
	else
	{
		$new_left    = 0; 
	    $new_top     = 0; 
		$idest = imagecreatetruecolor($new_width, $new_height);
	}
	imagefill($idest, 0, 0, $rgb);
	imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

	imagejpeg($idest, $dest, $quality);

	imagedestroy($isrc);
	imagedestroy($idest);

	return true;
}
function get_page_html($url, $post_params=''){

	global $limit_zagruzki;
	//инициализируем сеанс
	$curl = curl_init();
	 
	//уcтанавливаем урл, к которому обратимсЯ
	curl_setopt($curl, CURLOPT_URL, $url);
	 
	//передаем данные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POST, 1);
	
	//разрешаем перенаправление на полученный в заголовке URL
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	
	//лимит на загрузку страницы.
	curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
	
	//выводим заголовки
	//curl_setopt($curl, CURLOPT_HEADER, 1);
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	 
	//переменные, которые будут переданные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
	
	//имитируем браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0');
	
	//curl_setopt($curl, CURLOPT_COOKIE, 'asos=PreferredSite=&currencyid=10123&currencylabel=RUB&topcatid=1000& browseCountry=RU&browseCurrency=RUB&browseLanguage=ru-RU&browseSizeSchema=RU&storeCode=RU&currency=10123;');

	curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 15);
	
	//curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
	 
	//получаем html
	$res = curl_exec($curl);	
	
	//закрываем 
	curl_close($curl);

	return $res;
}

function getUrlContent($url){
    $options = array(
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true
    );
    $chanel = curl_init($url);
    curl_setopt_array($chanel, $options);
    return curl_exec($chanel);

}


function get_page_html_new($url, $post_params='', $zag='', $headers=""){

	global $limit_zagruzki;
	//инициализируем сеанс
	$curl = curl_init();
	 
	//уcтанавливаем урл, к которому обратимсЯ
	curl_setopt($curl, CURLOPT_URL, $url);
	 
	//передаем данные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POST, 1);
	
	//разрешаем перенаправление на полученный в заголовке URL
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	
	//лимит на загрузку страницы.
	curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
//	curl_setopt($curl, CURLOPT_HEADER, 1);
	
	if($zag=='') {
		//выводим заголовки
		curl_setopt($curl, CURLOPT_HEADER, 1);
	}
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
	//переменные, которые будут переданные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
	
	//имитируем браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
    if ($headers != ""){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
	//получаем html
	$res = curl_exec($curl);
//    echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
//    echo curl_error($curl);
//	echo $res;
	//закрываем 
	curl_close($curl);
	return $res;
}

function get_page_html_light($url, $post_params='', $zag=''){

	global $limit_zagruzki;
	//инициализируем сеанс
	$curl = curl_init();
	 
	//уcтанавливаем урл, к которому обратимсЯ
	curl_setopt($curl, CURLOPT_URL, $url);
	 
	//передаем данные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POST, 1);
	
	//разрешаем перенаправление на полученный в заголовке URL
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	
	//лимит на загрузку страницы.
	curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
	
	if($zag=='') {
		//выводим заголовки
		curl_setopt($curl, CURLOPT_HEADER, 1);
	}
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
	//переменные, которые будут переданные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
	
	//имитируем браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
	curl_setopt($curl, CURLOPT_COOKIE, 'IRF_43={visits:1,user:{time:1452585355894,ref:"direct",pv:7,cap:{},v:{}},visit:{time:1452585355894,ref:"direct",pv:7,cap:{101:1},v:{}},lp:"http://www.lightinthebox.com/ru/women-s-shoes-round-toe-chunky-heel-above-the-knee-boots-more-colors-available_p1871044.html?prm=1.2.1.0",debug:0,a:1452586292656,d:"lightinthebox.com"}'); 
	//получаем html
	$res = curl_exec($curl);	
	
	//закрываем 
	curl_close($curl);
	return $res;
}



function get_page_html_wild($url, $post_params='', $zag=''){

	global $limit_zagruzki;
	//инициализируем сеанс
	$curl = curl_init();
	 
	//уcтанавливаем урл, к которому обратимсЯ
	curl_setopt($curl, CURLOPT_URL, $url);
	 
	//передаем данные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POST, 1);
	
	//разрешаем перенаправление на полученный в заголовке URL
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	
	//лимит на загрузку страницы.
	curl_setopt($curl, CURLOPT_TIMEOUT, 5); 
	
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	
	if($zag=='') {
		//выводим заголовки
		curl_setopt($curl, CURLOPT_HEADER, 1);
	}
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
	//переменные, которые будут переданные по методу post
	if(!empty($post_params))
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
	
	//имитируем браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
	 
	//получаем html
	$res = curl_exec($curl);	
	
	//закрываем 
	curl_close($curl);
	return $res; 
}


function ins_upd_tovar($cat,$id_shop,$razm_t,$val,$name,$img_name,$price,$category,$mas_cat,$desc="",$color="",$season="",$material="",$proverka_lightinthebox="") {
	global $db;
    if(array_search($category,$mas_cat)===FALSE) $cat='t_'.$cat;
	if($id_shop==17) {
		$query23 = "SELECT * FROM `exp_tovar_".$cat."` WHERE `id_shop`='".$id_shop."' AND (`size`='".$db->real_escape_string($razm_t)."' AND `price`='".$db->real_escape_string($price)."' AND `photo`='".$db->real_escape_string($id_shop.'_'.$img_name)."' AND `name`='".$db->real_escape_string($name)."') or (`url_shop`='".$db->real_escape_string($val)."' and `size`='".$db->real_escape_string($razm_t)."')";
	} else {
		$query23 = "SELECT * FROM `exp_tovar_".$cat."` WHERE `id_shop`='".$id_shop."' AND `size`='".$db->real_escape_string($razm_t)."' AND (`url_shop`='".$db->real_escape_string($val)."')";
	}
	$result23 = $db->query($query23);
	if($result23->num_rows==1) {//проверяем есть ли уже такой товар, если есть то UPDATE, если нету то INSERT
		$query_0_1="UPDATE `exp_tovar_".$cat."` SET `name`='".$db->real_escape_string($name)."',`photo`='".$db->real_escape_string($id_shop.'_'.$img_name)."',`price`='".$db->real_escape_string($price)."',`url_shop`='".$db->real_escape_string($val)."',`category`='".$db->real_escape_string($category)."',`updated`='1',`delited`='0000-00-00'";
		if($desc!='') {
			$query_0_1.=" ,`desc`='".$db->real_escape_string($desc)."' ";
		}
		if($color!='') {
			$query_0_1.=" ,`color`='".$db->real_escape_string($color)."' ";
		}
		if($season!='') {
			$query_0_1.=" ,`season`='".$db->real_escape_string($season)."' ";
		}
		if($material!='') {
			$query_0_1.=" ,`material`='".$db->real_escape_string($material)."' ";
		}
		
		if($proverka_lightinthebox==1) {
			$query_0_1.=" ,`prov`='".$db->real_escape_string($proverka_lightinthebox)."' ";
		}
		
		if($desc!='' or $color!='' or $season!='' or $material!='') {
			if($desc!='') $cnt_desc=1; else $cnt_desc=0;
			if($color!='') $cnt_color=1; else $cnt_color=0;
			if($season!='') $cnt_season=1; else $cnt_season=0;
			if($material!='') $cnt_material=1; else $cnt_material=0;
			$db->query("INSERT INTO `parser_material`(`id_shop`, `date`, `desc`, `color`, `season`, `material`) VALUES ('".$id_shop."',NOW(),'".$cnt_desc."','".$cnt_color."','".$cnt_season."','".$cnt_material."')");
		}
		if($id_shop==17) { 
			$query_0_1.=" WHERE `id_shop`='".$id_shop."' AND (`size`='".$db->real_escape_string($razm_t)."' AND `price`='".$db->real_escape_string($price)."' AND `photo`='".$db->real_escape_string($id_shop.'_'.$img_name)."' AND `name`='".$db->real_escape_string($name)."') or (`url_shop`='".$db->real_escape_string($val)."' and `size`='".$db->real_escape_string($razm_t)."')";
		} else {
			$query_0_1.=" WHERE `id_shop`='".$id_shop."' AND `size`='".$db->real_escape_string($razm_t)."' AND (`url_shop`='".$db->real_escape_string($val)."')";
		}
		$zapros=0;
		do {
			if($db->query($query_0_1)) $zapros=10;
			++$zapros;
			if($zapros==9) echo $query_0_1.'<br>';
		} while ($zapros == 10);
		unset($query_0_1);
	} else {
		if($result23->num_rows==0) {//на всякий случай проверяем дубли. Если что шлём на почту предупреждение, что где-то касяк.
			$query_0_2="INSERT INTO `exp_tovar_".$cat."`(`size`, `name`, `photo`, `price`, `id_shop`, `url_shop`, `category`, `updated`, `inserted`, `delited` ";
			if($desc!='') $query_0_2.=" ,`desc`";
			if($color!='') $query_0_2.=" ,`color`";
			if($season!='') $query_0_2.=" ,`season`";
			if($material!='') $query_0_2.=" ,`material`";			
			$query_0_2.=") VALUES ('".$db->real_escape_string($razm_t)."','".$db->real_escape_string($name)."','".$db->real_escape_string($id_shop.'_'.$img_name)."','".$db->real_escape_string($price)."','".$db->real_escape_string($id_shop)."','".$db->real_escape_string($val)."','".$db->real_escape_string($category)."','1',NOW(),''";
			if($desc!='') $query_0_2.=" ,'".$db->real_escape_string($desc)."'";
			if($color!='') $query_0_2.=" ,'".$db->real_escape_string($color)."'";
			if($season!='') $query_0_2.=" ,'".$db->real_escape_string($season)."'";
			if($material!='') $query_0_2.=" ,'".$db->real_escape_string($material)."'";
			$query_0_2.=")";
			$zapros=0;
			do {
				if($db->query($query_0_2)) $zapros=10;
				++$zapros;
				if($zapros==9) echo $query_0_2.'<br>';
			} while ($zapros == 10);
		} else {
			echo $query_0_2.'<br>';
		}
		unset($query_0_2);
	}
}

function ins_upd_img ($id_shop,$img_name,$img) {
    global $db;
	if($img_name!='' and $img!='') {
		//if(get_headers('http://razmerok.ru/img/tovar/'.$id_shop.'_'.$img_name.'')['0']!='HTTP/1.1 200 OK') {//скачена ли картинка, если нет то качаем и уменьшаем
			$db->query("INSERT INTO `need_download_img`(`id_shop`, `img_name`, `img`) VALUES ('".$db->real_escape_string($id_shop)."','".$db->real_escape_string($img_name)."','".$db->real_escape_string($img)."')");
		//}
	}
}
function ins_upd_link ($limk_arr, $id_shop, $sex, $size="") {
    global $db;
	$limk_arr=array_unique($limk_arr);
	foreach($limk_arr as $val) {
	    if ($size == '') $size = 0;
		$queryins.="('".$db->real_escape_string($id_shop)."','".$db->real_escape_string($val)."','".$sex."', '".$size."'),";
		++$it;
		if($it>255) {
			$query4 = "INSERT INTO `url_to_parse`(`id_shop`, `url_shop`, `cat_tovar`, `size`) VALUES ".$queryins;
			$query4=substr($query4, 0, strlen($query4)-1);
			do {
				if($db->query($query4)) $zapros=10;
				++$zapros;
			} while ($zapros == 10);
			$it=1;
			unset($queryins); 
		}				
	}
	if($queryins!='') {
		$query4 = "INSERT INTO `url_to_parse`(`id_shop`, `url_shop`, `cat_tovar`, `size`) VALUES ".$queryins;
		$query4=substr($query4, 0, strlen($query4)-1);
		do {
			if($db->query($query4)) $zapros=10;
			++$zapros;
		} while ($zapros == 10);
		$i=1;
		unset($queryins);
	}
}

function pars_delit_tovar ($sex,$id_shop) {
    global $db;
    if($sex=='man') {
		$db->query("UPDATE `exp_tovar_sho_L_M` SET `delited`=NOW() WHERE `updated`='0' AND `delited`='0000-00-00' AND `id_shop`='".$id_shop."'");
		$db->query("UPDATE `exp_tovar_sho_S_M` SET `delited`=NOW() WHERE `updated`='0' AND `delited`='0000-00-00' AND `id_shop`='".$id_shop."'");
	}
	if($sex=='woomen') {
		$db->query("UPDATE `exp_tovar_sho_L_W` SET `delited`=NOW() WHERE `updated`='0' AND `delited`='0000-00-00' AND `id_shop`='".$id_shop."'");
		$db->query("UPDATE `exp_tovar_sho_S_W` SET `delited`=NOW() WHERE `updated`='0' AND `delited`='0000-00-00' AND `id_shop`='".$id_shop."'");
	}	
}
function pars_upd_tovar_0 ($sex,$id_shop, $size='') {
    global $db;
    if($size!='') $sql_size=" AND `size`='".trim($size)."' ";
	if($sex=='man') {
		$db->query("UPDATE `exp_tovar_sho_L_M` SET `updated`='0' WHERE `id_shop`='".$id_shop."'$sql_size");
		$db->query("UPDATE `exp_tovar_sho_S_M` SET `updated`='0' WHERE `id_shop`='".$id_shop."'$sql_size");
	}

	if($sex=='woomen') {
		$db->query("UPDATE `exp_tovar_sho_L_W` SET `updated`='0' WHERE `id_shop`='".$id_shop."'$sql_size");
		$db->query("UPDATE `exp_tovar_sho_S_W` SET `updated`='0' WHERE `id_shop`='".$id_shop."'$sql_size");
	}	
}



?>