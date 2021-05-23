<?php 
//代码 jobdata by zx 2012-08-13
//电信-ZX  2012-08-01
//步骤1 $DataPublic.jobdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工调职记录");//需处理
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
	<table width="750" height="120" border="0" cellspacing="5">
<tr>
	  <td width="150" height="18" align="right" scope="col">1、起效月份</td>
	  <td  valign="middle" scope="col"><input name="Month" type="text" id="Month" size="79" maxlength="7" dataType="Month" msg="月份格式不对"></td>
	  </tr>
	  <tr>
	 		<td height="4" align="right" scope="col">2、原 职 位</td>
	 		<td valign="middle" scope="col">
			<select name="JobId" id="JobId" style="width:430px" onchange="ClearList('ListId')">
			<?php 
			$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
								    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
			if($outRow = mysql_fetch_array($outResult)) {
				do{
					$outId=$outRow["Id"];
					$outName=$outRow["Name"];
					echo "<option value='$outId' selected>$outName</option>";
					}while ($outRow = mysql_fetch_array($outResult));
				}
			?>			  
            </select>              
		</td>
	  </tr>
	  <tr>
	    <td height="9" align="right" scope="col">3、新 职 位</td>
	    <td valign="middle" scope="col"><select name="ActionIn" id="ActionIn" style="width:430px" datatype="unSame" toid="JobId" msg="原职位与新职位不能相同">
          <?php 
			$inResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
								  WHERE Estate=1   AND (cSign=$Login_cSign OR cSign=0 ) order by Id DESC",$link_id);
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
		<td height="18" align="right" valign="top" scope="col">4、调动员工</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 430px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,5)" dataType="PreTerm" Msg="没有指定员工" readonly>
		 	</select>
		</td>
	</tr>	
	<tr>
	  <td height="18" align="right" valign="top" scope="col">5、调动原因</td>
	  <td valign="middle" scope="col"><textarea name="Remark" cols="51" rows="4" id="Remark"></textarea>
		</td>
	  </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>