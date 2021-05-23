<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="ch_samplemailing";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="寄样费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");$ColsNumber=17;					//必选参数,需处理
		$sumCols="13";			//求和列,需处理
		$MergeRows=8;
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|寄件日期|70|快递公司|60|客户|70|目的地|120|提单号码|100|发票|40|样品<br>照片|40|寄送<br>进度|40|件数|40|重量<br>(KG)|40|单价|40|金额|50|经手人|50|签收日期|70|备注|40";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$sumCols="14";			//求和列,需处理
		$ColsNumber=19;					//必选参数,需处理
		$Th_Col="选项|40|序号|30|所属公司|60|寄件日期|70|快递公司|60|客户|70|目的地|120|提单号码|100|发票|40|样品<br>照片|40|寄送<br>进度|40|件数|40|重量<br>(KG)|40|单价|40|金额|50|经手人|50|签收日期|70|状态|40|备注|40";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>