<?php 
//电信-zxq 2012-08-01
//MC、DP共享代码
include "../model/modelhead.php";
echo "<SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=10;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="6";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_gyssk";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="供应商税款";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany." ".$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|选项|60|序号|40|请款日期|75|货款月份|60|供应商|80|货币|40|税款金额|60|说明|300|发票号|80|状态|40|请款人|50";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=8;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany." ".$Log_Item."未结付记录");
		$Th_Col="选项|60|序号|40|请款日期|75|货款月份|60|供应商|80|货币|40|税款金额|60|说明|300|发票号|80|状态|40|请款人|50";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>