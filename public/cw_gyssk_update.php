<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新供应商税款");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.cw2_gyssksheet Where Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Forshort=$upRow["Forshort"];
	$Currency=$upRow["Currency"];
	$Date=$upRow["Date"];
	$PayMonth=$upRow["PayMonth"];
	$Amount=$upRow["Amount"];
	$Currency=$upRow["Currency"];
	$Remark=$upRow["Remark"];
	$InvoiceNUM=$upRow["InvoiceNUM"];
	$InvoiceFile=$upRow["InvoiceFile"];
	$Rate=$upRow["Rate"];
	$Fpamount=$upRow["Fpamount"];
	$Getdate=$upRow["Getdate"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";

//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="100" height="25" align="right" scope="col">供 应 商</td>
            <td scope="col"><input name="Forshort" type="text" id="Forshort" style="width:420px" value="<?php  echo $Forshort?>" dataType="Require" Msg="未填写说明"
             onkeyup="showResult(this.value,'Forshort','nonbom3_retailermain|providerdata','12')" onblur="LoseFocus()" autocomplete="off"
            ></td>
		</tr>
		<tr>
          <td height="24" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
          <td scope="col"><select name="Currency" id="Currency" style="width:420px" dataType="Require"  msg="未选择货币">
              <?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					if($Currency==$cRow["Id"]){
						echo"<option value='$cRow[Id]' selected>$cRow[Name]</option>";
						}
					else{
						echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
						}
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
          </select></td>
	    </tr>
	     <tr>
		  <td height="29" align="right" scope="col">货款月份</td>
		  <td scope="col"><input name="PayMonth" type="text" id="PayMonth" onfocus="WdatePicker({dateFmt:'yyyy-MM'})" value="<?php  echo $PayMonth ?>" style="width:420px" maxlength="10" dataType="Require" Msg="未选日期或格式不对" readonly></td>
	    </tr>
	    
		<tr>
		  <td height="29" align="right" scope="col">请款日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo $Date?>" style="width:420px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly>          </td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">税款金额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width:420px" value="<?php  echo $Amount?>" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">加税率</td>
		  <td scope="col"><input name="Rate" type="text" id="Rate" style="width:420px" value="<?php  echo $Rate?>" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" style="width:420px" rows="3" id="Remark" dataType="Require" Msg="未填写说明"><?php  echo $Remark?></textarea></td>
		</tr>
		<tr>
          <td height="24" align="right" scope="col">发 票 号</td>
          <td scope="col"><input name="InvoiceNUM" type="text" id="InvoiceNUM" style="width:420px" maxlength="20" value="<?php  echo $InvoiceNUM?>" dataType="Require" Msg="未填写说明"></td>
	    </tr>
		<tr>
          <td height="24" align="right" scope="col">发 票 金 额</td>
          <td scope="col"><input name="Fpamount" type="text" id="Fpamount" style="width:420px" maxlength="20" value="<?php  echo $Fpamount?>" dataType="Require" Msg="未填写说明"></td>
	    </tr>
		<tr>
		  <td height="29" align="right" scope="col">收到发票日期</td>
		  <td scope="col"><input name="Getdate" type="text" id="Getdate" onfocus="WdatePicker()" value="<?php  echo $Getdate?>" style="width:420px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly>          </td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">发票凭证</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" style="width:420px" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="6" Cel="1"></td>
	    </tr>
		<?php 
		if($InvoiceFile==1){
			echo"<tr><td height='13' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传凭证</LABEL></td></tr>";
			}?>
            
       <tr>
          <td align="right" valign="top" scope="col">非BOM采购单</td>
          <td valign="middle" scope="col"><p>
            <select name="ListId[]" size="10" id="ListId" multiple style="width: 420px;" datatype="autoList"   onclick="SearchRecord('cw_gyssk','<?php  echo $funFrom?>',2,0)" readonly>
            </select>
          </p>
            </td>
        </tr>
                    
        </table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>