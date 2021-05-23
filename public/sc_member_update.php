<?php 
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工小组");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT Name,GroupId FROM $DataPublic.staffmain WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$oldGroupId=$upData["GroupId"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
		  <td height="40" align="right" scope="col">员工姓名</td>
		  <td scope="col"><?php  echo $Name?></td>
	    </tr>
		<tr>
          <td height="40" align="right">调至小组</td>
          <td><select name="GroupId" id="GroupId" style="width:380px">
              <?php 
			$outResult=mysql_query("SELECT GroupId,GroupName FROM $DataIn.staffgroup WHERE Estate=1 AND BranchId>4 ORDER BY GroupId",$link_id);
			if($outRow = mysql_fetch_array($outResult)) {
				do{
					$GroupId=$outRow["GroupId"];
					$GroupName=$outRow["GroupName"];
					if($oldGroupId==$GroupId){
						echo "<option value='$GroupId' selected>$GroupName</option>";
						}
					else{
						echo "<option value='$GroupId'>$GroupName</option>";
						}
					}while ($outRow = mysql_fetch_array($outResult));
				}
			?>
          </select></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>