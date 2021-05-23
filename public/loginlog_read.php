<?php 
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
include "../model/modelheadNew.php";
include "../model/monipdb/IP.class.php";
$IPClass = new IP();
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=730;
ChangeWtitle("$SubCompany 登录记录");
$funFrom="loginlog";
$nowWebPage=$funFrom."_read";
if($Action==""){
$uType=$uType==""?1:$uType;
switch($uType){
	case 2:
		$uTypeSign2="selected";
		$Th_Col="选项|50|序号|50|客户|50|姓名|60|登录IP|180|登录位置|200|在线开始时间|130|在线最后时间|130|在线时长|100";
		break;
	case 3:
		$uTypeSign3="selected";
		$Th_Col="选项|50|序号|50|供应商|100|姓名|60|登录IP|180|登录位置|200|在线开始时间|130|在线最后时间|130|在线时长|100";
		break;
	default:
		$uTypeSign1="selected";
		$Th_Col="选项|50|序号|50|部门|50|姓名|60|登录IP|180|登录位置|200|在线开始时间|130|在线最后时间|130|在线时长|100";

		break;
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="0";
$checkDay=$checkDay==""?date("Y-m-d"):$checkDay;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项onfocus='new WdatePicker(this,null,false,\"whyGreen\")' readonly
if($From!="slist"){
	$SearchRows="";
	echo"<input name='checkDay' type='text' id='checkDay' size='10' maxlength='10' value='$checkDay' onchange='document.form1.submit()'  onFocus='WdatePicker()'/>&nbsp;";
	$SearchRows="AND DATE_FORMAT(L.sTime,'%Y-%m-%d') ='$checkDay'";
	$SearchRows.=" AND L.uType=$uType";
	echo"<select name='uType' id='uType' onChange='ResetPage(this.name)'>";
	echo"<option value='1' $uTypeSign1>内部员工</option>
		<option value='2' $uTypeSign2>客户</option>
		<option value='3' $uTypeSign3>供应商</option>
      	</select>&nbsp;";
	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr <a href=\"?Action=1\">查询模式</a>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT L.uType,L.uFrom,L.uIP,L.sTime,L.eTime,U.Number  
FROM $DataIn.loginlog L 
LEFT JOIN $DataIn.usertable U ON U.Id=L.uId
WHERE 1  $SearchRows ORDER BY L.sTime DESC";//eTime, AND L.uType=0

//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$uFrom=$myRow["uFrom"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$uType=$myRow["uType"];
		switch($uType){
			case 1:
				$checkUname=mysql_fetch_array(mysql_query("SELECT M.Name,B.Name AS Branch FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId WHERE M.Number='$Number' LIMIT 1",$link_id));
				$Name=$checkUname["Name"];
				$uType=$checkUname["Branch"];
				break;
			case 2:
				$checkUname=mysql_fetch_array(mysql_query("SELECT L.Name,P.Forshort FROM $DataIn.linkmandata L LEFT JOIN $DataIn.trade_object P ON P.CompanyId=L.CompanyId WHERE L.Id='$Number' LIMIT 1",$link_id));
				$uType=$checkUname["Forshort"];
				$Name=$checkUname["Name"];
				break;
			case 3:
				$checkUname=mysql_fetch_array(mysql_query("SELECT L.Name,P.Forshort FROM $DataIn.linkmandata L LEFT JOIN $DataIn.trade_object P ON P.CompanyId=L.CompanyId WHERE L.Id='$Number' LIMIT 1",$link_id));
				$uType=$checkUname["Forshort"];
				$Name=$checkUname["Name"];
				break;
			}
		$sTime=$myRow["sTime"];
		$eTime=$myRow["eTime"];
		if($eTime=="0000-00-00 00:00:00"){
			$eTime="未确定";$onTime="未确定";
			}
		else{
			$onTime=strtotime($eTime)-strtotime($sTime);
			//时
			$onTimeH=floor($onTime/3600);
			//分
			$onTimI=floor(($onTime-$onTimeH*3600)/60);
			$onTimI=$onTimI<10?"0".$onTimI:$onTimI;
			//秒
			$onTimeS=$onTime%60;
			$onTimeS=$onTimeS<10?"0".$onTimeS:$onTimeS;
			
			$onTimeH=$onTimeH>0?$onTimeH."小时":"";
			$onTime=$onTimeH.$onTimI."分".$onTimeS."秒";
		//	$onTime=date("H:i:s",strtotime($onTime));
			}
		$Add="内部登录";
		switch($uFrom){
			case 1:$uIP="内网:".$myRow["uIP"];break;
			case 2:$uIP="<div class='yellowB'>内网域名:".$myRow["uIP"]."</div>";break;
			case 3:$uIP="<div class='redB'>外网:".$myRow["uIP"]."</div>";
			//$Add="<div class='redB'>".convertip($myRow["uIP"])."</div>";
			$Add="<div class='redB'>". $IPClass->getIpAddress($myRow["uIP"])."</div>";
			break;
			}		
			$ValueArray=array(
			array(0=>$uType,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$uIP,		1=>"align='center'"),
			array(0=>$Add),
			array(0=>$sTime,	1=>"align='center'"),
			array(0=>$eTime,	1=>"align='center'"),
			array(0=>$onTime,	1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
}
else{//查询
	///////////////////////////
	$Th_Col="选项|50|序号|50|部门|50|姓名|60|最后登录IP|180|最后登录位置|200|最后登录时间|130|最后在线时间|130|登录明细|60";
	include "../model/subprogram/read_model_3.php";
	if($From!="slist"){
		$SearchRows="";
		$checkBranchSql=mysql_query("SELECT * FROM  $DataPublic.branchdata 
									 WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) ORDER BY SortId,Id",$link_id);
		if($checkBranchRow=mysql_fetch_array($checkBranchSql)){
			echo"<select name='BranchId' id='BranchId' onChange='ResetPage(this.name)'>";
			do{
				$BranchId=$BranchId==""?$checkBranchRow["Id"]:$BranchId;
				if($BranchId==$checkBranchRow["Id"]){
					echo"<option value='$checkBranchRow[Id]' selected>$checkBranchRow[Name]</option>";
					$SearchRows=" AND M.BranchId='$BranchId'";
					}
				else{
					echo"<option value='$checkBranchRow[Id]'>$checkBranchRow[Name]</option>";
					}
				}while($checkBranchRow=mysql_fetch_array($checkBranchSql));
			echo"</select>&nbsp; <a href=\"?\">标准模式</a>";
			}
		}
	include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Name AS Branch,M.Name,M.BranchId,L.uId
FROM $DataIn.loginlog L 
LEFT JOIN $DataIn.usertable U ON U.Id=L.uId
LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
WHERE 1 $SearchRows AND L.uType=0 AND M.Estate=1 GROUP BY L.uId ORDER BY B.SortId,B.Id,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Branch=$myRow["Branch"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];
		$uId=$myRow["uId"];
		
		//该员工最后一次登录资料
		$checkLastSql=mysql_query("SELECT uFrom,uIP,sTime,eTime FROM $DataIn.loginlog WHERE uId='$uId' ORDER BY sTime DESC LIMIT 1",$link_id);
		if($checkLastRow=mysql_fetch_array($checkLastSql)){
			$uFrom=$checkLastRow["uFrom"];
			$Add="内部登录";
			switch($uFrom){
				case 1:$uIP="内网:".$checkLastRow["uIP"];break;
				case 2:$uIP="<div class='yellowB'>内网域名:".$checkLastRow["uIP"]."</div>";break;
				case 3:$uIP="<div class='redB'>外网:".$checkLastRow["uIP"]."</div>";
				//$Add="<div class='redB'>".convertip($checkLastRow["uIP"])."</div>";
				 $Add="<div class='redB'>".$IPClass->getIpAddress($checkLastRow["uIP"])."</div>";
				break;
				}		
			$sTime=$checkLastRow["sTime"];
			$eTime=$checkLastRow["eTime"];
			if($eTime=="0000-00-00 00:00:00"){
				$eTime="未确定";
				}
			}
		$View="<a href=\"loginlog_view.php?uId=$uId\" target=\"_blank\">查看</a>";
		$ValueArray=array(
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$uIP,		1=>"align='center'"),
			array(0=>$Add),
			array(0=>$sTime,	1=>"align='center'"),
			array(0=>$eTime,	1=>"align='center'"),
			array(0=>$View,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
	///////////////////////////
	}
?>