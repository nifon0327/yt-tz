<?php
//电信-ZX  2012-08-01
$url="http://tianqi.2345.com/shenzhen/59493.htm";
$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
$content=iconv("GB2312","UTF-8",$str);	//将收到的内容再转回UTF-8
$start="相关地区：";
$end="仅供参考！";
$content = strstr( $content, $start );

$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start ));
$content= str_replace("\"","'",$content);
$content= str_replace("</li>","*",$content);
$sImg="<img";
$eImg="/>";
$IMG= strstr( $content, $sImg );
$IMG = substr( $IMG,0, strpos( $IMG, $eImg )); //取第个IMG图片
$tqImg=$IMG." width='60' height='50'>";
$tqImg=str_replace("src='","src='http://tianqi.2345.com",$tqImg);

$content=strip_tags($content);
$start="今天)";
$end="*";
$content = strstr( $content, $start );
$tqTable= substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start ));
/*
$url="http://www.koubei.com/city/weatherinfo.html?name=上海";
$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
$content=iconv("GB2312","UTF-8",$str);								//将收到的内容再转回UTF-8
$start="<ul id=\"ThreeDays\" class=\"FLli\">";
$end="</ul>";
$content = strstr( $str, $start );
$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start ));
$tq= explode("</li>",str_replace("<li>","",$content));
//echo $content;
$ToDay=date("Y-m-d");
for($i=0;$i<1;$i++){
	$theContent=$tq[$i];
	$start="<img";
	$end="alt";
	$Img = strstr( $theContent, $start );
	$Img = substr( $Img, strlen( $start ), strpos( $Img, $end ) - strlen( $start ) );
	$theContent =iconv("GB2312","UTF-8",chop(strip_tags(str_replace("</td>","@</td>",$theContent)))); //防止乱码，再转UTF-8
	$theContent= explode("@",$theContent);
	$tqImg="<img $Img width='60' height='60'>";
	$tqTable.="$theContent[2] $theContent[3] $theContent[4]";
	}
*/
?>