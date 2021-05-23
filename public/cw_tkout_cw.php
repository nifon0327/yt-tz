<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=11;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置

$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_tkout";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="客户退款配件货款";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		$sumCols="4,5,8";			//求和列,需处理
		$MergeRows=8;
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|对帐单|40|实付金额|60|结付银行|100|选项|35|序号|35|采购流水号|100|配件名称|260|订单数量|55|采购数量|55|单价|55|单位|45|金额|55|出货日期|80|Invoice|100|请款月份|75";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
	    $ColsNumber=13;			
		$sumCols="4,5,6,7,8,11";			//求和列,需处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|采购流水号|100|配件名称|230|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|单价|55|单位|45|金额|60|出货日期|80|Invoice|100|状态|40|采购员|50";
		$Estate=3;
		$EstateSTR3="selected";
		$ActioToS="1,18,15";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>