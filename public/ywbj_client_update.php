<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新报价产品资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.yw3_pirules WHERE 1 AND Id=$Id LIMIT 1",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$uCompanyId=$upRow["CompanyId"];
	$Remark=$upRow["Remark"];
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
            <td align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 490px;" dataType="Require"  msg="未选择客户">
                <?php  
				$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 AND ObjectSign IN (1,2) order by Id",$link_id);
				if($myrow = mysql_fetch_array($result)){
					do{
						if($uCompanyId==$myrow["CompanyId"]){	
							echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
							}
						else{
							echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
							}
						} while ($myrow = mysql_fetch_array($result));
					}
			  ?>
              </select>
            </td>
          </tr>
       
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="58" rows="5" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>