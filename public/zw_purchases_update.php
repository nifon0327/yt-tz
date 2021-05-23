<?php 
/*
$DataIn.zw3_purchases
$DataPublic.staffmain
$DataIn.usertable
$DataIn.zw3_purchaset
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新申购记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT 
S.Date,S.TypeId,S.Remark,S.Operator,S.Price,S.Unit,S.Qty,S.Bill,S.WorkAdd,S.cgSign,S.Estate,S.Cid,T.TypeName,T.Attached AS Picture   
 FROM $DataIn.zw3_purchases S 
 LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId 
 WHERE S.Id='$Id' LIMIT 1",$link_id));
$Date=$upData["Date"];
$TypeId=$upData["TypeId"];
$TypeName=$upData["TypeName"];
$Remark=$upData["Remark"];
$BuyerId=$upData["Operator"];
$Qty=$upData["Qty"];
$Unit=$upData["Unit"];
$Cid=$upData["Cid"];
$Price=$upData["Price"];
$Bill=$upData["Bill"];
$Estate=$upData["Estate"];
$WorkAdd=$upData["WorkAdd"];

$Picture=$upData["Picture"];
$Picture=$Picture==0?"":"<div style='color:#F00;'>已上传</div>";

if(floor($Qty)==$Qty){
      $Qty=floor($Qty);
}

$cgSign=$upData["cgSign"];
$Temp="SignSTR".strval($cgSign);
$$Temp=$cgSign;
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
if ($cgSign==1){
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
	  <tr>
        <td width="138" height="22" align="right">申购日期</td>
        <td width="612"><input name="PDate" type="text" id="PDate" size="89" value="<?php  echo $Date?>" onfocus="WdatePicker()" readonly dataType="Date" msg="未填写"></td>
	    </tr>
	  <tr>
        <td align="right">申 购 人</td>
        <td><select name='BuyerId' id='BuyerId' style='width: 480px;' dataType='Require'  msg='未选择'>
            <option value='' selected>请选择</option>
            <?php 
               echo "<option value='' selected>请选择</option>";
            
			$checkStaff ="SELECT P.Number,P.Name FROM $DataPublic.staffmain P,$DataIn.usertable U WHERE U.Number=P.Number AND P.Estate=1 ORDER BY P.BranchId,JobId,P.Number";
			$staffResult = mysql_query($checkStaff); 
			while ( $staffRow = mysql_fetch_array($staffResult)){
				$pNumber=$staffRow["Number"];
				$PName=$staffRow["Name"];
				if($pNumber==$BuyerId){
					echo "<option value='$pNumber' selected>$PName</option>";
					}
				else{
					echo "<option value='$pNumber'>$PName</option>";
					}
				} 
			?>
             </select></td>
	    </tr>
	  <tr>
        <td align="right">物品名称</td>
        <td><select name="TypeId" id="TypeId" style="width:480px" dataType="Require"  msg="未选择">
          <?php 
			$TypeSql = mysql_query("SELECT * FROM $DataIn.zw3_purchaset WHERE Estate='1' ORDER BY Id",$link_id);
			while ($TypeRow = mysql_fetch_array($TypeSql)){
				$tempId=$TypeRow["Id"];
				$TypeName=$TypeRow["TypeName"];
				if($tempId==$TypeId){
					echo"<option value='$tempId' selected>$TypeName</option>";
					}
				else{
					echo"<option value='$tempId'>$TypeName</option>";
					}
				}
			?>
          </select></td>
	    </tr>
          <tr>
	    <td  colspan="2">
        <table width="750" border="0" cellspacing="5" name="picTable" id="picTable">         <tr>
		  <td td width="128" height="22" align="right">物品图片</td>
		  <td><input name="Picture" type="file" id="Picture"  style="width: 480px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Cel="1"></td>
                  <td width="40"><?php  echo $Picture?></td>
	    </tr> 
		</table>
		</td>
	</tr>   
	<tr>
          <td align="right">使用地点</td>
          <td>
		<?php 
			  //选择地点
          	 $SelectWidth="480px"; 
             include "../model/subselect/WorkAdd.php";  
          ?>
            </td>
        </tr>
 
        <tr>
          <td align="right">申购数量</td>
          <td><input name="Qty" type="text" id="Qty" value="<?php  echo $Qty?>" style="width: 480px;" dataType="Price" msg="错误的数量"></td>
        </tr>
        <tr>
            <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td><input name="Unit" type="text" id="Unit" value="<?php  echo $Unit?>" style="width: 480px;" dataType="Require" msg="未填写"></td>
        </tr>
        <tr>
            <td align="right" valign="top">申购说明</td>
            <td><textarea name="Remark" style="width: 480px;" rows="4" id="Remark" dataType="Require" msg="未填写"><?php  echo $Remark?></textarea></td>
        </tr>
	   <input name='cgSign' type='hidden' id='cgSign' value='1'>
	
	</table>
</td></tr></table>
<?php  }else{ ?>
       <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
      <tr>
        <td width="138" height="22" align="right">申购日期:</td>
        <td width="612"><?php  echo $Date?></td>
    </tr>
    <tr>
        <td align="right">物品名称:</td>
        <td><?php  echo $TypeName?></td><input name='TypeId' type='hidden' id='TypeId' value='<?php  echo $TypeId?>'>
    </tr>
       <tr>
	    <td  colspan="2">
        <table width="750" border="0" cellspacing="5" name="picTable" id="picTable" >         <tr>
		  <td td width="128" height="22" align="right">物品图片:</td>
		  <td><input name="Picture" type="file" id="Picture"  style="width: 480px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Cel="1"></td>
                  <td width="40"><?php  echo $Picture?></td>
	    </tr>
		</table>
		</td>
	</tr>    
	        <tr>
          <td align="right">申购数量:</td>
          <td><?php  echo $Qty?></td>
        </tr>
        <tr>
            <td align="right" valign="top">申购说明:</td>
            <td><?php  echo $Remark?></td>
       </tr> 
      	<tr>
          <td align="right">使用地点:</td>
          <td>
		<?php 
			  //选择地点
	         if ($WorkAdd>0){
	              $WorkAddFrom=$WorkAdd;
	               include "../model/subselect/WorkAdd.php"; 
	               echo $WorkAdd;
	          }
	        else{
		           //选择地点
          	      $SelectWidth="480px"; 
                  include "../model/subselect/WorkAdd.php";  
	         }
          ?>
            </td>
        </tr>
         <tr>
          <td align="right" valign="top">采购状态:</td>
          <td><select name="cgSign" id="cgSign" style="width: 480px;" dataType="Require"  msg="未选择">
            <option value="0" <?php  echo $SignSTR0?>>已购</option>
          </select></td>
        </tr>
         <tr>
          <td align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购:</td>
          <td><select name="BuyerId" id="BuyerId" style="width: 480px;" dataType="Require"  msg="未选择">
              <option value='' selected>请选择</option>
              <?php 
			$checkStaff ="SELECT P.Number,P.Name FROM $DataPublic.staffmain P,$DataIn.usertable U WHERE U.Number=P.Number AND P.Estate=1 ORDER BY P.BranchId,JobId,P.Number";
			$staffResult = mysql_query($checkStaff); 
			while ( $staffRow = mysql_fetch_array($staffResult)){
				$pNumber=$staffRow["Number"];
				$PName=$staffRow["Name"];
				if($pNumber==$BuyerId){
					echo "<option value='$pNumber' selected>$PName</option>";
					}
				else{
					echo "<option value='$pNumber'>$PName</option>";
					}
				} 
			?>
          </select></td>
        </tr>
        <tr>
          <td align="right">物品单价:</td>
          <td><input name="Price" type="text" id="Price" value="<?php  echo $Price?>" style="width: 480px;" dataType="Currency" msg="格式不对"></td>
        </tr>
        <tr> 
          <td align="right">供应商:</td>
          <td><select name="cName" id="cName" style="width: 435px;" dataType="Require"  msg="未选择">
                      <option selected  value="">请选择</option>
                <?php 
			$checkCname =mysql_query("SELECT Id,cName FROM $DataIn.retailerdata  WHERE Estate=1",$link_id);
			while ( $checkRow = mysql_fetch_array($checkCname)){
				$theCid=$checkRow["Id"];
				$cName=$checkRow["cName"];
                                if ($theCid==$Cid){
                                  echo "<option value='$theCid' selected>$cName</option>";  
                                }
                                else{
                                     echo "<option value='$theCid'>$cName</option>";   
                                }
				
				} 
		?>
           </select>
            <input name="cNameCheck" type="checkbox" id="cNameCheck" style="vertical-align:middle;" onclick='newAddcName();'/>新增
          </td>
        </tr>
        <tr> 
         <td  colspan="2">
        <table width="750" border="0" cellspacing="5" name="cNameTable" id="cNameTable" style='display:none'>
          <tr>
           <td width="138" height="22" align="right"></td>
           <td width="612"><b>新增供应商资料</b></td>
          </tr>
         
          <tr>
           <td height="22" align="right">公司名称:</td>
           <td width="612"><input name="NewcName" type="text" id="NewcName" style="width: 480px;" dataType="Require" msg="未填写" Disabled/>   
          </tr>
         <tr>
           <td height="22" align="right">联系人:</td>
           <td width="612"><input name="NewLinkmen" type="text" id="NewLinkmen" style="width: 480px;" dataType="Require" msg="未填写" Disabled/>   
          </tr>  
         <tr>
           <td height="22" align="right">联系电话:</td>
           <td width="612"><input name="NewTel" type="text" id="NewTel" style="width: 480px;" dataType="Require" msg="未填写" Disabled/>   
          </tr>  
        </table>
         </td>
        </tr>
        <tr>
          <td align="right" valign="top">购买凭证:</td>
          <td><input name="Attached" type="file" id="Attached" style="width: 480px;"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="9" Cel="1"></td>
        </tr>
	<?php 
	if($Bill==1){
	    echo"<tr><td height='13' scope='col'>&nbsp;</td>
		<td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1' style='width: 300px;'><LABEL for='oldAttached'> 删除已传单据</LABEL></td></tr>";
	 }?>
        </table>
</td></tr></table>
<?php 
 }
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script type="text/javascript">
 function newAddcName(){
   cNameCheck=document.getElementById('cNameCheck');
   cName=document.getElementById('cName');
   NameTable=document.getElementById('cNameTable');
   newcName=document.getElementById('NewcName');
   newLinkmen=document.getElementById('NewLinkmen');
   newTel=document.getElementById('NewTel');
   if (cNameCheck.checked){
       newcName.disabled=false;
       newLinkmen.disabled=false;
       newTel.disabled=false;
       NameTable.style.display="";
       NameTable.visibled=true;
       cName.disabled=true;
       }
   else{
       newcName.disabled=true;
       newLinkmen.disabled=true;
       newTel.disabled=true;
       NameTable.style.display="none";
       cName.disabled=false;
    }
 }
</script>