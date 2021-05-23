<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增报介配件资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		 <tr>
		    <td height="42" width="60">&nbsp;</td>
            <td align="right">客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 400px;" dataType="Require"  msg="未选择客户">
                <option value="" selected>请选择</option>
                <?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 AND ObjectSign IN (1,2) order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>
              </select>
            </td>
          </tr>
        <tr>
		    <td height="42" width="60">&nbsp;</td>
            <td  align="right">备注</td>
            <td><textarea name="Remark" id="Remark" cols="47" rows="3"></textarea></td>
        </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>