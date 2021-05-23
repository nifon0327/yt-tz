<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.res_drivers
$DataPublic.res_driverstype
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 驱动程序列表");
$funFrom="res_drivers";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|45|分类|100|名称|200|备注|300|附件|60|日期|70|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";//
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	//月份
		$date_Result = mysql_query("SELECT D.TypeId,T.Name FROM $DataPublic.res_drivers D,$DataPublic.res_driverstype T WHERE 1 AND T.Id=D.TypeId GROUP BY D.TypeId ORDER BY T.Id",$link_id);
		if($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='Type' id='Type' onchange='RefreshPage(\"$nowWebPage\")'>";
			echo"<option value='' selected>全部</option>";
			do{			
				$TypeId=$dateRow["TypeId"];
				$Name=$dateRow["Name"];
				if($TypeId==$Type){
					echo"<option value='$TypeId' selected>$Name</option>";
					$SearchRows.=" AND D.TypeId='$TypeId'";
					}
				else{
					echo"<option value='$TypeId'>$Name</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
			}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.Name,D.Remark,D.Attached,D.Date,D.Locks,D.Operator,T.Name AS TypeName 
FROM $DataPublic.res_drivers D,$DataPublic.res_driverstype T
 WHERE 1 AND T.Id=D.TypeId $SearchRows ORDER BY D.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$TypeName=$myRow["TypeName"];
		$Name=$myRow["Name"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/drivers/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$Checksheet=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Checksheet\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];		
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$TypeName),
			array(0=>$Name),
			array(0=>$Remark),
			array(0=>$Attached,1=>"align='center'"),
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