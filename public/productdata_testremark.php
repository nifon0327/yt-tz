<?php 
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 产品标准图备注");	
$fromWebPage=$funFrom."_ts";		
$nowWebPage =$funFrom."_testremark";	
$toWebPage  =$funFrom."_testremarkd";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$myData = mysql_fetch_array(mysql_query("SELECT P.Id,P.ProductId,P.cName,P.eCode,T.Remark FROM $DataIn.productdata P
LEFT JOIN $DataIn.test_remark T ON T.ProductId=P.ProductId
WHERE P.ProductId='$Id'",$link_id));
$ProductId=$myData["ProductId"];
$cName=$myData["cName"];
$eCode=$myData["eCode"];
$Remark=$myData["Remark"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">产品ID：</td>
				<td valign="middle" scope="col"><?php  echo $ProductId?><input type="hidden" id="ProductId" name="ProductId" value="<?php  echo $ProductId?>"></td>
			  </tr>
			  <tr>
                <td align="right" scope="col">产品名称：</td>
                <td valign="middle" scope="col"><?php  echo $cName?></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">产品代码：</td>
                <td valign="middle" scope="col"><?php  echo $eCode?></td>
	    </tr>
			  			  <tr>
			    <td align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
			    <td valign="top"><textarea name="Remark" cols="60" rows="10" id="Remark" dataType="Remark"  msg="未填写"><?php  echo $Remark?></textarea></td>				
	    </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>