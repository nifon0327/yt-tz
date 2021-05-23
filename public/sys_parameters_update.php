<?php 
//步骤1 $DataIn.sys6_parameters 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新系统参数值");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT PNumber,Name,pValue,Remark,ActionId FROM $DataIn.sys6_parameters WHERE Id='$Id'",$link_id));
$PNumber=$upData["PNumber"];
$pValue=$upData["pValue"];
$Remark=$upData["Remark"];
$Name  =$upData["Name"];
$ActionId=$upData["ActionId"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
	  <tr>
        <td height="13" align="right" scope="col">参 数 ID</td>
        <td scope="col"><?php  echo $PNumber?></td>
	    </tr>
	    <tr>
            <td width="100"  align="right" scope="col">参数名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" value="<?php echo $Name?>" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td></tr>
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
			     if($thisActionId==$ActionId){
				     echo "<option value='$thisActionId' selected>$thisActionName</option>";
			     }else{
				     echo "<option value='$thisActionId'>$thisActionName</option>";
			     }
				 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>
		<tr>
		  	<td width="100" height="13" align="right" scope="col">参 数 值</td>
		  	<td scope="col"><input name="pValue" type="text" id="pValue" value="<?php  echo $pValue?>" style="width: 380px;"  dataType="Double" Msg="未填写或格式不对">
		  	<input type="hidden" id="OldpValue" name="OldpValue" value="<?php  echo $pValue?>">
		  	<input type="hidden" id="OldActionId" name="OldActionId" value="<?php  echo $ActionId?>">
		  	</td>
		</tr>
        <tr>
            <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="50" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
        </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>