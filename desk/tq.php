<?php   
//电信-zxq 2012-08-01
include "kqfun/tqtohtml.php";
CreateShtml(0101);

$url="http://tianqi.2345.com/d/city/59493.htm";
$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
$content=iconv("GB2312","UTF-8",$str);								//将收到的内容再转回UTF-8
$start="相关地区：";
$end="尊敬的";
$content = strstr( $content, $start ); 
$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start )); 
$content= str_replace("\"","'",$content);

$content=str_replace("&&","&",str_replace("' />","'&",str_replace("><img",">&", str_replace("<td width='14%'>","*",$content))));
$content=strip_tags($content);
//以*号分割

$tqTable="<table width='100%' border='0' cellspacing='0'><tr>";
$theContent= explode("*",$content);
$lenTemp=count($theContent);
for($i=1;$i<4;$i++){
	$tqTemp=explode("&",$theContent[$i]);
	$lendayTemp=count($tqTemp);
	if($lendayTemp<4){//长度为3
		$tqTable.="<td width='8%' align='center'><img $tqTemp[1]></td><td align='left' class='TQ'>$tqTemp[0]<br>$tqTemp[2]</td>";
		}
	else{//长度为4
		$tqTable.="<td width='8%' align='center'><img $tqTemp[1]><img $tqTemp[2]></td><td align='left' class='TQ'>$tqTemp[0]<br>$tqTemp[3]</td>";
		}
	}
$tqTable.="</tr></table>";
echo $tqTable;

/*

$sImg="<img";
$eImg="/>";
$IMG= strstr( $content, $sImg );
$IMG = substr( $IMG,0, strpos( $IMG, $eImg )); //取第个IMG图片
echo $IMG.">";


$start="今天)";
$end="*";
$content = strstr( $content, $start ); 
$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start )); */
//echo $content;

?>