<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-zxq 2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增奖金");//需处理
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
	<table width="650" border="0" align="center" cellspacing="0">
      <tr>
        <td align="right">指定部门</td>
        <td><select name="BranchId" id="BranchId" style="width:275px">
          <option value="">全部</option>
          <?php 
			$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
								    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by B.Id",$link_id);
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
						    WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
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
        <td><select name="ListId[]" size="18" multiple id="ListId" style="width: 275px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,2)" datatype="autoList" readonly>
        </select></td>
      </tr>
      <tr>
        <td align="right" valign="top">请款月份</td>
        <td><input name="Month" type="text" id="Month" size="48" maxlength="7" dataType="Require" msg="未填写请款月份"></td>
      </tr>
      <tr>
        <td align="right" valign="top">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="top">计算公式</td>
        <td align="center" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="right" valign="top"><table width="100%" height="32" border="0" cellspacing="0">
          <tr>
            <td align="center" class="A1111">
				<input name="theYear" type="text" id="theYear" size="4" value="<?php  echo date("Y")?>">
				&nbsp;
<select name="ItemName" id="ItemName" title="奖金项目" dataType="Require"  msg="参数错误">
				  <option selected>请选择</option>
				  <option value="端午节奖金">端午节奖金</option>
				  <option value="中秋节奖金">中秋节奖金</option>
				  <option value="年终奖金">年终奖金</option>
				</select> =(
				<input name="MonthS" type="text" id="MonthS" title="计薪起始月" size="10"> 
				~
				<input name="MonthE" type="text" id="MonthE" title="计薪终止月" size="10"> 
				) /
				<input name="Divisor" type="text" id="Divisor" title="除数(月份)" size="10"> 
				*
				<input name="Rate" type="text" id="Rate" title="比率" size="10"> 
				%
              </td>
            </tr>
        </table></td>
        </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>