<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=700;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' height='20'>序号</td>
		<td width='60' align='center'>ID</td>
		<td width='100' align='center'>客户</td>
		<td width='100' align='center'>分类</td>
		<td width='250' align='center'>中文名</td>		
		<td width='150' align='center'>Product Code</td>";
echo "</tr>";
$proResult = mysql_query("SELECT P.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,T.TypeName,D.Forshort
FROM $DataIn.errorcasedata E
LEFT JOIN casetoproduct C ON C.cId=E.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object D ON D.CompanyId=P.CompanyId
WHERE  1 AND E.Id='$Id'",$link_id);
$i=1;
if ($proRows = mysql_fetch_array($proResult)) {
	do{
		   $ProductId=$proRows["ProductId"];
		   $cName=$proRows["cName"];	
		   $eCode=$proRows["eCode"];
		   $Code=$proRows["Code"];
		   $TypeName=$proRows["TypeName"];
		   $Forshort=$proRows["Forshort"];
		   $TestStandard=$proRows["TestStandard"];
		   include "../admin/Productimage/getProductImage.php";	
	       echo"<tr bgcolor='$theDefaultColor'><td bgcolor='$Sbgcolor' align='center' height='20'>$i</td>";//
		   echo"<td  align='center'>$ProductId</td>";//
		   echo"<td  align='Left'>$Forshort</td>";
		   echo"<td  align='Left'>$TypeName</td>";
		   echo"<td  align='Left' >$TestStandard</td>";		
		   echo"<td  align='Left'>$eCode</td>";
		   echo"</tr>";
		   $i=$i+1;
	     }while ($proRows = mysql_fetch_array($proResult));
       }
else{
	    echo"<tr><td height='30' colspan='6'>无相关的产品.</td></tr>";
	  }
echo"</table>"."";
echo"<tr height='10'><td  colspan='6' align='center'>&nbsp;</td> ";


//**************************************相关配件
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' height='20'>序号</td>
		<td width='60' align='center'>ID</td>
		<td width='100' align='center'>供应商</td>
		<td width='100' align='center'>分类</td>
		<td width='340' align='center'>配件名</td>		
		<td width='60' align='center'>历史订单</td>";
echo "</tr>";
$stuffResult = mysql_query("SELECT  D.StuffId,D.StuffCname,D.StuffEname,D.TypeId,D.Gfile,D.Gstate,D.Picture,P.Forshort,G.TypeName
FROM $DataIn.errorcasedata E
LEFT JOIN casetostuff C ON C.cId=E.Id
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=C.StuffId
LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 	
LEFT JOIN $DataIn.stufftype G ON G.TypeId=D.TypeId  
WHERE  1 AND E.Id='$Id'",$link_id);
$i=1;
if ($stuffResult && $StuffRows = mysql_fetch_array($stuffResult)) {
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		   $StuffId=$StuffRows["StuffId"];
		   $StuffCname=$StuffRows["StuffCname"];	
		   $StuffEname=$StuffRows["StuffEname"];
		   $TypeName=$StuffRows["TypeName"];
		   $Forshort=$StuffRows["Forshort"];
		   $Gstate=$StuffRows["Gstate"];
		   $Gfile=$StuffRows["Gfile"];
		   $Picture=$StuffRows["Picture"];
		   include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		   include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
           $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
	       echo"<tr bgcolor='$theDefaultColor'><td bgcolor='$Sbgcolor' align='center' height='20'>$i</td>";//
		   echo"<td  align='center'>$StuffId</td>";//
		   echo"<td  align='Left'>$Forshort</td>";
		   echo"<td  align='Left'>$TypeName</td>";
		   echo"<td  align='Left' >$StuffCname</td>";		
		   echo"<td  align='center'>$OrderQtyInfo</td>";
		   echo"</tr>";
		   $i=$i+1;
	     }while ($StuffRows = mysql_fetch_array($stuffResult));
       }
else{
	    echo"<tr><td height='30' colspan='6'>无相关的配件.</td></tr>";
	  }
echo"</table>"."";
?>