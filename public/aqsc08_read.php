<?php
//2013-09-25 ewen
include "../model/modelhead.php";
$ColsNumber=12;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc08";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 员工受训记录");
$Th_Col="选项|45|序号|45|受训日期|80|受训员工|50|部门|40|职位|40|受训类型|80|受训项目|200|考核|40|教程|40|签到表|40|讲师|50|核实人|50";
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
$mySql="SELECT A.Id,A.ItemId,A.Exam,
B.DefaultDate,B.ItemName,B.ItemTime,B.Tutorial,B.Lecturer,B.Reviewer,B.ExamId,B.Date,B.Estate,B.Locks,B.Operator,B.Img,B.Movie,B.List,
C.Name AS theObject,
D.Name AS theOU,
E.Name AS theTeach,
F.Name AS theType,
G.Attached,
H.Name,
I.Name AS Branch,
J.Name AS Job
FROM $DataPublic.aqsc08 A 
LEFT JOIN $DataPublic.aqsc07 B ON B.Id=A.ItemId
LEFT JOIN $DataPublic.aqsc07_object C ON B.Id=B.ObjectId
LEFT JOIN $DataPublic.aqsc07_ou D ON D.Id=B.OUId
LEFT JOIN $DataPublic.aqsc07_teach E ON E.Id=B.TeachId
LEFT JOIN $DataPublic.aqsc07_type F ON F.Id=B.TypeId
LEFT JOIN $DataPublic.aqsc04 G ON G.Id=B.Tutorial
LEFT JOIN $DataPublic.staffmain H ON H.Number=A.Number
LEFT JOIN $DataPublic.branchdata I ON I.Id=H.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=H.JobId
WHERE 1 $SearchRows ORDER BY A.Id ASC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$ItemId=$myRow["ItemId"];
		$Exam=$myRow["Exam"];
		$DefaultDate=$myRow["DefaultDate"];
		$theType=$myRow["theType"];
		$theObject=$myRow["theObject"];
		$ItemName=$myRow["ItemName"];
		$ItemTime=$myRow["ItemTime"];
		$theOU=$myRow["theOU"];
		$theTeach=$myRow["theTeach"];
		$ExamId=$myRow["ExamId"]==1?"<sapn class='greenB'>是</sapn>":"<sapn class='redB'>否</sapn>";
		$Attached=$myRow["Attached"];
		$FileType=substr($Attached, -3, 3);
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			switch($FileType){
				case "ppt":
				$FileTitle="幻灯片文件";
				break;
				case "mp4":
				$FileTitle="视频文件";
				break;
				case "pdf":
				$FileTitle="PDF文件";
				break;
				}
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/$FileType.gif' title='$FileTitle'/></a>";
			}
		else{
			$Attached="-";
			}
		$Estate="<sapn class='greenB'>已执行</sapn>";
			$Lecturer=$myRow["Lecturer"];
			$Img=$myRow["Img"]==1?$myRow["Img"]:"-";
			$Movie=$myRow["Movie"]==1?$myRow["Movie"]:"-";
			$List=$myRow["List"]==1?$myRow["List"]:"-";
			$Reviewer=$myRow["Reviewer"];
			if($Img==1){
				$Img="aqsc07_img_".$ItemId.".pdf";
				$Img=anmaIn($Img,$SinkOrder,$motherSTR);
				$Img="<a href=\"openorload.php?d=$d&f=$Img&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif' /></a>";
				}
			if($Movie==1){
				$Movie="aqsc07_movie_".$ItemId.".mp4";
				$Movie=anmaIn($Movie,$SinkOrder,$motherSTR);
				$Movie="<a href=\"openorload.php?d=$d&f=$Movie&Type=&Action=6\" target=\"download\"><img src='../images/mp4.gif' /></a>";
				}
			if($List==1){
				$List="aqsc07_list_".$ItemId.".pdf";
				$List=anmaIn($List,$SinkOrder,$motherSTR);
				$List="<a href=\"openorload.php?d=$d&f=$List&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif' /></a>";
				}
				
		$ValueArray=array(
			array(0=>$DefaultDate,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Branch),
			array(0=>$Job),
			array(0=>$theType),
			array(0=>$ItemName),		
			array(0=>$Exam,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$List,1=>"align='center'"),	
			array(0=>$Lecturer,1=>"align='center'"),
			array(0=>$Reviewer,1=>"align='center'")
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