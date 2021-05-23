<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增摄像头记录");//需处理
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
	  <table width="700" border="0" align="center" cellspacing="0">
		
		 <tr>
        <td width="201" align="right">楼层</td>
        <td><input name="Floor" type="text" id="Floor" size="50"  dataType="Require"  msg="未填写"></td>
         </tr>
		
		<tr>
        <td width="201" align="right">摄像头位置</td>
        <td><input name="Info" type="text" id="Info" size="50" dataType="Require"  msg="未填写" ></td>
         </tr>

         <td  width="201" align="right">摄像头名字</td>
         <td><input name="Name" type="text" id="Name" size="50" dataType="Require"  msg="未填写">
         </td>
         </tr>
		 
		 <tr>
         <td width="201" align="right">IP</td>
         <td><input name="IP" type="text" id="IP" size="50" dataType="Require"  msg="未填写"></td>
         </tr>
		  <tr>
         <td width="201" align="right">OutIP</td>
         <td><input name="OutIP" type="text" id="OutIP" size="50" dataType="Require"  msg="未填写"></td>
         </tr>
		 <tr>
         <td width="201" align="right">端口号</td>
         <td><input name="Port" type="text" id="Port" size="50" dataType="Require"  msg="未填写"></td>
         </tr>
         <tr>
         <td width="201" align="right">连接参数</td>
         <td><input name="Params" type="text" id="Params" size="80" dataType="Require"  msg="未填写"></td>
         </tr>
         <tr>
         <td width="201" align="right">所在公司</td>
         <td><input name="cFrom" type="text" id="cFrom" size="50" dataType="Require"  msg="未填写"></td>
         </tr>
		 <tr>
		 <td width="201" align="right"></td>
		 <td><span style="color:#FF0000" >所在公司填mc,47,48,cf,bsd</span></td>
		 </tr>
     </table>
    </td>
  </tr>
</table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>