<?php
//电信-zxq 2012-08-01
//二合一已更新
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="ch_freight_declaration";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="中港报关费用";
$MergeRows=0;
$TypeId=$TypeId==""?1:$TypeId;
$PayType=$PayType==""?0:$PayType;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$ColsNumber=21;					//必选参数,需处理
		$sumCols="11,12,13,14,15,16,17,18,19,20";	//求和列,需处理
		$MergeRows=7;
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|结付总额|70|结付银行|100|选项|40|序号|40|出货日期|80|$TypeName|120|货运公司|80|提单号码|100|件数|40|公司称重|60|上海称重|60|单价(元/KG)|60|运费<br>(RMB)|60|搬运费<br>(RMB)|60|报关费<br>(RMB)|60|续页费<br>(RMB)|60|无缝清关<br>(RMB)|60|仓储费<br>(RMB)|60|登记费<br>(RMB)|60|停车费<br>(RMB)|60|快递费<br>(RMB)|60|其他费<br>(RMB)|60|合计<br>(RMB)|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$sumCols="9,10,11,12,13,14,15,16,17,18,19";			//求和列,需处理
		$ColsNumber=32;					//必选参数,需处理
		$Th_Col="选项|40|序号|30|货运公司|80|目的地|80|提单号码|100|件数|40|公司称重|60|上海称重|60|单价(元/KG)|60|运费<br>(RMB)|60|搬运费<br>(RMB)|60|报关费<br>(RMB)|60|续页费<br>(RMB)|60|无缝清关<br>(RMB)|60|仓储费<br>(RMB)|60|登记费<br>(RMB)|60|停车费<br>(RMB)|60|快递费<br>(RMB)|60|其他费<br>(RMB)|60|合计<br>(RMB)|60|状态|40|备注|40|操作|50|物流对账日期|80|$TypeName|110|出货日期|70";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>