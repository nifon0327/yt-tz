<?php 
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 新增开发费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="760" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td width="77" align="right" scope="col">开发项目</td>
            <td width="655" scope="col"><input name="ItemStr" type="text" id="ItemStr" size="93" DataType="Require" Msg="没有选取项目" onclick="SearchRecord('development','<?php  echo $funFrom?>',1)" readonly></td>
		</tr>

          <tr>
            <td width="77" align="right" scope="col">费用分类</td>
            <td  scope="col">
			<select name="TypeID" id="TypeID" style="width:498px" dataType="Require" msg="未选择">
			<option value="">请选择</option>
			<?php 
			$kftypedata_Result = mysql_query("SELECT Id,Name FROM $DataPublic.kftypedata WHERE Estate=1 order by Id",$link_id);
			if($kftypedata_Row = mysql_fetch_array($kftypedata_Result)){
				do{
					$Id=$kftypedata_Row["Id"];
					$Name=$kftypedata_Row["Name"];
					echo"<option value='$Id'>$Name</option>";
					}while ($kftypedata_Row = mysql_fetch_array($kftypedata_Result));
				}
			?>
            </select> 
			</td>
          </tr>        
        
		<tr>
		  <td width="77" align="right" scope="col">请款金额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" size="93" DataType="Currency" Msg="没有填写或格式不对"></td>
		</tr>
		<tr>
		  <td width="77" align="right" scope="col">退回金额</td>
		  <td scope="col"><input name="OutAmount" type="text" id="OutAmount" size="93" DataType="Currency" Msg="没有填写或格式不对" value="0.00"></td>
		</tr>
		 
          <tr>
            <td width="77" align="right" scope="col">结付货币</td>
            <td  scope="col">
			<select name="Currency" id="Currency" style="width:498px" dataType="Require" msg="未选择">
			<option value="">请选择</option>
			<?php 
			$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
			if($Currency_Row = mysql_fetch_array($Currency_Result)){
				do{
					$Id=$Currency_Row["Id"];
					$Name=$Currency_Row["Name"];
					echo"<option value='$Id'>$Name</option>";
					}while ($Currency_Row = mysql_fetch_array($Currency_Result));
				}
			?>
            </select> 
			</td>
          </tr>
          <!--   ///////////////////////////////////////////////////////////////
		<tr>
		  <td width="77" align="right" scope="col">机&nbsp;&nbsp;&nbsp;&nbsp;型</td>
		  <td scope="col"><input name="ModelDetail" type="text" id="ModelDetail" size="93" DataType="LimitB"  Max="60" Min="1" Msg="没有填写或字符超出60字节"></td>
		</tr>
        -->
        <tr>
          <td align="right" valign="top">凭&nbsp;&nbsp;&nbsp;&nbsp;证</td>
          <td><input name="Attached" type="file" id="Attached" size="80"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="2" Cel="1"></td>
        </tr>
		<tr>
		  <td width="77" align="right" scope="col">供 应 商</td>
		  <td scope="col"><input name="Provider" type="text" id="Provider" size="93" DataType="LimitB"  Max="30" Min="1" Msg="没有填写或字符超出30字节"></td>
		</tr>
		<tr>
            <td width="77" align="right" valign="top" scope="col">请款说明</td>
            <td scope="col"><textarea name="Description" cols="60" rows="6" id="Description" DataType="Require" Msg="没有填写"></textarea></td>
		</tr>
		<tr>
			<td width="77" align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
			<td scope="col"><textarea name="Remark" cols="60" rows="6" id="Contant"></textarea></td>
		</tr>
     </table>
   </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>