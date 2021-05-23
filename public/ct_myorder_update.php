<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新点餐记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.Id,A.MenuId,A.Price,A.Qty,A.Amount,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS MenuName,C.Name AS CTName,D.Name AS MenuType 
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.ct_menu B ON B.Id=A.MenuId
LEFT JOIN $DataPublic.ct_data C ON C.Id=B.CtId
LEFT JOIN $DataPublic.ct_type D ON D.Id=B.mType
WHERE  A.Id=$Id",$link_id));
		$CTName=$upData["CTName"];	
		$MenuId=$upData["MenuId"];
		$MenuType=$upData["MenuType"];	
		$MenuName=$upData["MenuName"];	
		$Price=$upData["Price"];	
		$Qty=$upData["Qty"];	
		$Amount=$upData["Amount"];	
		$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
		  	<td height="13" scope="col" align="right">餐厅名称</td>
		 	<td width="460" scope="col"><input name="CTName" type="text" id="CTName" value="<?php  echo $CTName?>" style="width:180px;" readonly></td>
		</tr> 
		<tr>
            <td height="42" scope="col" align="right">菜式分类</td>
            <td scope="col"><input name="MenuType" type="text" id="MenuType" value="<?php  echo $MenuType?>" style="width:180px;" readonly></td></tr>

<tr>
            <td height="42" scope="col" align="right">菜式名称</td>
            <td scope="col"><input name="MenuName" type="text" id="MenuName" value="<?php  echo $MenuName?>" style="width:180px;" readonly></td></tr>

<tr>
            <td height="42" scope="col" align="right">数量</td>
            <td scope="col"><input name="Qty" type="text" id="Qty" value="<?php  echo $Qty?>" style="width:180px;" ></td></tr>

<tr>
            <td height="42" scope="col" align="right">价格</td>
            <td scope="col"><input name="Price" type="text" id="Price" value="<?php  echo $Price?>" style="width:180px;"></td></tr>

<tr>
            <td height="42" scope="col" align="right">备注</td>
            <td scope="col"><textarea name="Remark" style="width: 380px;" id="Remark"><?php  echo $Remark?>
            </textarea></td></tr>            
            
                        
      </table>
</td></tr></table>
<input name='MenuId' type='hidden' id='MenuId' value=<?php echo $MenuId?> />
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>