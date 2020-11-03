<?
 ini_set('error_reporting', E_ALL);
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
set_time_limit (0);

$id_shop=12;//айди магазина
$sex='man';//женская обувь
//include_once '/var/www/admin/www/russia-fin.online/parser/parser_header_mysqli.php';
//include_once '/var/www/admin/www/russia-fin.online/parser/parser_sin_iskl_mysqli.php';
include_once 'parser_header_mysqli.php';
include_once 'parser_sin_iskl_mysqli.php';
//include_once '/var/w  ww/html/parsers/phpQuery.php';


$query2 = $db->query("SELECT * FROM `parser_tovar_man` WHERE id_shop=12 and `date`!=DATE(NOW())");
//echo  $query2;
//echo $query2->num_rows;
//echo $db->error;
while($row = $query2->fetch_array(MYSQLI_ASSOC)) {
	pars_upd_tovar_0 ($sex,$id_shop, $row['size']);
	$content=get_page_html_new($row['url'],'');
	echo $content;

	
	$num = 1;
	
	if(strpos($content,'<div class="pages">')!==false) {
		$pages=explode('<div class="pages">',$content);
		$pages=explode('</div>',$pages[1]);

		$pages=explode('page=',$pages[0]);

		foreach($pages as $p_val) {
			$pages_cont=explode('">', $p_val, 2);
			if (is_numeric($pages_cont[0])) $pages_arr[]=$pages_cont[0];
		}

		if($pages_arr!='')$num=max($pages_arr); 
		unset($pages_arr);
	}
	$k=1;$ti=0;

 

	for($i=1;$i<=$num;$i++) {
		$content2=get_page_html_new($row['url'].'&page='.$i,'',1);
        echo $content2;
//        return 0;
        $html2 = phpQuery::newDocument($content2);
//		echo 'test1';
		foreach ($html2->find("div.product-item") as $e) {
            echo 1;

            $e = pq($e);
			$cat=$e->attr('data-category');
			$cat=explode('/',$cat);
			$cat=array_pop($cat);
			//находим синоним категории(делается для того чтобы небыло дублей категорий)
			$cat1=mb_strtolower($cat, 'UTF-8');
			if(array_search($cat1,$sin_arr)!==FALSE) {
				$cat=explode("@",array_search($cat1,$sin_arr)); 
				$cat=$cat[1];
			}
			//конец			
			$cat_tovar = explode("@",array_search($row['size'],$iskl_raz_arr));//выесняем из какой категории товар(женский большой, женский маленький)
			$cat_tovar = $cat_tovar[1];
			
			if(array_search($cat,$iskl_cat_arr)===FALSE) {//исключения
				$tovar_id=$e->attr('data-id');
				$name=$e->attr('data-brand');
				$price=str_replace(" ","",$e->attr('data-price'));//price
				
				$photo =$e->find('img:eq(0)')->attr('src');
						
				if(strpos($photo,'.gif')!==false) {
					$photo =$e->find('img:eq(0)')->attr('data-src');
				}

				$dl_photo='product-'.$tovar_id;
			
				$url_tovar = 'http://www.kupivip.ru/spl/'.$tovar_id;
	 

				if($photo!='') {
					ins_upd_img ($id_shop,$dl_photo.'.jpg',$photo);															
					$logo = $dl_photo.'.jpg';						
				}
				
				$status_tovar=$e->attr('class');
				
				if($cat!='' and $name!='' and $logo!='' and $price!=''){} else {
					$erro_val.=$row['url'].'&page='.$i.'<br>';
					++$pars_polom;
					if($pars_polom==50) {
						$to1 = 'dotaantifrag@gmail.com';
						$subject1 = 'Парсер поломался'; 
						$message1 = 'Парсер сломался. '.$_SERVER['PHP_SELF'].'<br>'.$erro_val;
						$yournam = 'kompaskreditov.ru';
						$yourmylo = 'mail@razmerok.ru';
						$mailheader = "From: Razmerok <mail@razmerok.ru>\nReply-To: \"".strip_tags($yournam)."\"<$yourmylo>\nX-Mailer: razmerok.ru Mailer\nContent-Type: text/html; charset=\"utf-8\"\nMime-Version: 1.0";
						mail($to1, $subject1, $message1, $mailheader);				
					}
				}
				if($status_tovar!='product-item unavailable')
				    ins_upd_tovar($cat_tovar,$id_shop,$row['size'],$url_tovar,$cat." ".str_replace("'","ˮ",$name),$logo,$price,$cat,$mas_cat);
				unset($cat);unset($url_tovar);unset($name);unset($photo);unset($price);unset($rt);unset($logo);unset($status_tovar);
			}
			$ti++;
			
		}
		phpQuery::unloadDocuments($html2);
	}
	$db->query("UPDATE `parser_tovar_man` SET `date`='".date('Y-m-d')."' WHERE `id_shop`='".$row['id_shop']."' AND `size`='".$row['size']."';") or die ("ERROR 49");
	echo '<hr>Файл '.$row['file'].', размер = '.$row['size'].'<br>Всего '.$ti.' (вместе с кросовками)<br>Добавленно = '.($k-1).'<br><br>';
}
pars_delit_tovar ($sex,$id_shop);
/*
$query = "SELECT t.* FROM (SELECT url_shop FROM `exp_tovar_sho_L_M` WHERE `color`='' AND `season`='' AND `material`='' AND `id_shop`='".$id_shop."'
	UNION
SELECT url_shop FROM `exp_tovar_sho_S_M` WHERE `color`='' AND `season`='' AND `material`='' AND `id_shop`='".$id_shop."') t
GROUP BY `url_shop`";
$result = $db->query ( $query ) or die ( "Error while selecting ".$db->error() );
if ($result->num_rows>0) {
	while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
		

		$cont = get_page_html(str_replace('http://','https://',$row['url_shop']));
		
		$html = str_get_html($cont);		
		
		if($html!='') {
			
			foreach ($html->find("//div[@class='product__desc-block']/dl") as $e) {
				if(strpos(trim(mb_strtolower($e->find("//dt",0)->plaintext, 'UTF-8')), 'цвет') !== false) $color=$e->find("//dd",0)->plaintext;
				if(strpos(trim(mb_strtolower($e->find("//dt",0)->plaintext, 'UTF-8')), 'состав') !== false) $material=$e->find("//dd",0)->plaintext;
			}
			 
			$color=trim($color);
			$material=trim($material); 
			
			$db->query("UPDATE `exp_tovar_sho_L_M` SET `color`='".$db->real_escape_string($color)."',`material`='".$db->real_escape_string($material)."' WHERE `url_shop`='".$row['url_shop']."'");
			$db->query("UPDATE `exp_tovar_sho_S_M` SET `color`='".$db->real_escape_string($color)."',`material`='".$db->real_escape_string($material)."' WHERE `url_shop`='".$row['url_shop']."'");
			
			if($desc!='' or $color!='' or $season!='' or $material!='') {
				if($desc!='') $cnt_desc=1; else $cnt_desc=0;
				if($color!='') $cnt_color=1; else $cnt_color=0;
				if($season!='') $cnt_season=1; else $cnt_season=0;
				if($material!='') $cnt_material=1; else $cnt_material=0;
				$db->query("INSERT INTO `parser_material`(`id_shop`, `date`, `desc`, `color`, `season`, `material`) VALUES ('".$id_shop."',NOW(),'".$cnt_desc."','".$cnt_color."','".$cnt_season."','".$cnt_material."')");
			}
		
			unset($color);
			unset($material);
			
		} else {
			++$rrrr;
			if($rrrr>200) exit();
		}
	}
}
*/
?>