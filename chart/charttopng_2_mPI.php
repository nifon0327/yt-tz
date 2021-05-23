<?php   
//独立已更新电信---yang 20120801

include "../basic/parameter.inc";
$image = imagecreate (1200,500);	
imagecolorallocate($image,255,255,255);		


////以下是饼形图****************************************************************
 include "../model/3DPI.php";
//以下获取数据:

/*
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


*/
//主分类在出货中的比例
$strMName="";
$strValue="";
$strColor="";
$ShipResult = mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType,R.Name,R.Color
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY T.mainType
",$link_id);

if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$cbAmount=0;
		$TempRMB=0;
		$TempPC=0;
		$mainType=$ShipRow["mainType"];
		$MName=$ShipRow["Name"];
		$Color=$ShipRow["Color"];
		$Color=str_replace("#","0x",$Color)*1;
		$TempRMB=sprintf("%.0f",$ShipRow["Amount"]);
		
		if($i<2){
			$strMName="$MName";  //客户名称
			$strValue="$TempRMB";  //客户名称
			$strColor="$Color";  //客户名称
		}
		else{
			$strMName=$strMName."|".$MName;  //客户名称
			$strValue=$strValue."|".$TempRMB;  //客户名称
			$strColor=$strColor."|".$Color;  //客户名称
		}
		
	
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}


$labLst=explode("|",$strMName);
$datLst=explode("|",$strValue);
$clrLsts =explode("|",$strColor);
$HeadTitle='出货 ';
//echo "123:$strCompnayName";
//$datLst = array(30, 20, 20, 20, 10, 20, 10, 20); //数据
//$labLst = array("浙江省", "广东省", "上海市", "北京市", "福建省", "江苏省", "湖北省", "安徽省"); //标签
//$clrLsts = array(0x99ff00, 0xff6666, 0x0099ff, 0xff99ff, 0xffff99, 0x99ffff, 0xff3333, 0x009999,0x8561FA,0xCB05FD,0xAA098E,0x1D690E,0xFBB202,0xF1CAFE,0x53AAA7,0x085451,0x9D9C54);
//$clrLsts = array(0xff00ff, 0xFF6633, 0x00b700, 0x0000c6, 0x86007E, 0xea0000, 0xff3333, 0x009999,0x8561FA,0xCB05FD,0xAA098E,0x1D690E,0xFBB202,0xF1CAFE,0x53AAA7,0x085451,0x9D9C54);

//画图
//draw_img($image,$datLst,$labLst,$clrLst,$a=320,$b=140,$v=16,$font=12)
$ox=350; //圆心点,
$oy=160;
draw_img($image,$HeadTitle,$datLst,$labLst,$clrLsts,$ox,$oy,$a=200,$b=90);   //画饼图
//imagedestroy($image);   //释放资源
// include "../model/3DPI.php";

//主分类在下单的比例

$strMName="";
$strValue="";
$strColor="";
$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType,R.Name,R.Color
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 $TJB   and T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY T.mainType 
			",$link_id);


if ($ShipRow = mysql_fetch_array($mainTypeSql)) {
	$i=1;
	do{
		$cbAmount=0;
		$TempRMB=0;
		$TempPC=0;
		$mainType=$ShipRow["mainType"];
		$MName=$ShipRow["Name"];
		$Color=$ShipRow["Color"];
		$Color=str_replace("#","0x",$Color)*1;
		$TempRMB=sprintf("%.0f",$ShipRow["Amount"]);
		
		if($i<2){
			$strMName="$MName";  //客户名称
			$strValue="$TempRMB";  //客户名称
			$strColor="$Color";  //客户名称
		}
		else{
			$strMName=$strMName."|".$MName;  //客户名称
			$strValue=$strValue."|".$TempRMB;  //客户名称
			$strColor=$strColor."|".$Color;  //客户名称
		}
		
	
		$i++;
		}while ($ShipRow = mysql_fetch_array($mainTypeSql));
	}

//


$labLst=explode("|",$strMName);
$datLst=explode("|",$strValue);
$clrLsts =explode("|",$strColor);
$HeadTitle='接单 ';
$ox=950; //圆心点,
$oy=160;
//$oy=160;
//$clrLsts = array(0x99ff00, 0xff6666, 0x0099ff, 0xff99ff, 0xffff99, 0x99ffff, 0xff3333, 0x009999,0x8561FA,0xCB05FD,0xAA098E,0x1D690E,0xFBB202,0xF1CAFE,0x53AAA7,0x085451,0x9D9C54);
draw_img($image,$HeadTitle,$datLst,$labLst,$clrLsts,$ox,$oy,$a=200,$b=90);


//饼形图结束



header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源
?>