<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工借支记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" cellspacing="0" id="NoteTable">
		<tr>
          <td width="150" height="27" align="right" valign="top" scope="col">借支员工：</td>
          <td scope="col"><select name="ListId[]" size="10" multiple id="ListId" style="width:420px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,2)" datatype="PreTerm" readonly Msg="未选择员工">
                        </select>
       	  </td>
		</tr>
				<tr>
				  <td height="32" align="right" scope="col">借支金额：</td>
				  <td scope="col"><input name="Amount" type="text" id="Amount" size="77" dataType="Currency" Msg="未填写或格式不对"></td>             
				</tr>
                <tr>
				  <td height="32" align="right" scope="col">结付银行：</td>
				  <td scope="col">
				<?php 
                include "../model/selectbank1.php";
				?>
                </td></tr>
          <tr>
            <td height="27" align="right">借支日期：</td>
            <td ><input name="PayDate" type="text" id="PayDate" value="<?php  echo date("Y-m-d")?>" size="77" maxlength="10" dataType="Date" format="ymd" Msg="未填写或格式不对" onfocus="WdatePicker()">
            </td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
            <td><textarea name="Remark" cols="50" rows="5" id="Remark" dataType="Require" Msg="未填写备注"></textarea></td>
          </tr>
		  <?php 
		  /*
          <tr>
            <td height="27" valign="top"><div align="right">借&nbsp;&nbsp;&nbsp;&nbsp;据：</div></td>
            <td><input name="Attached" type="file" id="Attached" size="66" dataType="Filter" msg="非jpg格式" accept="jpg" Row="4"></td>
          </tr>
		  */
		  ?>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>