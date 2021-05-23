<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";


//步骤2：需处理
$ColsNumber=18;
$tableMenuS=800;
ChangeWtitle("$SubCompany 未使用配件列表");
$funFrom="stuffdata_nouse";
$From=$From==""?"read":$From;

$Th_Col="选项|55|序号|40|配件Id|45|配件名称|280|图档|30|图档日期|70|状态|30|历史<br>订单|40|参考买价|60|默认供应商|100|送货<br>楼层|40|采购|50|规格|120|单品<br>重量|50|在库|60|品检<br>类型|40|备注|30|更新日期|70|传图职责|60|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;
//每页默认记录数量

$Keys="31";//权限
//if(!(session_is_registered("Keys"))){
if(!($_SESSION["Keys"])){	
    //session_register("Keys");
	$_SESSION["Keys"] = $Keys;
}

$ActioToS="1,6";
//$ActioToS="1,2,3,4,5,6,7,8,13,40,98";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选

if($From!="slist"){
   $SearchRows="";
   $result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND S.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
}

 $selType=$selType==""?1:$selType;
 echo"<select name='selType' id='selType' onchange='ResetPage(this.name)'>'";
 switch ($selType){
    case 1:
     echo "<option value='1' selected>未使用配件(未下采购单)</option>";
     echo "<option value='2'>未使用配件(有下采购单)</option>";
     echo "<option value='3'>产品禁用后未使用配件</option>";
     //$linkTable="";
     $SearchRows.=" AND S.StuffId NOT IN (SELECT DISTINCT StuffId FROM $DataIn.pands ) ";
     $SearchRows.=" AND S.StuffId NOT IN (SELECT DISTINCT StuffId FROM $DataIn.cg1_stocksheet ) ";
     break;
    case 2:
     echo "<option value='1'>未使用配件(未下采购单)</option>";
     echo "<option value='2'  selected>未使用配件(有下采购单)</option>";
     echo "<option value='3'>产品禁用后未使用配件</option>";
     //$linkTable="";
     $SearchRows.=" AND S.StuffId NOT IN (SELECT DISTINCT StuffId FROM $DataIn.pands ) ";
     $SearchRows.=" AND S.StuffId IN (SELECT DISTINCT StuffId FROM $DataIn.cg1_stocksheet ) ";
     break;
    case 3:
     echo "<option value='1'>未使用配件(未下采购单)</option>";
     echo "<option value='2'>未使用配件(有下采购单)</option>";
     echo "<option value='3' selected>产品禁用后未使用配件</option>";
     $SearchRows.=" AND S.StuffId IN (SELECT DISTINCT P.StuffId FROM $DataIn.pands P LEFT JOIN $DataIn.productdata D ON D.ProductId=P.ProductId WHERE D.Estate=0) ";
     $SearchRows.=" AND S.StuffId NOT IN (SELECT DISTINCT P.StuffId FROM $DataIn.pands P LEFT JOIN $DataIn.productdata D ON D.ProductId=P.ProductId WHERE D.Estate=1) ";
     break;
     
 }    
echo "</select>&nbsp;";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
 //增加快带查询Search按钮
  $searchtable="stuffdata|S|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";
 //步骤5：
 include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	S.Id,S.StuffId,S.StuffCname,S.StuffEname,S.Gfile,S.Gstate,S.Picture,J.Name as Jobname,S.Gremark,S.Estate,S.Price,S.SendFloor,P.Forshort,M.Name,S.Spec,S.Remark,S.Weight,S.Date,S.GfileDate,S.Operator,S.Locks,K.tStockQty
	FROM $DataIn.stuffdata S 
	LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
	LEFT JOIN $DataPublic.jobdata J ON J.Id=S.Jobid 
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
	WHERE 1 $SearchRows AND S.Estate=1 order by S.Id DESC";

//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Price=$myRow["Price"];
		//$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
                $CheckSign=$myRow["CheckSign"]==1?"<div style='color:#E00;' >全检</div>":"抽检";
	/*	switch($Picture){
			 case 1://已上传
			  $Picstyle="style='color:#F63;'";
			  break;
			case 2://图片审核中
				$Picstyle="style='color:#F0F;'";
				break;
			case 4:
			case 7://图片要重新上传
				$Picstyle="style='color:#06C;'";
			break;
		    default:
			  $Picstyle="";
			}*/
		$tStockQty=$myRow["tStockQty"];
		$tStockQty=$tStockQty==0?"&nbsp;":$tStockQty;
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
                $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
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
		
		$Date=substr($myRow["Date"],0,10);
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$Jobname=$myRow["Jobname"];		
		$Jobname=$Jobname==""?"&nbsp;":$Jobname;
		$Weight=$myRow["Weight"];
		if ($Weight>0){
			$Weight=number_format($Weight, 3, '.', '');
		}
		else{
			$Weight="&nbsp;";
		}
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		$Buyer=$myRow["Name"];


		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		//echo "$theParam";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			

		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
                        array(0=>$OrderQtyInfo,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Weight, 		1=>"align='center'"),
			array(0=>$tStockQty, 		1=>"align='center'"),
            array(0=>$CheckSign, 	1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Jobname, 		1=>"align='center'"),
		//	array(0=>$Jobname, 		1=>"align='center' $Picstyle"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";



			
		echo $StuffListTB;
		
		
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

<script language="JavaScript" type="text/JavaScript">
<!--
function checkChange(obj){
	var e=document.getElementById("checkAccept");
    if (e.checked){
	  //document.getElementById("AcceptText").value="";
	  document.location.replace("../Admin/stuffdata_read.php");
	}
}
</script>