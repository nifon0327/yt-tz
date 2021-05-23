<?php 
//ewen 1012-12-16
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新申购记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT 
A.Date,A.Purchaser,A.Qty,A.Remark,B.TypeId,B.GoodsName
 FROM $DataIn.zwwp4_purchase A 
 LEFT JOIN $DataPublic.zwwp3_data B ON B.Id=A.GoodsId 
 WHERE A.Id='$Id' LIMIT 1",$link_id));
$Date=$upData["Date"];
$Purchaser=$upData["Purchaser"];
$TypeName=$upData["TypeName"];
$Qty=$upData["Qty"];
$Remark=$upData["Remark"];
$TypeId=$upData["TypeId"];
$GoodsName=$upData["GoodsName"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$checkStaff =mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Purchaser' AND Estate=1 ORDER BY BranchId,JobId,Number",$link_id));
$PName=$checkStaff["Name"];
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
	  <tr>
        <td height="22" align="right">申购日期</td>
        <td width="612"><input name="Date" type="text" id="Date" style="width: 480px;" value="<?php  echo $Date;?>" onfocus="WdatePicker()" readonly dataType="Date" msg="未填写"></td>
	    </tr>
	  <tr>
        <td align="right">申 购 人</td>
        <td><?php echo $PName?></td>
	    </tr>
         <tr>
           <td align="right">物品类别</td>
           <td>
            <?php 
			$checkType =mysql_fetch_array(mysql_query("SELECT TypeName FROM $DataPublic.zwwp2_subtype  WHERE Id='$TypeId'",$link_id));
			echo $checkType["TypeName"];
			?>
             </select>
          </td>
         </tr>
	<tr>
        <td align="right">物品名称</td>
        <td><?php echo $GoodsName;?></td>
	</tr>
        <tr>
          <td align="right">申购数量</td>
          <td><input name="Qty" type="text" id="Qty" style="width: 480px;"  dataType="Number" value="<?php echo $Qty;?>" msg="错误的数量"></td>
        </tr>        
        <tr>
            <td align="right" valign="top">申购说明</td>
            <td><textarea name="Remark" style="width: 480px;" rows="4" id="Remark" dataType="Require" msg="未填写"><?php echo $Remark;?></textarea></td>
        </tr>
   
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>