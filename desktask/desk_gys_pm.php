<?php 
/*电信---yang 20120801
未更新
$DataPublic.staffmain

$DataPublic.jobdata
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 供应商货款月排名统计");//需处理
$TempY=$TempY==""?date("Y"):$TempY;
$Pm=$Pm==""?15:$Pm;
$CelWidth=80;
$CelSumWidth=$CelWidth*14+14;
$RowHeight=38;
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	width:<?php  echo $CelSumWidth?>px;
	text-align: center;
	font-size: 26px;
	line-height: 26px;
	}
#RecordCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	width:<?php  echo $CelWidth?>px;
	font-size: 12px;
	}
#NoteCel{
	margin:0px;
	padding:0px;
	float:left;
	margin-top:-1px;
	POSITION: relative;
	width:<?php  echo $CelWidth?>px;
	height:<?php  echo $RowHeight?>px;
	line-height: <?php  echo $RowHeight?>px;
	text-align: center;
	margin-left:0px;
	border: 1px solid #000000;
	}
#NoteCel ul{
	margin:0px;
	padding:0px;
	float:left;
	POSITION: relative;
	width:98%;
	height:<?php  echo $RowHeight/2?>px;
	line-height: <?php  echo $RowHeight/2?>px;
	text-align: right;
	overflow: hidden;
	}
#NoteCel ul.Amount{
	color: #999999;
	}
	
#NoteCel ul.Amount1{
	color: #FE8800;
	}

#NoteCel ul.Amount2{
	color: #FF0000;
	}
		
.BGColor1
{
  background-color: #FFFFFF;
} 

.BGColor2
{
  background-color: #DDDDDD;
} 

-->
</style>
<form action="" method="get" name="form1">
	<input name="Pm" type="hidden" id="Pm" value="<?php  echo $Pm?>">
	<div id='BodyDiv'><?php  echo $TempY?>年每月请款金额前<?php  echo $Pm?>位</div>
	<select name="TempY" onchange="javascript:document.form1.submit();">
    <?php 
	$checkY=mysql_query("SELECT left(Month,4) AS Y FROM $DataIn.cw1_fkoutsheet WHERE left(Month,4)>2008 GROUP BY left(Month,4) ORDER BY Month DESC",$link_id);
	if($checkR=mysql_fetch_array($checkY)){
		do{
			$theY=$checkR["Y"];
			if($TempY==$theY){
				echo"<option value='$theY' selected>$theY 年</option>";
				}
			else{
				echo"<option value='$theY'>$theY 年</option>";
				}
			}while($checkR=mysql_fetch_array($checkY));
		}
    ?>
	</select>
  
	<div id='BodyDiv'>
  		<div id="RecordCel">
			<div id="NoteCel" class='BGColor2'>排位</div>
	  		<?php 
	  		for($i=1;$i<=$Pm;$i++){
				$rowType=($i+1)%2==0?"class='BGColor1'":" class='BGColor2' ";
				echo "<div id='NoteCel' $rowType>$i</div>";
				}
			?>
		</div>
		<?php 
		for($i=1;$i<13;$i++){
			$j=$i<10?"0".$i:$i;
			$TempM=$TempY."-".$j;
			$SqlStr="
			SELECT SUM(F.Amount*C.Rate) AS Amount,P.Forshort
			FROM $DataIn.cw1_fkoutsheet F 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=F.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			WHERE F.Month='$TempM' group by F.CompanyId ORDER BY Amount Desc LIMIT 0,$Pm";
			$checkPmSql=mysql_query("$SqlStr",$link_id);
			echo"<div id='RecordCel'><div id='NoteCel' class='BGColor2'>$i 月</div>";
			$n=0;
			if($checkPm_Row=mysql_fetch_array($checkPmSql)){
				do{
					$Amount=number_format(sprintf("%.0f",$checkPm_Row["Amount"]));
					$Forshort=$checkPm_Row["Forshort"];
					$rowType=$n%2==0?" class='BGColor1' ":" class='BGColor2' ";
					if ($checkPm_Row["Amount"]>=500000) 
					{
						if  ($checkPm_Row["Amount"]>=1000000)  $AmountClass="Amount2"; else  $AmountClass="Amount1";
					}
					else{
						$AmountClass="Amount";
					}
					echo"<div id='NoteCel' $rowType><ul>$Forshort</ul><ul class='$AmountClass'>$Amount</ul></div>";
					$n++;
					}while($checkPm_Row=mysql_fetch_array($checkPmSql));
				}
			for($k=$n;$k<$Pm;$k++){
				$rowType=$k%2==0?" class='BGColor1' ":" class='BGColor2' ";
				echo"<div id='NoteCel' $rowType>&nbsp;</div>";
				}
			echo"</div>";
			}

//输出年度排名
$checkYpmSql=mysql_query("
SELECT SUM(F.Amount*C.Rate) AS Amount,P.Forshort
	FROM $DataIn.cw1_fkoutsheet F 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=F.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
	WHERE left(F.Month,4)='$TempY' group by F.CompanyId ORDER BY Amount Desc LIMIT 0,$Pm
",$link_id);
echo"<div id='RecordCel'><div id='NoteCel'  class='BGColor2' >年度</div>";
if($checkYpmRow=mysql_fetch_array($checkYpmSql)){
	$n=0;
	do{
		$Amount=number_format(sprintf("%.0f",$checkYpmRow["Amount"]));
		$Forshort=$checkYpmRow["Forshort"];
		$rowType=$n%2==0?" class='BGColor1' ":" class='BGColor2' ";
		echo"<div id='NoteCel' $rowType><ul>$Forshort</ul><ul class='Amount'>$Amount</ul></div>";
		$n++;
		}while($checkYpmRow=mysql_fetch_array($checkYpmSql));

	}
//分隔
echo"<div>&nbsp;</div>";
echo"</div></div>";//end <div id='BodyDiv'>


//输出年度月总计
$checkAllSql=mysql_query("SELECT SUM(F.Amount*.R.Rate) AS Amount,right(F.Month,2) AS Month 
FROM $DataIn.cw1_fkoutsheet F
LEFT JOIN $DataIn.trade_object C ON F.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.currencydata R ON R.Id=C.Currency
WHERE left(F.Month,4)='$TempY' GROUP BY F.Month ORDER BY F.Month",$link_id);
$rowType=" class='BGColor2' ";
echo"<div id='BodyDiv'>
	<div id='RecordCel'><div id='NoteCel' $rowType>总计(RMB)</div></div>";
if($checkALLRow=mysql_fetch_array($checkAllSql)){
	$i=1;
	$YearAmount=0;
	do{
		$Month=$checkALLRow["Month"]*1;
		$Amount=$checkALLRow["Amount"];
		$YearAmount+=$Amount;
		$Amount=number_format(sprintf("%.0f",$Amount));
		if($i!=$Month){
			echo"<div id='RecordCel'><div id='NoteCel' $rowType>&nbsp;</div></div>";
			}
		echo"<div id='RecordCel'><div id='NoteCel' $rowType>$Amount</div></div>";
		$i=$Month+1;
		}while ($checkALLRow=mysql_fetch_array($checkAllSql));
	$YearAmount=number_format(sprintf("%.0f",$YearAmount));
	}
for($j=$i;$j<13;$j++){
	echo"<div id='RecordCel'><div id='NoteCel' $rowType>&nbsp;</div></div>";
	}
echo"<div id='RecordCel'><div id='NoteCel' $rowType>$YearAmount</div></div>";
?>
</form>
