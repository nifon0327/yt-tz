<?php 
include "../model/modelhead.php";
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc10";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全生产负责人培训记录");
$Th_Col="选项|45|序号|45|培训日期|80|培训地点|150|培训内容|300|受训人员|60|受训人职位|60|附件|40|状态|40";
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
$mySql="SELECT A.Id,A.TrainDate,A.Address,A.TrainContent,A.Job,A.Attached,A.Estate,A.Locks,B.Name FROM $DataPublic.aqsc10 A 
LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$TrainDate=$myRow["TrainDate"];
		$Address=$myRow["Address"];
		$TrainContent=$myRow["TrainContent"];
		$Name=$myRow["Name"];
		$Job=$myRow["Job"];
		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif'/></a>";
			}
		else{
			$Attached="-";
			}
		$Estate=$myRow["Estate"]==1?"<sapn class='greenB'>可用</sapn>":"<sapn class='redB'>禁用</sapn>";;
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$TrainDate),
			array(0=>$Address),
			array(0=>$TrainContent),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Job,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
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