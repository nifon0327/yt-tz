<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
$ColsNumber=10;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="nonbom11";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="非BOM采购预付订金";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$MergeRows=7;
		$Th_Col="更新|35|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|结付总额|60|结付银行|100|选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|货币|40|预付金额|60|状态|60|请款人|50|请款日期|75";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
        $sumCols="4";			//求和列,需处理
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|货币|40|预付金额|60|状态|60|请款人|50|请款日期|75";
		$Estate=3;
		$EstateSTR3="selected";
		$ActioToS="18,15";
        $sumCols="6";			//求和列,需处理
		include $funFrom."_cw3.php";
		break;
	}
?>