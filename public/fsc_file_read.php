<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany FSC资料");
$funFrom="fsc_file";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|40|序号|50|FSC资料说明|400|FSC源文件|100|上传日期|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.cg3_fscdata A WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Remark=$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";	
		$Date=$myRow["Date"];		
		$Attached=$myRow["Attached"];
		if($Attached!=""){			
			$d=anmaIn("download/fscdata/",$SinkOrder,$motherSTR);			
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$PdfFile="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$PdfFile="-";
			}			
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Remark),
			array(0=>$PdfFile,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'"),
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
