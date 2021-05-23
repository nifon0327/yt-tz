<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=10;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_fkdj";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="预付订金";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$MergeRows=7;
		$Th_Col="更新|35|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|结付总额|60|结付银行|100|选项|40|序号|35|供应商|120|预付说明|300|预付金额|60|分类|80|抵付状态|60|抵付日期|80|请款人|50|请款日期|70";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
        $sumCols="4";			//求和列,需处理
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|供应商|80|货币|40|预付说明|400|预付金额|60|分类|80|状态|40|请款人|50|请款日期|75";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="18,15";}
		else{$ActioToS="18";}
        $sumCols="5";			//求和列,需处理
		include $funFrom."_cw3.php";
		break;
	}
?>