<?php 
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 检讨报告连接");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_caselink";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata WHERE Id='$Id' ORDER BY Id LIMIt 1",$link_id));
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];

//步骤4：
$tableWidth=950;$tableMenuS=500;$ColsNumber=5;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";

$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId,ActionId,81";
//步骤5：//需处理
?><input name="MergeRows" type="hidden" id="MergeRows">
<input name="sumCols" type="hidden" id="sumCols">

<table border="0" width="950" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <table width="920" border="0" align="center" cellspacing="0">
		<tr>
            <td colspan="4" scope="col">产品中文名：
            <?php  echo $cName?></td>
         </tr>
		<tr>
		  <td colspan="2" align="right" scope="col">&nbsp;</td>
		  <td colspan="2" scope="col">&nbsp;</td>
	     </tr>
		<tr align="center">
		  <td width="40" height="25" class="A1111" scope="col">&nbsp;</td>
		  <td width="50" class="A1101" scope="col">序号</td>
		  <td width="750" class="A1101" scope="col">检讨主题</td>
	     <td width="80" class="A1101" scope="col">查看</td>
		</tr>
		<tr>
		  <td height="475" colspan="4" scope="col"><div style='width:920;height:475;overflow-x:hidden;overflow-y:scroll'>
	  		<?php 
			$Result = mysql_query("SELECT * FROM $DataIn.errorcasedata WHERE Estate=1 ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($Result)) {
				$i=1;
				do{
					$cId=$myrow["Id"];
					$Title=$myrow["Title"];
					$Picture=$myrow["Picture"];
					$Result2 = mysql_query("SELECT * FROM $DataIn.casetoproduct WHERE ProductId=$ProductId and cId=$cId",$link_id);
					if($myrow2 = mysql_fetch_array($Result2)){
						echo"
						<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
						<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
						onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
						onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
						<td class='A0111' height='25' width='43' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$cId' checked disabled></td>
						<td class='A0101' width='53' align='center'>$i</td>
						<td class='A0101' width='744'>$Title</td>
						<td class='A0101' width='80'>查看</td>
						<td >&nbsp;</td></tr></table>";
					echo"<script>chooseRow(ListTable$i,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber)</script>";
					}
				else{
					echo"
					<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
					<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
					<td class='A0111' width='43' height='25' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$cId' disabled></td>
					<td class='A0101' width='53' align='center'>$i</td>
					<td class='A0101' width='744'>$Title</td>
					<td class='A0101' width='80'>查看</td>
					<td >&nbsp;</td></tr></table>";
					}
				$i++;
				}while ($myrow = mysql_fetch_array($Result));
			}
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
	  ?>
	  </div>
		  </td>
	     </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script  type=text/javascript>
function CheckForm(){
	//解除
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		var NameTemp=e.name;
		var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
		if (e.type=="checkbox" && Name!=-1){
			e.disabled=false;
			} 
		}
	document.form1.action="productdata_updated.php";
	document.form1.submit();
	}
</script>
