<?php 
//电信-zxq 2012-08-01
//共享代码
include "../basic/chksession.php";
include "../basic/parameter.inc";
$ValueM=24;//要计算的月份数
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -$ValueM month"));
$StartM=date("Y-m",strtotime("$NowDate -$ValueM month"));
$EndM=date("Y-m");
//获取配件名称
$checkName=mysql_fetch_array(mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$StuffId' ORDER BY StuffId DESC LIMIT 1",$link_id));
$StuffCname=$checkName["StuffCname"];
$tjStr=" AND M.OrderDate>'$StartM'";
//计算月下单最高数量
$MaxResult = mysql_fetch_array(mysql_query("
SELECT MAX(Qty) AS MaxValue1 FROM ( 
SELECT SUM(G.OrderQty) AS Qty 
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE  G.StuffId='$StuffId' $tjStr GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
)A
",$link_id));
$MaxValue1=intval($MaxResult["MaxValue1"]);	//最高
$MaxValue1=$MaxValue1<1000?1000:$MaxValue1;

//********************
//间隔值计算
$Ys=11;					//Y轴间隔数,每隔25点
$Temp1=intval($MaxValue1/$Ys);
$YvlaueLenght=strlen($Temp1)-2;//平均值长度-2
$Ystep=pow(10,$YvlaueLenght);			
$Yvalue=$Ystep*intval($MaxValue1/($Ystep*10));	//Y轴间隔值
$imH=425;				//固定Y轴高度,

//********************

$MonthStep=50;													//月份间隔步长
$imW=$ValueM*$MonthStep+200;									//图像宽度,随月份数变化

$Diameter=8;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
$image = imagecreate ($imW,$imH);								//输出空白图像
imagecolorallocate($image,255,255,255);							//图像背景色

////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$TextColor = imagecolorallocate($image,$rColor,$gColor,$bColor);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextGray= imagecolorallocate($image,128,0,0);
$TextWhite= imagecolorallocate($image,255,255,255);
$redline 	= 	imagecolorallocate($image,255,0,0);				//红色
$greenline 	= 	imagecolorallocate($image,40,170,40);			//绿色
$blueline 	= 	imagecolorallocate($image,0,25,168);			//蓝色
//颜色列表
$T1001=imagecolorallocate($image,0,60,121); 	
$F1001=imagecolorallocate($image,0,60,121); 	
$R1001=imagecolorallocate($image,0,60,121);
$T1002=imagecolorallocate($image,111,141,185);		
$F1002=imagecolorallocate($image,111,141,185); 	
$R1002=imagecolorallocate($image,111,141,185);  
$th1002=imagecolorallocate($image,0,0,255); 
$Y1001=imagecolorallocate($image,255,128,0);


$titleX=$imW/2-130;												//标题输出的起始X位置
$imL=80;														//顶部X:左边距
//$Tile=$Y."年 ".$Name." 月下单、出货数量统计图 ";
$TFontSize=15;
$fontareaName = ImageTTFBBox($TFontSize,0,$UseFont,$StuffCname);  //获取文字的宽度
$Name_width = $fontareaName[2]-$fontareaName[0];//256
imagettftext($image,$TFontSize,0,$titleX+$Year_width,20,$TextBlack,$UseFont,"2年内配件订单数量趋势图");	//输出
imagettftext($image,11,0,80,40,$TextBlack,$UseFont,$StuffCname);	//输出

$imT=50;														//顶部Y:上边距
$imR=$imW-120;													//底部X:
$imB=$imH-100;													//底部Y:
imagerectangle($image,$imL,$imT,$imR+50,$imB,$TextBlack); 			//画矩形：曲线图范围
$MoveLeft=0;													//上下偏移量，柱体厚度
for($i=0;$i<=$ValueM+1;$i++){
	if($i==0){
		//imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep,$imB,$TextBlack);
		}
	else{
		//imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imT,$Gridcolor);
		}
	}

//金额间隔线
$YStep=25;
for($i=0;$i<=$Ys;$i++){	
	$TempValue=$Yvalue*$i;//输出的数量
	if($i==0 || $i==$countY){
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$TextBlack);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$YStep,$imR+50,$imB-$i*$YStep,$Gridcolor);			//间隔线
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$Gridcolor);//短线
		//输出金额
		$TempValueX=$TempValue<100000?10:3;
 		imagestring($image,3,$TempValueX-$MoveLeft+25,($imB-$YStep*$i)+$MoveLeft-7,$TempValue,$TextBlack);
		}
	}

//客户过滤:最高出货、或下单金额超过20万的客户

