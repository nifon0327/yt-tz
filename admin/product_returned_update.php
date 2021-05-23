<?php   
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新退货记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.product_returned WHERE Id='$Id'",$link_id));
$CompanyId=$upData["CompanyId"];
$ProductId=$upData["ProductId"];
$eCode=$upData["eCode"];
$ReturnMonth=$upData["ReturnMonth"];
$Qty=$upData["Qty"];
$Price=$upData["Price"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
         <tr>
            <td width="150" align="right">客户</td>
            <td><select name="CompanyId" id="CompanyId" style="width:300px" dataType="Require"  msg="未选择部门类别">
                <?php   
				$J_Result=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE CompanyId IN (1004,1059) order by Id",$link_id);
				if($J_Row = mysql_fetch_array($J_Result)) {
					do{
						$theCompanyId=$J_Row["CompanyId"];
						$theForshort=$J_Row["Forshort"];
						if($CompanyId==$theCompanyId){
							echo "<option value='$theCompanyId' selected>$theForshort</option>";
							}
						else{
							echo "<option value='$theCompanyId'>$theForshort</option>";
							}
						}while ($J_Row = mysql_fetch_array($J_Result));
					}
				?>
              </select>
            </td>
          </tr>
         <?php   
         $StaffSql = mysql_query("SELECT M.Id,M.Number,M.Name, B.Name AS Branch
	FROM $DataPublic.staffmain M 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId	
	WHERE 1 AND M.Estate='1' ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
                $sName=$StaffRow["Name"];
		$sBranch=$StaffRow["Branch"];
                if ($Manager==$sNumber) $ManagerName=$sBranch . "-" . $sName;
                $subName[]=array($sNumber,$sName,$sBranch);
	};
         ?>
       <tr>
		  <td height="40" align="right" scope="col">Product Code</td>
		  <td scope="col"><input name="eCode" type="text" id="eCode"  value="<?php    echo $eCode?>"  style="width:300px" dataType="Require" Msg="未填写或格式不对"></td>
                   <input name='Manager' type='hidden' id='Manager'  value="<?php    echo $Manager?>">
	    </tr>
        <tr>
          <td height="40" align="right" scope="col">退货月份</td>
          <td scope="col"><input name="ReturnMonth" type="text" id="ReturnMonth" style="width:300px" value="<?php    echo $ReturnMonth?>" maxlength="7" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
        <tr>
          <td height="40" align="right" scope="col">退货数量</td>
          <td scope="col"><input name="Qty" type="text" id="Qty" value="<?php    echo $Qty?>" style="width:300px" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
        <tr>
          <td height="43" align="right" scope="col">单价</td>
          <td height="43" scope="col"><input name="Price" type="text" id="Price" value="<?php    echo $Price?>" style="width:300px" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
  </table></td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>