<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Parcing</title>
</head>
<body>
<?php
require ('simple_html_dom.php');


// ������ DOM �� URL ������
$category='http://demo.radiocity.kz/avtomatika/';
$html = file_get_html($category.'?limit=100');
 

$connect=mysqli_connect("localhost","root","","test");//����������� � ����� ������

$count=0;
// ������� ��� ��������
foreach($html->find('div[class=caption]') as $element)
    foreach ($element->find('a') as $link){
        
        if(is_numeric($link->plaintext[28]) and $link->plaintext[27]==' ') break; # ���������� �������� �� �������� �������, ����� ������������� �� ��������� ���� �������� ��������������� ������� � �����


        $html= file_get_html($link->href);


        $image=$html->find('div[id=content] img',0);    #����������� ������
        
        $image = file_get_contents($image->src);

        file_put_contents("images/file_{$count}.jpg", $image);#��������� �����������
        $count+=1;
        
        $name=$link->plaintext;
        echo $name;
        
        $description=$html->find('div[id=tab-description]', 0);
        echo $description;  #�������� ������

        $attribute=$html->find('div[id=content] ul[class=list-unstyled]',0);
        echo $attribute;    #�������� ������

        $price=$html->find('div[id=content] ul[class=list-unstyled]',1);
        echo $price;    #���� ������
        
        #�������� � ����������� ����
        $attribute=trim($attribute->plaintext);
        $description=trim($description->plaintext);
        $price=trim($price->plaintext);
        
        #��������� � ���� ������
        mysqli_query($connect,"INSERT INTO `goods`(`name`, `attribute`,`description`,`price`) VALUES ('{$name}','{$attribute}','{$description}','{$price}')");
}
?>

</body>
</html>