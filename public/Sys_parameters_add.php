<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增行政资料分类");//需处理
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
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td width="100" height="31" align="right" scope="col">参数名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td></tr>
            </tr>
		<tr >
            <td  align="right" scope="col">工艺流程</td>
            <td scope="col" ><select name="ActionId" id="ActionId" style="width: 380px;"  dataType="Require"  msg="未选择">
            <option value=''>请选择</option>
              <?php 
	          $mySql="SELECT ActionId,Name FROM $DataPublic.workorderaction WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $thisActionId=$myrow["ActionId"];
			     $thisActionName=$myrow["Name"];
				 echo "<option value='$thisActionId'>$thisActionName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>
		<tr>
            <td align="right" valign="top">参数值</td>
            <td><input id="pValue" name="pValue" value="0.00" type="text" style="width: 380px;" dataType="Double" Msg="未填写或格式不对"></td>
          </tr>
		
          <tr>
            <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px;" rows="6" id="Remark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>