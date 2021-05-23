<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新备品入库记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT B.StuffId,B.Qty,B.Remark,B.Date,D.StuffCname,K.tStockQty,K.oStockQty,B.Estate,B.LocationId,L.Identifier AS LocationName  
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=B.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = B.LocationId
WHERE 1 AND B.Id=$Id LIMIT 1",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StuffCname="($StuffId)".$upData["StuffCname"];
	$Qty=$upData["Qty"];
	$Remark=$upData["Remark"];
	$Date=$upData["Date"];
	$Estate=$upData["Estate"];
	$LocationId=$upData["LocationId"];
	$LocationName=$upData["LocationName"];
	}
	
$Ck_Result = mysql_query("SELECT Id,Identifier FROM  $DataIn.ck_location WHERE 1 AND Estate=1",$link_id); 
while ( $PD_Myrow = mysql_fetch_array($Ck_Result)){
	$LocationId=$PD_Myrow["Id"];
	$LocationName=$PD_Myrow["Identifier"];
	$subLocationId[]=$LocationId;
    $subLocationName[]=$LocationName;
}
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,StuffId,$StuffId";
//步骤5：//需处理
?>
<script language = "JavaScript">
function CheckForm(){ 
	var Message="";
	var changeQty=document.form1.changeQty.value;								//新+/-的数量

	var CheckSTR=fucCheckNUM(changeQty,"");
	if(CheckSTR==0){
		Message="不是规范或不允许的值！";		
		}

	
	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value="";
		return false;
		}
	else{
		document.form1.Remark.value=toGB(document.form1.Remark.value);
		document.form1.action="ck_bp_updated.php";document.form1.submit();
		}
	} 
	
window.onload = function(){
        var subLocationName=<?php  echo json_encode($subLocationName);?>;
        var subLocationId=<?php  echo json_encode($subLocationId);?>;

		var sinaSuggestByMan= new InputSuggest({
			input: document.getElementById('LocationName'),
			poseinput: document.getElementById('LocationId'),
			data: subLocationName,
            id:subLocationId,
			width: 290
		});             	
	}
</script> 
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">	
		<table width="600" border="0" align="center" cellspacing="5">
			<tr>
            	<td align="right" height="25">入库配件</td>
            	<td><?php  echo $StuffCname?></td>
          	</tr>
			<tr>
          		<td align="right" height="25">出库库位</td>
            	<td> <input name="LocationName" type="text" id="LocationName" value="<?php echo $LocationName?>" size="60" dataType="Require"  msg="未输入入库库位" >
                <input name='LocationId' type='hidden' id='LocationId' value="<?php echo $LocationId?>" >
            	</td>
          	</tr>
			<tr>
          		<td height="8" align="right">入库数量</td>
           	 	<td><input name="oldQty" type="text" id="oldQty" size="11" value="<?php  echo $Qty?>" class="I0000L" readonly>
            </td>
          	</tr>
			<tr>
            	<td align="right" height="25">入库日期</td>
            	<td><input name="bpDate" type="text" id="bpDate" size="11" value="<?php  echo $Date?>" onfocus="WdatePicker()" readonly></td>
          	</tr>
		  <tr>
			<td height="30"><div align="right">数量更新</div></td>
			<td><?php 

				echo"<select name='Operators' id='Operators' style='width: 100px;' >
				<option value='-1'>减少</option>
				<option value='1'>增加</option>
				</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='10'>";
				?>
			</td>
		  </tr>
          	<tr>
            	<td height="25" align="right" valign="top">入库备注</td>
            	<td><textarea name="Remark" cols="59" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
          	</tr>
        </table>
   </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>