<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 摄像头信息更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.ot2_cam WHERE Id='$Id'",$link_id));
$Floor=$upData["Floor"];
$Info=$upData["Info"];
$Name=$upData["Name"];
$IP=$upData["IP"];
$OutIP=$upData["OutIP"];
$Port=$upData["Port"];
$Params=$upData["Params"];
$Order=$upData["Order"];
$From=$upData["From"];

$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">

      <tr>
        <td width="201" align="right">所在公司</td>
        <td><select name="cFrom" id="cFrom" style="width:285px" >
           <?php 
          $fromSql=mysql_query("select DISTINCT C.From FROM $DataPublic.ot2_cam C  where 1",$link_id);
		    if($fromRow=mysql_fetch_array($fromSql)){
			do{
			     $cFrom=$fromRow["From"];
			   if($cFrom==$From)
			     {
			       echo "<option value='$From' style='font-weight: bold' selected>$From</option>";
				 }
				else
				 {
				  echo"<option value='$cFrom' style='font-weight: bold'>$cFrom</option>";
				 }
			   }while($fromRow=mysql_fetch_array($fromSql));
			}
		  ?>
		  </select>
         </td>
       </tr>
	   
	   <tr>
        <td width="201" align="right">楼层</td>
        <td><input name="Floor" type="text" id="Floor" value="<?php  echo $Floor?>" size="50" maxlength="10"></td>
       </tr>
	  
	   <tr>
        <td width="201" align="right">摄像头位置</td>
        <td><input name="Info" type="text" id="Info" value="<?php  echo $Info?>" size="50" maxlength="20"></td>
       </tr>
	  
	   <tr>
        <td width="201" align="right">摄像头名字</td>
        <td><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" size="50" maxlength="100"></td>
       </tr>
	  
	  <tr>
        <td width="201" align="right">IP</td>
        <td><input name="IP" type="text" id="IP" value="<?php  echo $IP?>" size="50" maxlength="20">        </td>
      </tr>

  <tr>
        <td width="201" align="right">OutIP</td>
        <td><input name="OutIP" type="text" id="OutIP" value="<?php  echo $OutIP?>" size="50" maxlength="30">        </td>
      </tr>
      	  
	   <tr>
        <td width="201" align="right">端口号</td>
        <td><input name="Port" type="text" id="Port" value="<?php  echo $Port?>" size="50" maxlength="10"></td>
      </tr>
  
   <tr>
        <td width="201" align="right">连接参数</td>
        <td><input name="Params" type="text" id="Params" value="<?php  echo $Params?>" size="100" maxlength="100">        </td>
      </tr>
          
	   <tr>
        <td width="201" align="right">排序</td>
        <td><input name="cOrder" type="text" id="cOrder" value="<?php  echo $Order?>" size="50" maxlength="10"></td>
      </tr>	  
	  
 </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>