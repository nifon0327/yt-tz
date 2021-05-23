<?php 
//ewen 2013-03-12 OK
include "../basic/chksession.php";
include "../basic/parameter.inc";
$ValueM=12;//要计算的月份数
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -$ValueM month"));
$StartM=date("Y-m",strtotime("$NowDate -$ValueM month"));
$EndM=date("Y-m");
//获取配件名称
$checkName=mysql_fetch_array(mysql_query("SELECT GoodsName FROM $DataPublic.nonbom4_goodsdata WHERE GoodsId='$GoodsId' LIMIT 1",$link_id));
$GoodsName=$checkName["GoodsName"];
$tjStr=" AND B.Date>'$StartM'";

//计算月下单最高数量
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Qty) AS MaxValue1 FROM (
		SELECT SUM(A.Qty) AS Qty 
		FROM $DataIn.nonbom6_cgsheet A 
		LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
		WHERE A.GoodsId='$GoodsId' $tjStr
		GROUP BY DATE_FORMAT(B.Date,'%Y-%m')
		)Z						   
	",$link_id));
$MaxValue1=intval($MaxResult["MaxValue1"]);	//最高
$MaxValue1=$MaxValue1<100?100:$MaxValue1;

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
$fontareaName = ImageTTFBBox($TFontSize,0,$UseFont,$GoodsName);  //获取文字的宽度
$Name_width = $fontareaName[2]-$fontareaName[0];//256
imagettftext($image,$TFontSize,0,$titleX+$Year_width,20,$TextBlack,$UseFont,"1年内非BOM配件采购趋势图");	//输出
imagettftext($image,11,0,80,40,$TextBlack,$UseFont,$GoodsName);	//输出

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
	$thisM=date("Y-m",strtotime("$StartM + $j months "));
	$NowM=date("n",strtotime("$StartM + $j months  "));
	$PointX=$imL+$MonthStep*($j+1);					//点的X坐标
	//曲线图左移参数值:10
	//条形图左移参数值:30
	$moveLeft=30;
	if($NowM==1){
		imagettftext($image,10,0,$PointX-$moveLeft,$imB+15,$TextRed,$UseFont,$NowM."月");	//输出月份
		$NowY=date("Y",strtotime("$StartM + $j months  "));
		imagettftext($image,10,0,$PointX-($moveLeft+10),$imB+30,$TextRed,$UseFont,$NowY."年");	//输出年
		}
	else{
		imagettftext($image,10,0,$PointX-$moveLeft,$imB+15,$TextBlack,$UseFont,$NowM."月");	//输出月份
		}
	//采购
	$clientSql=mysql_query("SELECT SUM(A.Qty) AS Qty 
		FROM $DataIn.nonbom6_cgsheet A 
		LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
		WHERE A.GoodsId='$GoodsId' AND DATE_FORMAT(B.Date,'%Y-%m')='$thisM'",$link_id);
	if($clientRow=mysql_fetch_array($clientSql)){
		$cgQty=$clientRow["Qty"];					//当月订单总数
		if($cgQty>0){
			$QtyHeight=round($cgQty/($Yvalue/25));		//当月高度
			$PointY=$imB-$QtyHeight;					//点的Y坐标
			//画条形图
			imagefilledrectangle($image,$PointX-30,$imB-1,$PointX-10,$PointY,$th1002);
			imagettftext($image,10,0,$PointX-40,$PointY-5,$TextBlack,$UseFont,number_format($cgQty));				//输出订单数量
			}
		}
	}
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>