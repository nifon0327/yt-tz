
<?php 
include "../model/modelhead.php";
$ColsNumber=10;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 半成品未组BOM列表");
$funFrom="stuffdata_nobom";
$From=$From==""?"read":$From;

$Th_Col="选项|55|序号|40|配件Id|50|配件名称|420|单位|40|配件类型|80|默认供应商|100|采购|50|备注|30|更新日期|80|状态|30";
	
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT G.TypeId,G.Letter,G.TypeName 
	FROM $DataIn.stuffdata A 
	LEFT JOIN $DataIn.stufftype G ON G.TypeId = A.TypeId
	LEFT JOIN (
      SELECT mStuffId  FROM semifinished_bom  GROUP BY mStuffId
     )  S  ON S.mStuffId = A.StuffId
	 WHERE G.Estate=1 AND S.mStuffId IS NULL  AND G.mainType ='".$APP_CONFIG['SEMI_MAINTYPE']."' GROUP BY G.TypeId ORDER BY G.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
	do{
		$theTypeId=$myrow["TypeId"];
		$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
		if ($StuffType==$theTypeId){
			echo "<option value='$theTypeId' selected >$TypeName</option>";
			$SearchRows=" AND A.TypeId='$theTypeId' ";
			}
		else{
			echo "<option value='$theTypeId'  >$TypeName</option>";
			}
		}while ($myrow = mysql_fetch_array($result));
		echo "</select>&nbsp;";
	}
		
}
	

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";

//步骤5：
include "../model/subprogram/read_model_5.php";




$NowYear=date("Y");
$NowMonth=date("m");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);



$mySql="
	SELECT A.Id,A.StuffId,A.StuffCname,A.StuffEname,A.TypeId,A.Gfile,A.Gstate,A.Picture,	A.Gremark,A.Estate,A.Price,A.SendFloor,E.Forshort,B.BuyerId,C.Name,A.Remark,G.TypeName,D.Name AS UnitName,B.CompanyId,S.mStuffId
FROM $DataIn.stuffdata A 
LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
LEFT JOIN $DataIn.staffmain C ON C.Number=B.BuyerId 
LEFT JOIN $DataIn.stuffunit D ON D.Id=A.Unit
LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3) 
LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId
LEFT JOIN (
      SELECT mStuffId  FROM semifinished_bom  GROUP BY mStuffId
  )  S  ON S.mStuffId = A.StuffId
WHERE 1 $SearchRows   AND S.mStuffId IS NULL AND G.mainType ='".$APP_CONFIG['SEMI_MAINTYPE']."' ORDER BY A.Estate DESC,A.Id DESC";
	
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$TypeName=$myRow["TypeName"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];		
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
        $TypeId=$myRow["TypeId"];    			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
        
        $OrderSignColor="";
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staff_getname.php";
		$Buyer=$myRow["Name"]==""?"&nbsp;":$myRow["Name"];
		$Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];	
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),	
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'")
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