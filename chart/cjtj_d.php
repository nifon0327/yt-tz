<?php   
/*
图高固定
刻度自适应

已更新*********电信---yang 20120801
*/
//车缝日生产曲线图  要求间隔代表的数量可调整
include "../basic/chksession.php";
include "../basic/parameter.inc";
$SearchRows=" AND C.Tid='$Tid'";
$StartDay=$m."-01";
$PreYandM=date("Y-m",strtotime("$StartDay -1 month"));	//上月
$GaplineW=date("t",strtotime($StartDay));				//显示日期天数																			
$Diameter=4;											//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 				//使用的中文字体

//首月默认值
$CheckType=mysql_fetch_array(mysql_query("SELECT C.FirstObj,C.Remark FROM $DataIn.sc1_counttype C WHERE 1 AND Id=$Tid LIMIT 1",$link_id));
$FirstObj=$CheckType["FirstObj"];
$Remark=$CheckType["Remark"];

//求最高日生产数量
$MaxResult=mysql_fetch_array(mysql_query("
		SELECT MAX(Qty) AS MaxQty FROM(
			SELECT SUM(Qty) AS Qty,Date FROM(
			SELECT SUM(C.Qty) AS Qty,C.Date FROM $DataIn.sc1_cjtj C,$DataIn.yw1_ordersheet Y WHERE Y.POrderId=C.POrderId  AND left(C.Date,7)='$m' $SearchRows GROUP BY C.Date
			)M GROUP BY Date
		)N
		",$link_id));
$MaxValue1=intval($MaxResult["MaxQty"]);		//出货和下单的最大值,为与金额计算共用，加上取整函数

$YvlaueLenght=strlen($MaxValue1)-2;				//最高值长度-2：
$Ystep=pow(10,$YvlaueLenght);					//间隔步长:长度*10
$Yvalue=$Ystep*intval($MaxValue1/($Ystep*10));	//Y轴间隔值
$Ys=ceil($MaxValue1/$Yvalue)+1;					//Y轴间隔数
$imH=$Ys*25+100;								//图片高度
$imW=800;										//图片固定宽度
$titleX=$imW/2-80;								//标题输出的起始X位置
$HjgSetp=20;									//网格Y轴间隔像素
$WjgSetp=20;									//网格X轴间隔像素
$wCube=20;										//柱形图宽
$imT=50;										//图域上坐标
$imL=60;										//图域左坐标
$imR=$imW-10;									//图域右坐标
$imB=$imH-50;									//图域下坐标

$image = imagecreate ($imW,$imH);								//输出空白图片区域
imagecolorallocate($image,255,255,255);							//图片背景色
$BlackColor = 	imagecolorallocate($image,0,0,0);				//黑色
$GridColor	=	imagecolorallocate($image,153,153,153);			//网格颜色，灰色
$redline 	= 	imagecolorallocate($image,255,0,0);				//红色
$yellowline 	= 	imagecolorallocate($image,255,0,0);				//红色
$greenline 	= 	imagecolorallocate($image,40,170,40);			//绿色
$blueline 	= 	imagecolorallocate($image,0,25,168);			//蓝色
$Gridbgcolor=imagecolorallocate($image,221,221,221);

$Tile="车间生产统计图($Remark)";										//图像标题

imagettftext($image,13,0,$titleX,20,$BlackColor,$UseFont,$Tile);//输出标题
imagerectangle($image,$imL,$imT,$imR,$imB,$BlackColor); 		//画矩形：曲线图范围



//输出日期线
$dayToline = array();
$NowDay=$StartDay;
for($i=1;$i<=$GaplineW;$i++){
	if($i%2==0){
		//矩形
		imagefilledrectangle($image,($imL+$WjgSetp*($i-1))+1,$imT+1,($imL+$WjgSetp*$i),$imB-1,$Gridbgcolor); //画填充矩形
		}
	$xJGvalue=date("d",strtotime($NowDay));
	imageline($image,($imL+$WjgSetp*$i),$imT+1,($imL+$WjgSetp*$i),$imB,$GridColor);						//输出纵向网格线:
	imageline($image,($imL+$WjgSetp*$i),$imB,($imL+$WjgSetp*$i),$imB+15,$BlackColor);						//输出纵向分隔线:
	$dayToline[$i]=$i;
	imagestring($image,3,($imL-15+$WjgSetp*$i),$imT-15,$xJGvalue,$BlackColor);					//上输出日期
	imagestring($image,3,($imL-15+$WjgSetp*$i),$imH-48,$xJGvalue,$BlackColor);					//下输出日期
	$NowDay=date("Y-m-d",strtotime("+ 1 days",strtotime($NowDay)));
  	}
$Month=date("Y年m月",strtotime($StartDay));
imagettftext($image,10,0,(60+$WjgSetp*$i),$imT-5,$BlackColor,$UseFont,$Month);				//上输出月份
imagettftext($image,10,0,(60+$WjgSetp*$i),$imH-35,$BlackColor,$UseFont,$Month);				//下输出月份


//输出横向金额线
$YStep=25;
for($i=0;$i<=$Ys;$i++){	
	$TempValue=$Yvalue*$i;//输出的数量
	if($i==0 || $i==$Ys){
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$BlackColor);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$YStep,$imR,$imB-$i*$YStep,$GridColor);			//间隔线
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$BlackColor);//短线
		$TempValueX=50-strlen($TempValue)*6;//数量缩进位
 		imagestring($image,3,$TempValueX-$MoveLeft,($imB-$YStep*$i)+$MoveLeft-7,$TempValue,$BlackColor);//输出数量
		}
	}




