<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo "<SCRIPT src='../model/publicfun.js' type=text/javascript></script>";

//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");

$From=$From==""?"ALL":$From;		//必选参数：是否来自查询结果浏览
$funFrom="product_returned";			//必选参数：功能模块
$nowWebPage=$funFrom."_ALL";		//必选参数：功能页面
$MergeRows=0;
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
//echo "Estate:$Estate";	
$Estate=3;	
switch($Estate){
	/*case "0":			//已结付处理		
		$sumCols="7,10";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany."Defective Goods");
		$Th_Col="choose|60|No.|40|Rejected Date|80|ID|60|产品名称|250|Product Code|150|Order History|100|Rejected Qty|70|Date of Latest Shipment|80|Price|60|Amount|80|Date of Latest Order|80";
		$EstateSTR0="selected";
		$flag=0;
		//$ActioToS="1,2,4,5,6,7,8";		
		include $funFrom."_ALL0.php";
		break;*/
	/*case "5":			//已结付处理		
		$sumCols="5,8";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany."Defective Goods");
		$Th_Col="choose|60|No.|40|Rejected Date|80|Product Code|150|Order History|100|Rejected Qty|70|Date of Latest Shipment|80|Amount|80|Date of Latest Order|80";
		$EstateSTR5="selected";
		$flag=0;
		$ActioToS="1";		
		include $funFrom."_ALL5.php";
		break;		*/
	case "3":			//未结付处理
		$sumCols="7,9";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany."Defective Goods");
		$Th_Col="choose|60|No.|40|Rejected Date|80|ID|60|产品名称|250|Product Code|150|Order History|100|Rejected Qty|70|Date of Latest Shipment|80|Amount|80|Date of Latest Order|80";
		$EstateSTR3="selected";
		$flag=0;	
		include $funFrom."_ALL3.php";
		break;
	}
?>
