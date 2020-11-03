<?
$search = 'Сапожки';

$num = (key(preg_grep('|'.preg_quote($search).'|i',file('sinon.txt')))+1);
if($num>1) {
	$mas = file('sinon.txt');
	$exp = explode(',',$mas[$num-1]);
	$sinon = $exp[0];
}

echo $sinon;
?>