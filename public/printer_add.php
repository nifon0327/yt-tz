<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增标签打印机记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=750;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
     <td class='A0011'>
	  <table width="700" border="0" align="center" cellspacing="5">
		<tr>
		     <td scope="col" align="right">公司地点</td>
		     <td><?php 
             include "../model/subselect/WorkAdd.php";
			 ?></td>
        </tr>
		 <tr>
        <td scope="col" align="right">楼层</td>
        <td scope="col"><input name="Floor" type="text" id="Floor" style="width:380px"  dataType="Require"  msg="未填写"></td>
         </tr>
         
          <tr>
         <td  scope="col" align="right">设备编号</td>
         <td scope="col"><input name="Identifier" type="text" id="Identifier" style="width:380px" dataType="Require"  msg="未填写">
         </td>
         </tr>
         
		 <tr>
         <td  scope="col" align="right">打印机名称</td>
         <td scope="col"><input name="Name" type="text" id="Name" style="width:380px" dataType="Require"  msg="未填写">
         </td>
         </tr>
		 
		 <tr>
         <td scope="col" align="right">IP</td>
         <td scope="col"><input name="IP" type="text" id="IP" style="width:380px" dataType="Require"  msg="未填写"></td>
         </tr>
		 <tr>
         <td scope="col" align="right">端口号</td>
         <td scope="col"><input name="Port" type="text" id="Port" style="width:380px" dataType="Require" value='0' msg="未填写"></td>
         </tr>
     </table>
    </td>
  </tr>
</table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>