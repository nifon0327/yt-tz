<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/shiplablefun.php";
include "../model/modelfunction.php";
//解密
$strArray = explode("|", $Parame2);
$RuleStr2 = $strArray[0];
$EncryptStr2 = $strArray[1];
$Str = anmaOut($RuleStr2, $EncryptStr2, "d");
if ($Str == "Mid") {
    $midArray = explode("|", $Parame1);
    $RuleStr1 = $midArray[0];
    $EncryptStr1 = $midArray[1];
    $$Str = anmaOut($RuleStr1, $EncryptStr1, "f");
}
$PackingResult = mysql_query("SELECT L.POrderId,L.BoxRow,L.BoxPcs,L.BoxQty,L.WG,L.BoxSpec 
	FROM $DataIn.ch0_packinglist L 
	WHERE L.Mid='$Mid' ORDER BY L.Id", $link_id);
if ($PackingRow = mysql_fetch_array($PackingResult)) {
    $POrderId = $PackingRow["POrderId"];  //订单流水号

    $ProductRow = mysql_fetch_array(mysql_query("SELECT S.OrderPO,S.PackRemark,P.productId,P.cName,P.eCode,P.Description,U.Name AS PackingUnit,P.Code,P.Remark,P.Weight,P.pRemark 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId 
					LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit WHERE S.POrderId=$POrderId LIMIT 1", $link_id));
    $OrderPO = $ProductRow["OrderPO"] == "" ? "&nbsp;" : $ProductRow["OrderPO"];
    /*出CEL
              if($ModelId==30){
                  $PackRemark=$ProductRow["PackRemark"];
                  if($PackRemark!=""){
                      $Field=explode("#",$PackRemark);
                      //$OrderPO=$Field[1];
                      }
                  else{
                      $OrderPO="&nbsp;"; //以#分隔
                      }
                  }
                  */
    $eCode = $ProductRow["eCode"];

    //echo "$OrderPO:$eCode ";

}
?>

  <html>
<head>
  <META content='MSHTML 6.00.2900.2722' name=GENERATOR>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
  <link rel='stylesheet' href='../model/tl/read_line.css'>
  <link rel='stylesheet' href='../model/css/sharing.css'>
  <link rel='stylesheet' href='../model/Totalsharing.css'>
  <link rel='stylesheet' href='../model/keyright.css'>
  <link rel='stylesheet' href='../model/SearchDiv.css'>
  <script src='../model/pagefun.js' type=text/javascript></script>
  <script src='../model/checkform.js' type=text/javascript></script>
  <script src='../model/lookup.js' type=text/javascript></script>
  <script src='../model/js/jquery-1.11.1.js'></script>
  <script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script>
</head>
<html>
<head>
  <SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>
</head>
<SCRIPT type=text/javascript>top.document.title = "研砼 -  模拟已出订单列表";</script>
<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action='ch0_shippinglist_print.php'>
  <table width="541" border="1">
    <tr>
      <td width="238" height="81">Artikel-Nr./
        Item NO.: <br/><input name="ItemNO" id="ItemNO" type="text" value="<?php echo $eCode ?>"/>(*表示使用自动生成)
      </td>
      <td width="287">Menge/
        Quantity: <br/><input name="QuantityNo" id="QuantityNo" type="text" value="*"/>(*表示使用自动生成)
      </td>
    </tr>
    <tr>
      <td height="166">Auftrags-Nr./PO No.:
        <br/><input name="PoNo" id="PoNo" type="text" value="<?php echo $OrderPO ?> "/>
        (*表示使用自动生成)
      </td>
      <td>Karton-Nr./Carton No.: <br/><input name="CartonNo" id="CartonNo" type="text" value="*"/>(*表示使用自动生成)
      </td>
    </tr>

    <tr>
      <td height="49" colspan="2" align="center">
        <input type="submit" name="button" id="button" value="提交"/></td>
    </tr>
  </table>
  <!-- ch0_shippinglist_print_zd.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=2  -->
  <input type="hidden" id="Parame1" name="Parame1" value="<? echo $Parame1 ?>"/>
  <input type="hidden" id="Parame2" name="Parame2" value="<? echo $Parame2 ?>"/>
  <input type="hidden" id="LablePos" name="LablePos" value="<? echo $LablePos ?>"/>

</form>
</body>
</html>
<?php
//电信-zxq 2012-08-01

?>