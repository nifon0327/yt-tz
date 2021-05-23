<?php 
//电信-EWEN 2012-11-23
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增总务用品分类");//需处理
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
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
        	<tr>
            	<td width="150" height="40" align="right" scope="col">所属主类</td>
                <td>
                <?php 
                include "../model/subselect/zwwp_mType.php";
				?>
                </td>
			</tr>
            <tr>
            	<td width="150" height="40" align="right">分类名称</td>
                <td><input name="Name" type="text" id="Name" style="width: 380px;" maxlength="16" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
            </tr>
            
            <tr>
              <td height="40" align="right">分类说明</td>
              <td><input name="Remark" type="text" id="Remark" style="width: 380px;"  Msg="未填写或格式不对"></td>
            </tr>
          </table>
    </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>