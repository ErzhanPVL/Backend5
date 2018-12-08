<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Parcing</title>
</head>
<body>
<?php

#Делаем DOM из URL ссылки
$category='http://demo.radiocity.kz/avtomatika/';
$html = file_get_contents($category.'?limit=100');

$connect=mysqli_connect("localhost","root","","test");//Соединяемся с базой данных

preg_match_all('#<div[^>]+?class\s*?=\s*?(["\'])caption\1[^>]*?>(.+?)</div>#su', $html, $res);

$count=0;

foreach ($res[0] as $element)
{
	preg_match_all('#<a.*>(.+?)</a>#su', $element, $link);
	
	if (is_numeric($link[1][0][28]) && $link[1][0][27]==' ') continue;
	$name=$link[1][0];
	
	foreach ($link[0] as $good)
	{
		preg_match_all('#href="([^"]+)"#su', $good, $href);
		$html=file_get_contents(substr($href[0][0], 6, -1));
		
		preg_match_all('#<div[^>]+?id\s*?=\s*?(["\'])content\1[^>]*?>(.+?)</div>#su',$html,$content);
		preg_match_all('#<img[^>]+?src\s*?=(.+?)"#su',$content[0][0],$img);
		$image=$img[0][0];
		echo $image.'>';
		$image = file_get_contents(substr($image, 10, -1));
		file_put_contents("images/file_{$count}.jpg", $image);#сохраняем изображения
        $count+=1;
		
		preg_match_all('#<ul[^>]+?class\s*?=\s*?(["\'])list-unstyled\1[^>]*?>(.+?)</ul>#su',$html,$res);
		$attribute=$res[0][4];
		echo $attribute;
		
		$price=$res[0][5];
		echo $price;
		
		preg_match_all('#<div[^>]+?id\s*?=\s*?(["\'])tab-description\1[^>]*?>(.+?)</div>#su',$html,$res);
		$description=strip_tags($res[0][0]);
		echo $description.'<br>';
		
		#приводим к нормальному виду
        $attribute=trim(strip_tags($attribute));
        $description=trim(strip_tags($description));
        $price=trim(strip_tags($price));
        
        #добавляем в базу данных
        mysqli_query($connect,"INSERT INTO `goods`(`name`, `attribute`,`description`,`price`) VALUES ('{$name}','{$attribute}','{$description}','{$price}')");
	
	}
}
?>

</body>
</html>