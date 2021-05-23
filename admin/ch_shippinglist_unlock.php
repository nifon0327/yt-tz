<?php   
//电信-zxq 2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 待出订单解锁");//需处理
$funFrom="ch_shippinglist";
$nowWebPage =$funFrom."_unlock";
$fromWebPage=$funFrom."_add";
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage;

$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$IdArr=explode("^^", $checkid[$i]) ;
	$Id=$IdArr[0];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids."|".$Id);
		}
}
$ActionId=$ActionId==""?137:$ActionId;
$Parameter="Ids,$Ids,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId";
//步骤3
//echo $Ids;
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
  <td class="A0010" align="right" height="30" width="100">解锁原因:</td>
  <td class="A0001" ><input name="Reason" type="text" id="Reason" size="100" dataType="Require" msg="未填"></td>
  </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>