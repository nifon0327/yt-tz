<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$tableMenuS=750;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="ch_shipforward";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="Forward杂费";
$MergeRows=0;
$TypeId=$TypeId==""?1:$TypeId;
$PayType=$PayType==""?0:$PayType;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");$ColsNumber=14;					//必选参数,需处理
		$sumCols="7,8,9,10";			//求和列,需处理
		$MergeRows=7;
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|结付总额|70|结付银行|100|选项|40|序号|40|发票日期|70|研砼Invoice|110|Forward公司|80|入仓号|100|Forward Invoice|90|件数|35|研砼<br>称重|60|上海<br>称重|60|金额(HKD)|60|出货日期|70|ETD/ETA|80|备注|30";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";//0,
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$sumCols="6,7,8,9,10,10,11,12,13,14,15,16,17,18,19,20,21,22";			//求和列,需处理
		$ColsNumber=35;					//必选参数,需处理
		$Th_Col="选项|40|序号|35|发票日期|70|Forward公司|80|入仓号|100|Forward Invoice|90|件数|35|研砼<br>称重|60|上海<br>称重|60|研砼<br>体积|50|上海<br>体积|50|研砼<br>体积重|50|上海<br>体积重|50|CFS费|60|THC费|60|文件费|60|手续费|60|ENS费|60|保险费|60|过桥费|60|电放费|60|提单费|60|其它费用|60|金额(HKD)|60|ETD/ETA|80|状态|30|备注|30|操作|50|$TypeName|110|出货日期|70";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>