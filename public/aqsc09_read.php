<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
$ColsNumber=11;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc09";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 员工安全生产知识考核");
$Th_Col="选项|45|序号|45|考核日期|70|员工姓名|50|考核内容|200|考核方式|50|考核成绩|50|答卷|40|审核人|50|审核人意见|300|状态|40";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";

	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.ExamDate,A.TypeId,A.Attached,A.Results,A.Checker,A.Opinion,A.Date,A.Estate,A.Locks,A.Operator,B.Caption AS ExamContent,C.Name
FROM $DataPublic.aqsc09 A 
LEFT JOIN $DataPublic.aqsc06 B ON B.Id=A.ExamContent
LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Number
WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ExamDate=$myRow["ExamDate"];
		$Name=$myRow["Name"];
		$ExamContent=$myRow["ExamContent"]==""?"安全生产知识在线考核":$myRow["ExamContent"];
		$Attached=$myRow["Attached"];
		$Results=$myRow["Results"];
		$Checker=$myRow["Checker"];
		$Opinion=$myRow["Opinion"];
		if($myRow["TypeId"]==1){
			$TypeId="笔试考核";
			//连接附件
			if($Attached!=""){
				$f=anmaIn($Attached,$SinkOrder,$motherSTR);
				$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">查看</a>";
				}
			else{
				$Attached="-";
				}
			}
		else{
			$TypeId="电子考核";
			//打开网页
			$Attached="<a href=\"aqsc09_view.php?Id=$Id\" target=\"_blank\">查看</a>";
			}
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<sapn class='greenB'>可用</sapn>":"<sapn class='redB'>禁用</sapn>";;
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$ExamDate,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$ExamContent),
			array(0=>$TypeId,1=>"align='center'"),
			array(0=>$Results,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Checker,1=>"align='center'"),
			array(0=>$Opinion),
			array(0=>$Estate,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>