///////////////////////////
//读取数据
imagesetthickness ($image,1);
//如果是当前月，则至今天为止
$YB=$imB-1;	//底部座标
$NowDay=$StartDay;
//前一月平均生产值
$CheckObjRow=mysql_fetch_array(mysql_query("
	SELECT AVG(vQty) AS vQty FROM(
		SELECT SUM(vQty) AS vQty FROM(
		SELECT SUM(C.Qty) AS vQty,C.Date FROM $DataIn.sc1_cjtj C,
		$DataIn.yw1_ordersheet Y WHERE Y.POrderId=C.POrderId 
		AND DATE_FORMAT(C.Date,'%Y-%m')='$PreYandM' $SearchRows GROUP BY C.Date 
		)N GROUP BY Date
	) M",$link_id));
$CheckObjRow=floor($CheckObjRow["vQty"]);
$ObjQty=$CheckObjRow==0?$FirstObj:$CheckObjRow;
//如果是当前月，则至今天为止
for($i=1;$i<=$GaplineW-1;$i++){
	$curveRow=mysql_fetch_array(mysql_query("
		SELECT SUM(Qty) AS Qty FROM(
		SELECT SUM(C.Qty) AS Qty FROM $DataIn.sc1_cjtj C,$DataIn.yw1_ordersheet Y WHERE Y.POrderId=C.POrderId  AND C.Date='$NowDay' $SearchRows
		)M
		",$link_id));
		$cfQty=$curveRow["Qty"]==""?0:$curveRow["Qty"];
		if($cfQty>0){
			$TempColor=$greenline;
			
			if($cfQty<$ObjQty){
				$TempColor=$yellowline;
				}
			$XL=$imL+($i-1)*$WjgSetp;
			$XR=$XL+$wCube;
			$YT =$imB+$AmountStart-intval($cfQty/($Yvalue/25));	//当前Y轴坐标
			imagefilledrectangle($image,$XL+3,$YB,$XR-3,$YT,$TempColor); //画填充矩形
			}
		$NowDay=date("Y-m-d",strtotime("+ 1 days",strtotime($NowDay)));
		if($NowDay>date("Y-m-d")){
			break;
			}
	}
if($ObjQty!=0){
	//画上月均值
	$YT =$imB+$AmountStart-intval($ObjQty/($Yvalue/25));	//当前Y轴坐标
	imageline($image,$imL,$YT,$imR-100,$YT,$redline);
	//输出说明文字
	imagettftext($image,10,0,$imR-90,$YT+5,$redline,$UseFont,"$ObjQty PCS");		//输出金额线说明
	}
//////////////////////////
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);               //释放资源
?>