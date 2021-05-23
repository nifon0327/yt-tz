<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$upSql = mysql_query("SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId,D.CheckSign,T.AQL,
(G.AddQty+G.FactualQty) AS cgQty 
FROM $DataIn.gys_shsheet S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
WHERE S.Id=$Id LIMIT 1", $link_id);
if ($upData = mysql_fetch_array($upSql)) {
    $StuffId = $upData["StuffId"];
    $StockId = $upData["StockId"];
    $Qty = $upData["Qty"];
    $cgQty = $upData["cgQty"];
    $StuffCname = $upData["StuffCname"];
    $TypeId = $upData["TypeId"];
    $CheckSign = $upData["CheckSign"];
    $AQL = $upData["AQL"];
    if ($CheckSign == 0) {
        $CheckSignStr = "抽检";
        $CheckQtyStr = "抽样数量";
        $AQL = "";
        $ReQty = 1;
    }
    else {
        $AQL = "";
        $CheckSignStr = "全检";
        $CheckQtyStr = "全检数量";
    }
    $CheckQty = $Qty;
    $SendSign = $upData["SendSign"];
    switch ($SendSign) {
        case 1:
            $StockId = "本次补货";
            break;
        case 2:
            $StockId = "本次备品";
            break;
    }
}

$saveWebPage = "item2_3_ajax.php?ActionId=19";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php echo $saveWebPage ?>" method="post" enctype="multipart/form-data" target="FormSubmit" name="saveForm" id="saveForm" onsubmit="if(Validator.Validate(this,3) && checkObadValue()){return true}else{return false;}">
  <table width="750" height="70" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
      <td width="60" height="30" class="A1111">配件ID<input name="Id" type="hidden" id="Id" value="<?php echo $Id ?>">
      </td>
      <td width="340" class="A1101">配件名称</td>
      <td width="60" class="A1101">来料数量</td>
      <td width="100" class="A1101">品检标准<input name="CheckAQL" type="hidden" id="CheckAQL" value="<?php echo $AQL ?>">
      </td>
      <td width="60" class="A1101"><?php echo $CheckQtyStr ?></td>
    </tr>
    <tr align="center">
      <td height="30" class="A0111"><?php echo $StuffId ?></td>
      <td class="A0101"><?php echo $StuffCname ?></td>
      <td class="A0101" id="incoming"><?php echo $Qty ?></td>
      <td class="A0101" style='color:#0000FF;font-weight:bold;'><?php echo $CheckSignStr ?>
        <input name="CheckSign" type="hidden" id="CheckSign" value="<?php echo $CheckSign ?>"/></td>
      <td class="A0101"><?php echo $CheckQty ?>
        <input name="ReQty" type="hidden" id="ReQty" value="<?php echo $ReQty ?>"/><input name="CheckQty" type="hidden" id="CheckQty" value="<?php echo $CheckQty ?>"/>
      </td>
    </tr>
  </table>
  <br/>

  <table width="750" height="100%" border="0" cellpadding="0" cellspacing="0" name="NoteTable" id="NoteTable">
    <tr align="center">
      <td width="80" height="30" bgcolor="#d6efb5" class="A0111">序号</td>
      <td width="400" bgcolor="#d6efb5" class="A0101">不良原因</td>
      <td width="100" bgcolor="#d6efb5" class="A0101">不良数量</td>
      <td width="150" bgcolor="#d6efb5" class="A0101">不良图片</td>
    </tr>
      <?php
      $Cause_Str = "";
      $i = 1;
      $check_Result = mysql_query("SELECT Id FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1 LIMIT 1", $link_id);
      if ($check_row = mysql_fetch_array($check_Result)) {
          $cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1", $link_id);
      }
      else {
          $cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=1 AND Estate=1", $link_id);
      }
      while ($cause_row = mysql_fetch_array($cause_Result)) {
          $cId = $cause_row["Id"];
          $Cause = $cause_row["Cause"];

          ?>
        <tr align="center">
          <td width="80" height="35" bgcolor="#FFFFFF" class="A0101"><?php echo $i ?>
            <input name="CauseId[]" type="hidden" id="CauseId<?php echo $i ?>" value="<?php echo $cId ?>">
          </td>
          <td width="400" bgcolor="#FFFFFF" align="left" class="A0101" onclick="CauseClick(this)"><?php echo $Cause ?></td>
          <td width="100" bgcolor="#FFFFFF" class="A0101">
            <input name='badQty[]' id='badQty<?php echo $i ?>' type='text' style='border:0;text-align:right;background:#EEE;' value="0" size='6' onclick='showKeyboard(this,<?php echo $Qty ?>,<?php echo $i ?>)' readonly>
          </td>
          <td width="150" bgcolor="#FFFFFF" class="A0101" valign="middle">
            <input type="file" name="fileinput[]" id="fileinput<?php echo $i ?>" style="width:145px;height: 22px;display: none;">
          </td>
        </tr>
          <?php
          $i++;
      }
      $Cause_Str .= "<option value='-1'>其它原因</option>";
      ?>
    <tr align="center">
      <td width="80" height="35" bgcolor="#FFFFFF" class="A0101"><?php echo $i ?></td>
      <td width="400" bgcolor="#FFFFFF" align="left" class="A0101">
        其它原因:<input name="otherCause" type="text" id="otherCause" value="" style="width:320px;">

      </td>
      <td width="100" bgcolor="#FFFFFF" class="A0101">
        <input name='otherbadQty' id='otherbadQty' type='text' style='border:0;text-align:right;background:#EEE;' size='6' onkeypress="if(!this.value.match(/^[\+]?\d*?\.?\d*?$/))this.value=this.t_value;else this.t_value=this.value;if(this.value.match(/^(?:[\+]?\d+(?:\.\d+)?)?$/))this.o_value=this.value" onkeyup="if(!this.value.match(/^[\+]?\d*?\.?\d*?$/))this.value=this.t_value;else this.t_value=this.value;if(this.value.match(/^(?:[\+]?\d+(?:\.\d+)?)?$/))this.o_value=this.value" onblur="if(!this.value.match(/^(?:[\+]?\d+(?:\.\d+)?|\.\d*?)?$/))this.value=this.o_value;else{if(this.value.match(/^\.\d+$/))this.value=0+this.value;if(this.value.match(/^\.$/))this.value=0;this.o_value=this.value}">
      </td>
      <td width="150" bgcolor="#FFFFFF" class="A0101" valign="middle">
        <input type="file" name="otherfileinput" id="otherfileinput" style="width:145px;height: 22px;display: none;">
      </td>
    </tr>

    <tr align="center">
      <td colspan="2" height="30" bgcolor="#d6efb5" class="A0111">合&nbsp;&nbsp;&nbsp;&nbsp;计</td>
      <td width="100" bgcolor="#d6efb5" class="A0101">
        <input name='sumQty' id='sumQty' type='text' value="0" style='border:0;text-align:right;' size='6' readonly></input>
      </td>
      <td width="150" bgcolor="#d6efb5" class="A0101">&nbsp;</td>
    </tr>
  </table>

  </br>
  <table height="61" colspan="6" border="0" cellpadding="0" cellspacing="8" align="right" width="750" class="A0000" bgcolor="#d6efb5">
    <tr align="center">
      <td class="A0000" height="45" id="InfoBack">&nbsp;</td>
      <td width="20">&nbsp;</td>
      <td width="80"><input type="button" name="cancel" value="取消" onclick="closeMaskDiv()"></td>
      <td width="20">&nbsp;</td>
      <td width="80"><input type="submit" name="Submit" value="提交"></td>
    </tr>
  </table>
</form>


