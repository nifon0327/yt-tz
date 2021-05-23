<?php 
//代码共享、数据库共享-EWEN 2012-11-02
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增行政审核分类");//需处理
$nowWebPage ="CGLabel_19";	
$toWebPage  ="CGLabel_19_save";	
//$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
			<tr>
            	<td width="150" height="40" align="right" scope="col">KORDERNO</td>
            	<td scope="col"><input name="KORDERNO" type="text" id="KORDERNO" style="width:380px" maxlength="50" ></td>
			</tr>
			<tr>
            	<td width="150" height="40" align="right" scope="col">KPackingList</td>
            	<td scope="col"><input name="KPackingList" type="text" id="KPackingList" style="width:380px" maxlength="50" ></td>
			</tr>  
 			<tr>
            	<td width="150" height="40" align="right" scope="col">CAddress</td>
            	<td scope="col"><input name="CAddress" type="text" id="CAddress" style="width:380px" maxlength="50" ></td>
			</tr>
			<tr>
            	<td width="150" height="40" align="right" scope="col">Volume</td>
            	<td scope="col"><input name="Volume" type="text" id="Volume" style="width:380px" maxlength="50" ></td>
			</tr>  
 			<tr>
            	<td width="150" height="40" align="right" scope="col">KDestination</td>
            	<td scope="col"><input name="KDestination" type="text" id="KDestination" style="width:380px" maxlength="50" ></td>
			</tr>
			<tr>
            	<td width="150" height="40" align="right" scope="col">KStyle</td>
            	<td scope="col"><input name="KStyle" type="text" id="KStyle" style="width:380px" maxlength="50" ></td>
			</tr>  
 			<tr>
            	<td width="150" height="40" align="right" scope="col">Materal</td>
            	<td scope="col"><input name="Materal" type="text" id="Materal" style="width:380px" maxlength="50" ></td>
			</tr>
			<tr>
            	<td width="150" height="40" align="right" scope="col">KColor</td>
            	<td scope="col"><input name="KColor" type="text" id="KColor" style="width:380px" maxlength="50" ></td>
			</tr>   
			<tr>
            	<td width="150" height="40" align="right" scope="col">Drop</td>
            	<td scope="col"><input name="Drop" type="text" id="Drop" style="width:380px" maxlength="50" ></td>
			</tr>  
 			<tr>
            	<td width="150" height="40" align="right" scope="col">Tone</td>
            	<td scope="col"><input name="Tone" type="text" id="Tone" style="width:380px" maxlength="50" ></td>
			</tr>
                                            

      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function CheckForm(){ 	
	document.form1.action="CGLabel_19_save.php";
	document.form1.submit();
}
</script>
