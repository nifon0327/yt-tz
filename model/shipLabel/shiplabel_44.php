<?php 

/*
//CG出CEL
//echo "FromCounty:$FromCounty";
$Field=explode("|",$FromCounty);  //把括号中的提取出来
$Count=count($Field);
if($Count==2){
	$ClientTitle=$Field[0];
	$ClientItemNO=$Field[1];	

}
$BoxSpec=explode("CM",$BoxSpec); 
$BoxSpec=$BoxSpec[0];

$tempcName =str_replace("&","",$cName);
$markstr = substr($tempcName,0,2);
if($markstr =="SB" || $markstr =="MW" || $markstr =="SK"  || $markstr =="VL"){
   $markstr=$markstr;
}
else{
    $markstr ="";
}
*/
echo"	<table  width='570' height='305' border='0' cellSpacing=0 cellPadding=0 style='WORD-WRAP: break-word;font-size:14px;'>
	       <tr>
           		<td width='360' height='55'  align='left' class='A1110' style='font-size:40px;font-weight:bold'>&nbsp;Asiaxess Limited </td>
                <td width='' class='A1111' style='font-size:15px; '>&nbsp;$eCode</td>
             </tr>
              <tr>
           		<td class='A0010' valign='top'>
                <table  width='100%' height='100%' border='0' cellSpacing=0 cellPadding=0 style='WORD-WRAP: break-word;font-size:14px;'>
                	<tr>
                    <td  height='55'  align='left'  style='font-size:40px;font-weight:bold'>&nbsp;BOX $i OF $BoxTotal </td>
                    </tr>
                	<tr>
                    <td  align='left' height='25' style='font-size:20px; '>&nbsp;&nbsp;PO:$OrderPO</td>
                    </tr>
                	<tr>
                    <td height=''  > &nbsp; </td>
                    </tr>
                	<tr>
                    <td height='75' style='font-size:32px;font-weight:bold'>&nbsp;AY0534_EO - Cartons:$BoxTotal <br />&nbsp;QD Cellular(Pty) Ltd</td>
                    </tr>
                                                                                
                </table>
                
                </td>
                <td class='A0011' align='center' style='font-size:20px; font-weight:bold'><img src='../model/shipLabel/asiaxessQ.png'  height='180' /></td>
             </tr>
         </table>";
//echo "</div>";
?>