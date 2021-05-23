<?php 
//电信-ZX  2012-08-01
//MC、DP共用代码
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;
$tableMenuS=600;
ChangeWtitle("$SubCompany IT任务列表");
$funFrom="it_worktask";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|任务发布|60|发布日期|80|任务内容|300|任务类型|100|任务状态|60|处理人|60|处理说明|300";
if($Login_P_Number==10002){
	$ColsNumber=13;
	$Th_Col.="|任务等级|60|发布人奖金|70|处理人奖金|70|完成日期|100";
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	if ($Estate!=""){
		$SearchRows=" and A.Estate=$Estate";
	}
	//$SearchRows=$Estate==""?"":" and A.Estate=$Estate";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>处理中</option>
	<option value='0' $EstateSTR0>已处理</option>
	</select>&nbsp;";
	if($Estate==0 && $Estate!=""){//已处理
		$monthResult = mysql_query("SELECT A.Date FROM $DataPublic.it_worktask A WHERE 1 $SearchRows group by DATE_FORMAT(A.Date,'%Y-%m') order by A.Date DESC",$link_id);
		if($monthRow = mysql_fetch_array($monthResult)) {
			echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["Date"]));
				if($FirstValue==""){
					$FirstValue=$dateValue;}
				if($chooseMonth==$dateValue){
					echo "<option value='$dateValue' selected>$dateValue</option>";
					$PEADate=" and DATE_FORMAT(A.Date,'%Y-%m')='$dateValue'";
					}
				else{
					echo "<option value='$dateValue'>$dateValue</option>";					
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			if($PEADate==""){
				$PEADate=" and DATE_FORMAT(A.Date,'%Y-%m')='$FirstValue'";
				}
			echo"</select>&nbsp;";
			}
		$SearchRows.=$PEADate;
		}
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
$mySql="SELECT A.Id,A.Sponsor,A.TaskDate,T.TypeName,A.TaskContent,A.TaskLevel,A.BonusS,A.BonusH,A.Handled,A.Estate,A.Locks,A.Date,A.Remark
FROM $DataPublic.it_worktask A 
LEFT JOIN $DataPublic.it_worktype T ON T.Id=A.TaskType
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Date DESC,A.TaskDate DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Sponsor=$myRow["Sponsor"];			//任务发布人
		$TaskDate=$myRow["TaskDate"];		//发布日期
		$TypeName=$myRow["TypeName"];		//任务类型
		$TaskContent=nl2br($myRow["TaskContent"]);	//任务内容
		$TaskLevel=$myRow["TaskLevel"];		//任务等级
		$BonusS=$myRow["BonusS"];
		$BonusH=$myRow["BonusH"];
		
		$Handled=$myRow["Handled"];			//任务处理人
		$Estate=$myRow["Estate"];			//任务状态
		$Locks=$myRow["Locks"];				//记录状态
		$Date=$myRow["Date"];				//完成日期
		$Remark=nl2br($myRow["Remark"]);			//处理说明
		$Remark=$Remark==""?"&nbsp;":$Remark;
		switch($Estate){
			case 1:$Estate="<div class='redB'>未处理</div>";break;
			case 2:$Estate="<div class='yellowB'>处理中</div>";break;
			case 0:$Estate="<div class='greenB'>已处理</div>";break;
			}
		if($Handled!=0){
			$Operator=$Handled;
			include "../model/subprogram/staffname.php";
			}
		else{
			$Operator="&nbsp;";
			}
		$ValueArray=array(
			array(0=>$Sponsor, 		1=>"align='center'"),
			array(0=>$TaskDate,		1=>"align='center'"),
			array(0=>$TaskContent),
			array(0=>$TypeName, 	1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'"),
			array(0=>$Remark),
			);
			if($Login_P_Number==10002){
				$ValueArray[]=array(0=>$TaskLevel,	1=>"align='center'");
				$ValueArray[]=array(0=>$BonusS,	1=>"align='center'");
				$ValueArray[]=array(0=>$BonusH,	1=>"align='center'");
				$ValueArray[]=array(0=>$Date,	1=>"align='center'");
				}
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