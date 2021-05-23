<?php 
//步骤1 $DataPublic.zw2_hzdoctype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新行政资料分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,SubName,Remark FROM $DataPublic.zw2_hzdoctype WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$SubName=$upData["SubName"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
		  	<td width="100" height="13" align="right" scope="col">分类名称</td>
		  	<td scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" size="38" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围">
          <!--  <select  style="width:175"  onChange="document.getElementById('Name').value=value">--> 
			<?php 
			/*$checktype ="SELECT  distinct P.Name FROM $DataPublic.zw2_hzdoctype P where  P.Estate>0  ORDER BY P.Name ";
			$typeResult = mysql_query($checktype); 
			while ( $typeRow = mysql_fetch_array($typeResult)){
				 $PName=$typeRow["Name"];					
				echo "<option value='$PName'>$PName</option>";
				} */
			?>		 
			<!--</select> -->           
            
            </td>
		</tr>
        <!-- <tr>
            <td width="100" height="31" align="right" scope="col">二级名称</td>
            <td scope="col"><input name="SubName" type="text" id="SubName"   value="<?php  echo $SubName?>" size="74" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td>

           </tr>-->
                   
        <tr>
            <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="48" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
        </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>