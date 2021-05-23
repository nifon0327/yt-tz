<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 联系人列表");
$funFrom="linkman";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|公司简称|120|联系人|100|默认|40|职务|80|昵称|80|移动电话|160|固定电话|160|Skype|40|MSN|40|备注|40|状态|40";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,9";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
$Type=$Type==""?3:$Type;
$TypeSTR=" AND A.Type='$Type'";
switch($Type){
	case 2:
		$TypeSign1="selected";
		$otherAction="<span onClick='ComeBack(\"clientdata_read\",\"\")' $onClickCSS>返回客户资料</span>";
		$TypeSTR.=" AND (B.cSign=$Login_cSign OR B.cSign=0)";
		$CompanyData="$DataIn.trade_object";
		break;
	case 3:
		$TypeSign2="selected";
		$otherAction="<span onClick='ComeBack(\"providerdata_read\",\"\")' $onClickCSS>返回供应商资料</span>";
		$TypeSTR.=" AND (B.cSign=$Login_cSign OR B.cSign=0)";
		$CompanyData="$DataIn.trade_object";
		break;
	case 4:
		$TypeSign3="selected";
		$otherAction="<span onClick='ComeBack(\"ch_forwardinfo_read\",\"\")' $onClickCSS>返回Forward资料</span>";
		$CompanyData="$DataPublic.freightdata";
		break;
	case 5:
		$TypeSign4="selected";
		$otherAction="<span onClick='ComeBack(\"ch_freightinfo_read\",\"\")' $onClickCSS>返回货运公司资料</span>";
		$CompanyData="$DataPublic.freightdata";
		break;
	}
//步骤4：需处理-条件选项
if($ComeFrom==""){//正常页面浏览，加选框
	if($From!="slist"){
		echo"<select name='Type' id='Type' onChange='ResetPage(this.name)'>
		<option value='2' $TypeSign1>客户</option>
		<option value='3' $TypeSign2>供应商</option>
		<option value='4' $TypeSign3>Forward</option>
		<option value='5' $TypeSign4>快递公司</option>
		</select>&nbsp;";
		}
	else{
		echo"<input name='Type' type='hidden' id='Type' value='$Type'>";
		}
	$otherAction="";
	}
else{
	echo"<input name='Type' type='hidden' id='Type' value='$Type'><input name='ComeFrom' type='hidden' id='ComeFrom' value='$ComeFrom'>";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
A.Id,A.CompanyId,A.Email,A.Name,A.Defaults,A.Headship,A.Nickname,A.Sex,A.Mobile,A.Tel,A.SKYPE,A.MSN,A.Remark,A.Type,A.Estate,A.Locks,B.Forshort 
FROM $DataIn.linkmandata A 
LEFT JOIN $CompanyData B ON B.CompanyId=A.CompanyId 
WHERE 1 $TypeSTR $SearchRows ORDER BY A.Estate DESC,A.CompanyId,A.Defaults,A.Id";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];		
		$Name=$myRow["Email"]==""?$myRow["Name"]:"<a href='mailto:$myRow[Email]'>$myRow[Name]</a>";
		$Defaults=$myRow["Defaults"]==0?"Y":"N";
		$Headship=$myRow["Headship"]==""?"&nbsp;":$myRow["Headship"];
		$Nickname=$myRow["Nickname"]==""?"&nbsp;":$myRow["Nickname"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];		
		$SKYPE=$myRow["SKYPE"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[SKYPE]' width='18' height='18'>";
		$MSN=$myRow["MSN"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[MSN]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Type=$myRow["Type"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		switch($Type){
			case 2:$TypeSign="客户";break;
			case 3:$TypeSign="供应商";break;
			case 4:$TypeSign="Forward";break;
			case 5:$TypeSign="快递公司";break;
			}
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$Name),
			array(0=>$Defaults,1=>"align='center'"),
			array(0=>$Headship),
			array(0=>$Nickname),
			array(0=>$Mobile),
			array(0=>$Tel),
			array(0=>$SKYPE,1=>"align='center'"),
			array(0=>$MSN,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
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