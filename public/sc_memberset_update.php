<?php 
//可以做批量调动电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车间小组成员调动");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("
	SELECT G.GroupLeader,G.GroupName,M.Date,P.Name 
	FROM $DataIn.staffgroup G 
	LEFT JOIN $DataIn.sc1_memberset M ON M.GroupId=G.GroupId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=G.GroupLeader 
	WHERE G.GroupId='$GroupId' AND M.Date='$Date'
",$link_id));
$GroupName=$upData["GroupName"];		//调动前组名
$GroupLeader=$upData["GroupLeader"];	//调动前班长编号
$Name=$upData["Name"];					//调动前班长姓名
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Date,$Date,GroupId,$GroupId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
        <tr>
          <td height="15" align="right" scope="col">调整日期</td>
          <td scope="col"><?php  echo $Date?></td>
        </tr>
		<tr>
            <td width="265" height="15" align="right" scope="col">原 小 组</td>
            <td width="476" scope="col">
              <?php 
				echo"$GroupName - $Name";
			 ?>
			</td>
		</tr>
		<tr>
		  <td width="265" height="20" align="right" valign="top" scope="col">小组成员</td>
		  <td scope="col"><select name="ListId[]" size="18" multiple id="ListId" style="width: 180px;" dataType="Require"  msg="未选择成员">
		    <?php 
			$ListSql=mysql_query("SELECT S.Number,M.Name
			FROM $DataIn.sc1_memberset S 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
			WHERE S.Date='$Date' AND S.GroupId='$GroupId' ORDER BY M.KqSign DESC,M.BranchId,M.JobId",$link_id);
			if($ListRow = mysql_fetch_array($ListSql)){
				do{
					$Number=$ListRow["Number"];
					$Name=$ListRow["Name"];
					echo"<option value='$Number'>$Name</option>";
					}while($ListRow = mysql_fetch_array($ListSql));
				}
            ?>
		  </select></td>
	    </tr>
		<tr>
		  <td height="40" align="right" scope="col">调&nbsp;动&nbsp;至</td>
		  <td scope="col"><select name="newGroupId"  id="newGroupId" style="width:180px" dataType="Require"  msg="未选择小组">
		  <option value="" selected>请选择</option>
            <?php 
			 $Result1 = mysql_query("SELECT G.GroupId,G.GroupName,M.Name 
			 FROM $DataIn.staffgroup G 
			 LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
			 LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
			 WHERE 1 AND G.GroupId!='$GroupId' AND B.TypeId=2 ORDER BY G.GroupId",$link_id);
			 if($myRow1 = mysql_fetch_array($Result1)){
				do{
					echo" <option value='$myRow1[GroupId]'>$myRow1[GroupName] - $myRow1[Name]</option>";
					}while($myRow1 = mysql_fetch_array($Result1));
				}
			 ?>
          </select></td>
	    </tr>
		<tr>
		  <td height="40" colspan="2" scope="col">操作说明：在小组成员列表中，选取要调动的员工(按CTRL键，然后鼠标点选员工姓名)，然后选择分配至的小组，点保存。</td>
	    </tr>
      </table>
      </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>