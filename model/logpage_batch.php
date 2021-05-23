<?php 
if ($MySQLiEnabled==1) {
	$mysqli->close();
}

if ($MyPDOEnabled==1) {
    if ($SaveOprationLog){
        foreach (  $logInfoArray as $logInfo) {
            $IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('".$logInfo['DateTime']."','".$logInfo['Log_Item']."','".$logInfo['Log_Funtion']."','".$logInfo['Log']."','".$logInfo['OperationResult']."','".$logInfo['Operator']."')";
            $IN_res = $myPDO->exec($IN_recode);
        }
    }
	$myPDO=null;
}

$tableMenuS=500;
$tableWidth=850;
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<?php
include "../model/characterset.php";
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<META   HTTP-EQUIV="Pragma"   CONTENT="no-cache">   
  <META   HTTP-EQUIV="Cache-Control"  CONTENT="no-cache">   
  <META   HTTP-EQUIV="Expires"   CONTENT="0">   
</head>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false"   onhelp="return false;">
<p>&nbsp;</p>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
			<p>&nbsp;</p>
			<table width="88%" height="213" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="124" height="203" valign="top" >
					<div align="right">操作日志：</div>
				</td>
				<td width="550" valign="top" marquee class="A1111">
				<div style="width:550;height:450;overflow-y:auto">
				<?php
                foreach ($logInfoArray as $logInfo ){
				echo $logInfo['Login_P_Number']."于".$logInfo['DateTime']."进行 ".$logInfo['Log_Item']." - ".$logInfo['Log_Funtion']." 的操作，操作结果如下：<br>".$logInfo['Log']."<br/>";
                }
				?>			
				</div>
				</td>
			  </tr>
		</table>
</td></tr></table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td class="timeBottom" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<span onClick="javascript:ComeBack('<?php  echo $fromWebPage?>','<?php  echo $ALType?>');" <?php  echo $onClickCSS?>>返回</span> 
					</nobr>					
				</td>
			</tr>
	 </table>
   </td>
   <td class="A0100">&nbsp;</td>
  </tr>
</table>
</body>
</html>