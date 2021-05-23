<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage = "bom_check";
$toWebPage = "bom_object_read";
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter = "fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,proId,$proId,Estate,$Estate";

ChangeWtitle("$SubCompany 审核-BOM");

//步骤3：
$tableWidth = 850;
$tableMenuS = 500;
?>
<style type="text/css">
  .select1 {
    min-width: 100px;
    height: 25px;
    margin-right: 25px;
    border: 1px solid lightgray;
  }

  .table_td {
    padding: 10px 0;
    border-bottom: 1px solid lightgray;
  }

  .td1 {
    width: 130px;
    text-align: center;
  }
</style>
<body>
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post">
  <div class="div-select div-mcmain" style='width:<?php echo $tableWidth ?>'>
  <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5" id='NoteTable'>
    <tr>
      <td>
        <table width="760" border="0" align="center" cellspacing="0">
          <tr>
            <td scope="col" align="right" class="table_td td1" style="font-weight: bold;">项目名称</td>
            <td scope="col" class="table_td">
              <select name='tradeChoose' id='tradeChoose' class="select1">
                  <?php
                  //项目数据检索
                  $mySql = "SELECT a.Id, a.Forshort, b.Estate FROM $DataIn.trade_object a
            			INNER JOIN $DataIn.bom_object b on a.Id = b.TradeId and b.Estate = 1  
                        where a.ObjectSign = 2 order by a.Date";
                  $myResult = mysql_query($mySql, $link_id);
                  if ($myResult && $myRow = mysql_fetch_array($myResult)) {
                      do {
                          $Id = $myRow["Id"];
                          $Forshort = $myRow["Forshort"];
                          $Estate = $myRow["Estate"];
                          echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
                      } while ($myRow = mysql_fetch_array($myResult));
                  }
                  ?>
              </select>
            </td>
          </tr>
          <tr>
            <td scope="col" align="right" class="table_td td1">审核意见</td>
            <td scope="col" class="table_td">
              <textarea rows="3" cols="50" id="txtReasons" name="txtReasons"></textarea>
            </td>
          </tr>
          <tr>
            <td scope="col" align="right" class="table_td td1">审核状态</td>
            <td scope="col" class="table_td">
              <select id="chooseState" name="chooseState" class="select1">
                <option value="2" checked>通过</option>
                <option value="3">不通过</option>
                <option value="4">退回</option>
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <?php //二合一已更新?>
  <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5">
    <tr>
      <td width="150" height="40" id="menuT2" align="center">
          <?php
          echo "<span class='btn-confirm' style='display:inline-block;float: right;margin-right: 40px;' onClick='toCheck()' $onClickCSS>审核</span>&nbsp;";

          if ($fromWebPage) {
              echo "&nbsp;<span class='btn-cancel' style='display:inline-block;float: right;margin-right: 40px;' onClick='javascript:ReOpen(\"$fromWebPage\");' $onClickCSS>返回</span>";
          }
          ?>
      </td>
    </tr>
      <?php
      $SearchRows = "";
      if ($Parameter != "") {
          PassParameter($Parameter);
      }
      ?>
  </table>
  </div>
</form>
<script>
function toCheck() {

    var proId = jQuery("#tradeChoose").val();
    var Estate = jQuery("#Estate").val();
    var txtReasons = jQuery("#txtReasons").val();
    var chooseState = jQuery("#chooseState").val();

    if (proId == null) {
        alert("请选择审核项目!");
        return;
    }
    if (chooseState == 3) {
        //不通过
        if (txtReasons == "" || txtReasons.replace(/(^\s*)|(\s*$)/g, "") == "") {
            alert("请输入审核意见!");
            return;
        }
    }
    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }
    //alert(Estate+ "  " + chooseState);
    jQuery.ajax({
        url: 'bom_check_handle.php',
        type: 'post',
        data: {
            proId: proId,
            Estate: Estate,
            txtReasons: txtReasons,
            chooseState: chooseState
        },
        dataType: 'json',
        beforeSend: function () {
            //alert("beforeSend");
        },
        success: function (result) {
            if (result.rlt) {
                //window.location.reload();
                <?php if ($fromWebPage) {
                echo "ReOpen('$fromWebPage');";
            }
            else {
                echo "ReOpen('bom_check');";
            }
                ?>
                //ReOpen("<?php echo $fromWebPage?>");
            } else {
                alert(result.msg);
            }
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
}
</script>
</body>
</html>
