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
S.Date,S.TypeId,T.TypeName,S.Remark,S.BuyerId,S.Price,S.Bill,S.cgSign,S.Estate,T.Attached AS Picture 
 FROM $DataIn.zw3_purchases S,$DataIn.zw3_purchaset T WHERE S.Id='$Id' AND S.TypeId=T.Id LIMIT 1",$link_id));
$Date=$upData["Date"];
$TypeId=$upData["TypeId"];
$TypeName=$upData["TypeName"];
$Remark=$upData["Remark"];
$BuyerId=$upData["BuyerId"];
$Price=$upData["Price"];
$Bill=$upData["Bill"];
$Picture=$upData["Picture"];
//$Picture=$Picture==0?"":"style='display:none;'";
$Picture=$Picture==0?"":"<div style='color:#F00;'>已上传</div>";
$Estate=$upData["Estate"];
$cgSign=$upData["cgSign"];
$Temp="SignSTR".strval($cgSign);
$$Temp=$cgSign;

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
	  <tr>
        <td height="22" align="right" width="138">申购日期</td>
        <td><?php  echo $Date?></td>
	    </tr>
		<tr>
            <td align="right" scope="col">物品名称</td>
            <td width="612" scope="col"><?php  echo $TypeName?></td><input name='TypeId' type='hidden' id='TypeId' value='<?php  echo $TypeId?>'>
		</tr>
           <tr>
	    <td  colspan="2">
        <table width="750" border="0" cellspacing="5" name="picTable" id="picTable" >         <tr>
		  <td width="138" align="right">物品图片</td>
		  <td width="572" ><input name="Picture" type="file" id="Picture"  style="width: 300px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Cel="1"></td>
                  <td width="40"><?php  echo $Picture?></td>
	    </tr>
		</table>
		</td>
	</tr>    
        <tr>
            <td align="right" valign="top">申购说明</td>
            <td><?php  echo $Remark?></td>
        </tr>
        <tr>
          <td align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购</td>
          <td><select name="BuyerId" id="BuyerId" style="width: 300px;" dataType="Require"  msg="未选择">
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
          <td align="right">物品单价</td>
          <td><input name="Price" type="text" id="Price" value="<?php  echo $Price?>" size="53" dataType="Currency" msg="格式不对"></td>
        </tr>
        <tr>
          <td align="right" valign="top">购买凭证</td>
          <td><input name="Attached" type="file" id="Attached" size="40"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
        </tr>
		<?php 
		if($Bill==1){
			echo"<tr><td height='13' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传单据</LABEL></td></tr>";
			}?>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>