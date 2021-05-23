<?php   
//电信-zxq 2012-08-01
//include "../basic/chksession.php" ;
include "../basic/parameter.inc";
//include "../model/shiplablefun.php";
include "../model/modelfunction.php";
//解密
$strArray=explode("|",$Parame2);
$RuleStr2=$strArray[0];
$EncryptStr2=$strArray[1];
$Str=anmaOut($RuleStr2,$EncryptStr2,"d");
if($Str=="Mid"){
	$midArray=explode("|",$Parame1);
	$RuleStr1=$midArray[0];
	$EncryptStr1=$midArray[1];
	$$Str=anmaOut($RuleStr1,$EncryptStr1,"f");
	}

//if($OrderPOSa!="") {
  $_SESSION["OrderPOS"] = $OrderPOSa;
  //echo 
	
//}
//echo "OrderPOS:" .$OrderPOS."----" ;
?>
<html>
<head>
<?php    include "../model/characterset.php";?>
<link rel="stylesheet" href="../model/outputlable.css">
<link rel="stylesheet" href="../model/style/ship.css"/>
<title>标签列印</title>
</head>
<script LANGUAGE="JavaScript">
<!-- Begin
function window.onload() {
	factory.printing.header ="";  	factory.printing.footer ="";  	factory.printing.portrait = true ;//纵向,false横向
  	factory.printing.topMargin = 2;//MCA2
  	factory.printing.bottomMargin = 1;
	factory.printing.leftMargin =2;
	factory.printing.rightMargin = 1;
	}
	
var hkey_root,hkey_path,hkey_key;
hkey_root="HKEY_CURRENT_USER";
hkey_path="\\Software\\Microsoft\\Internet Explorer\\PageSetup\\";
//设置网页打印的页眉页脚为空
function pagesetup_null(){
	try{
		var RegWsh = new ActiveXObject("WScript.Shell");
		hkey_key="header" ;
		RegWsh.RegWrite(hkey_root+hkey_path+hkey_key,"");
		hkey_key="footer";
		RegWsh.RegWrite(hkey_root+hkey_path+hkey_key,"");
		}
	catch(e){}
	}
//  End -->
</script>
<body><object id="factory" viewastext  style="display:none" classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814" codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70"></object>

<?php
	
	$Box_Sql = mysql_query("SELECT M.InvoiceNO,M.Date,M.Remark,M.ModelId,M.PreSymbol,SUM(L.BoxQty) AS BoxTotal,SUM(L.BoxRow*L.BoxQty) AS LableSUM,D.CompanyId,D.StartPlace,D.EndPlace,D.LabelModel
FROM $DataIn.ch2_packinglist L
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=L.Mid
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId
WHERE L.Mid=$Mid and D.LabelModel>0 AND BoxRow>0 GROUP BY L.Mid",$link_id);

	
	
?>

</body>
</html>
