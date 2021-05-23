<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo "<SCRIPT src='../model/publicfun.js' type=text/javascript></script>";

//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");

$From=$From==""?"ALL":$From;		//必选参数：是否来自查询结果浏览
$funFrom="product_returned";			//必选参数：功能模块
$nowWebPage=$funFrom."_ALL";		//必选参数：功能页面
$Log_Item="退货产品记录";
$MergeRows=0;

$Page_Size = 100;
switch($Estate){
	case "0":
		$Pagination=$Pagination==""?1:$Pagination;
		//已结付处理		
		$sumCols="7,10";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany.$Log_Item."产品退货明细");
		$Th_Col="选项|60|序号|40|退货日期|80|产品ID|60|产品名称|250|Product Code|150|已出数量<br>(下单次数)|100|退货数量|60|最后出货|80|单价|60|退货金额|80|最后下单|80|操作|60";
		$EstateSTR0="selected";
		$flag=0;
		$ActioToS="1,2,4,5,6,7,8";		
		include $funFrom."_ALL0.php";
		break;
	case "5":	
	     $Pagination=$Pagination==""?1:$Pagination;
		 //已结付处理		
		$sumCols="5,8";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany.$Log_Item."产品退货明细");
		$Th_Col="选项|60|序号|40|退货日期|80|Product Code|150|已出数量<br>(下单次数)|100|退货数量|60|最后出货|80|退货金额|80|最后下单|80|操作|60";
		$EstateSTR5="selected";
		$flag=0;
		$ActioToS="1";		
		include $funFrom."_ALL5.php";
		break;	
	case "7":
	    $Pagination=$Pagination==""?0:$Pagination; 
		//未结付处理
		$sumCols="7,9";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany.$Log_Item."产品退货明细");
		$Th_Col="选项|60|序号|40|退货日期|80|产品ID|60|产品名称|250|Product Code|150|已出数量<br>(下单次数)|100|退货数量|60|最后出货|80|退货金额|80|最后下单|80|操作|60";
		$EstateSTR7="selected";
		$flag=0;
		$ActioToS="1";		
		include $funFrom."_ALL7.php";
		break;	
	default:
	    $Pagination=$Pagination==""?1:$Pagination; 
		//未结付处理
		$sumCols="7,9";		//求和列
		$ColsNumber=11;		//必选参数,需处理		
		$tableMenuS=600;    //必选参数,功能项列出的起始位		
		ChangeWtitle($SubCompany.$Log_Item."产品退货明细");
		$Th_Col="选项|60|序号|40|退货日期|80|产品ID|60|产品名称|250|Product Code|150|已出数量<br>(下单次数)|100|退货数量|100|最后出货|80|退货金额|80|最后下单|80|操作|60";
		$EstateSTR3="selected";
		$flag=0;
		$ActioToS="1,2";		
		include $funFrom."_ALL3.php";
		break;
	}
?>
