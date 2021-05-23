<?php 
/*
$DataPublic.adminitype
$DataPublic.currencydata
*/
//步骤1 二合一已更新
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：//需处理
ChangeWtitle("$SubCompany 添加行政费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="100" height="25" align="right" scope="col">取款银行</td>
            <td scope="col"><?php 
                include "../model/selectbank1.php";
				?></td></tr>
          <tr>
            <td width="100" height="25" align="right" scope="col">取款人</td>
            <td scope="col"><input name="Name" type="text" id="Name" title="必填项,2-30个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','staffmain','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off"></td></tr>      
                
		<tr>
		  <td height="29" align="right" scope="col">取款日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d");?>" style="width:380px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">取款金额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width:380px" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  	<td height="24" align="right" scope="col">货币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">取款备注</td>
		  <td scope="col"><textarea name="Remark" style="width:380px" rows="3" id="Remark" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function changeAmount(){
	var Typetemp=document.getElementById('TypeId').value;
	if(Typetemp!=""){
		//分解内容
		var Split_ValueStr=Typetemp.split("|");
		document.form1.Amount.value=Split_ValueStr[1];
		}
	else{
		document.form1.Amount.value="";
		}
	}
</script>
