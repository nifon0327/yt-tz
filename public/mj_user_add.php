<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增用户");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,pNumber,,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
include "../model/livesearch/modellivesearch.php"
?>
	<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="595" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td align="right">用户姓名</td>
            <td><input name="Name" type="text" id="Name" style="width:380px" maxlength="16" Onkeyup="showResult(this.value,'Name','staffmain')" Onblur="LoseFocus()" title="必选项，点击在查询窗口选取" DataType="Require" Msg="没有选取用户姓名"></td>
          </tr>
           <tr>
            <td align="right">校验方式</td>
            <td><select name="chkType" type="text" id="chkType" style="width:380px" maxlength="16" DataType="Require" Msg="没有选择">
            <option value="">请选择</option>
            <?php 
            $checkSql=mysql_query("SELECT * FROM $DataPublic.accessguard_chktype WHERE Estate=1 ORDER BY Id",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				do{
					echo"<option value='$checkRow[Id]'>$checkRow[TypeName]</option>";
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
include "../model/subprogram/add_model_b.php";
?>