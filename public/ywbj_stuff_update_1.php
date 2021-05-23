<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新报价配件资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.ywbj_stuffdata S WHERE S.Id='$Id' LIMIT 1",$link_id));
$Name=$upData["Name"];
$Price=$upData["Price"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="103" height="57" align="right" scope="col">配件名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" size="89" dataType="LimitB" min="3" max="50"  msg="必须在2-50个字节之内" title="必填项,2-50个字节内"></td>
          </tr>
          <tr>
            <td height="52" align="right">参考买价</td>
            <td><input name="Price" type="text" id="Price" value="<?php  echo $Price?>" size="89" dataType="Currency" msg="错误的价格"></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>