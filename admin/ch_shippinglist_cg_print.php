<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/shiplablefun.php";
include "../model/modelfunction.php";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
?>
<style>
#head_left {
       position:relative;
       float:left;
	   width:330px; 
	   text-align:left;
	   }
#head_right {
       position:relative;
       float:left;
	   margin-left:20px;  
	   text-align:left;
	   }
-->
</style>
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
$Remain=array();
$ItemNoArray=array();
$DescriptionArray=array();
$PONoArray=array();
$BoxCodeArray=array();
$i=1;
$StuffTypeSTR="and T.TypeId='9040'";
$BoxSql="SELECT S.Qty,P.eCode,P.Code,O.PONo,O.ItemNo,P.ProductId,O.Description
         FROM $DataIn.yw1_ordersheet S 
		 LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
		 LEFT JOIN $DataIn.cg_order O ON O.POrderId=S.POrderId
		 WHERE S.Id IN ($Ids)";
  $Box_Result = mysql_query($BoxSql,$link_id);
  if($BoxRow=mysql_fetch_array($Box_Result)){
    do{ 
         $eCode=$BoxRow["eCode"]; 
	     $Remark=explode("<<",$BoxRow["Description"]);
		 $Description=$Remark[0]."<<"."<br>"."&nbsp;".$Remark[1];
	     $Qty=$BoxRow["Qty"];
	     $BoxCode=$BoxRow["Code"];
	     $ItemNo=$BoxRow["ItemNo"];
	     $PONo=$BoxRow["PONo"];
	     $ProductId=$BoxRow["ProductId"];
	     $Spec="";//外箱规格
	     $Relation="";
	     $SpecResult = mysql_query("SELECT D.Spec,D.Weight,P.Relation FROM $DataIn.pands P LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 $StuffTypeSTR",$link_id);
	if($SpecRows = mysql_fetch_array($SpecResult)){
		$SpecArray=explode("CM",$SpecRows["Spec"]);//以CM为界拆分
		$Spec=$SpecArray[0]."CM";
		$Relation=$SpecRows["Relation"];
		$BoxWeight=$SpecRows["Weight"];
		}
	  if ($Relation!=""){
						  $RelationArray=explode("/",$Relation);
						  $Relation=$RelationArray[1];
						  $BoxNum=floor($Qty/$Relation);
						  //echo $BoxNum;尾箱处理
						  $Remain[]=$Qty%$Relation;
						}
				   else { $Relation="&nbsp;";$BoxNum="&nbsp;";}
	
	$ItemNoArray[]=$ItemNo;
	$DescriptionArray[]=$Description;
	$PONoArray[]=$PONo;
	$BoxCodeArray[]=$BoxCode;								
	//条码		
	$path = $_SERVER["DOCUMENT_ROOT"];						
	if($BoxCode!=""){
		$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
		if(is_numeric($BoxCode0)){	
			$BoxCode1=preg_replace("/,/","<br>",$BoxCode1);
			$path = $_SERVER["DOCUMENT_ROOT"];
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='175' height='80'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1.5&hi=60'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
			}
		else{
			$BoxCode0=preg_replace("/,/","<br>",$BoxCode0);
			if(is_numeric($BoxCode1)){
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='175' height='80'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1.5&hi=60'></iframe></td></tr></table>";
			  }
			 else{
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
			 }
			}
		}
	else{$BoxCodeTable="&nbsp;";}
$j=$i;
for($i=$j;$i<$j+$BoxNum;$i++){
  if($i>1){echo "<div style='PAGE-BREAK-AFTER: always'></div>";}
?>

<div>
<div id="head" style="height:80px;width:580px">
<TABLE  style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;" cellSpacing="0" cellPadding="0"  border="0">

<tr><td>&nbsp;</td>
</tr>
</TABLE>
</div>
<div id="body" style=" width:580px;height:220px;">
<div id="head_left">
<TABLE  style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;" width="350" cellSpacing="0" cellPadding="0"  border="0">
<tr ><td width="25">&nbsp;</td>
     <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
	   <tr><td width="160"align="left"><span style="font:bold;font-family:Arial">Artikel-Nr./<br>Item No.:</span></td>
	       <td width="10">&nbsp;</td>
	       <td width="150" align="left">
	       <span style="font:bold;font-family:Arial">&nbsp;Menge/<br>&nbsp;Quantity:</span></td>
	   </tr>
	   <tr><td class="A1111" height="40" width="160">&nbsp;&nbsp;
	       <span style="font:bold;font-size:26px"><?php    echo $ItemNo?></span></td>
	       <td width="10">&nbsp;</td>
	       <td height="40" width="150">
	           <table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
	              <tr><td class="A1111" height="40" width="90">&nbsp;&nbsp;&nbsp;&nbsp;
			          <span style="font:bold;font-size:26px"><?php    echo $Relation?></span></td>
	                  <td class="A1101" width="50" >
				     <span style="font:bold;font-family:Arial">Stuck/Pcs.</span></td>
			      </tr>
	           </table>
           </td>
		 </tr>
	 </table></td>
</tr>
<tr><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr><td width="350"><span style="font:bold;font-family:Arial">ArtikeIbezeichnung/Item description</span></td></tr>
		<tr><td class="A1111" height="40"><span style="font:bold">&nbsp;<?php    echo $Description?></span></td></tr>
        </table>
	</td>
</tr>
<tr ><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr ><td width="100"><span style="font:bold;font-family:Arial">Auftrags-Nr./<br>PO No.:</span></td>
		     <td width="30">&nbsp;</td>
		     <td><span style="font:bold;font-family:Arial">
			      &nbsp;&nbsp;Karton-Nr./<br>&nbsp;&nbsp;Carton No.:</span>
				  </td>
			 <td width="30" colspan="2">&nbsp;</td>
		</tr>
		<tr><td class="A1111" height="40">
		     &nbsp;&nbsp;&nbsp;<span style="font:bold;font-size:26px"><?php    echo $PONo?></span></td>
		     <td width="30">&nbsp;</td>
		     <td class="A1111" height="40" align="center">
			 &nbsp;<span style="font:bold;font-size:26px"><?php    echo $i?></span>
			 </td>
			 <td width="30">&nbsp;</td><td>&nbsp;</td>
		</tr>
      </table></td>
</tr>
<tr><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr><td><span style=" vertical-align:top;font:bold;font-size:16px;font-family:Arial">www.hama.com</span></td></tr>
        </table>
	</td>
</tr>
</TABLE>
</div>
<div id="head_right" style="margin-top:-5px">
<table border="0" width="180">
	  <tr><td ><span style="font:bold;font-family:Arial">Anmerkung(en)/<br>Remark:</span></td></tr>
	  <tr><td  height="100" class="A1111">&nbsp;</td></tr>
	  <tr><td align="right" height=" "><?php    echo $BoxCodeTable?></td></tr>
</table>
</div>
</div>
</div>

<!--<div style="PAGE-BREAK-AFTER: always"></div>-->
<?php        }//end for
     }while($BoxRow=mysql_fetch_array($Box_Result));
 }//end if
