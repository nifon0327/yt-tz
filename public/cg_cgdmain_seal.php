<?php

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//$tableWidth=$_GET["divWidth"];
//$tableHeight=$_GET["divHeight"];
$typeId=$_GET["param1"];
//echo "typeId:$typeId <br>";


?>

	<table border="0" width="<?php  echo $tableWidth?>" height="<?php  echo $divHeight?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    	<tr><td >
        <table width="100%" height="100%" border="0" align="center" cellspacing="5" id="NoteTable">

          <tr>
            <td height="12" align="right">公司选择</td>
            <td >
		    <select name='SealCompanyId' id='SealCompanyId'  style="width:200px" >
			<option value='1' selected >上海研砼治筑建筑科技有限公司</option>
			<option value='2'  >研砼贸易有限公司</option>
			</select>
            </td>
          </tr>
  <tr valign="bottom"><td height="27" colspan="2" align="right"><a href="#" onclick="ToPDF('<?php echo $param1 ?>')">确定</a> &nbsp;&nbsp; <a href="javascript:CloseDiv()">取消</a></td></tr>
	</table>
</td></tr></table>
