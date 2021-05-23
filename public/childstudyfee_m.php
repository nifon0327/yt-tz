<?php 
include "../model/modelhead.php";
$sumCols="7";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 助学小孩费用审核");
$funFrom="childstudyfee";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|40|所属公司|60|申请月份|70|员工姓名|100|小孩姓名|100|性别|40|申请金额|60|凭证|60|备注|200|目前就读学校|180|状态|40|更新日期|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="17,15";

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
List_Title($Th_Col,"1",0);
$mySql="SELECT   S.Id,S.Amount,S.Remark,S.Month,S.Attached,S.Date,S.Estate,S.Locks,S.Operator,M.Name,B.Name AS Branch,J.Name AS Job,A.Number,A.ChildName,A.Sex,C.Name AS ClassName,S.cSign
FROM  $DataIn.cw19_studyfeesheet   S 
LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
LEFT JOIN $DataPublic.childclass C ON C.Id=S.NowSchool
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows  AND S.Estate=2";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/childinfo/",$SinkOrder,$motherSTR);
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$ChildName=$myRow["ChildName"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Amount=$myRow["Amount"];
		$NowSchool=$myRow["NowSchool"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
		$Month=$myRow["Month"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$Estate=$myRow["Estate"];
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];
		$ClassName=$myRow["ClassName"];

		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
               $Attached="<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$ChildName,1=>"align='center'"),
			array(0=>$Sex,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Attached),
			array(0=>$Remark),
			array(0=>$ClassName,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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