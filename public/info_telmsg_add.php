<?php   
//电信-ZX
//步骤1 $DataPublic.staffmain 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增来电留言记录");//需处理
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
	<table width="700" border="0" align="center" cellspacing="0">
      <tr>
        <td width="101" align="right"> 留 言 给 </td>
        <td><select name="Number" id="Number" style="width:456px" dataType="Require"  msg="未选择">
          <option value="">请选择</option>
		  <?php 
		  $checkUse=mysql_query("SELECT M.Number,M.Name FROM $DataPublic.staffmain M   
								LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
								WHERE M.Estate=1 and B.TypeId=1 ORDER BY M.BranchId,M.JobId",$link_id);
		  if($UseRow=mysql_fetch_array($checkUse)){
		  	do{
				$Number=$UseRow["Number"];
				$Name=$UseRow["Name"];
				echo"<option value='$Number'>$Name</option>";
				}while ($UseRow=mysql_fetch_array($checkUse));
			}
		  ?>
        </select>
        </td>
      </tr>
      <tr>
        <td align="right" valign="top">留言内容</td>
        <td>          <textarea name="Remark" cols="54" rows="8" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>