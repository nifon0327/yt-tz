<?php 
//二合一已更新
//电信-joseph
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=11;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="8";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="zw_purchasesqk";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="总务费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany." ".$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|选项|40|序号|40|请款日期|70|申购物品名称|100|图片|40|数量|50|单位|50|单价|50|金额|60|采购说明|260|凭证|40";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=8;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany." ".$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|申购日期|70|申购物品名称|100|图片|40|数量|50|单位|50|单价|50|金额|60|供应商|80|采购说明|260|采购人|60|请款日期|70|凭证|40";
		$Estate=3;
		$EstateSTR3="selected";
		if($Login_P_Number=='10871'||$Login_P_Number=='10006'){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>