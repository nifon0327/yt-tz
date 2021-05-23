<?php   
//电信-zxq 2012-08-01
$Th_Col="配件|40|序号|35|客户|50|产品ID|50|中文名|300|Product Code|200|检讨|30|外箱条码|150|所属分类|100";
$Field=explode("|",$Th_Col);
$Count=count($Field);

$ClientList="";
$ClientResult= mysql_query("SELECT C.CompanyId,C.Forshort
FROM $DataIn.trade_object C 
LEFT JOIN $DataIn.productdata P ON P.CompanyId=C.CompanyId 
 WHERE 1 AND cSign=7 AND C.Estate=1 GROUP BY P.CompanyId ORDER BY C.Id",$link_id);
if ($ClientRow = mysql_fetch_array($ClientResult)){
	$ClientList="<select name='CompanyId' id='CompanyId' style='width:150px' onChange='ResetPage(1,3)'>";//
	$i=1;
	do{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
		if($CompanyId==$theCompanyId){
			$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
			$SearchRows=" AND P.CompanyId='$theCompanyId'";
			$nowInfo="当前:产品查询 - ".$theForshort;
			}
		else{
			$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($ClientRow = mysql_fetch_array($ClientResult));
		$ClientList.="</select>";
	}
echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr >
	<td colspan='6' height='40px' class=''>$ClientList</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px'  style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,T.TypeName,C.Forshort
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	where 1 $SearchRows AND P.Estate=1 ORDER BY P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/productfile/",$SinkOrder,$motherSTR);	
	$dirforstuff=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	$i=1;
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$Client=$myRow["Forshort"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Code=$myRow["Code"]==""?"&nbsp;":$myRow["Code"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		//include "../admin/subprogram/product_teststandard.php";
		$TypeName=$myRow["TypeName"];
		
		//检查是否有BOM表
		$showPurchaseorder="[ + ]";
			$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
		echo"<tr>
			<td class='A0111' id='theCel$i' align='center' height='25' onClick='StuffSOrH(ListRow$i,theCel$i,$i,$ProductId);'>$showPurchaseorder</td>";
		echo"<td class='A0101' align='center'>$i</td>";
		echo"<td class='A0101'>$Client</td>";
		echo"<td class='A0101' align='center'>$ProductId</td>";
		echo"<td class='A0101'>$TestStandard</td>";
		echo"<td class='A0101'>$eCode</td>";
		echo"<td class='A0101' align='center'>$CaseReport</td>";
		echo"<td class='A0101'>$Code</td>";
		echo"<td class='A0101'>$TypeName</td>";
		echo"</tr>";
		echo $ListRow;
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='9' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
?>
</form>
</body>
</html>