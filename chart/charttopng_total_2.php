<?php   
//设置颜色字体**********电信---yang 20120801
/////////////////////////////////////////////////
$UseFont = "../model/Fonts/simhei.ttf"; 						//使用的中文字体
//imagecolorallocate($image,155,200,255);							//图像背景色
////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);
$TextGray=imagecolorallocate($image,102,0,126);
$JgColor= imagecolorallocate($image,155,200,255);
//颜色列表
//*
$Tallin=imagecolorallocate($image,191,208,234); $Tallout=imagecolorallocate($image,0,55,152);
//*/
//客户颜色
$ColorSql=mysql_query("SELECT CompanyId,ColorCode FROM $DataIn.chart2_color ORDER BY CompanyId",$link_id);
if($ColorRow= mysql_fetch_array($ColorSql)){
	$i=1;
	do{
		$TheCompanyId=$ColorRow["CompanyId"];
		$ColorCode=$ColorRow["ColorCode"];
		$R=hexdec(substr($ColorCode,0,2));
		$G=hexdec(substr($ColorCode,2,2));
		$B=hexdec(substr($ColorCode,-2));
		$mtColor="T".strval($TheCompanyId)."in";
		$$mtColor=imagecolorallocate($image,$R,$G,$B);
		$mtColor="T".strval($TheCompanyId)."out";
		$$mtColor=imagecolorallocate($image,$R,$G,$B);
		//imagettftext($image,12,0,905,500-$i*30,$TextBlack,$UseFont,$mtColor."-".$R."-".$G."-".$B);$i++;
		}while($ColorRow= mysql_fetch_array($ColorSql));
	}
$Totherin=imagecolorallocate($image,186,87,136);$Totherout=imagecolorallocate($image,150,0,75);//20万以下
?>