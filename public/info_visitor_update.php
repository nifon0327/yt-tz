<?
/*
$DataIn`.`net_cpdata`
`$DataIn`.`staffmain`
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 来访登记");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM `$DataPublic`.`come_data` WHERE 1 AND `Id`='$Id'",$link_id));
$ComeDate=$upData["ComeDate"];
$Name=$upData["Name"];
$TypeId=$upData["TypeId"];
$Persons=$upData["Persons"];
$InTime=$upData["InTime"];
$OutTime=$upData["OutTime"];
$Estate=$upData["Estate"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?=$tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">来访日期：</td>
                <td><input name="ComeDate" type="text" id="ComeDate" value="<?=$ComeDate?>" style="width:380px"  maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="未填写或来访日期不正确" readonly></td>
              </tr>
              <tr>
			<td height="40" align="right" valign="middle" scope="col">来访分类：</td>
			<td scope="col">
              <select name="TypeId" id="TypeId" style="width:380px" dataType="Require" msg="未选择">
                <?php 
					 $type_Result = mysql_query("SELECT C.Id,C.Name AS TypeName FROM $DataPublic.come_type C WHERE C.Estate=1",$link_id);
						if($typeRow = mysql_fetch_array($type_Result)) {
							do{			
								$theTypeId=$typeRow["Id"];
								$TypeName=$typeRow["TypeName"];
								if ($theTypeId==$TypeId){
									echo"<option value='$theTypeId'>$TypeName</option>";	
								}
								else{
									echo"<option value='$theTypeId'>$TypeName</option>";	
								}		
								}while($typeRow = mysql_fetch_array($type_Result));
							}
					  ?>
              </select>
			</td>
		 </tr> 

	    	  <tr>
                <td align="right" scope="col">来访单位：</td>
                <td valign="middle" scope="col"><input name="Name" type="text" id="Name" value="<?=$Name?>"  style="width:380px" DataType="Require" Msg="没有填写"></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">来访人数：</td>
                <td valign="middle" scope="col"><input name="Persons" type="text" id="Persons" value="<?=$Persons?>" style="width:380px"   DataType="Require" Msg="没有填写"></td>
	    </tr>
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">来访说明：</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="6" id="Remark"><?=$Remark?></textarea></td>
		</tr>	
		  <tr>
                <td align="right" scope="col">状    态：</td>
                <td valign="middle" scope="col">
	                <select name="Estate" id="Estate" style="width:380px" dataType="Require" msg="未选择" onchange="EstateChange()">
                <?php
                    if ($Estate==1){
	                       echo"<option value='1' selected>未到访</option>";	
	                       echo"<option value='2'>来访中</option>";	
                    }
                    else{
	                     if ($Estate==2){
	                          echo"<option value='2' selected>来访中</option>";	
	                          echo"<option value='0'>已来访</option>";	
	                     }
	                     else{
	                         echo"<option value='0' selected>已来访</option>";	
	                     }
                    }
				?>
              </select>
                </td>
	    </tr>
	     <tr>
                <td align="right" scope="col">来访起始时间：</td>
                <td valign="middle" scope="col"><input name="InTime" type="text" id="InTime" value="<?=$InTime?>" style="width:380px"   onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" dataType="Require"  msg="未填写或起始时间不正确" readonly /></td>
	    </tr>
	    <tr>
                <td align="right" scope="col">来访结束时间：</td>
                <td valign="middle" scope="col"><input name="OutTime" type="text" id="OutTime" value="<?=$OutTime?>" style="width:380px"   onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" dataType="Require"  msg="未填写或结束时间不正确" readonly/></td>
	    </tr>      			  
	  </table>
</td></tr></table>
<?
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
EstateChange();
function EstateChange()
{
   var Estate=document.getElementById("Estate").value;
   if (Estate==2) {
	    document.getElementById("InTime").disabled="";
	    document.getElementById("OutTime").disabled="disabled";
   }
   else{
	    if (Estate==0) {
	      document.getElementById("InTime").disabled="";
	      document.getElementById("OutTime").disabled="";
        }
        else{
           document.getElementById("InTime").disabled="disabled";
           document.getElementById("OutTime").disabled="disabled";
        }
     }
}
</script>