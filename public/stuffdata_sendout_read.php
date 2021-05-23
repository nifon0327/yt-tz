<?php 
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
$upFlag=$_GET["ID"];
if ($upFlag!=""){
 $JobType=$upFlag;
}
$ColsNumber=24;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 外发供应商配件列表");
$funFrom="stuffdata_sendout";
$From=$From==""?"read":$From;
$Th_Col="选项|55|序号|40|配件Id|50|配件名称|280|图档|30|图档日期|70|历史<br>订单|40|QC图|40|认证|40|品检</br>方式|40|状态|30|参考买价|60|单位|40|配件类型|60|默认供应商|100|送货</br>楼层|40|采购|50|规格|120|备注|30|更新日期|80|操作|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,4";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT A.TypeId,G.TypeName,G.Letter FROM $DataIn.stuffout O 
                   LEFT JOIN $DataIn.stuffdata A ON A.StuffId=O.StuffId
                   LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  WHERE 1 GROUP BY A.TypeId order by G.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND A.TypeId='$theTypeId' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
}

	

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
  $searchtable="stuffdata|A|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";
//}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
if($NameRule!=""){
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF'><tr ><td height='15' class='A0011' width='$tableWidth' >
       <span style='color:red'>命名规则:</span>$NameRule
	   </td></tr></table>";
  }
  $NowYear=date("Y");
$NowMonth=date("m");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT O.Id,A.StuffId,A.StuffCname,A.StuffEname,A.TypeId,A.Gfile,A.Gstate,A.Picture,A.Gremark,A.Estate,A.Price,
A.SendFloor,E.Forshort,B.BuyerId,C.Name,A.Spec,A.Remark,A.Weight,A.Date,
A.GfileDate,A.ForcePicSpe,A.Operator,A.Locks,A.CheckSign,G.TypeName,D.Name AS UnitName
FROM $DataIn.stuffout O 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=O.StuffId
LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
LEFT JOIN $DataPublic.staffmain C ON C.Number=B.BuyerId 
LEFT JOIN  $DataPublic.stuffunit D ON D.Id=A.Unit
LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 	
LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  	
WHERE 1 $SearchRows  $ShipMonthStr ORDER BY A.Estate DESC,A.Id DESC";
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
		$Price=$myRow["Price"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];		
		
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
        $TypeId=$myRow["TypeId"];
        //配件QC检验标准图
        $QCImage="";
        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage=$QCImage==""?"&nbsp;":$QCImage;               
        $CheckSign=$myRow["CheckSign"]==1?"<div style='color:#E00;' >全检</div>":"抽检";
		include "../model/subprogram/stuffreach_file.php";
		
		$mStockQty=$myRow["mStockQty"];
		$mStockQty=$mStockQty==0?"&nbsp;":$mStockQty;
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
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
		$BuyerId=$myRow["BuyerId"];		//登录人员不是属于采购者锁定操作
	
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";         //历史订单
		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
            array(0=>$OrderQtyInfo, 1=>"align='center'"),
            array(0=>$QCImage, 	    1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
            array(0=>$CheckSign, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$Forshort),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
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