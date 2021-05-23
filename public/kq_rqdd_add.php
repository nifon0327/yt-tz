<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增调班记录");//需处理
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
	<table width="600" border="0" align="center" cellspacing="0">
      <tr>
        <td width="104" align="right">原工作日</td>
        <td width="492"><input name="GDate" type="text" id="GDate" size="48" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">原休息日</td>
        <td><input name="XDate" type="text" id="XDate" size="48" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">指定部门</td>
        <td><select name="BranchId" id="BranchId" style="width:275px">
          <option value="">全部</option>
          <?php 
			$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
								    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 )  order by Id",$link_id);
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
        <td align="right">指定职位</td>
        <td><select name="JobId" id="JobId" style="width:275px">
          <option value="">全部</option>
          <?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
						    WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 )  order by Id",$link_id);
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
        <td align="right" valign="top">指定员工</td>
        <td><select name="ListId[]" size="18" multiple id="ListId" style="width: 275px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,3)" datatype="autoList" readonly>
        </select></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>