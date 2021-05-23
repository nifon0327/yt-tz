<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.cg1_stockmain
*/
include "../model/modelhead.php";
//模板基本参数:模板$Login_WebStyle
ChangeWtitle("$SubCompany 配件历史价格列表");
$Login_help="historyprice_read";
//session_register("Login_help"); 
$_SESSION["Login_help"] = $Login_help;

$tableMenuS=350;
$tableWidth=750;
$CustomFun="历史价格列表";//自定义功能
$SaveSTR="NO";$isBack="N";
include "../model/subprogram/add_model_t.php";
?>
<table width="<?php  echo $tableWidth?>" height="38" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011" align="center">
	<table width="700" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
		<tr class="">
		<td class='A1111' align="center" width="40" height="25">序号</td>
    	<td class='A1101' align="center" width="80">配件ID</td>
		<td class='A1101' align="center">配件名称</td>
		<td class='A1101' align="center" width="80">购买单价</td>
		<td class='A1101' align="center" width="100">购买日期</td>
	</tr>
	<?php 
	 $PriceResult = mysql_query("SELECT S.Price,D.StuffCname,M.Date
	 FROM $DataIn.cg1_stocksheet S
	 LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	 LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
	 WHERE S.StuffId=$StuffId and S.Mid!=0 group by S.Price order by M.Date",$link_id);
	 if($PriceRows = mysql_fetch_array($PriceResult)){
		$i=1;
		$hPrice=0;
		$lPrice=0;
		do{
			$Date=$PriceRows["Date"];
			$Price=$PriceRows["Price"];
			if($i==1){
				$hPrice=$Price;
				$lPrice=$Price;
				}
			else{
				$hPrice=$Price>$hPrice?$Price:$hPrice;
				$lPrice=$Price<$lPrice?$Price:$lPrice;
				}
			$StuffCname=$PriceRows["StuffCname"];
			echo"<tr>
					<td class='A0111' align='center' height='25'>$i</td>
					<td class='A0101' align='center'>$StuffId</td>
					<td class='A0101'>$StuffCname</td>
					<td class='A0101' align='center'>$Price</td>
					<td class='A0101' align='center'>$Date</td>
				</tr>";
			$i++;
			}while($PriceRows = mysql_fetch_array($PriceResult));
			echo"<tr>
				<td class='A0111' align='right' colspan='5' height='25'><span class='redB'>最高历史价格：$hPrice </span> &nbsp;&nbsp;<span class='greenB'>最低历史价格：$lPrice</span>&nbsp;&nbsp;</td>
				</tr>";
		}
	else{
		echo"<tr><td class='A0111' align='center' colspan='5' height='25'>无历史价格记录</td></tr>";
		}
?>
</table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>