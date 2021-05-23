<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 显示屏信息更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.ot2_display WHERE Id='$Id'",$link_id));
$Floor=$upData["Floor"];
$WorkAdd=$upData["WorkAdd"];
$Name=$upData["Name"];
$IP=$upData["IP"];
$Port=$upData["Port"];
$Url=$upData["Url"];
$Identifier=$upData["Identifier"];

$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">

      <tr>
        <tr>
		     <td align="right">工作地点</td>
		     <td><?php 
             include "../model/subselect/WorkAdd.php";
			 ?></td>
        </tr>
	   <tr>
        <td scope="col" align="right">楼层</td>
        <td scope="col"><input name="Floor" type="text" id="Floor" style="width:380px"  value="<?php  echo $Floor?>" dataType="Require"  msg="未填写"></td>
         </tr>
         
           <tr>
         <td  scope="col" align="right">设备编号</td>
         <td scope="col"><input name="Identifier" type="text" id="Identifier" style="width:380px" value="<?php  echo $Identifier?>"  dataType="Require"  msg="未填写">
         </td>
         </tr>
         
		 <tr>
         <td  scope="col" align="right">显示屏名称</td>
         <td scope="col"><input name="Name" type="text" id="Name" style="width:380px" value="<?php  echo $Name?>" dataType="Require"  msg="未填写">
         </td>
         </tr>
		 
		 <tr>
         <td scope="col" align="right">IP</td>
         <td scope="col"><input name="IP" type="text" id="IP" style="width:380px" value="<?php  echo $IP?>" dataType="Require"  msg="未填写"></td>
         </tr>
		 <tr>
         <td scope="col" align="right">端口号</td>
         <td scope="col"><input name="Port" type="text" id="Port" style="width:380px" value="<?php  echo $Port?>" dataType="Require"  msg="未填写"></td>
         </tr>
         <tr>
         <td scope="col" align="right">URL</td>
         <td scope="col"><input name="Url" type="text" id="Url" style="width:380px" value="<?php  echo $Url?>" dataType="Require"  msg="未填写"></td>
         </tr> 
	  
 </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>