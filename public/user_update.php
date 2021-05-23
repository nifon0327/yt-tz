<?php 
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新用户帐户资料");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$checkUserType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.usertype WHERE Id='$uType' LIMIT 1",$link_id));
$UserType=$checkUserType["Name"];
switch($uType){
	case 1://员工
	$upRow = mysql_fetch_array(mysql_query("
			SELECT A.uName,A.uSeal,A.Number,A.WebStyle,A.uSign,B.Name,C.CShortName AS Forshort,A.roleId
			FROM $DataIn.usertable A 
			LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
			LEFT JOIN $DataPublic.companys_group C ON C.cSign=B.cSign
			WHERE A.Id='$Id'",$link_id));
	break;
	case 2://客户
	$upRow = mysql_fetch_array(mysql_query("
			SELECT A.uName,A.uSeal,A.Number,A.WebStyle,A.uSign,B.Name,C.Forshort ,A.roleId
			FROM $DataIn.usertable A 
			LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
			WHERE A.Id='$Id'",$link_id));
	break;
	case 3://供应商
	$upRow = mysql_fetch_array(mysql_query("
			SELECT A.uName,A.uSeal,A.Number,A.WebStyle,A.uSign,B.Name,A.roleId 
			FROM $DataIn.usertable A 
			LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
			WHERE A.Id='$Id'",$link_id));
	break;
	case 4://外部人员
	$upRow=mysql_fetch_array(mysql_query("SELECT A.Id,A.uName,A.uPwd,A.Number,A.lDate,A.Estate,A.Locks,A.uSign,B.Name,B.Forshort,A.roleId 
			FROM $DataIn.UserTable A 
			LEFT JOIN $DataIn.ot_staff B ON B.Number=A.Number
			WHERE A.Id='$Id'",$link_id));
	break;
	case 5://参观人员
	break;
	}
$Forshort=$upRow["Forshort"];
$Number=$upRow["Number"];
$Name=$upRow["Name"];
$uName=$upRow["uName"];
$uSeal=$upRow["uSeal"];
$uSign=$upRow["uSign"];
//$thisroleId=$upRow["roleId"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Number,$Number,uType,$uType,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
	<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="595"  border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td width="100"  align="right">用户类型</td>
            <td><?php  echo $UserType?></td>
          </tr>
          <tr>
            <td align="right">公司名称</td>
            <td><?php  echo $Forshort?></td>
          </tr>
          <tr>
            <td align="right">用户姓名</td>
            <td><?php  echo $Name?></td>
          </tr>
          <tr>
            <td align="right">新登录名</td>
            <td><input name="uName" type="text" id="uName" style="width:380px;" maxlength="16" value="<?php  echo $uName?>" title="必选项,英文、下划线、数字的组合字串" DataType="Username" Msg="不符合规定"></td>
          </tr>
          <tr>
            <td align="right">登录密码</td>
            <td><input name="uPwd" type="password" id="uPwd" style="width:380px;" maxlength="16" title="必选项,字母、数字或符合的混合字符串" DataType="Require" msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">确认密码</td>
            <td><input name="Repassword" type="password" id="Repassword" style="width:380px;" maxlength="16" title="必选项" DataType="Repeat" to="uPwd" msg="两次输入的密码不一致"></td>
          </tr>
          <tr>
            <td align="right">印章图片</td>
            <td><input name="Attached" type="file" id="Attached" style="width:380px;" DataType="Filter" msg="请选择gif图片" accept="gif" Row="5" Cel="1"></td>
          </tr>
		 <?php 
		 if($uSeal==1){
		 	$FileName="u".$Number.".gif";
		 	echo"<tr><td height='4'>&nbsp;</td><td>
				<input name='oldAttached' type='checkbox' id='oldAttached' value='$FileName'><LABEL for='oldAttached'>删除已传印章</LABEL>
				</td>
         		</tr>";
			}
		 if($uSign==1){
		 	$upRowSTR="checked";
			}
		  ?>
          <tr>
            <td><div align="right"><input name="uSign" type="checkbox" id="uSign" value="1" <?php  echo $upRowSTR?>></div>
			</td>
            <td>用户可以更改密码</td>
          </tr>
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>