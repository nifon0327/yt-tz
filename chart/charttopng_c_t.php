<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 全部客户每月下单、出货金额条形图");
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" id="form1" enctype="multipart/form-data" action="charttopng_total.php" method="post" target="mainFrame">
<select name="M" id="M" onChange="javascript:document.form1.submit();">
  <option value="6" selected>最近6个月</option>
  <option value="12">最近12个月</option>
  <!--<option value="13" >最近13个月</option>-->
  <!--<option value="14">最近14个月</option>-->
  <!--<option value="15">最近15个月</option>-->
  <!--<option value="16">最近16个月</option>-->
  <!--<option value="20">最近20个月</option>-->
 <option value="24">最近24个月</option>
  <!--<option value="25">最近25个月</option>-->
  <!--<option value="28">最近28个月</option>-->
  <!--<option value="32">最近32个月</option>-->
  <option value="36">最近36个月</option>
 <!-- <option value="37">最近37个月</option>-->
</select>
</form>

</body>
</html>