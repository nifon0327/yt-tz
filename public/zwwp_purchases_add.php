<?php 
//ewen 2012-12-16
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
$checkStaff =mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' AND Estate=1 ORDER BY BranchId,JobId,Number",$link_id));
$PName=$checkStaff["Name"];
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
        <td><input name="BuyerId" type="text" id="BuyerId" style="width: 480px;" dataType="Require"  value="<?php echo $PName?>" msg="未填写"/></td>
	    </tr>
         <tr>
           <td align="right">物品类别</td>
           <td><select name="TypeId" id="TypeId" style="width: 480px;" dataType="Require"  msg="未选择">
            <option value='' selected>请选择</option>
            <?php 
			$checkType =mysql_query("SELECT Id,TypeName FROM $DataPublic.zwwp2_subtype  WHERE Estate=1",$link_id);
			while ( $checkTypeRow = mysql_fetch_array($checkType)){
				echo "<option value='$checkTypeRow[Id]'>$checkTypeRow[TypeName]</option>";
				} 
			?>
             </select>
          </td>
         </tr>
	<tr>
        <td align="right">物品名称</td>
        <td><input name="GoodsName" type="text" id="GoodsName" style="width: 430px;" dataType="Require" msg="未填写"/>
            <input name="newGoodsName" type="text" id="newGoodsName" style="width: 430px;display:none;" dataType="Require" msg="未填写" disabled/>
           <input name="newCheck" type="checkbox" id="newCheck" style="vertical-align:middle;" onclick='newAddName();'/>新名称
           <input name="GoodsId" type="hidden" id="GoodsId" value='' />
        </td>
	</tr>
	<tr>
	      <td  colspan="2">
        <table width="100%" border="0" cellspacing="5" name="cNameTable" id="cNameTable" style='display:none; background-color:#CCC'><tr>
		  <td height="22" align="right">物品图片</td>
		  <td width="605"><input name="Attached" type="file" id="Attached"  style="width: 480px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Cel="1"></td>
	    </tr>
		</table>
        
		</td>
		</tr>
        
        <tr>
          <td align="right">申购数量</td>
          <td><input name="Qty" type="text" id="Qty" style="width: 480px;"  dataType="Price" msg="错误的数量"></td>
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
$TypeSql = mysql_query("SELECT * FROM $DataPublic.zwwp3_data WHERE Estate='1' ORDER BY GoodsName",$link_id);
$Id="";$GoodsName="";$oneFlag=0;
while ($TypeRow = mysql_fetch_array($TypeSql)){
	if ($oneFlag==0){
		$oneFlag=1;
		$Id=$TypeRow["Id"];
		$GoodsName="'" . $TypeRow["GoodsName"] . "'";
		}
	else{
		$Id=$Id . "," . $TypeRow["Id"];
		$GoodsName=$GoodsName .  ",'" . $TypeRow["GoodsName"] . "'";
		}
	}
$Id= "[" . $Id . "]";
$GoodsName= "[" . $GoodsName . "]";
?>
<script type="text/javascript">
 window.onload = function(){	
		var sinaSuggest = new InputSuggest({
			input: document.getElementById('GoodsName'),
			poseinput: document.getElementById('GoodsId'),
			id: <?php  echo $Id;?>,
			data: <?php  echo $GoodsName;?>,
			width: 476
		});
				
	}
 function newAddName(){
   newCheck=document.getElementById('newCheck');
   typeName=document.getElementById('GoodsName');
   newGoodsName=document.getElementById('newGoodsName');
   NameTable=document.getElementById('cNameTable');
   Attached= document.getElementById('Attached');
   if (newCheck.checked){
       newGoodsName.disabled=false;
       newGoodsName.style.display="";
       typeName.disabled=true;
       typeName.style.display="none";
       NameTable.style.display="";
       NameTable.visibled=true;
       }
   else{
       newGoodsName.disabled=true;
       newGoodsName.style.display="none";
       typeName.disabled=false;
       typeName.style.display="";
	   NameTable.style.display="none";   
    }
 }
</script>