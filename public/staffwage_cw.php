<?php 
//加入餐费、加班奖金 ewen 2013-08-04
//加入支付货币 ewen 2014-06-11
include "../model/modelhead.php";
$ColsNumber=28;						//必选参数,需处理
$tableMenuS=600;						//必选参数,功能项列出的起始位置

$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="staffwage";					//必选参数：功能模块
$nowWebPage=$funFrom."_cw";	//必选参数：功能页面
$Log_Item="员工薪资";
$MergeRows=0;
switch($Estate){
	case "0":									//已结付处理
		$sumCols="21,27";				//求和列
		$MergeRows=7;
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$Th_Col="更新|35|结付<br>日期|50|结付凭证|35|结付备注|35|结付总额|60|结付银行|100|所属公司|60|
		选项|35|序号|35|薪资<br>月份|50|部门|40|职位|40|员工<br>ID|40|员工<br>姓名|50|工龄<br>Y(M)|40|货币|35|底薪|35|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|生活/住宿<br>补助|60|交通<br>补助|40|就学<br>补助|40|购房<br>补助|40|绩效<br>奖金|40|其他<br>奖金|40|考勤<br>扣款|40|津贴<br>扣款|40|小计|50|借支|40|社保|50|个税|40|公积金|40|其它|40|实付|50|状态|35";//加班费(2)
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:									//未结付处理
		$sumCols="9,10,21,27";			//求和列
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|30|分类|30|所属公司|60|部门|40|职位|40|员工<br>姓名|50|工龄<br>Y(M)|40|货币|35|底薪|40|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|生活/住宿<br>补助|60|交通<br>补助|40|就学<br>补助|40|购房<br>补助|40|绩效<br>奖金|40|其他<br>奖金|40|考勤<br>扣款|40|津贴<br>扣款|40|小计|55|借支|40|社保|50|个税|40|公积金|40|其它|40|实付|55|状态|40|银行|40|备注|40";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="64,18,15";}
		else{$ActioToS="64,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>