//初始化XL,XR,YB,YT
$wCube=30;		//柱体宽度
$jCube=10;		//柱体间隔
$ToDay=date("Y-m-d");
//月份检查:包括出货或下单的月份
imagesetthickness ($image,2);//线宽
$PrePointX="";
$PrePointY="";
for($j=0;$j<=$ValueM;$j++){
	//$thisM=date("Y-m",strtotime("$StartD + $j months + 5 day"));
	$thisM=date("Y-m",strtotime("$StartM + $j months "));
	//$NowM=date("n",strtotime("$StartD + $j months + 5 day "));
	$NowM=date("n",strtotime("$StartM + $j months  "));
	$PointX=$imL+$MonthStep*($j+1);					//点的X坐标
	//曲线图左移参数值:10
	//条形图左移参数值:30
	$moveLeft=30;
	if($NowM==1){
		imagettftext($image,10,0,$PointX-$moveLeft,$imB+15,$TextRed,$UseFont,$NowM."月");	//输出月份
		$NowY=date("Y",strtotime("$StartM + $j months  "));
		//$NowY=date("Y",strtotime("$StartD + $j months -5 day "));
		imagettftext($image,10,0,$PointX-($moveLeft+10),$imB+30,$TextRed,$UseFont,$NowY."年");	//输出年
		}
	else{
		imagettftext($image,10,0,$PointX-$moveLeft,$imB+15,$TextBlack,$UseFont,$NowM."月");	//输出月份
		}
	$clientSql=mysql_query("SELECT SUM(G.OrderQty) AS OrderQty,SUM(G.AddQty) AS AddQty FROM $DataIn.cg1_stocksheet G LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$thisM' AND G.StuffId='$StuffId'",$link_id);
	if($clientRow=mysql_fetch_array($clientSql)){
		$OrderQty=$clientRow["OrderQty"];					//当月订单总数
		$AddQty=$clientRow["AddQty"];						//当月增购总数
		if($OrderQty>0){
			$QtyHeight=round($OrderQty/($Yvalue/25));		//当月高度
			$PointY=$imB-$QtyHeight;					//点的Y坐标
			//画条形图
			imagefilledrectangle($image,$PointX-30,$imB-1,$PointX-10,$PointY,$th1002);
			if($AddQty>0){
				imagettftext($image,10,10,$PointX-35,$PointY-15,$TextRed,$UseFont,number_format($AddQty));		//输出增购数量
				}
			imagettftext($image,10,0,$PointX-40,$PointY-5,$TextBlack,$UseFont,number_format($OrderQty));				//输出订单数量
			/*
			曲线图
			imagefilledarc($image,$PointX,$PointY,$Diameter,$Diameter,0,360,$TextRed,IMG_ARC_PIE);	//输出当前点
			if($AddQty>0){
				imagettftext($image,10,0,$PointX+5,$PointY-15,$blueline,$UseFont,number_format($AddQty));				//输出增购数量
				}
			imagettftext($image,10,0,$PointX,$PointY-5,$TextRed,$UseFont,number_format($OrderQty));				//输出订单数量
			if($PrePointX!="" && $PrePointY!=""){
				imageline($image,$PrePointX,$PrePointY,$PointX,$PointY,$TextRed);					//画与前一点的连线
				}
			$PrePointX=$PointX;
			$PrePointY=$PointY;
			*/
			}
		}
		
		
	 //补数据
	  $bcResult=mysql_query("SELECT SUM(S.Qty) AS bcQty FROM $DataIn.ck3_bcsheet S
	                   LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid
					   WHERE DATE_FORMAT(M.Date,'%Y-%m')='$thisM' AND S.StuffId='$StuffId'",$link_id);
	   if($bcRow=mysql_fetch_array($bcResult)){
	      $bcQty=$bcRow["bcQty"];
		  if($bcQty>0){
		     $bcQtyHeight=round($bcQty/($Yvalue/25));
		     $PointY=$imB-$bcQtyHeight;
		     imagefilledrectangle($image,$PointX-9,$imB-1,$PointX+5,$PointY,$Y1001);
		     imagettftext($image,10,20,$PointX-9,$PointY,$Y1001,$UseFont,number_format($bcQty));
		  }
	   }
	 //退数据
      $thResult=mysql_query("SELECT SUM(S.Qty) AS thQty FROM $DataIn.ck2_thsheet S
			          LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid
					  WHERE DATE_FORMAT(M.Date,'%Y-%m')='$thisM' AND S.StuffId='$StuffId'",$link_id);
	  if($thRow=mysql_fetch_array($thResult)){
	     $thQty=$thRow["thQty"];
		 if($thQty>0){
		     $thQtyHeight=round($thQty/($Yvalue/25));
		     $PointY=$imB+$thQtyHeight;
		     imagefilledrectangle($image,$PointX-9,$imB-1,$PointX+5,$PointY,$TextGray);
		     imagettftext($image,10,0,$PointX-9,$PointY+10,$TextGray,$UseFont,number_format($thQty));		 
		  }
	   }
	 
	   
		
	}
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>