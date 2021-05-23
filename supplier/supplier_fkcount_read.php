<?php 
include "../model/modelhead.php";
echo "<SCRIPT src='supplier.js' type=text/javascript></script>";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
ChangeWtitle("$SubCompany 货款结付情况");

?>
<style type="text/css">
body{
	background: #fff;
}
.BgColorSet{
	background: #bbb;
}
.th{
	height: 25px;
	line-height: 25px;
	vertical-align: middle;
}
</style>

 <table  width="1170" border="0" align="center" cellspacing="0" style='margin:10 10 10 10;'>
         <tr><td colspan='15' style='height:40px;font-size:18px;font-weight:bold;' align="center">货款结付统计表</td></tr>
		 <tr class='BgColorSet'>
            <td width="80" scope="col" align="right" class="A1110"  height="30">年度 \</td>
            <td  width="50" align="left" scope="col" class="A1101">月份</td>
            <td  width="80" align="center" scope="col" class="A1101" >1月</td>
		    <td width="80"  align="center" scope="col" class="A1101">2月</td>
		    <td width="80"  align="center" scope="col" class="A1101">3月</td>
		    <td width="80"  align="center" scope="col" class="A1101">4月</td>
		    <td width="80"  align="center" scope="col" class="A1101">5月</td>
		    <td width="80"  align="center" scope="col" class="A1101">6月</td>
		    <td width="80"  align="center" scope="col" class="A1101">7月</td>
		    <td width="80"  align="center" scope="col" class="A1101">8月</td>
		    <td width="80"  align="center" scope="col" class="A1101">9月</td>
		    <td width="80"  align="center" scope="col" class="A1101">10月</td>
		    <td width="80"  align="center" scope="col" class="A1101">11月</td>
		    <td width="80"  align="center" scope="col" class="A1101">12月</td>
	        <td width="80"  align="center" scope="col" class="A1101">小计</td>
		 </tr>


<?

//起始交易年 与终止交易年
/*
		$MonthRow=mysql_fetch_array(mysql_query("
			SELECT  MAX(EndY) AS EndY,MIN(StartY) AS StartY FROM ( 
				SELECT MAX(DATE_FORMAT(M.Date,'%Y')) AS EndY,MIN(DATE_FORMAT(M.Date,'%Y')) AS StartY FROM $DataIn.cg1_stockmain M WHERE M.CompanyId='$myCompanyId'
				UNION ALL 
				SELECT MAX(DATE_FORMAT(M.Date,'%Y')) AS EndY,MIN(DATE_FORMAT(M.Date,'%Y')) AS StartY FROM $DataIn.cg1_stockmain M WHERE M.CompanyId='$myCompanyId'
			)A
			",$link_id));
*/	
		$MonthRow=mysql_fetch_array(mysql_query("
			SELECT  MAX(EndY) AS EndY,MIN(StartY) AS StartY FROM ( 
				SELECT MAX(DATE_FORMAT(M.Date,'%Y')) AS EndY,MIN(DATE_FORMAT(M.Date,'%Y')) AS StartY FROM $DataIn.cg1_stockmain M WHERE M.CompanyId='$myCompanyId'
				UNION ALL 
				SELECT MAX(LEFT(M.Month,4)) AS EndY,MIN(LEFT(M.Month,4)) AS StartY FROM $DataIn.cw1_fkoutsheet M WHERE M.CompanyId='$myCompanyId' AND M.Month!=''
			)A
			",$link_id));

		$StartY=$MonthRow["StartY"];
		$EndY=$MonthRow["EndY"];

$StartY=$StartY==""?2008:$StartY;
$EndY=$EndY==""?date("Y"):$EndY;
$n=1;
for($i=$EndY;$i>=$StartY;$i--){
        $bgcolor=$n%2==0?"#eee":"#fff";
        echo "<tr style='background:$bgcolor;'><td scope='col' align='center'  class='A0111' height='40'><b>$i 年</b></td>";
           echo "<td  class='A0101' align='center'><div>应付</div><div  class='greenB th'>已付</div><div  class='redB'>未付</div></td>";
        $n++;
        
		$Sum_Amount=0;$Sum_PayAmount=0;$Sum_NoAmount=0;
		
		for($j=1;$j<13;$j++){
		    $d=$j<10?"0".$j:$j;$Month=$i."-".$d;
			$Amount=0;$PayAmount=0;$NoAmount=0;
	       //未付货款
	       //DATE_FORMAT(M.Date,'%Y-%m')='$Month'
			$NoPaySql=mysql_query("SELECT  SUM(ROUND((S.AddQty+S.FactualQty)*S.Price,2)) as Amount 
			FROM  $DataIn.cw1_fkoutsheet S 
			WHERE S.CompanyId='$myCompanyId' AND S.Estate ='3'  AND S.Month='$Month' ",$link_id);
           if($NoPayRows = mysql_fetch_array($NoPaySql)){
                  $NoAmount=$NoPayRows["Amount"];
           }
           
           //已付货款
           // DATE_FORMAT(M.Date,'%Y-%m')
           $PaySql=mysql_query("SELECT  SUM(ROUND((S.AddQty+S.FactualQty)*S.Price,2)) as Amount 
			FROM  $DataIn.cw1_fkoutsheet S 
			WHERE S.CompanyId='$myCompanyId' AND S.Estate ='0'  AND S.Month='$Month'",$link_id);
           if($PayRows = mysql_fetch_array($PaySql)){
                  $PayAmount=$PayRows["Amount"];
           }           
           $Amount=$PayAmount+$NoAmount;
           $Sum_Amount+=$Amount;
           $Sum_PayAmount+=$PayAmount;
           $Sum_NoAmount+=$NoAmount;

		   $TempId="$Month|1";
	       	$onClick1=$Amount==0?"":"onclick='SandH(\"a\",\"\",this,\"$TempId\",\"supplier_fkcount\");'";
	       	
	       	$TempId="$Month|2";
	       	$onClick2=$PayAmount==0?"":"onclick='SandH(\"a\",\"\",this,\"$TempId\",\"supplier_fkcount\");'";
	       	
	       	$TempId="$Month|3";
	       	$onClick3=$NoAmount==0?"":"onclick='SandH(\"a\",\"\",this,\"$TempId\",\"supplier_fkcount\");'";
	       	
	       	$Amount=zerotospace(number_format($Amount,2));
	       $PayAmount=zerotospace(number_format($PayAmount,2));
	       $NoAmount=zerotospace(number_format($NoAmount,2)); 
           echo "<td scope='col' class='A0101' align='right'><div $onClick1>$Amount</div><div  class='greenB th' $onClick2>$PayAmount</div><div  class='redB' $onClick3>$NoAmount</div></td>";
		}
		   $Sum_Amount=zerotospace(number_format($Sum_Amount,2));
	       $Sum_PayAmount=zerotospace(number_format($Sum_PayAmount,2));
	       $Sum_NoAmount=zerotospace(number_format($Sum_NoAmount,2)); 
	       
		   echo "<td scope='col' class='A0101' align='right'>$Sum_Amount<div  class='greenB th'>$Sum_PayAmount</div><div  class='redB'>$Sum_NoAmount</div></td></tr> ";
}
 echo "</table>";
 
 echo"<table width='1020' cellspacing='0' border='0' id='HideTable_a' style='display:none;margin:10 10 10 10;'>
				<tr>
					<td height='30'>
					<div id='HideDiv_a' width='1020' align='center'>&nbsp;</div>
					</td>
	</tr></table>";
?>


