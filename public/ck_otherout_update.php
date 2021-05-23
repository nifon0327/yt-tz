<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新其它出库记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT F.ProposerId,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,D.StuffCname,K.tStockQty,K.oStockQty,F.Estate,F.LocationId,L.Identifier AS LocationName  
FROM $DataIn.ck8_bfsheet F 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=F.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=F.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = F.LocationId
WHERE 1 AND F.Id=$Id LIMIT 1",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$ProposerId=$upData["ProposerId"];
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	$Qty=$upData["Qty"];
	$Remark=$upData["Remark"];
	$Type=$upData["Type"];
	$Date=$upData["Date"];
	$tStockQty=$upData["tStockQty"];
	$oStockQty=$upData["oStockQty"];
	$Estate=$upData["Estate"];
	$LocationId=$upData["LocationId"];
	$LocationName=$upData["LocationName"];
	$TypeSTR="TypeSTR".strval($Type); 
	$$TypeSTR="selected";
		
	if($Estate > 0){
	    $addInfo = "";
		if($tStockQty-$Qty<=0 || $oStockQty-$Qty<=0){
			$unllQtyINFO="<span class='redB'>(不可做增加其它出库数量的操作.)</span>";
			}
		else{
		    $addQty = ($tStockQty-$Qty)>($oStockQty-$Qty) ? ($oStockQty-$Qty) :($tStockQty-$Qty);
		    $addInfo= "<span class='redB'>最多增加的数量:$addQty</span>";
			$OperatorsSTR="<option value='1'>增加</option>";
			}
		$OperatorsSTR.=" <option value='-1'>减少</option>";
	}else{
		$unllQtyINFO  = "<span class='redB'>(已审核，不能做增加或减少操作)</span>";
		$OperatorsSTR = "";
	}
}

$Ck_Result = mysql_query("SELECT Id,Identifier FROM  $DataIn.ck_location WHERE 1 AND Estate=1",$link_id); 
while ( $PD_Myrow = mysql_fetch_array($Ck_Result)){
	$LocationId=$PD_Myrow["Id"];
	$LocationName=$PD_Myrow["Identifier"];
	$subLocationId[]=$LocationId;
    $subLocationName[]=$LocationName;
}



$SaveSTR=$OperatorsSTR==""?"NO":"";
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
	var oldQty=Number(document.form1.oldQty.value);								//原其它出库数量
	var Operators=Number(document.getElementById("Operators").value);			//运算符
	var changeQty=document.form1.changeQty.value;								//新+/-的数量
	var tStockQty=Number(document.form1.tStockQty.value);						//在库数量
	var oStockQty=Number(document.form1.oStockQty.value);						//可用库存数量
	if(changeQty!=""){
		var CheckSTR=fucCheckNUM(changeQty,"");
		if(CheckSTR==0){
			Message="不是规范或不允许的值!";
			}
		else{
			changeQty=Number(changeQty);			
			if(Operators<0){		//减速少其它出库数量：
				if(changeQty>=oldQty){
					Message="超出原其它出库数量.";
					}
				}
			else{					//增加其它出库数量
			    
				if((changeQty + oldQty)>tStockQty || (changeQty + oldQty)>oStockQty){
					Message="超出实物或可用库存的范围!";
					}
				}
				
			}
		}
	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value="";
		return false;
		}
	else{
		if(changeQty==""){document.form1.changeQty.value=0;}
		document.form1.Remark.value=toGB(document.form1.Remark.value);
		document.form1.action="ck_bf_updated.php";document.form1.submit();
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
        <table width="688" border="0" align="center" cellspacing="5">
          <tr>
            <td align="right">配&nbsp;&nbsp;&nbsp;&nbsp;件</td>
            <td width="578"><?php  echo $StuffCname?>
                <input name="StuffId" type="hidden" id="StuffId" value="<?php  echo $StuffId?>"></td>
          </tr>
          <tr>
            <td align="right">出库数量</td>
            <td><input name="oldQty" type="text" id="oldQty" size="20" value="<?php  echo $Qty?>" class="I0000L" readonly>
                <input name="TempValue" type="hidden" id="TempValue">
            </td>
          </tr>
          <tr>
            <td align="right">在库</td>
            <td><input name="tStockQty" type="text" id="tStockQty" size="20" value="<?php  echo $tStockQty?>" class="I0000L" readonly></td>
          </tr>
          <tr>
            <td align="right">可用库存</td>
            <td><input name="oStockQty" type="text" id="oStockQty" size="20" value="<?php  echo $oStockQty?>" class="I0000L" readonly></td>
          </tr>
          
          <tr>
          		<td align="right" >入库库位</td>
            	<td> <input name="LocationName" type="text" id="LocationName" value="<?php echo $LocationName?>" size="60" dataType="Require"  msg="未输入入库库位" >
                <input name='LocationId' type='hidden' id='LocationId' value="<?php echo $LocationId?>" >
            	</td>
          	</tr>
          	
          <tr>
            <td height="22" align="right">申 请 人</td>
            <td height="22">
			<select name="ProposerId" id="ProposerId" width="60" style="width: 155px;">
			<?php 
			//员工资料表
			$PD_Sql = "SELECT M.Number,M.Name FROM $DataPublic.staffmain M 
			LEFT JOIN $DataIn.usertable U ON U.Number=M.Number WHERE M.Estate=1 ";
			$PD_Result = mysql_query($PD_Sql); 
			while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
				$Number=$PD_Myrow["Number"];
				$Name=$PD_Myrow["Name"];					
				if($ProposerId==$Number){
					echo "<option value='$Number' selected>$Name</option>";
					}
				else{
					echo "<option value='$Number'>$Name</option>";
					}
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td align="right">出库日期</td>
            <td><input name="bfDate" type="text" id="bfDate" size="20" value="<?php  echo $Date?>" onfocus="WdatePicker()" readonly>
            </td>
          </tr>
          <tr>
            <td height="30" align="right">数量更新</td>
            <td><?php 
			  if($OperatorsSTR==""){
				echo $unllQtyINFO;
				}
			  else{
				echo"<select name='Operators' id='Operators' style='width: 80px;' >$OperatorsSTR</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='7'>$addInfo";
				}
				?>
            </td>
          </tr>
		<tr>
            <td align="right">出库分类</td>
            <td>
          <select name="Type" id="Type" style="width: 400px;" dataType="Require"  msg="未选择分类">
			<?php 
			
			$Ck8_Sql = "SELECT Id,TypeName FROM  $DataPublic.ck8_bftype WHERE 1 AND Estate=1 AND mainType=2";
			$Ck8_Result = mysql_query($Ck8_Sql); 
			echo "<option value=''>请选择</option>";
			while ( $PD_Myrow = mysql_fetch_array($Ck8_Result)){
				$TypeId=$PD_Myrow["Id"];
				$TypeName=$PD_Myrow["TypeName"];
				if($TypeId==$Type){
					echo "<option value='$TypeId' selected>$TypeName</option>";
					}
				else{
					echo "<option value='$TypeId'>$TypeName</option>";
					}
				}
			?>
            </select>                     
            
			</td>
          </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
          <tr>
            <td align="right" valign="top">出库原因</td>
            <td><textarea name="Remark" cols="60" rows="5" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
</table>
	</td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>