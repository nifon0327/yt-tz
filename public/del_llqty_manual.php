<?php 
//步骤1 $DataPublic.branchdata 二合一已更新 电信-yang 20120801
include "../model/modelhead.php";
?>
<body>
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="del_llqty_updated.php" method="post" >
  <table width="760" border="0" align="center" cellspacing="5">
        <tr>
          <td width="150" height="40" align="right" scope="col">订单流水号</td>
          <td width="250"><input name="POrderId" type="text" id="POrderId"  size="30" maxlength="12"></td>
          <td scope="col"> <input type="submit" id="delButton" name="delButton"  value="册除领料记录" /></td>
        </tr>
    </table>
 </form>
</body>