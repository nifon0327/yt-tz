<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增备品入库记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$Ck_Result = mysql_query("SELECT Id,Identifier FROM  $DataIn.ck_location WHERE 1 AND Estate=1",$link_id); 
while ( $PD_Myrow = mysql_fetch_array($Ck_Result)){
	$LocationId=$PD_Myrow["Id"];
	$LocationName=$PD_Myrow["Identifier"];
	$subLocationId[]=$LocationId;
    $subLocationName[]=$LocationName;
}
 
?>
<script language = "JavaScript">
function ViewStuffId(){
	var SafariReturnValue=document.getElementById("SafariReturnValue");
	var r=Math.random();  
	var BackData=window.showModalDialog("stuffdata_s1.php?r="+r+"&tSearchPage=stuffdata&fSearchPage=ck_bp&SearchNum=1&Action=5","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes"); 
	if (SafariReturnValue.value!='') BackData=SafariReturnValue.value;
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.StuffId.value=CL[0];
		document.form1.StuffCname.value=CL[1];
		document.form1.Price.value = CL[2];
		document.form1.CompanyId.value = CL[3];
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
		<table width="90%" border="0" align="center" cellspacing="5">
			<tr>
            	<td width="100" height="25" align="right">入库日期</td>
            	<td><input name="Date" type="text" id="Date" size="60" value="<?php  echo date("Y-m-d")?>" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
          	</tr>
			<tr>
            	<td align="right" height="25">入库配件
           	    <input name="StuffId" type="hidden" id="StuffId"></td>
            	<td><input name="StuffCname" type="text" id="StuffCname" size="60" onClick="ViewStuffId(2)" datatype="Require"  msg="未选择入库配件" readonly><input name='CompanyId' type='hidden' id='CompanyId' ><input name='Price' type='hidden' id='Price' ></td>
			</tr>
			<tr>
          		<td align="right" height="25">入库库位</td>
            	<td> <input name="LocationName" type="text" id="LocationName" size="60" dataType="Require"  msg="未输入入库库位" >
                <input name='LocationId' type='hidden' id='LocationId' >
            	</td>
          	</tr>
          	<tr>
          		<td align="right" height="25">入库数量</td>
            	<td><input name="Qty" type="text" id="Qty" size="60" dataType="Price"  msg="入库数量不正确"></td>
          	</tr>
          	<tr>
            	<td height="25" align="right" valign="top">入库备注</td>
            	<td><textarea name="Remark" cols="62" rows="6" id="Remark" dataType="Require"  msg="未输入入库备注"></textarea></td>
          	</tr>
        </table>
   </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>