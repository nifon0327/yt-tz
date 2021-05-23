<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 损益表子项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.sys8_pandlsheet WHERE 1 AND Id='$Id'",$link_id));
$Mid=$upData["Mid"];
$BigName=$upData["BigName"];
$ItemName=$upData["ItemName"];
$SortId=$upData["SortId"];
$Remark=$upData["Remark"];
$Parameters=$upData["Parameters"];
$AjaxView=$upData["AjaxView"];
$AjaxNo=$upData["AjaxNo"];
$Sign=$upData["Sign"]==1?"checked":"";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">项目类型：</td>
				<td valign="middle" scope="col"><select name="Mid" id="Mid" style="width: 380px;" dataType="Require"  msg="未选择">
				
				<?php 
				$typeResult = mysql_query("SELECT Id,ItemName FROM $DataPublic.sys8_pandlmain WHERE 1 GROUP BY SortId",$link_id);
				if($typeRow = mysql_fetch_array($typeResult)){
				     echo"<option value='' selected>请选择</option>";
					do{
					    $theId=$typeRow["Id"];
						$theItemName=$typeRow["ItemName"];
						if($theId==$Mid){
							echo"<option value='$theId' selected>$theItemName</option>";
							}
						else{
							echo"<option value='$theId'>$theItemName</option>";
							}
						}while ($typeRow = mysql_fetch_array($typeResult));
					}
					
				 ?>
				 </select></td>
			  </tr>
 
	    	  <tr>
                <td align="right" scope="col">项目名称：</td>
                <td valign="middle" scope="col"><input name="ItemName" type="text" id="ItemName" value="<?php  echo $ItemName?>" style="width: 380px;" maxlength="15" DataType="Require" Msg="没有填写"></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">排序数字：</td>
                <td valign="middle" scope="col"><input name="SortId" type="text" id="SortId" value="<?php  echo $SortId?>" style="width: 380px;" maxlength="17" DataType="Number" Msg="没有填写或格式不对"></td>
	    </tr>

			  <tr>
			    <td align="right" scope="col">参&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数：</td>
			    <td valign="middle" scope="col"><input name="Parameters" type="text" id="Parameters" value="<?php  echo $Parameters?>" style="width: 380px;"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">明&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;细：</td>
			    <td valign="middle" scope="col">
			    <select name="AjaxView" id="AjaxView" style="width: 380px;">
			<?php 
			if($AjaxView==1){
				echo"<option value='1' selected>显示</option>";
				echo"<option value='0'>不显示</option>";
				}
			else{
				echo"<option value='1'>显示</option>";
				echo"<option value='0' selected>不显示</option>";
				}
			?>
            </select>
			    </td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">Ajax&nbsp;&nbsp;No：</td>
			    <td valign="middle" scope="col"><input name="AjaxNo" type="text" id="AjaxNo" value="<?php  echo $AjaxNo?>" style="width: 380px;" DataType="Require" Msg="没有填写"></td>
		      </tr>
              <tr>
			<td align="right" valign="middle" scope="col">行政项目：</td>
			<td scope="col"><input name="Sign" type="checkbox" id="Sign" value="1"  <?php echo $Sign; ?>/></td>
		</tr>
			  <tr>
			    <td align="right" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
			    <td valign="middle" scope="col">
			    <textarea name="Remark" style="width: 380px;" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
		      </tr>
			  
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>