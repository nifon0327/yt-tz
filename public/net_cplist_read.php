<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.staffmain
$DataPublic.net_cpsfdata
$DataPublic.net_cpcheckdiary
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 公司设备清单");
$funFrom="net_cplist";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|设备编号|70|设备分类|70|领用人|100|IP地址|90|Mac地址|120|设备型号|150|服务编号|70|购买日期|80|保修期|50|软件<br>列表|40|维护<br>日志|40|备注|150|销售商|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT D.Id,D.CpName,D.IpAddress,D.MacAddress,D.User,D.Model,D.SSNumber,D.BuyDate,D.Warranty,D.Attached,T.Name AS TypeName,D.Remark,D.Date,D.Operator,D.Locks,S.Id AS CId,S.CompanyId,S.Forshort 
FROM $DataPublic.net_cpdata D 
LEFT JOIN $DataPublic.net_facilitytype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
WHERE 1 $SearchRows ORDER BY D.TypeId,D.CpName";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$CompanyData="$DataPublic.dealerdata";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$sfList="-";$diaryList="-";
		$CpName=$myRow["CpName"];
		$TypeName=$myRow["TypeName"];
		$IpAddress=$myRow["IpAddress"]==""?"&nbsp;":$myRow["IpAddress"];
		$MacAddress=$myRow["MacAddress"]==""?"&nbsp;":$myRow["MacAddress"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$CId=$myRow["CId"];
		$Ids=anmaIn($CId,$SinkOrder,$motherSTR);
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		$User=$myRow["User"];
		$Model=$myRow["Model"];
		$SSNumber=$myRow["SSNumber"];
		$BuyDate=$myRow["BuyDate"];
		$Warranty=$myRow["Warranty"];
		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$CpName="<a href='../download/cpreport/$Attached' target='_blank'>$CpName</a>";
			}
		$Date=$myRow["Date"];
		//$Remark=$myRow["Remark"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		//软件列表
		$cpsfResult = mysql_query("SELECT * FROM $DataPublic.net_cpsfdata WHERE hdId=$Id LIMIT 1",$link_id);
		if($cpsfRow = mysql_fetch_array($cpsfResult)){
			$sfList="<a href='net_cpsfsetup_view.php?hdId=$Id' target='_blank'>查看</a>";
			}
		$diaryResult = mysql_query("SELECT * FROM $DataPublic.net_cpcheckdiary WHERE hdId=$Id LIMIT 1",$link_id);
		if($diaryRow = mysql_fetch_array($diaryResult)){
			$diaryList="<a href='net_cpcheck_view.php?hdId=$Id' target='_blank'>查看</a>";
			}
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//计算保修期是否已过
		$BDate=date("Y",strtotime($BuyDate))+$Warranty."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
		$BuyDate=$BDate>=date("Y-m-d")?"<span class='greenB'>$BuyDate</span>":"<span class='redB'>$BuyDate</span>";
		$ValueArray=array(
			array(0=>$CpName, 	1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$User),
			array(0=>$IpAddress,1=>"align='center'"),
			array(0=>$MacAddress,1=>"align='center'"),
			array(0=>$Model),
			array(0=>$SSNumber, 1=>"align='center'"),
			array(0=>$BuyDate,	1=>"align='center'"),
			array(0=>$Warranty." 年",	 1=>"align='center'"),
			array(0=>$sfList,	1=>"align='center'"),
			array(0=>$diaryList,1=>"align='center'"),
			array(0=>$Remark,1=>"align='Left'"),
			array(0=>$Forshort,1=>"align='Left'")
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