?>

<?php   
 //===================================================尾箱处理
$count=count($Remain);
for($k=0;$k<$count;$k++){
if($Remain[$k]!=0){
//条码		
    echo "<div style='PAGE-BREAK-AFTER: always'></div>";					
	if($BoxCodeArray[$k]!=""){
		$Field=explode("|",$BoxCodeArray[$k]);
		$BoxCode0=$Field[0];$BoxCode1=$Field[1];
		if(is_numeric($BoxCode0)){	
			$BoxCode1=eregi_replace(",","<br>",$BoxCode1);
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='175' height='80'  src='ean_13code.php?Code=$BoxCode0&lw=1.5&hi=60'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
			}
		else{
			$BoxCode0=eregi_replace(",","<br>",$BoxCode0);
			if(is_numeric($BoxCode1)){
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='155' height='80'  src='ean_13code.php?Code=$BoxCode1&lw=1.5&hi=60'></iframe></td></tr></table>";
			  }
			 else{
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
			 }
			}
		}
	else{$BoxCodeTable="&nbsp;";}
?>

<div>
<div id="head" style="height:80px;width:580px">
<TABLE  style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;" cellSpacing="0" cellPadding="0"  border="0">
<tr><td>&nbsp;</td>
</tr>
</TABLE>
</div>
<div id="body" style=" width:580px;height:195px;">
<div id="head_left">
<TABLE  style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;" width="350" cellSpacing="0" cellPadding="0"  border="0">
<tr ><td width="25">&nbsp;</td>
     <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
	   <tr><td width="160"align="left"><span style="font:bold;font-family:Arial">Artikel-Nr./<br>Item No.:</span></td>
	       <td width="10">&nbsp;</td>
	       <td width="150" align="left">
	       <span style="font:bold;font-family:Arial">&nbsp;Menge/<br>&nbsp;Quantity:</span></td>
	   </tr>
	   <tr><td class="A1111" height="40" width="160">&nbsp;&nbsp;
	       <span style="font:bold;font-size:26px"><?php    echo $ItemNoArray[$k]?></span></td>
	       <td width="10">&nbsp;</td>
	       <td height="40" width="150">
	           <table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
	              <tr><td class="A1111" height="40" width="90">&nbsp;&nbsp;&nbsp;&nbsp;
			          <span style="font:bold;font-size:26px"><?php    echo $Remain[$k]?></span></td>
	                  <td class="A1101" width="50" >
				     <span style="font:bold;font-family:Arial">Stuck/Pcs.</span></td>
			      </tr>
	           </table>
           </td>
		 </tr>
	 </table></td>
</tr>
<tr><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr><td width="350"><span style="font:bold;font-family:Arial">ArtikeIbezeichnung/Item description</span></td></tr>
		<tr><td class="A1111" height="40"><span style="font:bold">&nbsp;<?php    echo $DescriptionArray[$k]?></span></td></tr>
        </table>
	</td>
</tr>
<tr ><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr ><td width="100"><span style="font:bold;font-family:Arial">Auftrags-Nr./<br>PO No.:</span></td>
		     <td width="30">&nbsp;</td>
		     <td><span style="font:bold;font-family:Arial">
			      &nbsp;&nbsp;Karton-Nr./<br>&nbsp;&nbsp;Carton No.:</span></td>
				   <td width="40" colspan="2">&nbsp;</td>
		</tr>
		<tr><td class="A1111" height="40">
		     &nbsp;<span style="font:bold;font-size:26px"><?php    echo $PONoArray[$k]?></span></td>
		     <td width="30">&nbsp;</td>
		     <td class="A1111" height="40" align="center">
			 &nbsp;<span style="font:bold;font-size:26px"><?php    echo $i?></span></td>
			 <td width="40">&nbsp;</td><td>&nbsp;</td>
		</tr>
      </table></td>
</tr>
<tr><td width="25">&nbsp;</td>
    <td><table border="0" style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0>
        <tr><td><span style="font:bold;font-size:16px;font-family:Arial">www.hama.com</span></td></tr>
        </table>
	</td>
</tr>
</TABLE>
</div>
<div id="head_right" style="margin-top:-5px">
<table border="0" width="180">
	  <tr><td><span style="font:bold;font-family:Arial">Anmerkung(en)/<br>Remark:</span></td></tr>
	  <tr><td height="100" class="A1111">&nbsp;</td></tr>
	  <tr><td align="right"><?php    echo $BoxCodeTable?></td></tr>
</table>
</div>
</div>
</div>

<?php   
   }//end if
 }//end for
?>
</body>
</html>