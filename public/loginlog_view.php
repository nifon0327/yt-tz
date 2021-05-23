<?php 
//电信-ZX  2012-08-01
include "../model/modelheadNew.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=730;
ChangeWtitle("$SubCompany 员工登录记录");
$Th_Col="选项|50|序号|50|部门|50|姓名|60|登录IP|180|登录位置|200|在线开始时间|130|在线最后时间|130|在线时长|100";
$checkDay=$checkDay==""?date("Y-m-d"):$checkDay;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项onfocus='new WdatePicker(this,null,false,\"whyGreen\")' readonly
if($From!="slist"){
	$SearchRows=" AND L.uId='$uId'";
	//////////////
	$checkMonthSql=mysql_query("SELECT DATE_FORMAT(L.sTime,'%Y-%m') AS theMonth FROM $DataIn.loginlog L WHERE 1  $SearchRows GROUP BY DATE_FORMAT(L.sTime,'%Y-%m') ORDER BY L.sTime DESC",$link_id);
		if($checkMonthRow=mysql_fetch_array($checkMonthSql)){
			echo"<select name='Month' id='Month' onChange='ResetPage(this.name)'>";
			do{
				$Month=$Month==""?$checkMonthRow["theMonth"]:$Month;
				if($Month==$checkMonthRow["theMonth"]){
					echo"<option value='$Month' selected>$Month</option>";
					$SearchRows.=" AND DATE_FORMAT(L.sTime,'%Y-%m')='$Month'";
					}
				else{
					echo"<option value='$checkMonthRow[theMonth]'>$checkMonthRow[theMonth]</option>";
					}
				}while($checkMonthRow=mysql_fetch_array($checkMonthSql));
			echo"</select>&nbsp;";
			}
	///////////////
	}
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT L.uType,L.uFrom,L.uIP,L.sTime,L.eTime,U.Number  
FROM $DataIn.loginlog L 
LEFT JOIN $DataIn.usertable U ON U.Id=L.uId
WHERE 1  $SearchRows ORDER BY L.sTime DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Number=$myRow["Number"];
	$checkUname=mysql_fetch_array(mysql_query("SELECT M.Name,B.Name AS Branch FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId WHERE M.Number='$Number' LIMIT 1",$link_id));
	$Name=$checkUname["Name"];
	$uType=$checkUname["Branch"];
	do{
		$m=1;
		$uFrom=$myRow["uFrom"];
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
			$Add="<div class='redB'>".convertip($myRow["uIP"])."</div>";
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
?>