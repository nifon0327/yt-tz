<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新员工购房补助信息");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT   A.Id,A.Number,A.Amount,A.Remark,A.Attached,A.Date,A.Estate,A.Locks,A.Operator,M.Name,B.Name AS Branch,J.Name AS Job
FROM $DataIn.staff_housesubsidy A 
LEFT JOIN $DataIn.staffmain M ON M.Number=A.Number
LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
WHERE 1 AND A.Id=$Id",$link_id));

$Amount=$upData["Amount"];
$Name=$upData["Name"];
$Number=$upData["Number"];
$Remark=$upData["Remark"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		    <tr>
            <td height="30" scope="col" align="right">公司员工</td>
            <td scope="col"><input name="Number" type="text" id="Number" value="<?php echo $Number?>" size="20" dataType="Require" Msg="未填写" readonly   onclick="SearchRecord('staff','<?php  echo $funFrom?>',1,2)" ></td></tr> 
			<tr>
            <td scope="col"align="right">金&nbsp;&nbsp;额</td>
            <td scope="col"><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" size="50"  dataType="Double" Msg="未填写或格式不对"></td></tr>
		<tr>
		  <td height="30" scope="col" align="right" >凭&nbsp;&nbsp;证</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="58" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="45" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
