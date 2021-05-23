<?php 
//电信-EWEN
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增其他收入名称");//需处理
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
      <table width="750" border="0" align="center" cellspacing="5">		
      	<tr>
      		<td scope="col" width="150" height="60" align="right">收入名称</td>
      		<td><input name="Name" type="text" id="Name" style="width: 380px;" dataType="Require" msg="未填写"/></td>
      	</tr>
		<tr>
            <td scope="col" width="150" height="60" align="right">对应行政费用</td>
            <td>
            	<?php 
            	include "../model/subselect/HzType.php";
            	?>
			</td>
		</tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>