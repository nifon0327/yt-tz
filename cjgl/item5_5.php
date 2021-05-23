<link href="css/keyboard.css" rel="stylesheet" type="text/css" />

<?php
include "../model/livesearch/modellivesearch.php";
$Th_Col="序号|35|入库日期|70|操作员|50|配件ID|60|配件名称|235|在库|60|可用库存|60|转入数量|60|库位|70|单位|30|备注|150|退回原因|80|状态|45|操作|45";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}
$SearchRows="";
$GysList="";
$nowInfo="当前:备品入库数据";
$funFrom="item5_5";
$addWebPage=$funFrom . "_add.php";
$updateWebPage=$funFrom . "_update.php";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$tempStuffCname%' OR D.StuffId='$tempStuffCname') ";
	$GysList2="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'/>";
   }
else{
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck7_bprk WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		$GysList="<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and  DATE_FORMAT(B.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$GysList.= "<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		$GysList.="</select>&nbsp;";
		}
      $GysList2="<input name='StuffCname2' type='text' id='StuffCname2' size='16' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery2' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname2').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}
//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
	$GysList1="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',405,300,'bottom')\" $addBtnDisabled>新 增</span>";


//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList  </td><td colspan='4'  class=''> $GysList2 &nbsp;$GysList1</td><td colspan='5' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="
SELECT B.Id,B.StuffId,B.Qty,D.Price*B.Qty AS Amount,D.Picture,B.Remark,B.Date,B.Locks,B.Operator,
D.StuffCname,D.Price,K.tStockQty,K.oStockQty,U.Name AS UnitName,B.Estate,B.ReturnReason,L.Identifier AS LocationName
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = B.LocationId
WHERE 1 $SearchRows ORDER BY  B.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Qty=$myRow["Qty"];
		$UnitName=$myRow["UnitName"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$LocationName=$myRow["LocationName"]==""?"&nbsp;":$myRow["LocationName"];
		$Date=$myRow["Date"];
		$ReturnReason=$myRow["ReturnReason"]==""?"&nbsp":$myRow["ReturnReason"];
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		$Price=$myRow["Price"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$SumAmount+=$Amount;
		$SumQty+=$Qty;
		$Estate=$myRow["Estate"];

		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";

        //审核通过后不能删除
	    if($SubAction==31 && $Estate>0 ){//有权限
		    $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
			$UpdateClick=" onclick=\"openWinDialog(this,'$updateWebPage?Id=$Id',405,300,'left')\" ";
			}
		else{//无权限

			if($SubAction==1){
				$UpdateClick="";
				$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
			}
		  }
		  switch($Estate){
		       case "0": $EstateStr = "<span class='greenB'>已审核</span>";  break;
		       case "1": $EstateStr = "<span class='redB'>未审核</span>";  break;
		       case "2": $EstateStr = "<span class='blueB'>退回</span>";  break;
			}

			echo"<tr><td class='A0111' align='center' height='25' >$i</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$tStockQty</td>";
			echo"<td class='A0101' align='right'>$oStockQty</td>";	//采购总数
			echo"<td class='A0101' align='right'>$Qty</td>";	//未收货数量
			echo"<td class='A0101' align='center'>$LocationName</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101'>$Remark</td>";
			echo"<td class='A0101' align='center'>$ReturnReason</td>";
			echo"<td class='A0101' align='center'>$EstateStr</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
	}while ($myRow = mysql_fetch_array($myResult));
    echo"</table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='10' align='center' height='30' class='A0111' style='background-color: #fff;'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
?>


</form>
</body>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script language = "JavaScript">
function CnameChanged(){
	StuffCname=document.getElementById("StuffCname2").value;
	if (StuffCname.length>=2){
	   document.getElementById("stuffQuery2").disabled=false;
	}
	else{
	  document.getElementById("stuffQuery2").disabled=true;
	}
}

function SetCname(){
	StuffCname=document.getElementById("StuffCname").value;
	if (StuffCname.length>=2){
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  document.getElementById("stuffQuery").disabled=true;
	}
}


function viewStuffdata() {
	var diag = new Dialog("live");
	var StuffCname=document.getElementById("StuffCname").value;
	if (StuffCname=="") return false;
	diag.Width = 950;
	diag.Height = 600;
	diag.Title = "配件资料";
	diag.URL = "viewStuffdata.php?Action=5&selModel=1&searchSTR="+StuffCname;
	diag.ShowMessageRow = false;
	diag.MessageTitle ="";
	diag.Message = "";
	diag.ShowButtonRow = true;
	diag.selModel=1; //1只选一条；0多选；
	diag.OKEvent=function(){
		var backData=diag.backValue();
		if (backData){
			var dtemp=backData.split("^^");
			if (dtemp.length>0){
			   document.getElementById("StuffId").value=dtemp[0];
			   document.getElementById("StuffCname").value=dtemp[1];
			   document.getElementById("Price").value = dtemp[2];
		       document.getElementById("CompanyId").value = dtemp[3];
			   document.getElementById("StuffCname").readOnly=true;
			}
		    diag.close();
		   }
		};
	diag.show();
}

</script>