<?php 
include "../model/modelhead.php";
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="rs_stafftj";		//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="员工体检费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|75|结付凭证|35|结付备注|35|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|体检类型|70|员工姓名|60|部门|80|职位|60|入职日期|70|金额|60|凭证|30|合格与否|50|状态|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=7;
		$sumCols="7";	
		$ColsNumber=11;	
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
	    $sumCols="8";	
		$ColsNumber=12;	
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
			$Th_Col="选项|40|序号|40|所属公司|60|体检类型|70|员工姓名|60|部门|60|职位|60|入职日期|70|金额|60|备注|250|凭证|30|合格与否|50|结付|60|登记日期|80|操作员|60";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="64,18,15";}
		else{$ActioToS="64,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>