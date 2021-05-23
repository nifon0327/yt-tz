<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=12;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="9,10,11";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_jj";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="节日奖金";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany." ".$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|奖金项目|120|部门|70|职位|60|员工ID|50|员工姓名|60|计算月份|110|比率参数|60|金额|80|个税|40|实付|80|状态|40|请款月份|70";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=9;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany." ".$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|所属公司|60|奖金项目|120|部门|60|职位|60|员工ID|50|员工姓名|60|入职日期|70|在职时间|70|计算月份|100|离职日期|70|离职原因|70|比率参数|60|总金额|60|结付比率|60|结付金额|60|个税|40|实付|80|状态|40|请款月份|70";
		$Estate=3;
		$ColsNumber=17;	
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,64,18,15";}
		else{$ActioToS="1,64,18";}
		$sumCols="14,16,17,18";
		include $funFrom."_cw3.php";
		break;
	}
?>