<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 会计科目分类列表");
$funFrom="acfirsttype";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|科目代码|80|科目名称|250|类别|80|方向|40|外币核算|80|期末调汇|50|辅助核算|65|行政费|50|无形资产|50|备注|200|状态|40|更新日期|75|操作|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$SearchRows="";

	$result = mysql_query("SELECT * FROM $DataPublic.acfirsttype WHERE LENGTH(FirstId)=4",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo"<select name='FirstId' id='FirstId' onchange='document.form1.submit();'><option value='' selected>一级科目</option>";
		do{
			$theTypeId=$myrow["FirstId"];
			$TypeName=$myrow["Name"];
			if ($FirstId==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows=" AND A.FirstId like '$theTypeId%' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
		}while ($myrow = mysql_fetch_array($result));
		echo "</select>&nbsp;";
	}
	
	$result = mysql_query("SELECT A.* FROM $DataPublic.acfirsttype  A WHERE LENGTH(A.FirstId)=6  $SearchRows ",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo"<select name='SFirstId' id='SFirstId' onchange='document.form1.submit();'><option value='' selected>二级科目</option>";
		do{
			$theTypeId=$myrow["FirstId"];
			$TypeName=$myrow["Name"];
			if ($SFirstId==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows.=" AND A.FirstId like '$theTypeId%' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
		}while ($myrow = mysql_fetch_array($result));
		echo "</select>&nbsp;";
	}
	
	$result = mysql_query("SELECT * FROM $DataPublic.actype WHERE Estate=1 order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo"<select name='acTypeId' id='acTypeId' onchange='document.form1.submit();'><option value='' selected>分类</option>";
		do{
			$theTypeId=$myrow["Id"];
			$TypeName=$myrow["Name"];
			if ($acTypeId==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows.=" AND A.TypeId='$theTypeId' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
		}while ($myrow = mysql_fetch_array($result));
		echo "</select>&nbsp;";
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
$mySql="SELECT A.Id,A.FirstId,A.Name,A.TypeId,A.OISignId,A.CalCurrencyId,A.EndTermSignId,A.AssistCalId,A.emptyStr,A.Remark,A.Estate,A.Date,A.Operator,A.Locks,A.ExpenseSign,A.IntangibleSign,
T.Name as TypeName,
O.Name as OISignName,
C.Name as CalCurrencyName,
E.Name as EndTermSignName,
S.Name as AssistCalName
FROM $DataPublic.acfirsttype A
LEFT JOIN $DataPublic.actype T ON T.Id=A.TypeId 
LEFT JOIN $DataPublic.acOISign O ON O.Id=A.OISignId 
LEFT JOIN $DataPublic.acCalCurrency C ON C.Id=A.CalCurrencyId 
LEFT JOIN $DataPublic.acEndTermSign E ON E.Id=A.EndTermSignId 
LEFT JOIN $DataPublic.acAssistCal S ON S.Id=A.AssistCalId 
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.FirstId";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);



if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$FirstId=$myRow["FirstId"];
		$Letter=$myRow["Letter"];
		$Name=$myRow["Name"];
		
		$TypeName=$myRow["TypeName"];
		$OISignName=$myRow["OISignName"];
		$CalCurrencyName=$myRow["CalCurrencyName"];
		$EndTermSignName=$myRow["EndTermSignName"];
		$AssistCalName=$myRow["AssistCalName"];
		$emptyStr=$myRow["emptyStr"];
		
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		
		$ExpenseSign=$myRow["ExpenseSign"]==1?"<div class='greenB'>√</div>":"&nbsp;";
		$IntangibleSign=$myRow["IntangibleSign"]==1?"<div class='greenB'>√</div>":"&nbsp;";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$FirstId,),
			array(0=>"$emptyStr".$Name),
			array(0=>$TypeName),
			array(0=>$OISignName,1=>"align='center'"),
			array(0=>$CalCurrencyName),
			array(0=>$EndTermSignName,1=>"align='center'"),
			array(0=>$AssistCalName,1=>"align='center'"),
			array(0=>$ExpenseSign,1=>"align='center'"),
			array(0=>$IntangibleSign,1=>"align='center'"),
			array(0=>$Remark,3=>"..."),
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