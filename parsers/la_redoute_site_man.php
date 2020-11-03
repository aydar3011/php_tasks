<?
set_time_limit (0);


$id_shop=5;//айди магазина
$sex='man';//мужская обувь
include_once '/var/www/admin/www/russia-fin.online/parser/parser_header_mysqli.php';
$headers = array(
    "User-Agent: La Redoute/9.5.1",
    "Authorization: Basic YjM2M2NkM2EtMGNiYS00NDE3LTllMDMtZTEzZDQyYTIxM2Fi",
    "Accept-Language: ru-RU"
);

$query2 = "SELECT * FROM `url_to_parse` WHERE `id_shop`='".$id_shop."' AND `cat_tovar`='man'";
$result2 = $db->query($query2);
if($result2->num_rows>0) {//проверяем есть ли url для парсинга, если есть то парсим товары, если нет то парсим URL чтобы после этого парсить товары
	include_once '/var/www/admin/www/russia-fin.online/parser/parser_sin_iskl_mysqli.php';
	while($row_pars = $result2->fetch_assoc()){

        $val='https://www.laredoute.ru'.$row_pars['url_shop'];

        $server_ne_boley=0;
        do {
            $html = get_page_html_new($val, "", 1, $headers);
            usleep(500000); //спим 0.5с, чтобы не забанили
            ++$server_ne_boley;
            if($server_ne_boley>2) {
                sleep(1);
                if($server_ne_boley>50){
                    exit();
                    $html='1';
                }
            }
        } while ($html == '');
        $data = json_decode($html);
        $product_data = $data->productVariants[0];
        $name = $product_data->name;
        $name=strip_tags($name);
        $name=trim($name);
        $url = "https://www.laredoute.ru/ppdp/prod-$product_data->productID.aspx";
        //Получаем цвета и размеры
        $color = array();
        $razmer_arr = array();
        foreach ($product_data->colors as $val){
            $color[] = $val->name;
            foreach ($val->sizes as $size){
                if ($size->defaultOffer->stockState  == "AVAILABLE"){
                    $razmer_arr[] = $size->name;
                }
            }
        }
        $color = implode(', ', $color);
        $desc=$product_data->detailedDescription;
        $desc=trim(strip_tags($desc));

        $prod_id='prod-'.$product_data->productID;

        $price = preg_replace('/[^,\d]/', '', $product_data->finalPrice);
        $price = str_replace(',','.',$price);

        $img=$product_data->defaultImage;

        $category=$name;
        $category=str_replace(',','',$category);
        $category=str_replace('.','',$category);
        $category = str_replace("'","",$category);
        $category = str_replace('"','`',$category);
        $category=trim($category);


        $cat_n=$category;
        $cat23 = explode(' ',$cat_n);


        $num_cat=count($cat23);
        if(count($cat23)>1) {
            for($i_cat=1;$i_cat<=$num_cat;$i_cat++) {
                $cat_prov=implode(' ',$cat23);
                //находим синоним категории(делается для того чтобы небыло дублей категорий)
                $cat1=mb_strtolower($cat_prov, 'UTF-8');
                if(array_search($cat1,$sin_arr)!==FALSE) {
                    $cat23=explode("@",array_search($cat1,$sin_arr));
                    $category=$cat23[1];
                    $cat_sin=1;
                    break;
                } else $last_cat=array_pop($cat23);
                //конец
            }
        }
        if($cat_sin!='1' and $last_cat!='') {
            $category=$last_cat;
        }
        unset($last_cat);
        unset($cat_sin);
        //находим синоним категории(делается для того чтобы небыло дублей категорий)
        $cat1=mb_strtolower($category, 'UTF-8');
        if(array_search($cat1,$sin_arr)!==FALSE) {
            $cat=explode("@",array_search($cat1,$sin_arr));
            $category=$cat[1];
        }
        //конец

        if($category!='' and $name!='' and $img!='' and $price!=''){} else {
            $erro_val.='@'.$val.'@'.$category.'@'.$name.'@'.$img.'@'.$price.'@<br>';
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

        if($name!='') {
			if($razmer_arr!='') {
				$razmer_arr=array_unique($razmer_arr);
				foreach($razmer_arr as $razm_t) {//1 размер=1 товар
					$desc_fr_1='<br><b>Внимание! На сайте LaRedout используются французские размеры, а на нашем сайте мы показываем соответствующий им российский размер. ';
					$desc_fr_2=' российский размер = ';
					$desc_fr_3=' французскому.</b>';
					if($razm_t=='38') {
						$razm_t='37';
					}elseif($razm_t=='39') {
						$razm_t='38';
						$desc1.=$desc.$desc_fr_1.'38'.$desc_fr_2.'39'.$desc_fr_3;
					}
					elseif($razm_t=='40') {
						$razm_t='39';
						$desc1=$desc.$desc_fr_1.'39'.$desc_fr_2.'40'.$desc_fr_3;
					}
					elseif($razm_t=='41') {
						$razm_t='40';
						$desc1=$desc.$desc_fr_1.'40'.$desc_fr_2.'41'.$desc_fr_3;
					}
					elseif($razm_t=='42') {
						$razm_t='40';
						$desc1=$desc.$desc_fr_1.'40'.$desc_fr_2.'42'.$desc_fr_3;
					}
					elseif($razm_t=='43') {
						$razm_t='42';
						$desc1=$desc.$desc_fr_1.'42'.$desc_fr_2.'43'.$desc_fr_3;
					}
					elseif($razm_t=='44') {
						$razm_t='43';
						$desc1=$desc.$desc_fr_1.'43'.$desc_fr_2.'44'.$desc_fr_3;
					}
					elseif($razm_t=='45') {
						$razm_t='44';
						$desc1=$desc.$desc_fr_1.'44'.$desc_fr_2.'45'.$desc_fr_3;
					}
					elseif($razm_t=='46') {
						$razm_t='45';
						$desc1=$desc.$desc_fr_1.'45'.$desc_fr_2.'46'.$desc_fr_3;
					}
					elseif($razm_t=='47') {
						$razm_t='46';
						$desc1=$desc.$desc_fr_1.'46'.$desc_fr_2.'47'.$desc_fr_3;
					}
					elseif($razm_t=='48') {
						$razm_t='47';
						$desc1=$desc.$desc_fr_1.'47'.$desc_fr_2.'48'.$desc_fr_3;
					}
					elseif($razm_t=='49') {
						$razm_t='48';
						$desc1=$desc.$desc_fr_1.'48'.$desc_fr_2.'49'.$desc_fr_3;
					}
					elseif($razm_t=='50') {
						$razm_t='49';
						$desc1=$desc.$desc_fr_1.'49'.$desc_fr_2.'50'.$desc_fr_3;
					}
					elseif($razm_t=='51') {
						$razm_t='50';
						$desc1=$desc.$desc_fr_1.'50'.$desc_fr_2.'51'.$desc_fr_3;
					}
					if($desc1!='') $desc1=trim($desc1,'. ');
					$cat = explode("@",array_search($razm_t,$iskl_raz_arr));//выесняем из какой категории товар(женский большой, женский маленький)
					$cat = $cat[1];
					if(array_search($category,$iskl_cat_arr)===FALSE and array_search($razm_t,$iskl_raz_arr)!==FALSE) {//проверка на категорию(которую не парсим) и размер(который нам не нужен)
						$img_name=substr(strrchr($img, "/"), 1);
						ins_upd_img ($id_shop,$img_name,$img);
						if($desc1!='') ins_upd_tovar($cat,$id_shop,$razm_t,$url,$name,$img_name,$price,$category,$mas_cat,$desc1,$color,$season,$material); else ins_upd_tovar($cat,$id_shop,$razm_t,$url,$name,$img_name,$price,$category,$mas_cat,$desc,$color,$season,$material);
					}
					//echo '1';//Костыль
				}

				unset($razmer_arr);
			}
		} else {
			echo $val;
		}
		$db->query("DELETE FROM `url_to_parse` WHERE `id`='".$row_pars['id']."'");//удаляем URL чтобы повторно не парсить
	}
    $db->query("UPDATE `parser_magaz` SET `date`='".date('Y-m-d')."' WHERE `id_shop`='".$id_shop."' AND `cat_tovar`='man'");
	pars_delit_tovar($sex,$id_shop);
} else {
    pars_delit_tovar($sex,$id_shop);
    $query3 = "SELECT * FROM `parser_magaz` WHERE `id_shop`='".$id_shop."' and `cat_tovar`='woomen' and `date`!=DATE(NOW())";
    $result3 = $db->query($query3);
//    echo "$query3<br>";
    if ($result3->num_rows > 0){
        $count_per_page = 24; // количество товаров на странице
        $page = 1;
        $limk_arr = [];
        while (true) {
            $pars_href = 'https://www.laredoute.ru/CatalogService/External/MobileApp/MobileAppService.svc/v7/products/list?catId=516&kwrd=&visualProductsKey=&pn='.$page.'&filters=&isHighRes=true&locale=RDRU&brand=LRDT';//отсюда берем URL для парсинга
            usleep(500000); //спим 0.5с, чтобы не забанили
            $html = get_page_html_new($pars_href, "", 1, $headers);
//            echo "$html<br>";
            $data = json_decode($html, 1);
//            var_dump($data);
            foreach ($data['products'] as $product){
                $limk_arr[] = "/CatalogService/External/MobileApp/MobileAppService.svc/v5/products/".$product['productID']."/".$product['docID']."?isHighRes=true&isMultiPdp=true&locale=RDRU&brand=LRDT";
            }
            if (count($data['products']) < $count_per_page) {
                break;
            }
            $page++;
        }


        if($limk_arr!='') {
            ins_upd_link($limk_arr, $id_shop, 'man');
            pars_upd_tovar_0 ($sex,$id_shop);
        }
    }
}
?>