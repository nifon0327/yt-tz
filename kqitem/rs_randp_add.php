<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工奖惩记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">    
<tr>
    	<td width="150" height="25" align="right" valign="middle" class='A0010'><p>部&nbsp;&nbsp;&nbsp;&nbsp;门：<br> 
      </td>
	    <td valign="middle" class='A0001'>
		  <select name="BranchId" id="BranchId" style="width:420px">
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
      <td height="25" align="right" valign="middle" class='A0010'>职&nbsp;&nbsp;&nbsp;&nbsp;位：</td>
      <td valign="middle" class='A0001'><select name="JobId" id="JobId" style="width:420px">
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
      <td align="right" valign="top" class='A0010'>指定员工：</td>
      <td valign="middle" class='A0001'><select name="ListId[]" size="8" multiple id="ListId" style="width: 420px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,2)" dataType="PreTerm" Msg="未指定员工" readonly></select></td>
    </tr>    <tr>
    	<td height="25" valign="middle" class='A0010' align="right">日&nbsp;&nbsp;&nbsp;&nbsp;期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="77" maxlength="10" value="<?php  echo date("Y-m-d")?>" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
    </tr>
    <tr>
      <td height="25" align="right" valign="middle" class='A0010'>金&nbsp;&nbsp;&nbsp;&nbsp;额：</td>
      <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="77" maxlength="6" dataType="Currency" Msg="未填写或格式不对"></td>
    </tr>
    <tr>
      <td height="25" align="right" valign="middle" class='A0010'>奖惩类别：</td>
      <td valign="middle" class='A0001'><select name="Type" id="Type" style="width:420px" dataType="Require"  msg="未选择类别">
        <option selected>请选择</option>
        <option value="1">奖励</option>
        <option value="-1">惩罚</option>
      </select></td>
    </tr>
    <tr>
      <td valign="top" class='A0010' align="right">奖惩内容：</td>
      <td valign="top" class='A0001'><textarea name="Content" cols="50" rows="2" id="Content" dataType="Require" Msg="未填写"></textarea></td>
    </tr>
    <tr>
      <td valign="middle" class='A0010'>&nbsp;</td>
      <td valign="middle" class='A0001'><p>注：<br>
        &nbsp;&nbsp;&nbsp;&nbsp;1、需指定员工<br>
        &nbsp;&nbsp;&nbsp;&nbsp;2、奖惩金额与当月工资一起结算
      </p>
      </td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>