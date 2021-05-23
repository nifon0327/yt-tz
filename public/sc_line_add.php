<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增拉线");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
	    </tr>
		<tr>
          <td height="40" align="right" scope="col">所属小组</td>
          <td scope="col"><select  id="GroupId" name="GroupId"  style="width:380px" dataType="Require" Msg="未选择">
            <?php 
               $GroupResult=mysql_query("SELECT GroupId,GroupName FROM $DataIn.staffgroup WHERE BranchId='8' AND Estate=1",$link_id);
               if($GroupRow=mysql_fetch_array($GroupResult)){
                   do{
                         $thisGroupId=$GroupRow["GroupId"];
                         $thisGroupName=$GroupRow["GroupName"];
                         echo "<option value='$thisGroupId'>$thisGroupName</option>";
                        }while($GroupRow=mysql_fetch_array($GroupResult));
                 }
            ?>
          </select>
            </td>
	    </tr>
		<tr>
		  <td height="40" align="right" scope="col">拉线名称</td>
		  <td scope="col"><input name="LineName" type="text" id="LineName" style="width:380px" dataType="Require" Msg="未填写"></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>