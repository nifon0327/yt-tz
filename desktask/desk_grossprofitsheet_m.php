<?php   	
//独立已更新电信---yang 20120801
//include "../basic/chksession.php";
include "../basic/parameter.inc";
//+------------------------+
//| pie3dfun.PHP//公用函数 |
//+------------------------+
define("ANGLE_STEP", 3); //定义画椭圆弧时的角度步长
//define("starX", 80); //在图的X位置
//define("starY", 40); //在图的Y位置

//global $font;
global $Fontcolor;

define("FONT_USED", "c:/windows/fonts/simhei.ttf"); // 使用到的字体文件位置
function draw_getdarkcolor($img,$clr) //求$clr对应的暗色
{
	$rgb = imagecolorsforindex($img,$clr);
	return array($rgb["red"]/2,$rgb["green"]/2,$rgb["blue"]/2);
}
function draw_getexy($a, $b, $d) //求角度$d对应的椭圆上的点坐标
{
	$d = deg2rad($d);
	return array(round($a*Cos($d)), round($b*Sin($d)));
}
function draw_arc($img,$ox,$oy,$a,$b,$sd,$ed,$clr) //椭圆弧函数
{
	$n = ceil(($ed-$sd)/ANGLE_STEP);
	$d = $sd;
	list($x0,$y0) = draw_getexy($a,$b,$d);
	for($i=0; $i<$n; $i++)
	{
		$d = ($d+ANGLE_STEP)>$ed?$ed:($d+ANGLE_STEP);
		list($x, $y) = draw_getexy($a, $b, $d);
		imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);
		$x0 = $x;
		$y0 = $y;
	}
}
function draw_sector($img, $ox, $oy, $a, $b, $sd, $ed, $clr,$strvalue) //画扇面(img,x=0+200,y=0+90,长半轴=200,短半轴90,角度起点0,角度终点, 色彩)
{
	$n = ceil(($ed-$sd)/ANGLE_STEP);
	$d = $sd;  //起点
	list($x0,$y0) = draw_getexy($a, $b, $d);  //求角度$d对应的椭圆上的点坐标
	imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);  //起如线直线，从圈外事到圆心
	for($i=0; $i<$n; $i++)
	{
		$d = ($d+ANGLE_STEP)>$ed?$ed:($d+ANGLE_STEP);
		list($x, $y) = draw_getexy($a, $b, $d);
		imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);  //不断用一段段直线外圈线，形成圆的外圈线
		$x0 = $x;
		$y0 = $y;
	}
	imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);  //终点线，从圆心到圈外
	list($x, $y) = draw_getexy($a/2, $b/2, ($d+$sd)/2);
	imagefill($img, $x+$ox, $y+$oy, $clr);  //对所封闭的区域进行填充色彩.也是
	//imagettftext($img, 10,-($sd+($d-$sd)/2), $x+$ox, $y+$oy, $Fontcolor, FONT_USED, $d."$sd+($d-$sd)/2");  //在饼图上输出数字
	imagettftext($img, 12,-($sd+($d-$sd)/2), $x+$ox, $y+$oy, $Fontcolor, FONT_USED, $strvalue);  //在饼图上输出数字
}
function draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clr,$strvalue) //3d扇面
{
	draw_sector($img, $ox, $oy, $a, $b, $sd, $ed, $clr,$strvalue);
	if($sd<180)  //要填充下面的三维的效果
	{
		list($R, $G, $B) = draw_getdarkcolor($img, $clr);
		$clr=imagecolorallocate($img, $R, $G, $B);
		if($ed>180) $ed = 180;
		list($sx, $sy) = draw_getexy($a,$b,$sd);
		$sx += $ox;
		$sy += $oy;
		list($ex, $ey) = draw_getexy($a, $b, $ed);
		$ex += $ox;
		$ey += $oy;
		imageline($img, $sx, $sy, $sx, $sy+$v, $clr);
		imageline($img, $ex, $ey, $ex, $ey+$v, $clr);
		draw_arc($img, $ox, $oy+$v, $a, $b, $sd, $ed, $clr);
		list($sx, $sy) = draw_getexy($a, $b, ($sd+$ed)/2);
		$sy += $oy+$v/2;
		$sx += $ox;
		imagefill($img, $sx, $sy, $clr);
	}
}
function draw_getindexcolor($img, $clr) //RBG转索引色
{
	$R = ($clr>>16) & 0xff;
	$G = ($clr>>8)& 0xff;
	$B = ($clr) & 0xff;
	return imagecolorallocate($img, $R, $G, $B);
}
// 绘图主函数，并输出图片
// $datLst 为数据数组, $datLst 为标签数组, $datLst 为颜色数组
// 以上三个数组的维数应该相等
$a=150;//椭圆长半轴
$b=50;//椭圆段半轴
$v=20;//圆饼高度
$font=5;//字体


