<?php 
/*
$DataPublic.staffmain
$DataIn.usertable
$DataIn.zw3_purchaset
二合一已更新
*/
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增申购物品资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffType,$StuffType";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
	  <tr>
        <td height="22" align="right">申购日期</td>
        <td width="612"><input name="Date" type="text" id="Date" style="width: 480px;" value="<?php  echo date("Y-m-d")?>" onfocus="WdatePicker()" readonly dataType="Date" msg="未填写"></td>
	    </tr>
	  <tr>
        <td align="right">申 购 人</td>
        <td><select name="BuyerId" id="BuyerId" style="width: 480px;" dataType="Require"  msg="未选择">
            <option value='' selected>请选择</option>
            <?php 
			$checkStaff ="SELECT P.Number,P.Name FROM $DataPublic.staffmain P,$DataIn.usertable U WHERE U.Number=P.Number AND P.Estate=1 ORDER BY P.BranchId,JobId,P.Number";
			$staffResult = mysql_query($checkStaff); 
			while ( $staffRow = mysql_fetch_array($staffResult)){
				$pNumber=$staffRow["Number"];
				$PName=$staffRow["Name"];
				if($pNumber==$Login_P_Number){
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
           <td align="right">物品类别</td>
           <td><select name="sTypeId" id="sTypeId" style="width: 480px;" dataType="Require"  msg="未选择">
            <option value='' selected>请选择</option>
            <?php 
			$checkType =mysql_query("SELECT Id,Name FROM $DataPublic.zw_goodstype  WHERE Estate=1",$link_id);
			while ( $checkTypeRow = mysql_fetch_array($checkType)){
				$sTypeId=$checkTypeRow["Id"];
				$Name=$checkTypeRow["Name"];				
				echo "<option value='$sTypeId'>$Name</option>";
				} 
			?>
             </select>
          </td>
         </tr>
	<tr>
        <td align="right">物品名称</td>
        <td><input name="TypeName" type="text" id="TypeName" style="width: 430px;" dataType="Require" msg="未填写"/>
            <input name="newTypeName" type="text" id="newTypeName" style="width: 430px;display:none;" dataType="Require" msg="未填写" disabled/>
           <input name="newCheck" type="checkbox" id="newCheck" style="vertical-align:middle;" onclick='newAddName();'/>新名称
           <input name="TypeId" type="hidden" id="TypeId" value='' />
        </td>
	</tr>
	<tr>
	      <td  colspan="2">
        <table width="650" border="0" cellspacing="5" name="cNameTable" id="cNameTable" style='display:none'>         <tr>
		  <td td width="128" height="22" align="right">物品图片</td>
		  <td><input name="Attached" type="file" id="Attached"  style="width: 480px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Cel="1"></td>
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
          <td><input name="Qty" type="text" id="Qty" style="width: 480px;"  dataType="Price" msg="错误的数量"></td>
        </tr>
        <tr>
            <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td><input name="Unit" type="text" id="Unit" style="width: 480px;" dataType="Require" msg="未填写"></td>
        </tr>
        
        <tr>
            <td align="right" valign="top">申购说明</td>
            <td><textarea name="Remark" style="width: 480px;" rows="4" id="Remark" dataType="Require" msg="未填写"></textarea></td>
        </tr>
   
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
    $TypeSql = mysql_query("SELECT * FROM $DataIn.zw3_purchaset WHERE Estate='1' ORDER BY TypeName",$link_id);
			 $Id="";$TypeName="";$oneFlag=0;
			while ($TypeRow = mysql_fetch_array($TypeSql)){
			    if ($oneFlag==0){
				   $oneFlag=1;
				   $Id=$TypeRow["Id"];
				   $TypeName="'" . $TypeRow["TypeName"] . "'";
				}else{
					$Id=$Id . "," . $TypeRow["Id"];
					$TypeName=$TypeName .  ",'" . $TypeRow["TypeName"] . "'";
				}
			 }
			 $Id= "[" . $Id . "]";
			 $TypeName= "[" . $TypeName . "]";
?>


<script type="text/javascript">
 window.onload = function(){	
		var sinaSuggest = new InputSuggest({
			input: document.getElementById('TypeName'),
			poseinput: document.getElementById('TypeId'),
			id: <?php  echo $Id;?>,
			data: <?php  echo $TypeName;?>,
			width: 476
		});
				
	}
 function newAddName(){
   newCheck=document.getElementById('newCheck');
   typeName=document.getElementById('TypeName');
   newTypeName=document.getElementById('newTypeName');
   NameTable=document.getElementById('cNameTable');
   Attached= document.getElementById('Attached');
   if (newCheck.checked){
       newTypeName.disabled=false;
       newTypeName.style.display="";
       typeName.disabled=true;
       typeName.style.display="none";
       NameTable.style.display="";
       NameTable.visibled=true;
       }
   else{
       newTypeName.disabled=true;
       newTypeName.style.display="none";
       typeName.disabled=false;
       typeName.style.display="";
	   NameTable.style.display="none";   
    }
 }
</script>