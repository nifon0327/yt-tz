<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新免抵退税结付资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("select M.Id,M.Taxdate,M.TaxNo,M.Taxamount,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.TaxIncome,M.Proof,B.Title
FROM $DataIn.cw14_mdtaxmain M  
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE M.Id='$Mid'",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
   $TaxNo=$MainRow["TaxNo"];
   $Remark=$MainRow["Remark"];
   $Taxamount=$MainRow["Taxamount"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,123,Id,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,TaxNo,$TaxNo";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col" width="150">免抵退税发票号:</td>
            <td scope="col"><?php    echo $TaxNo?></td>
		</tr>
	 <tr>
		  <td align="right" >免抵退税金额:</td>
		  <td scope="col"><?php    echo $Taxamount?></td>
	    </tr>             
	<tr>
		  <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="3" id="Remark" dataType="Require" Msg="未填写说明"><?php echo $Remark?></textarea></td>
		</tr>
		<tr>
		  <td  align="right" valign="top" scope="col">凭&nbsp;&nbsp;&nbsp;&nbsp;证</td>
		  <td scope="col"><input name="proof" type="file" id="proof" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>    
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>