function draw_img($datLst,$labLst,$clrLst,$a=320,$b=140,$v=16,$font=12)
{
	
	$starL=40;
	$starR=80;
	$starT=100;
	$starB=80;

	$ox = $starL+$a;  //5+$a;
	$oy = $starT+$b;  //5+$b;
	$fw = imagefontwidth($font);
	$fh = imagefontheight($font);
	$n = count($datLst);//数据项个数
	$w = $starL+10+$a*2+$starR;  //图片的宽度
	$h = $starT+10+$b*2+$v+($fh+2)*$n+$starB;  //图片的高度
	$img = imagecreate($w, $h);
	$Fontcolor=imagecolorallocate($img,0,0,0);  //字体色彩
	//转RGB为索引色
	for($i=0; $i<$n; $i++)
	$clrLst[$i] = draw_getindexcolor($img,$clrLst[$i]);
	$clrbk = imagecolorallocate($img, 0xff, 0xff, 0xff);
	$clrt = imagecolorallocate($img, 0x00, 0x00, 0x00);
	//填充背景色
	imagefill($img, 0, 0, $clrbk);
	//求和
	$tot = 0;
	for($i=0; $i<$n; $i++)
		$tot += $datLst[$i];  //求总数
	$sd = 0;
	$ed = 0;
	$ly = 10+$b*2+$v;
	for($i=0; $i<$n; $i++)
	{
		$sd = $ed;
		$ed += $datLst[$i]/$tot*360;
		//画圆饼
		$perValue=round(10000*($datLst[$i]/$tot))/100;
		$strvalue=$labLst[$i].":".$datLst[$i]."(".$perValue."%)";
		if($perValue<1){
			draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clrLst[$i],""); //$sd,$ed,$clrLst[$i]);
		}
		else{
			draw_sector3d($img, $ox, $oy, $a, $b, $v, $sd, $ed, $clrLst[$i],$strvalue); //$sd,$ed,$clrLst[$i]);
		}
		
		$colorBorder=imagecolorallocate($img,0,0,0);  //字体色彩
		//imagestring($img,$font,floor($ox+$sd ),floor($oy+$sd),$labLst[$i]."%",$colorBorder);
		//画标签
		//imagefilledrectangle($img, 5, $ly+$starT, 5+$fw, $ly+$fh+$starT, $clrLst[$i]);  //色彩
		//imagerectangle($img, 5, $ly+$starT, 5+$fw, $ly+$fh+$starT, $clrt);   //色彩外框
		//imagestring($img, $font, 5+2*$fw, $ly, $labLst[$i].":".$datLst[$i]."(".(round(10000*($datLst[$i]/$tot))/100)."%)", $clrt);
		//$str = iconv("GB2312", "UTF-8", $labLst[$i]);
		//$str=$labLst[$i];
				
		//imagettftext($img, $font, 0, 5+2*$fw, $ly+13+$starT, $clrt, FONT_USED, $strvalue);  //输出数字

		$ly += $fh+2;
	}
	//输出图形
	header("Content-type: image/png");
	//输出生成的图片
	imagepng($img);
}

// 以上生成图片,可以放在一个文件内

//以下获取数据:
$strCompnayName="";
$strValue="";
$noshipResult = mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 and S.Estate>'0'",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	}

$noProfitResult = mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0'",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($AllOrderAmount-$noProfitRow["oTheCost"]));
	}

//读取未出货订单金额
$ShipResult = mysql_query("
SELECT Amount,CompanyId,Forshort,oTheCost FROM(
SELECT Amount,CompanyId,Forshort,oTheCost,(Amount-oTheCost) AS OrderByAmount FROM(
SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,M.CompanyId,C.Forshort,A.oTheCost
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
LEFT JOIN (
SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,M.CompanyId
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0' GROUP BY M.CompanyId) A ON A.CompanyId=M.CompanyId
WHERE S.Estate>0  GROUP BY M.CompanyId ORDER BY C.OrderBy DESC) B )C ORDER BY OrderByAmount DESC
",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$cbAmount=0;
		$TempRMB=0;
		$TempPC=0;
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$TempRMB=sprintf("%.0f",$ShipRow["Amount"]);
		
		if($i<2){
			$strCompnayName="$Forshort";  //客户名称
			$strValue="$TempRMB";  //客户名称
		}
		else{
			$strCompnayName=$strCompnayName."|".$Forshort;  //客户名称
			$strValue=$strValue."|".$TempRMB;  //客户名称
		}
		
		//$strCompnayName=$strCompnayName.$Forshort."|";  //客户名称
		//$strValue=$strValue.$TempRMB."|";  //客户名称
		
		$TempRMBAmount+=$TempRMB;
		$cbAmount=sprintf("%.0f",$ShipRow["oTheCost"]);
		
		$ddbl=sprintf("%.1f",($TempRMB/$AllOrderAmount)*100);//=订单金额/总订单金额
		//毛利
		$TempProfit=sprintf("%.0f",$TempRMB-$cbAmount);
		$mlbl=sprintf("%.1f",($TempProfit/$AllProfitAmount)*100);//=毛利/总毛利
		//毛利率
		$TempPC=sprintf("%.0f",($TempProfit/$TempRMB)*100);
		//毛利总额
		$Total=$Total+$TempProfit;
			//传递客户
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
$AllPC=sprintf("%.0f",($Total/$TempRMBAmount)*100);

//

$labLst=explode("|",$strCompnayName);
$datLst=explode("|",$strValue);
//echo "123:$strCompnayName";
//$datLst = array(30, 20, 20, 20, 10, 20, 10, 20); //数据
//$labLst = array("浙江省", "广东省", "上海市", "北京市", "福建省", "江苏省", "湖北省", "安徽省"); //标签
$clrLst = array(0x99ff00, 0xff6666, 0x0099ff, 0xff99ff, 0xffff99, 0x99ffff, 0xff3333, 0x009999,0x8561FA,0xCB05FD,0xAA098E,0x1D690E,0xFBB202,0xF1CAFE,0x53AAA7,0x085451,0x9D9C54);
//画图
draw_img($datLst,$labLst,$clrLst);
//imagedestroy($image);   //释放资源
?>