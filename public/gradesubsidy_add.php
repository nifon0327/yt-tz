<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增等级津贴");//需处理
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
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
    <tr>
    	<td width="150" height="35"class='A0010' align="right">等&nbsp;&nbsp;&nbsp;&nbsp;级：
      </td>
	    <td valign="middle" class='A0001'>
			<select name="Grade" id="Grade" style="width:380px;" dataType="Require" msg="未选择">
			<?php 
			for($i=1;$i<=30;$i++){
				$Result = mysql_query("SELECT Id FROM $DataPublic.gradesubsidy WHERE Grade=$i order by Grade",$link_id);
				if(!$myrow = mysql_fetch_array($Result)){
					echo "<option value='$i'>$i</option>";}				
				}
			?>		 
		  </select>
		</td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">津&nbsp;&nbsp;&nbsp;&nbsp;贴：</td>
	    <td valign="middle" class='A0001'><input name="Subsidy" type="text" id="Subsidy" title="必选项,数值范围0-5000." style="width:380px;" maxlength="4" dataType="Range" msg="等级范围必须在0~5000之间" min="-1" max="5001"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>