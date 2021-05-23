<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 标准图备注");//需处理
$nowWebPage =$funFrom."_testremark";	
$toWebPage  =$funFrom."_remark";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$upResult = mysql_query("SELECT P.cName,P.ProductId,T.Remark
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.test_remark T ON T.ProductId=P.ProductId
where S.Id=$Id",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ProductId=$upData["ProductId"];
    $cName=$upData["cName"];
	$Remark=$upData["Remark"];
	}
?>
<form name="form" enctype="multipart/form-data" action="" method="post" >
<input name="ProductId" type="hidden" id="ProductId">
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
   <tr><td height="40" class="A0011">&nbsp;</td></tr>
    <tr>
     <td class='A0011'>
	  <table width="700" border="0" align="center" cellspacing="0">
	    <tr>
        <td width="1010" align="right">产品ID</td>
         <td>
		 <input type="text" name="ProductId" id="ProductId" value="<?php    echo $ProductId?>" readonly></td>
		 </td>
         </tr>
		<tr>
        <td width="1010" align="right">标准图备注</td>
         <td><textarea name="TestRemark" cols="65" rows="3" id="TestRemark"><?php    echo $Remark?></textarea>
		 </td>
         </tr>

     </table>
    </td>
  </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>