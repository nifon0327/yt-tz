<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增供应商提示");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$CheckFormURL="thisPage";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td  height="34" align="right" class='A0010'>日&nbsp;&nbsp;&nbsp;&nbsp;期: </td>
    <td class='A0001'><input name="Date" type="text" id="Date" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d")?>" size="78" maxlength="10" readonly  dataType="Date" format="ymd" msg="格式不对或未选择"></td>
  </tr>
  <tr>
            <td align="right" class='A0010'>供 应 商:</td>
            <td class='A0001'> <select name="CompanyId" id="CompanyId" style="width: 420px;" dataType="Require" onchange="whichselect(this);"  msg="未选择供应商">
			<option value='1'>全部</option>
            <option value='2'>自选</option>
            <?php 
			/*
			//供应商
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign OR cSign=0) AND Estate='1' order by Letter";
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$CompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				echo "<option value='$CompanyId'>$Forshort</option>";
				} 
				*/
			?>
            </select>
			</td>
    	</tr>
        
	  <tr>
		<td align="right"  class='A0010' >供应商自选：</td>
		<td  class='A0001'>
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 420px;" onclick="SearchRecord('cg_msg','<?php  echo $funFrom?>',2,1)"  readonly disabled="disabled">
		 	</select>
            <input name="PListId" id="PListId" type="hidden" value="">
		</td>
	</tr>        
        
    <tr>
		<td align="right"  class='A0010'>提示内容:</td>
	  <td class='A0001'><textarea name="Remark" cols="50" rows="8" id="Remark" datatype="Require" msg="未填写"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function whichselect(e){
	var srcObj=document.getElementById("ListId");
	if (e.value==1){
		
		srcObj.disabled=true;
		var srcOpts = srcObj.options;
		var len = srcOpts.length;
      	for (i = len - 1; i >= 0; i--) {
			srcOpts.remove(i);
		}
	}
	else {
		srcObj.disabled=false;
	}
	//alert (e.value);
}

function CheckForm(){
	var Message="";
	var Remark=document.getElementById('Remark').value;
	//alert ('1111111111111');
	if (Remark==''){
		alert('提示内容未填写');
		return false;
	}
	var id = "";
	var x = document.getElementById("ListId");
	var len = x.length;
	for(var i=0; i<len; i++){
		 id += x.options[i].value+"^"; 
	}
	id = id.substr(0, id.length-1);
	//alert(id);
	//return false;
	document.getElementById("PListId").value = id;
	document.form1.action="cg_msg_save.php";
	document.form1.submit();
}
</script>