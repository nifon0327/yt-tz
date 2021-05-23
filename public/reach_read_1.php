<?php 
//电信-ZX  2012-08-01
//已更新
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 认证图");
$funFrom="Reach";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|类别|80|认证图说明|300|连接配件数量|80|内容|50|状态|60|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8,82";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='Type' id='Type' onchange='ResetPage(this.name)'>";
	  echo "<option value=''>全 部</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$Letter=$myrow["Letter"];
			$TypeName=$myrow["TypeName"];
			if ($Type==$theTypeId){
				echo "<option value='$theTypeId' selected>$Letter-$TypeName</option>";
				$SearchRows=" AND Q.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$Letter-$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
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
$mySql="SELECT Q.Id,Q.TypeId,Q.Title,Q.Picture,Q.IsType,Q.Estate,Q.Date,Q.Operator,T.TypeName 
FROM $DataIn.stuffreach Q
LEFT JOIN $DataIn.stufftype T ON T.TypeId=Q.TypeId 
WHERE 1 $SearchRows ORDER BY Q.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"];
		$Title=$myRow["Title"]==""?"&nbsp":$myRow["Title"];	
		$Date=substr($myRow["Date"],0,10);
		$IsType=$myRow["IsType"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		if($Estate==2){
			$EstateSTR="审核中";$ClassColor="blueB";}
		else{
			$EstateSTR="View";$ClassColor="yellowB";}
		$FileName=$myRow["Picture"];
		$File=anmaIn($FileName,$SinkOrder,$motherSTR);
		$Dir=download."/stuffreach/";
		$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		//$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;' class='$ClassColor'>$EstateSTR</span>";
		$Picture="<a href=\"openorload.php?d=$Dir&f=$File&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$EstateSTR</a>";
		if ($IsType==1){
			$LinkQty="【类】图";
			}
		else{
			$QtyResult =mysql_fetch_array(mysql_query("SELECT count(*) as Qty FROM $DataIn.stuffreachlink WHERE QcId='$Id'",$link_id));
		    $LinkQty=$QtyResult["Qty"]==""?"&nbsp;":$QtyResult["Qty"];
		}
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$Title),
			array(0=>$LinkQty,1=>"align='center'"),
			array(0=>$Picture,
					 1=>"align='center'"),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Date,					
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
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
