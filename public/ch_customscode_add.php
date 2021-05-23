<?php 
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";
ChangeWtitle("$SubCompany 新增海关编码记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td width="250" height="30" valign="middle" class='A0010'><p align="right">产品类型:<br>
      </td>
      <td valign="middle" class='A0001'><select name="uType" id="uType" style="width:420px;" dataType="Require"  msg="未选择">
        <option value="" selected>请选择</option>
          <?php 
			$checkSql = "SELECT TypeId,TypeName FROM $DataIn.producttype  WHERE Estate=1 order by Id";
			$checkResult = mysql_query($checkSql); 
			while( $checkRow = mysql_fetch_array($checkResult)){
				$TypeId=$checkRow["TypeId"];
				$TypeName=$checkRow["TypeName"];					
				echo "<option value='$TypeId'>$TypeName</option>";
				} 
			?>
        </select></td>
    </tr>
    
	<tr>
	  <td align="right" valign="top" scope="col" class='A0010'>指定产品</td>
	  <td valign="middle" scope="col" class='A0001'><p>
	    <select name="ListId[]" size="10" id="ListId" multiple style="width: 420px;" datatype="autoList"   onclick="SearchRecord('productdata','<?php  echo $funFrom?>',2,6)" readonly>
        </select>
	  </p>
	    </td>
	</tr>
    
     <tr>
      <td  height="30" valign="middle" class='A0010'><div align="right">海关编码:</div></td>
      <td valign="middle" class='A0001'><input name="HSCode" type="text" id="HSCode"  style="width:420px;" dataType="Require" msg="请填写" onkeyup="showResult(this.value,'HSCode','customscode','6','')" onblur="LoseFocus()" autocomplete="off"></td>
    </tr>
      <tr>
      <td  height="30" valign="middle" class='A0010'><div align="right">商品名称:</div></td>
      <td valign="middle" class='A0001'><input name="GoodsName" type="text" id="GoodsName"  style="width: 420px;" dataType="Require" msg="请填写" onkeyup="showResult(this.value,'GoodsName','customscode','6','')" onblur="LoseFocus()" autocomplete="off"></td>
    </tr>
	<tr>
    	<td height="30" align="right" valign="top" class='A0010'>备注:</td>
	    <td valign="middle" class='A0001'><textarea name="Remark" cols="56" rows="3" id="Remark" ></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>  