<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 员工受训记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5"  id="NoteTable">
        <tr>
            <td width="100"  align="right" scope="col">受训计划</td>
            <td scope="col"><select name="ItemId" id="ItemId" style="width:380px" dataType="Require" msg='未选择'>
            <?php
			$checkResult = mysql_query("SELECT A.Id,A.ItemName FROM $DataPublic.aqsc07 A WHERE A.Estate=1 ORDER BY A.Id",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)){
				$i=1;
				do{
					if($ItemId==$checkRow["Id"]){
						echo"<option value='$checkRow[Id]' selected>$i $checkRow[ItemName]</option>";
						}
					else{
						echo"<option value='$checkRow[Id]'>$i $checkRow[ItemName]</option>";
						}
					$i++;
					}while($checkRow = mysql_fetch_array($checkResult));
				}
            ?>
            </select></td>
		</tr>
        <tr>
          <td height="32"  align="right" scope="col">&nbsp;</td>
          <td valign="bottom" scope="col">选定如下受训员工</td>
        </tr>
        <tr>
          <td  align="right" scope="col">考核结果</td>
          <td scope="col"><select name="Exam" id="Exam" style="width:380px">
	<option value="" selected="selected">无</option>
	<option value="优">优</option>
    <option value="良">良</option>
    <option value="及格">及格</option>
    <option value="不及格">不及格</option>
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col">部门</td>
          <td scope="col"><select name="BranchId" id="BranchId" style="width:380px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
						    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col">职位</td>
          <td scope="col"><select name="JobId" id="JobId" style="width:380px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
						     WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col" valign="top">员工</td>
          <td scope="col"><select name="ListId[]" size="18" multiple id="ListId" style="width: 180px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,3)" dataType="autoList" readonly></select></td>
        </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>