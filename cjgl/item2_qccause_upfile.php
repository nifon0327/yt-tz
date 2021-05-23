<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$upSql=mysql_query("SELECT B.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId,(G.AddQty+G.FactualQty) AS cgQty 
                FROM $DataIn.qc_badrecord B 
		LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=B.shMid AND B.StockId=S.StockId AND B.StuffId=S.StuffId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
WHERE B.Id=$Id LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$cgQty=$upData["cgQty"];
	$StuffCname=$upData["StuffCname"];
        $TypeId=$upData["TypeId"];
        $SendSign=$upData["SendSign"];
        switch ($SendSign){
           case 1:
               $StockId="本次补货";
               break;
           case 2:
               $StockId="本次备品";
               break;
        }
 }
 $saveWebPage="item2_4_ajax.php?ActionId=5";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  enctype="multipart/form-data"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "if(checkupfile()){return true}else{return false;}">
  <table width="780" height="70" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="100" height="30" class="A1111">流水号<input name="Id" type="hidden" id="Id" value="<?php    echo $Id?>"></td>
    <td width="60" class="A1101">配件ID</td>
    <td width="340" class="A1101">配件名称</td>
    <td width="60" class="A1101">采购数量</td>
    <td width="60" class="A1101">送货数量</td>
  </tr>
  <tr align="center">
    <td height="30" class="A0111"><?php    echo $StockId?></td>
    <td class="A0101"><?php    echo $StuffId?></td>
    <td class="A0101"><?php    echo $StuffCname?></td>
    <td class="A0101"><?php    echo $cgQty?></td>
    <td class="A0101"><?php    echo $Qty?></td>
  </tr>
</table>
<input name="CheckAQL" type="hidden" id="CheckAQL" value="">
<input name="ReQty" type="hidden" id="ReQty" value=""/>
<input name="CheckQty" type="hidden" id="CheckQty" value="<?php    echo $Qty?>"/>
<table width="780" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr align="center">
    <td width="80"height="30" bgcolor="#d6efb5" class="A0111">序号</td>
    <td width="400" bgcolor="#d6efb5" class="A0101">不良原因</td>
    <td  colspan="2" bgcolor="#d6efb5" class="A0101">不良图片</td>
  </tr>
 <?php
 $CauseOption="";
  $sheet_Result=mysql_query("SELECT B.Id,B.Qty,B.Reason,C.Cause  FROM $DataIn.qc_badrecordsheet B 
                            LEFT JOIN $DataIn.qc_causetype C ON C.Id=B.CauseId 
                            WHERE B.Mid='$Id' ",$link_id);
  echo "SELECT B.Id,B.Qty,B.Reason,C.Cause  FROM $DataIn.qc_badrecordsheet B 
                            LEFT JOIN $DataIn.qc_causetype C ON C.Id=B.CauseId 
                            WHERE B.Mid='$Id' AND B.Id='$Bid'";
 while ( $sheet_row = mysql_fetch_array($sheet_Result)){
      $sid=$sheet_row["Id"];
      $Cause=$sheet_row["Cause"];
      if ($Cause=="-1" || $Cause==""){
          $Cause=$sheet_row["Reason"];
      }
      if ($sid==$Bid){
           $CauseOption.="<option value='$sid' selected>$Cause</option>";
      }
      else{
          $CauseOption.="<option value='$sid'>$Cause</option>";
      }

 }

 for ($n=1;$n<=10;$n++){
     echo "<tr align=center'  bgcolor='#d6efb5'>
    <td width='80' height='30'  class='A0111'>$n</td>
    <td width='400' class='A0101'><select name='SelCauseId[]' id='SelCauseId$n' style='width:300px'>$CauseOption</select></td>
    <td  colspan='2' class='A0101'><input type='file' name='Pictures[]' id='Pictures$n' style='width:145px;height: 22px;' ></td>
     </tr>";
 }
 ?>
</table>
</br>
<table height="61"  border="0" cellpadding="0" cellspacing="8"  width=780" class="A0000" bgcolor="#d6efb5">
      <tr align="center">
         <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="submit" name="Submit" value="提交" ></td>
  </tr>
</table>
