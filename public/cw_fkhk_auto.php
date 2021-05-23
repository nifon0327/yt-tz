<?php
include "../model/modelhead.php";
$TempY=$TempY==""?date("Y"):$TempY;
$RowHeight=38;
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	width:900px;
	text-align: center;
	font-size: 24px;
	line-height: 26px;
	}
</style>
<form action="" method="get" name="form1">
	<div id='BodyDiv'><?php  echo $TempY?>年每月扣款金额</div>
	<select name="TempY" onchange="javascript:document.form1.submit();">
    <?php 
	$checkY=mysql_query("SELECT left(Month,4) AS Y FROM $DataIn.cw1_fkoutsheet WHERE left(Month,4)>2013 GROUP BY left(Month,4) ORDER BY Month DESC",$link_id);
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
	</select>&nbsp;
	<select name="CompanyId" id="CompanyId" onchange="javascript:document.form1.submit();">
    <?php 
//自动获取每个月的其他扣款CompanyId:康成泰(USD)2416(1%).Q-勤百扬(USD)2620(5%)
      $Company=array("康成泰(USD)","勤百扬(USD)");
     $CompanyArray=array("2416","2620");
     $Rate=array("0.02","0.05","0.05");
      $CompanyCount=count($CompanyArray);
     $CompanyId=$CompanyId==""?$CompanyArray[0]:$CompanyId;
      for($i=0;$i<$CompanyCount;$i++){
            if($CompanyId==$CompanyArray[$i]){
                    echo"<option value='$CompanyArray[$i]' selected>$Company[$i]</option>";
                     $thisRate=$Rate[$i];
                  }
           else{
                    echo"<option value='$CompanyArray[$i]'>$Company[$i]</option>";
                 }
   }
    ?>
	</select><span class="redB">返款比率:<?php echo $thisRate?></span>
<table  width="900" cellspacing="0" cellpadding="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
<tr bgcolor="#33CCCC">
<td width="150" align="center" class="A1110" height="30">月份</td>
<td width="150" align="center" class="A1110" >请款金额</td>
<td width="150" align="center" class="A1110" >需扣金额</td>
<td width="150" align="center" class="A1110" >已扣金额</td>
<td width="150" align="center" class="A1110" >剩余扣款</td>
<td width="150" align="center" class="A1111" >扣款</td>
</tr>
<?php
    for($k=1;$k<13;$k++){
		$j=$k<10?"0".$k:$k;
		$TempM=$TempY."-".$j;
		$CheckhkResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount ,G.BuyerId
		FROM $DataIn.cw1_fkoutsheet S 
        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
		WHERE S.Month='$TempM' AND  S.CompanyId IN ($CompanyId)",$link_id));
        $thisAmount=sprintf("%.2f",$CheckhkResult["Amount"]);
        $kkAmount=sprintf("%.2f",$thisAmount*$thisRate);
        $thisBuyerId=$CheckhkResult["BuyerId"];
        $UpdateIMG="";$UpdateClick="";
        if($thisAmount>0){
                 $kkCheckResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS kkAmount
 	              FROM $DataIn.cw2_hksheet S WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$TempM' AND  S.CompanyId IN ($CompanyId)",$link_id));
                 $overkk=$kkCheckResult["kkAmount"]==""?0:$kkCheckResult["kkAmount"];
                 $nokk=$kkAmount-$overkk;
                   if($nokk>0){
                                $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
                                $UpdateClick="onclick='RegisterEstate(\"$CompanyId\",\"$TempM\",\"$kkAmount\",\"$thisRate\",\"$thisBuyerId\",this)'";
                           }
              }
       else{
               $thisAmount="&nbsp;";
                $kkAmount="&nbsp;";
                $overkk="&nbsp;";
                $nokk="&nbsp;";
               }
         echo "<tr><td class='A0111' height='25px' width='150'  align='center'>$TempM</td>
                     <td class='A0101'  width='150' align='center'>$thisAmount</td>
                     <td class='A0101'  width='150'  align='center'>$kkAmount</td>
                     <td class='A0101' width='150'  align='center'>$overkk</td>
                     <td class='A0101'  width='150'  align='center'>$nokk</td>
                    <td class='A0101'  width='150'  align='center' $UpdateClick>$UpdateIMG</td></tr>";
     }
?>
</table>
</form>
<script>
function RegisterEstate(CompanyId,chooseMonth,SumAmount,thisRate,thisBuyerId,e){
var msgStr=chooseMonth+" 本次扣款金额为:"+SumAmount+"\n请确认!";
  if(confirm(msgStr)) {
	    var url="cw_fkhk_auto_ajax.php?CompanyId="+CompanyId+"&chooseMonth="+chooseMonth+"&SumAmount="+SumAmount+"&thisRate="+thisRate+"&thisBuyerId="+thisBuyerId+"&ActionId=2";
	    var ajax=InitAjax();
　	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                if(ajax.responseText=="Y"){
			        //更新该单元格底色和内容
                     document.form1.submit();
                  }
			 else{
			          alert ("确认失败！"); 
			         }
		    	}
		}
　	ajax.send(null);
      }
}
</script>