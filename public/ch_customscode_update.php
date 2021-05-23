<?php 
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";
ChangeWtitle("$SubCompany 海关编码更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT H.ProductId,H.HSCode,H.Remark,P.cName,P.ProductId,H.GoodsName
FROM $DataIn.customscode H 
LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
WHERE H.Id='$Id'",$link_id));
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];
$HSCode=$upData["HSCode"];
$Remark=$upData["Remark"];
$GoodsName=$upData["GoodsName"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

 <tr>
      <td  width="250" height="30" valign="middle" class='A0010'><div align="right">产品名称:</div></td>
      <td valign="middle" class='A0001'><?php  echo $cName?></td>
    </tr>
     <tr>
      <td  height="30" valign="middle" class='A0010'><div align="right">海关编码:</div></td>
      <td valign="middle" class='A0001'><input name="HSCode" type="text" id="HSCode"  style="width: 420px;" value="<?php  echo $HSCode?>" dataType="Require" msg="请填写"  onkeyup="showResult(this.value,'HSCode','customscode','6','')" onblur="LoseFocus()" autocomplete="off"></td>
    </tr>
    
     <tr>
      <td  height="30" valign="middle" class='A0010'><div align="right">商品名称:</div></td>
      <td valign="middle" class='A0001'><input name="GoodsName" type="text" id="GoodsName"  style="width: 420px;" value="<?php  echo $GoodsName?>" dataType="Require" msg="请填写" onkeyup="showResult(this.value,'GoodsName','customscode','6','')" onblur="LoseFocus()" autocomplete="off"></td>
    </tr>
    
	<tr>
    	<td height="30" align="right" valign="top" class='A0010'>备注:</td>
	    <td valign="middle" class='A0001'><textarea name="Remark" cols="56" rows="3" id="Remark" ><?php  echo $Remark?></textarea></td>
    </tr>
	</table>
<?php 
$subHSCode = array();
$HSCodemySql="SELECT HSCode FROM $DataPublic.customscode S WHERE 1 AND Estate=1 AND HSCode !='' GROUP BY HSCode  ";
$HSCodeResult = mysql_query($HSCodemySql,$link_id);
 if($HSCodeRow = mysql_fetch_array($HSCodeResult)){
do{
      $thisHSCode=$HSCodeRow["HSCode"];
      $subHSCode[]=$thisHSCode;
      echo "<option value='$thisNumber'>$thisName</option>"; 
	 }while ($HSCodeRow = mysql_fetch_array($HSCodeResult));
 }
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
 window.onload = function(){
        var subHSCode=<?php  echo json_encode($subHSCode);?>;
		var sinaSuggestByMan= new InputSuggest({
			input: document.getElementById('HSCode'),
			poseinput: document.getElementById('HSCode'),
			data: subHSCode,
            id:subHSCode,
			width: 290
		});           	
	}
</script>