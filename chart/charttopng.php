<?php   	
//独立已更新
//<form name="form1" method="post" action="">
/*$StartY=2009;
$NowY=date("Y");
for($i=$NowY;$i>$StartY;$i--){
	echo"<img src='charttopng_".$Type.".php?Y=$i&Id=$Id&chartType=$Type'><br>";
	}*/
//</form>
$choosemonth="12";
$url="charttopng_".$Type.".php?Y=$choosemonth&Id=$Id&chartType=$Type";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>每月下单、出货数量条形图</title>
</head>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" id="form1" enctype="multipart/form-data" action="" method="" target="mainFrame">
<select name="M" id="M" onChange="changeMonth()">
  <option value="12" selected>最近12个月</option>
  <option value="24">最近24个月</option>
  <option value="36" >最近36个月</option>
</select>
</form>
<img id="Dimg" src="<?php    echo $url?>">
</body>
</html>
<input name="chartType" id="chartType" type="hidden" value="<?php    echo $Type?>">
<input name="Id" id="Id" type="hidden" value="<?php    echo $Id?>">
<script language="javascript">

function changeMonth(){
  var Type=document.getElementById("chartType").value;
  var Id=document.getElementById("Id").value;
  var choosemonth=document.getElementById("M").value;
  document.getElementById("Dimg").src="charttopng_"+Type+".php?Y="+choosemonth+"&chartType="+Type+"&Id="+Id;
  
}
</script>
