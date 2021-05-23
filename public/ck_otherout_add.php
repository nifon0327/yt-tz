<?php
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增出库记录");//需处理
$nowWebPage = $funFrom . "_add";
$toWebPage = $funFrom . "_save";
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter = "fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
//步骤3：
$tableWidth = 850;
$tableMenuS = 500;
include "../model/subprogram/add_model_t.php";

$Ck_Result = mysql_query("SELECT SeatId,StuffId FROM  $DataIn.stuffdata WHERE 1 AND Estate=1", $link_id);
while ($PD_Myrow = mysql_fetch_array($Ck_Result)) {
    $LocationId = $PD_Myrow["SeatId"];
    $LocationName = $PD_Myrow["StuffId"];
    $subLocationId[] = $LocationId;
    $subLocationName[] = $LocationName;
}


//步骤4：需处理
?>
  <script language="JavaScript">
  //窗口打开方式修改为兼容性的模态框 by ckt 2018-01-17
  function ViewStuffId() {
      var SafariReturnValue = document.getElementById('SafariReturnValue');
      if (!arguments[0]) {
          var r = Math.random();
          SafariReturnValue.value = "";
          SafariReturnValue.callback = 'ViewStuffId(true)';
          var url = "/public/stuffdata_s1.php?r=" + r + "&tSearchPage=stuffdata&fSearchPage=ck_bf&SearchNum=1&Action=8";
          openFrame(url, 930, 500);//url需为绝对路径
          return false;
          // var BackData=window.showModalDialog(,"BackData","dialogHeight =px;dialogWidth=px;center=yes;scroll=yes");
      }
      if (SafariReturnValue.value) {
          var CL = SafariReturnValue.value.split("^^");
          SafariReturnValue.value = "";
          SafariReturnValue.callback = "";
          document.form1.StuffId.value = CL[0];
          document.form1.StuffCname.value = CL[1];
          document.form1.StuffCname.title = "此配件可用库存：" + CL[2];
      }

  }

  function getLocationId(e) {
      var StuffId = document.getElementById('StuffId').value;
      jQuery.ajax({
          url: 'get_location_id.php',
          type: 'post',
          data: {
              StuffId: StuffId
          },
          dataType: 'json',
          success: function (result) {
              e.value = result.SeatId;
              e.disabled = "true";
              document.getElementById('LocationId').value = result.SeatId;
          }
      }).done(function () {

      });

  }

  </script>
  <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td class="A0011">
        <table width="688" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td width="64" align="right">出库日期</td>
            <td>
              <input name="bfDate" type="text" id="bfDate" style="width: 500px;" value="<?php echo date("Y-m-d") ?>" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
            </td>
          </tr>
          <tr>
            <td width="64" align="right">配&nbsp;&nbsp;件</td>
            <td>
              <input name="StuffId" type="hidden" id="StuffId"><input name="StuffCname" type="text" id="StuffCname" onclick="ViewStuffId()" style="width: 500px;" dataType="Require" msg="未选择报废配件" readonly>
            </td>
          </tr>
          <tr>
            <td width="64" align="right">出库库位</td>
            <td>
              <input name="LocationName" type="text" id="LocationName" style="width: 500px;" dataType="Require" msg="未输入出库库位" onclick="getLocationId(this)" readonly>
              <input name='LocationId' type='hidden' id='LocationId'>
            </td>
          </tr>
          <tr>
            <td align="right">出库数量</td>
            <td>
              <input name="Qty" type="text" id="Qty" style="width: 500px;" dataType="double" msg="报废数量不正确">
            </td>
          </tr>
          <tr>
            <td height="22" align="right">申 请 人</td>
            <td height="22">
              <select name="ProposerId" id="ProposerId" width="60" style="width: 500px;" dataType="Require" msg="未选择申请人">
                  <?php
                  //员工资料表
                  $PD_Sql = "SELECT M.Number,M.Name FROM $DataIn.usertable U LEFT JOIN $DataIn.staffmain M ON U.Number=M.Number WHERE M.Estate=1";
                  $PD_Result = mysql_query($PD_Sql);
                  echo "<option value=''>请选择</option>";
                  while ($PD_Myrow = mysql_fetch_array($PD_Result)) {
                      $Number = $PD_Myrow["Number"];
                      $Name = $PD_Myrow["Name"];
                      if ($Number == $Login_P_Number) {
                          echo "<option value='$Number' selected>$Name</option>";
                      }
                      else {
                          echo "<option value='$Number'>$Name</option>";
                      }
                  }
                  ?>
              </select></td>
          </tr>

          <tr>
            <td align="right">出库分类</td>
            <td>

              <select name="Type" id="Type" style="width: 500px;" dataType="Require" msg="未选择分类">
                  <?php

                  $Ck8_Sql = "SELECT id,TypeName FROM $DataIn.ck8_bftype  C WHERE 1";
                  $Ck8_Result = mysql_query($Ck8_Sql);
                  echo "<option value=''>请选择</option>";
                  while ($PD_Myrow = mysql_fetch_array($Ck8_Result)) {
                      $TypeId = $PD_Myrow["id"];
                      $TypeName = $PD_Myrow["TypeName"];
                      if ($TypeId == $Type) {
                          echo "<option value='$TypeId' selected>$TypeName</option>";
                      }
                      else {
                          echo "<option value='$TypeId'>$TypeName</option>";
                      }
                  }
                  ?>
              </select>
            </td>
          </tr>
          <tr>
            <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
            <td scope="col">
              <input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1">
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">报废原因</td>
            <td>
              <textarea name="Remark" cols="60" rows="4" id="Remark" dataType="Require" msg="未输入报废原因"></textarea>
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