<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车辆违规记录更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.car_violation WHERE Id='$Id'",$link_id));
$CarId=$upData["CarId"];
$Person=$upData["Person"];
$Charge=$upData["Charge"];
$Date=$upData["Date"];
$Reason=$upData["Reason"];

$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">
	
	
		   <tr>
        <td width="101" align="right">违规车辆</td>
        <td><select name="CarId" id="CarId" style="width:380px" dataType="Require"  msg="未选择">
          <option value="" selected>请选择</option>
           <?php 
          $CarSql=mysql_query("SELECT DISTINCT TypeId,Id,CarNo,cSign FROM $DataPublic.cardata WHERE Estate=1 ORDER BY cSign,TypeId,CarNo",$link_id);
		    if($CarRow=mysql_fetch_array($CarSql)){
			do{
				$theId=$CarRow["Id"];
			   	$CarNo = $CarRow["CarNo"];
			   	$cSignFrom=$CarRow["cSign"];
				require"../model/subselect/cSign.php";
				$TypeFrom=$CarRow["TypeId"];
				require "../model/subselect/CarType.php";
				if($theId==$CarId){
					echo "<option value='$theId' selected>$cSign $TypeName $CarNo</option>";
					}
				else{
			   		echo "<option value='$theId'>$cSign $TypeName $CarNo</option>";
					}
				
			   }while($CarRow=mysql_fetch_array($CarSql));
			}
		  ?></select></td>
       </tr>
	   	   <tr>
        <td width="101" align="right">违规人</td>
        <td><input name="Person" type="text" id="Person" value="<?php  echo $Person?>" style="width:380px" maxlength="10" dataType="Require"  msg="未填写"></td>
       </tr>
	   	   <tr>
        <td width="101" align="right">违规费用</td>
        <td><input name="Charge" type="text" id="Charge" value="<?php  echo $Charge?>" style="width:380px" dataType="Currency"  msg="未填写或格式不对"></td>
       </tr>
	   	   <tr>
        <td width="101" align="right">违规时间</td>
        <td><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" style="width:380px" value="<?php  echo $Date?>" onfocus="WdatePicker()"  maxlength="10" dataType="Require" msg="未填写或格式不对" readonly></td>
       </tr>
		</tr>
	   	   <tr>
        <td width="101" align="right">原因</td>
        <td><input name="Reason" type="text" id="Reason" value="<?php  echo $Reason?>" style="width:380px"  dataType="Require"  msg="未填写"></td>
       </tr>
 </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>