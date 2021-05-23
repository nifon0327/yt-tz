<?php 
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
//步骤1 $DataPublic.branchdata/$DataPublic.cw3_basevalue 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增社保缴费记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
//$PassAction=$PassAction==""?6:$PassAction;
if($TypeId==1){
         $TypeId1="selected";$TypeId2="";
          $PassAction=6;
        }
elseif($TypeId==2){
                    $TypeId2="selected";$TypeId1="";
                    $PassAction=11;
            }
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="543" height="298" border="0" align="center" cellspacing="5">
	<tr>
	  <td width="77" height="7" align="right" valign="middle" scope="col">缴费类型</td>
	  <td width="447" valign="middle" scope="col"><input type="hidden" id="PassAction" name="PassAction" value="<?php echo $PassAction?>">
       <select id="TypeId" name="TypeId" style="width:200px" onchange="changeType()" dataType="Require" msg="未选择分类">
      <option value="">请选择</option>
      <option value="1" <?php echo $TypeId1?>>社保</option>
      <option value="2" <?php echo $TypeId2?>>公积金</option>
      </select></td>	  
	  </tr>
	<tr>
	<tr>
	  <td width="77" height="7" align="right" valign="middle" scope="col">缴费月份</td>
	  <td width="447" valign="middle" scope="col">
	  <input name="newMonth" type="text" id="newMonth" value="<?php  echo date("Y-m")?>" size="34" onchange="ClearList('ListId')" maxlength="7" dataType="Month" msg="月份格式不对"></td>	  
	  </tr>
	<tr>
	  <td height="6" align="right" valign="middle" scope="col">指定部门
          </td>
	  <td height="6" valign="middle" scope="col"><select name="BranchId" id="BranchId" style="width:200px">
      <option value="" selected>全部</option>
      <?php 
		$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
							    WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
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
	  <td valign="middle" scope="col"><p>
	    <select name="ListId[]" size="10" id="ListId" multiple style="width: 200px;" datatype="autoList"   onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,<?php echo $PassAction?>)" readonly>
        </select>
	  </p>
	    </td>
	</tr>
	<tr>
	  <td align="right" valign="top" scope="col">&nbsp;</td>
	  <td valign="middle" scope="col"><div class="redB">提示没有指定员工且指定部门为全部时，则加全部社保公积金有效员工的缴费记录</div></td>
	  </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function changeType(){
     var TypeId=document.getElementById("TypeId").value;
      if(TypeId==2){//公积金
         document.getElementById("PassAction").value=11;
         }
      else{
         document.getElementById("PassAction").value=6;
          }
         document.form1.submit();
}
</script>