<?php 
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
//$DataPublic.branchdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增社保资料");//需处理
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
     <table width="543" height="298" border="0" align="center" cellspacing="5">
	<tr>
	  <td width="77" height="-1" align="right" valign="middle" scope="col">加入月份</td>
	  <td width="447" valign="middle" scope="col">
	  <input name="sMonth" type="text" id="sMonth" value="<?php  echo date("Y-m")?>" size="34" maxlength="7" dataType="Month" msg="月份格式不对"></td>	  
	  </tr>
	<tr>
	  <td width="77" height="3" align="right" valign="middle" scope="col">社保类型</td>
	  <td valign="middle" scope="col"><select name="Type" id="Type" style="width:200px" dataType="Require"  msg="未选择">
        <option value="" selected>请选择</option>
        <?php 
		$tResult=mysql_query("SELECT Id,Name FROM $DataPublic.rs_sbtype WHERE Id<4 ORDER BY Id",$link_id);
		if($tRow = mysql_fetch_array($tResult)) {
			do{
				$tId=$tRow["Id"];
				$tName=$tRow["Name"];
				echo "<option value='$tId'>$tName</option>";
				}while($tRow = mysql_fetch_array($tResult));
			}
		?>
      </select></td>
	  </tr>
	<tr>
	  <td height="6" align="right" valign="middle" scope="col">指定部门
          </td>
	  <td height="6" valign="middle" scope="col"><select name="BranchId" id="BranchId" style="width:200px">
      <option value="" selected>全部</option>
      <?php 
		$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
							   WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
			if($B_Row = mysql_fetch_array($B_Result)) {
				do{
					$B_Id=$B_Row["Id"];
					$B_Name=$B_Row["Name"];
					echo "<option value='$B_Id'>$B_Name</option>";
					}while ($B_Row = mysql_fetch_array($B_Result));
				}
			?>
    </select>		
		</td></tr>
	<tr>
	  <td align="right" valign="top" scope="col">指定员工</td>
	  <td valign="middle" scope="col">
	    <select name="ListId[]" size="18" id="ListId" multiple style="width: 200px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,0)" dataType="PreTerm" Msg="没有指定员工" readonly>
        </select>
	    </td>
	</tr>
<tr>
	  <td width="77" height="-1" align="right" valign="middle" scope="col">备注</td>
	  <td width="447" valign="middle" scope="col">
	  <input name="Note" type="text" id="Note" size="60" dataType="LimitB" min="3" max="50"  msg="必须在2-50个字节之内" title="必填项,2-50个字节内">
	  </tr>    
	</table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>