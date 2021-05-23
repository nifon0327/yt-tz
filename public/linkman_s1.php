<?php
//电信-ZX  2012-08-01
//代码共享-EWEN
//
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|公司简称|120|联系人|100|默认|30|职务|140|昵称|80|移动电话|100|固定电话|120|Skype|40|MSN|40|备注|40|状态|40";
$ColsNumber=13;
$tableMenuS=500;
$Page_Size = 100;							//每页默认记录数量
//非必选,过滤条件
switch($Action){
	case"1"://来自于登录帐户
		$AddTB=" LEFT JOIN $DataIn.usertable Z ON Z.Number=L.Id ";
		$TypeSTR=" AND L.Type IN (2,3,8) AND Z.Number IS NULL ";
	break;
	}
switch($uType){
	case 2:
		$CompanyData="$DataIn.trade_object";
		$TypeSTR.=" AND C.ObjectSign IN (1,2) AND C.Estate=1";
		break;
	case 3:
		$CompanyData="$DataIn.trade_object";
		$TypeSTR.=" AND C.ObjectSign IN (1,3) AND C.Estate=1";
		break;
	case 4:
		$CompanyData="$DataPublic.freightdata";
		$TypeSTR.=" AND C.Estate=1";
		break;
	case 5:
		$CompanyData="$DataPublic.freightdata";
		$TypeSTR.=" AND C.Estate=1";
		break;
	}
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
L.Id,L.CompanyId,L.Email,L.Name,L.Defaults,L.Headship,L.Nickname,
L.Sex,L.Mobile,L.Tel,L.SKYPE,L.MSN,L.Remark,L.Type,L.Estate,L.Locks,C.Forshort 
FROM $DataIn.linkmandata L 
LEFT JOIN  $CompanyData C ON C.CompanyId=L.CompanyId 
$AddTB
WHERE 1 $TypeSTR $sSearch ORDER BY L.CompanyId,L.Defaults,L.Id";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$checkidValue=$Id."^^".$Name;
		$CompanyId=$myRow["CompanyId"];
		$Defaults=$myRow["Defaults"]==0?"Y":"N";
		$Headship=$myRow["Headship"]==""?"&nbsp;":$myRow["Headship"];
		$Nickname=$myRow["Nickname"]==""?"&nbsp;":$myRow["Nickname"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$SKYPE=$myRow["SKYPE"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[SKYPE]' width='18' height='18'>";
		$MSN=$myRow["MSN"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[MSN]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Type=$myRow["Type"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
        $Letter=$myRow["Letter"];
		$Forshort=$Letter."-".$myRow["Forshort"];
		$ValueArray=array(
			array(0=>$Forshort,
					 1=>"align='left'"),
			array(0=>$Name,
					 1=>"align='center'"),
			array(0=>$Defaults,
					 1=>"align='center'"),
			array(0=>$Headship,
					 1=>"align='center'"),
			array(0=>$Nickname,
					 1=>"align='center'"),
			array(0=>$Mobile,
					 1=>"align='center'"),
			array(0=>$Tel,
					 1=>"align='center'"),
			array(0=>$SKYPE,
					 1=>"align='center'"),
			array(0=>$MSN,
					 1=>"align='center'"),
			array(0=>$Remark,
					 1=>"align='center'"),
			array(0=>$Estate,
					 1=>"align='center'")
			);
		include "../model/subprogram/s1_model_6.php";
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