<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 菜式列表");
$funFrom="ct_menu";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|餐厅|80|菜式分类|80|菜式名称|200|价格|60|状态|40|更新日期|75|操作|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	//餐厅
	$checkCTSql=mysql_query("SELECT Id,Name FROM $DataPublic.ct_data WHERE Estate=1 ORDER BY Id",$link_id);
	echo"<select name=CtId id=CtId onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
	if($checkCTRow=mysql_fetch_array($checkCTSql)){
		do{
			$Id=$checkCTRow["Id"];
			$Name=$checkCTRow["Name"];
			if($Id==$CtId){
				echo"<option value='$Id' selected>$Name</option>";
				$SearchRows.=" AND A.CtId='$Id'";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkCTRow=mysql_fetch_array($checkCTSql));
		}
	echo"</select>&nbsp;";
	//菜式分类
	$checkTypeSql=mysql_query("SELECT * FROM $DataPublic.ct_type WHERE Estate=1 ORDER BY Id",$link_id);
	if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
		echo"<select name=mType id=mType onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
		do{
			$Id=$checkTypeRow["Id"];
			$Name=$checkTypeRow["Name"];
			if($Id==$mType){
				echo"<option value='$Id' selected>$Name</option>";
				$SearchRows.=" AND A.mType='$Id'";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
		echo"</select>&nbsp;";
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
$mySql="SELECT A.Id,A.Name,A.Price,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS CTName,C.Name AS MenuType FROM $DataPublic.ct_menu A 
LEFT JOIN $DataPublic.ct_data B ON B.Id=A.CtId
LEFT JOIN $DataPublic.ct_type C ON C.Id=A.mType
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$CTName=$myRow["CTName"];	
		$MenuType=$myRow["MenuType"];	
		$Name=$myRow["Name"];	
		$Price=$myRow["Price"];	
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$CTName),
			array(0=>$MenuType),
			array(0=>$Name),
			array(0=>$Price,1=>"align='right'"),
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