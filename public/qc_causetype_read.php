<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=550;
ChangeWtitle("$SubCompany QC不良原因查询");
$funFrom="qc_causetype";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|所属类别|80|不良原因|300|图片|60|有效状态|60|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	
	echo"<select name='Type' id='Type' onchange='ResetPage(this.name)'>";
          if ($Type==1 || $Type=="") {
              $SearchRows=" AND Q.Type='1'";
              echo "<option value='1' selected>默认类别</option>"; 
             }
          else echo "<option value='1'>默认类别</option>";
         $result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter  FROM $DataIn.qc_causetype Q 
                  LEFT JOIN $DataIn.stufftype T ON  Q.Type=T.TypeId 
                  WHERE Q.Estate='1' AND T.mainType='1' GROUP BY T.TypeId order by T.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){ 
		do{
			$theTypeId=$myrow["TypeId"];
			$Letter=$myrow["Letter"];
			$TypeName=$myrow["TypeName"];
			if ($Type==$theTypeId){
				echo "<option value='$theTypeId' selected>$Letter-$TypeName</option>";
				$SearchRows=" AND Q.Type='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$Letter-$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			
		}
         echo "</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT Q.Id,Q.Type,Q.Cause,Q.Picture,Q.Estate,Q.Date,Q.Operator,T.TypeName 
FROM $DataIn.qc_causetype Q
LEFT JOIN $DataIn.stufftype T ON T.TypeId=Q.Type 
WHERE 1 $SearchRows ORDER BY Q.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"]==""?"默认类别":$myRow["TypeName"];
		$Cause=$myRow["Cause"]==""?"&nbsp":$myRow["Cause"];	
		$Date=substr($myRow["Date"],0,10);
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		
                $FileName=$myRow["Picture"];
                if ($FileName==""){
                   $Picture="&nbsp;"; 
                }else{
		   $File=anmaIn($FileName,$SinkOrder,$motherSTR);
		   $Dir="download/qccause/";
		   $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		   $Picture="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;'>查看</a>";
                }
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),array(0=>$Cause),
			array(0=>$Picture, 1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
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
