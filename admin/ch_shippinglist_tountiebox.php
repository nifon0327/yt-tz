<?php   
///电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<body>
<form name="form1" method="post" action="">
<table width="100%" height="207"  border="0">
  <tr align="center">
    <th height="28" colspan="2" scope="col">输入并箱后资料:</th>
  </tr>
  <tr>
    <th width="100" height="21" align="right" class="A1111">毛&nbsp;&nbsp;&nbsp;&nbsp;重</th>
  <th align="left" class="A1101"><input name="WG" type="text" value="6.2" size="15">KG</th>
  </tr>
  <tr>
    <th height="21" align="right" scope="col">并 箱 数</th>
  <th height="21" align="left" scope="col"><input name="BOXS" type="text" id="BOXS" value="1" size="15"></th>
  </tr>
  <tr>
    <th height="21" align="right" scope="col">外箱尺寸</th>
  <th height="21" align="left" scope="col"><input name="SPEC" type="text" value="52*29*29CM" size="15"></th>
  </tr>
  <tr>
    <th height="23" align="left" scope="col">&nbsp;</th>
  <th height="23" align="right" scope="col"><input type="button" name="Submit" value="确定" onClick="retrunValue()">
&nbsp;
<input type="button" name="Submit" value="取消" onClick="window.close()"></th>
  </tr>
  <tr>
    <th height="30" colspan="2" align="left" scope="col">&nbsp;</th>
  </tr>
</table>
</form>
</body>
</html>
<script>
function retrunValue() 
{ 
var wg= document.form1.WG.value; 
var boxs=document.form1.BOXS.value;
var Spec=document.form1.SPEC.value;
window.returnValue=wg+"|"+boxs+"|"+Spec; 
window.close(); 
} 
</script> 