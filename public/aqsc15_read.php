<?php
//2013-10-14 ewen
include "../model/modelhead.php";		
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc15";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全生产软件投入");
$Th_Col="选项|45|序号|30|费用名称|200|金额|60|凭证|50|经手人|60|审核人|60|费用日期|70|状态|40|备注|300";
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
$mySql="SELECT * FROM $DataPublic.aqsc15 A WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Caption=$myRow["Caption"];
		$Amount=$myRow["Amount"];
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>View</a>";
			}
		else{
			$Attached="-";
			}
		$Handler=$myRow["Handler"];
		$Checker=$myRow["Checker"];
		$UseDate=$myRow["UseDate"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>有效</div>":"<div class='redB'无效</div>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Caption),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Handler,1=>"align='center'"),
			array(0=>$Checker,1=>"align='center'"),
			array(0=>$UseDate,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Remark)
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