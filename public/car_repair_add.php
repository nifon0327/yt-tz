<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增维修记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
     <td class='A0011'>
	  <table width="700" border="0" align="center" cellspacing="0">
	  
	
      <tr>
        <td align="right">维修车辆</td>
        <td><select name="CarId" id="CarId" style="width:380px" dataType="Require"  msg="未选择">
          <option value="" selected>请选择</option>
           <?php 
          $CarSql=mysql_query("SELECT DISTINCT TypeId,Id,CarNo,cSign FROM $DataPublic.cardata WHERE Estate=1 ORDER BY cSign,TypeId,CarNo",$link_id);
		    if($CarRow=mysql_fetch_array($CarSql)){
			do{
				$Id=$CarRow["Id"];
			   	$CarNo = $CarRow["CarNo"];
			   	$cSignFrom=$CarRow["cSign"];
				require"../model/subselect/cSign.php";
				$TypeFrom=$CarRow["TypeId"];
				require "../model/subselect/CarType.php";
			   echo "<option value='$Id'>$cSign $TypeName $CarNo</option>";
				
			   }while($CarRow=mysql_fetch_array($CarSql));
			}
		  ?></select>
		  </td>  
        </tr>
		
	  	 <tr>
        <td width="101" align="right">维修人</td>
        <td><input name="Reperson" type="text" id="Reperson" style="width:380px" maxlength="10" dataType="Require"  msg="未填写"></td>
         </tr>	  
	  
	  	 <tr>
        <td width="101" align="right">维修费用</td>
        <td><input name="Recharge" type="text" id="Recharge" style="width:380px" maxlength="10" dataType="Currency"  msg="未填写"></td>
         </tr>	  
	  
	  	 <tr>
        <td width="101" align="right">维修时间</td>
        <td><input name="Redate" type="text" id="Redate" style="width:380px" value="<?php  echo Date("Y-m-d")?>" onfocus="WdatePicker()" readonly></td>
         </tr>	  
		 
	   <tr>
        <td width="101" align="right">原因</td>
        <td><textarea name="Rereason" style="width:380px" rows="3" id="Rereason" dataType="Require"  msg="未填写"></textarea></td>
         </tr>	
	  
	 </table>
    </td>
  </tr>
</table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>