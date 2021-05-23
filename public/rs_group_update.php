<?php 
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新部门资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("
SELECT G.BranchId,G.TypeId,M.Name,G.GroupName  
FROM $DataIn.staffgroup G 
LEFT JOIN $DataIn.branchdata B ON B.Id = G.BranchId
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
WHERE G.Id='$Id'",$link_id));
$BranchId=$upData["BranchId"];
$TypeId=$upData["TypeId"];
$StaffName=$upData["Name"];
$GroupName=$upData["GroupName"];
$SelectedSTR=$TypeId==0?"selected":"";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="40" align="right" scope="col">部门名称</td>
            <td scope="col"><?php 
			include"../model/subselect/BranchId.php";
			?>
			</td>
		</tr>
		<tr>
          <td height="40" align="right" scope="col">生产分类</td>
          <td scope="col"><select name="TypeId" id="TypeId" style="width: 380px;">
			<?php 
			if($TypeId==0){
				echo "<option value='0' selected>非生产小组</option>";
				}
			else{
				$checkSql = mysql_query("SELECT TypeId,TypeName FROM $DataIn.stufftype WHERE Estate=1 AND mainType=3 ORDER BY Id",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					do{
						$TID=$checkRow["TypeId"];
						$TypeName=$checkRow["TypeName"];
						if($TypeId==$TID){
							echo "<option value='$TID' selected>$TID - $TypeName</option>";
							}
						else{
							echo "<option value='$TID'>$TID - $TypeName</option>";
							}
						}while ($checkRow=mysql_fetch_array($checkSql));
					} 
				}
			?>
           </select>
		  </td>
	    </tr>
		<tr>
          <td height="40" align="right" scope="col">小组组长</td>
          <td scope="col"><input name="StaffName" type="text" id="StaffName" style="width:380px" value="<?php  echo $StaffName?>" dataType="Require" Msg="未选择"></td>
	    </tr>
		<tr>
		  <td height="40" align="right" scope="col">小组名称</td>
		  <td scope="col"><input name="GroupName" type="text" id="GroupName" style="width:380px" value="<?php  echo $GroupName?>" maxlength="20" title="可输入2-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="2" Msg="没有填写或字符不在2-20个字节内"></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>