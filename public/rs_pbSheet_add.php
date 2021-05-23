<?php 
//代码 jobdata by zx 2012-08-13
//电信-ZX  2012-08-01
//步骤1 $DataPublic.jobdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工排班记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=650;$tableMenuS=300;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="640" height="120" border="0" cellspacing="5">
	  <tr>
	    <td height="9" align="right" scope="col">设定班次</td>
	    
	    <td valign="middle" scope="col"><select name="ActionIn" id="ActionIn" style="width:430px">
          <?php 
			$inResult=mysql_query("SELECT Id,Name FROM $DataPublic.pbsheet 
								  WHERE Estate=1",$link_id);
			if($inRow = mysql_fetch_array($inResult)) {
				do{
					$inId=$inRow["Id"];
					$inName=$inRow["Name"];
					echo "<option value='$inId' selected>$inName</option>";
					}while ($inRow = mysql_fetch_array($inResult));
				}
			?>
        </select></td>
	    </tr>
	  <tr>
		<td height="18" align="right" valign="top" scope="col">设定员工</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 430px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,5)" dataType="PreTerm" Msg="没有指定员工" readonly>
		 	</select>
		</td>
	</tr>	
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>