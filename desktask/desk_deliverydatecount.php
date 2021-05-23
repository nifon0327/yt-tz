<?php   
/*
MC、DP共享代码电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 生产统计");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1050;
$i=1;
//取得计算工价的时薪$OneHourSalaryt
include "../model/subprogram/onehoursalary.php";
?>
<script>
function SandH(divNum,RowId,TempId,ToPage,Action){
	var e=eval("HideTable_"+divNum+RowId);
	if(Action==0)e.style.display=(e.style.display=="none")?"":"none";
	else e.style.display=="";
	if (e.style.display==""){
		
		if(TempId!=""){			
			var url="../desktask/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId+"&Action="+Action;
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
			
		}
	}
</script>
<body>
<form name="form1" method="post" action="">
<table width="<?php echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24">&nbsp;</td>
    </tr>
<tr><td>
<select id='checkMonth' name="checkMonth" onchange="document.form1.submit()">
<?php
$NowMonth=date("m");
$NowYear=date("Y");
for ($i=0;$i<6;$i++){
       $TempMonth=$NowMonth-$i;
       if($TempMonth>0){$TempMonth=$TempMonth;$TempYear=$NowYear;}
       else {
                $TempMonth=$TempMonth+12;
                $TempYear=$NowYear-1;
               }
       if($TempMonth<10)$TempMonth="0".$TempMonth;
       $EechMonth=$TempYear."-".$TempMonth;
       $checkMonth=$checkMonth==""?date("Y-m"):$checkMonth;
       if($EechMonth==$checkMonth){
               echo "<option value='$EechMonth' selected>$EechMonth</option>";
              }
       else {
               echo "<option value='$EechMonth'>$EechMonth</option>";
               }

     }
?>
</select>
</td></tr>
<tr><td height="5">&nbsp;</td></tr>
</table>
<div>
<?php 
   $getArrayDate=array();
    include "desk_deliverydateCalendar.php";
    $Days=date("t",strtotime("$checkMonth"));
    for($i=1;$i<=$Days;$i++){
          if($i<10)$tempi="0".$i;
          else $tempi=$i;
          $everyDay=$checkMonth."-".$tempi;
          $ScResult=mysql_query("SELECT SUM(S.Qty) AS Qty,T.TypeId,T.TypeName FROM $DataIn.sc1_cjtj S
	      LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
          WHERE 1 AND DATE_FORMAT(S.Date,'%Y-%m-%d')='$everyDay' AND T.mainType=3 AND T.TypeId NOT IN ('7090','7070')  GROUP BY T.TypeId",$link_id);
          $j=0;
          while($scRow=mysql_fetch_array($ScResult)){
                  $dayQty=$scRow["Qty"];
                  $TypeId=$scRow["TypeId"];
                  $TypeName=$scRow["TypeName"];
                  $getArrayDate[$i][$j]=array(0=>$dayQty,1=> $TypeId,2=>$TypeName);
                  $j++;
                 }
       } 
    //$cal= new TechCalendarForm(2013,01);
    $dateArray=explode("-",$checkMonth);
    $getYear=$dateArray[0];
    $getMonth=$dateArray[1];
    //print_r($getArrayDate);
    $cal= new CalendarForm($getYear,$getMonth,$getArrayDate);
    $cal->showCodeMonth();
?>
</div>
<table width="<?php    echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24">&nbsp;</td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="30" width="60" align="center">序号</td>
	<td class="A1101" width="100" align="center">生产部门</td>
    <td class="A1101" width="200" align="center">部门员工数</td>
    <td class="A1101" width="100" align="center">预估产值(元/天)</td>
    <td class="A1101" width="100" align="center">未生产总值(元)</td>
    <td class="A1101" width="100" align="center">生产工期(天)</td>
  </tr>

<?php   
//条件：有下单给供应商 未传图片 配件可用 分类9000以上 采购在职？AND M.Estate>0
$ShipResult = mysql_query("SELECT SUM(G.OrderQty*G.Price) AS Amonut,T.TypeId,T.TypeName
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1 AND S.Estate=1 AND T.mainType=3 AND T.TypeId NOT IN ('7090','7070') GROUP BY T.TypeId ORDER BY T.TypeId",$link_id);
$xqAmonutSUM=0;
$ygAmountSUM=0;
$Num1SUM=0;
$Num2SUM=0;
$Num3SUM=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	$scAmount=0;
	do{
		$xqAmonut=sprintf("%.0f",$ShipRow["Amonut"]);
		
		$TypeId=$ShipRow["TypeId"];
		$TypeName=$ShipRow["TypeName"];
		//计算未出已生产
		$checkOverSql=mysql_fetch_array(mysql_query("
		SELECT IFNULL(SUM(S.Qty*C.Price),0) AS scAmount 
			FROM $DataIn.yw1_ordersheet Y,$DataIn.sc1_cjtj S,$DataIn.cg1_stocksheet C,$DataIn.stuffdata D 
			WHERE 1 AND S.TypeId='$TypeId' AND Y.Estate=1 AND Y.POrderId=S.POrderId AND C.POrderId=Y.POrderId AND D.StuffId=C.StuffId AND D.TypeId=S.TypeId
		",$link_id));
		$scAmount=sprintf("%.0f",$checkOverSql["scAmount"]);
		$xqAmonut-=$scAmount;
		$xqAmonutSUM+=$xqAmonut;
                
        $Num1=0;
		$Num2=0;
		$Num3=0;
		$ygAmount=0;
		//计算该分类的生产员工数量
		$checkStaffSql=mysql_query("SELECT count(*) AS Nums,S.KqSign 
		FROM $DataIn.staffgroup G 
		LEFT JOIN $DataPublic.staffmain S ON G.GroupId=S.GroupId
		WHERE G.TypeId='$TypeId' AND S.Estate=1 AND S.cSign='$Login_cSign' GROUP BY S.KqSign
		",$link_id);
               
		if($checkStaffRow=mysql_fetch_array($checkStaffSql)){
			
			do{
				$KqSign=$checkStaffRow["KqSign"];
				$Nums=$checkStaffRow["Nums"];
				if($KqSign==1){
					$Num1=$Nums;
					}
				else{
					$Num2+=$Nums;
					}
				}while($checkStaffRow=mysql_fetch_array($checkStaffSql));
			$Num3=$Num1+$Num2;
			$ygAmount=$Num3*10*$OneHourSalaryt;
			$ygAmountSUM+=$ygAmount;
			$Num1SUM+=$Num1;
			$Num2SUM+=$Num2;
			$Num3SUM+=$Num3;
			}
		//生产用时计算
		if ($ygAmount!=0)
		{
			$Days=ceil($xqAmonut/$ygAmount);
		} else
		   {
			   $Days=0;
		   }   
		if($Days<30){
			$DaysSTR="<span class=\"purpleB\">$Days</span>";
			if($Days<20){
				$DaysSTR="<span class=\"redB\">$Days</span>";
				}
			}
		else{
			$DaysSTR=$Days;
			}
	//传递
		$DivNum="a".$i;
		$TempId=$TypeId;
			$HideTableHTML="
			<tr id='HideTable_$DivNum$i' style='display:none' bgcolor='#B7B7B7'>
					<td class='A0111'  colspan=\"6\">
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>";


		echo"<tr><td class='A0111' align='center' height='25'>$i</td>";
		echo"<td class='A0101' onClick='SandH(\"$DivNum\",$i,\"$TempId\",\"desk_deliverydatecount_a1\",0);' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' alt='显示或隐藏下级资料. ' style='CURSOR: pointer;'><span class=\"yellowN\">$TypeName</sapn></td>";
		echo"<td class='A0101' align='right'><span style='color:#cccccc'>(不考勤$Num2,考勤$Num1)</span>$Num3&nbsp;</td>";//员工数
		echo"<td class='A0101' align='right'>".number_format($ygAmount)."&nbsp;</td>";//预估产值
		echo"<td class='A0101' align='right'>".number_format($xqAmonut)."&nbsp;</td>";//未出总需求
		echo"<td class='A0101' align='right'>$DaysSTR&nbsp;</td><tr>";//生产用时
		echo $HideTableHTML;		
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
	if ($ygAmountSUM!=0){
		$DaysSUM=ceil($xqAmonutSUM/$ygAmountSUM);
	}
	else{
		$DaysSUM=0;
	}

		if($DaysSUM<30){
			$DaysSUMSTR="<span class=\"purpleB\">$DaysSUM</span>";
			if($DaysSUM<20){
				$DaysSUMSTR="<span class=\"redB\">$DaysSUM</span>";
				}
			}
		else{
			$DaysSUMSTR=$DaysSUM;
			}

echo"<tr class=''><td class='A0110' align='center' height='25'>&nbsp;</td>";
echo"<td class='A0101'>总计</td>";
echo"<td class='A0101' align='right'><span style='color:#ffffff'>(不考勤$Num2SUM,考勤$Num1SUM)</span>$Num3SUM&nbsp;</td>";//员工数
echo"<td class='A0101' align='right'>".number_format($ygAmountSUM)."&nbsp;</td>";//预估产值
echo"<td class='A0101' align='right'>".number_format($xqAmonutSUM)."&nbsp;</td>";//未出总需求
echo"<td class='A0101' align='right'>$DaysSUMSTR&nbsp;</td><tr>";//生产用时
?>
  <tr>
    <td height="30" colspan="6" class="A0111">
	注：按工作日每人工作 10 小时，工价 <?php    echo $OneHourSalaryt?> 元/小时计算
	</td>
  </tr>
</table>
</html>
<script language="javascript">
function OrderSort(Action,TypeId,RowId){
     var DivNum="a"+RowId;
     SandH(DivNum,RowId,TypeId,'desk_deliverydatecount_a1',Action);
}

function ShowQty(TypeId,chooseDay){
	document.form1.action="../public/Sc_cjtj_read.php?chooseDay="+chooseDay+"&TypeId="+TypeId;
	document.form1.target="_blank";
	document.form1.submit();		
	document.form1.target="_self";
	document.form1.action="";
}
</script>