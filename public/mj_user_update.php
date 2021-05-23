<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新门禁用户");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.chkType,B.Name FROM $DataPublic.accessguard_user A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Id='$Id'",$link_id));
$chkType=$upData["chkType"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="595" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td align="right">用户姓名</td>
            <td><?php  echo $Name?></td>
          </tr>
           <tr>
            <td align="right">校验方式</td>
            <td><select name="chkType" type="text" id="chkType" style="width:380px" maxlength="16" DataType="Require" Msg="没有选择">
            <?php 
            $checkSql=mysql_query("SELECT * FROM $DataPublic.accessguard_chktype WHERE Estate=1 ORDER BY Id",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				do{
					if($checkRow["Id"]==$chkType){
						echo"<option value='$checkRow[Id]' selected>$checkRow[TypeName]</option>";
						}
					else{
						echo"<option value='$checkRow[Id]'>$checkRow[TypeName]</option>";
						}
					}while($checkRow=mysql_fetch_array($checkSql));
				}
			?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">登录密码</td>
            <td><input name="Password" type="password" id="Password" style="width:380px" maxlength="16"></td>
          </tr>
          <tr>
            <td align="right">确认密码</td>
            <td><input name="Repassword" type="password" id="Repassword" style="width:380px" maxlength="16"></td>
          </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>