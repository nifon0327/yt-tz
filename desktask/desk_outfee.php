<?php
//************yang/2013-01-09
include "../model/modelhead.php";
$Terms="WHERE 1 AND M.Estate=3"; //条件：处于审核通过但未结付的状态
$TempMonth=" GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY DATE_FORMAT(M.Date,'%Y-%m')";
include "outfee/desk_outfee_total.php";
$checkCurrency=mysql_query("SELECT Id,Symbol,Rate FROM $DataPublic.currencydata WHERE  Id>1 and (Estate=1 OR Id=4) ORDER BY Id",$link_id);
if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
	do{
		$TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
		$$TempRate=$checkCurrencyRow["Rate"];	
		
		$TempRate2="Rate_" .$checkCurrencyRow["Id"] ; 
		$$TempRate2=$checkCurrencyRow["Rate"];
		}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
	}
$total_1=0;$total_2=0;$total_3=0;$total_4=0;
$Amount=array();$SumAmount=array();
//******************************1供应商货款
//有请款未填月份的记录
$Result_1=mysql_query("SELECT IFNULL(SUM((M.AddQty+M.FactualQty)*M.Price*C.Rate),0) AS Amount,'1' AS Number,'供应商货款' AS ItemName,M.Month 
FROM $DataIn.cw1_fkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms  GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row1=mysql_fetch_array($Result_1)){
   $j=0;
   do{
         $Number=$Row1["Number"];
         $Month=$Row1["Month"]==""?0:$Row1["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row1["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row1["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row1=mysql_fetch_array($Result_1));
}
else{
        $ItemName[1]="供应商货款";
       }

//******************************2佣金
$Result_2=mysql_query("SELECT IFNULL(SUM(M.FactualQty*M.Price*C.Rate),0) AS Amount,'2' AS Number,'佣金' AS ItemName,M.Month 
FROM $DataIn.cw1_tkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms  GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row2=mysql_fetch_array($Result_2)){
   $j=0;
   do{
         $Number=$Row2["Number"];
         $Month=$Row2["Month"]==""?0:$Row2["Month"];
		 $ItemName[$Number]=$Row2["ItemName"];
         $subMonth[$Number][$j]=$Month;
         $Amount[$Number][$Month]=sprintf("%.2f",$Row2["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row2=mysql_fetch_array($Result_2));
}
else{
        $ItemName[2]="佣金";
       }

//******************************3未付订金
$Result_3=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,	'3' AS Number,'未付订金' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cw2_fkdjsheet M 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  $Terms AND M.Did=0 $TempMonth",$link_id);
if($Row3=mysql_fetch_array($Result_3)){
   $j=0;
   do{
         $Number=$Row3["Number"];
         $Month=$Row3["Month"]==""?0:$Row3["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row3["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row3["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row3=mysql_fetch_array($Result_3));
}
else{
        $ItemName[3]="未付订金";
       }


//******************************4未抵货款的订金
$Result_4=mysql_query("SELECT IFNULL(SUM(-M.Amount*C.Rate),0) AS Amount,'4' AS Number,'未抵货款的订金' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cw2_fkdjsheet M 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND M.Did=0 AND (M.Estate=0 OR M.Estate=3) $TempMonth",$link_id);
if($Row4=mysql_fetch_array($Result_4)){
   $j=0;
   do{
         $Number=$Row4["Number"];
         $Month=$Row4["Month"]==""?0:$Row4["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row4["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row4["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row4=mysql_fetch_array($Result_4));
}
else{
        $ItemName[4]="未抵货款的订金";
       }

//******************************5扣供应商货款
$Result_5=mysql_query("SELECT IFNULL(SUM(-T.Amount*C.Rate),0) AS Amount ,'5' AS Number,'扣供应商货款' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cw15_gyskksheet T 
LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND M.Estate=0 AND T.Kid=0 $TempMonth",$link_id);
if($Row5=mysql_fetch_array($Result_5)){
   $j=0;
   do{
         $Number=$Row5["Number"];
         $Month=$Row5["Month"]==""?0:$Row5["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row5["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row5["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row5=mysql_fetch_array($Result_5));
}
else{
        $ItemName[5]="扣供应商货款";
       }
//******************************6供应商税款
$Result_6=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'6' AS Number,'供应商税款' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cw2_gyssksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms $TempMonth",$link_id);
if($Row6=mysql_fetch_array($Result_6)){
   $j=0;
   do{
         $Number=$Row6["Number"];
         $Month=$Row6["Month"]==""?0:$Row6["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row6["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row6["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row6=mysql_fetch_array($Result_6));
}
else $ItemName[6]="供应商税款";

//******************************7未结付退税收益
$Result_7=mysql_query("SELECT IFNULL(SUM(-M.Taxamount ),0) AS Amount,'7' AS Number,'未结付退税收益' AS ItemName,DATE_FORMAT(M.Taxdate,'%Y-%m') AS Month 
FROM $DataIn.cw14_mdtaxmain M 
WHERE 1 AND M.Estate=3 GROUP BY DATE_FORMAT(M.Taxdate,'%Y-%m') ORDER BY DATE_FORMAT(M.Taxdate,'%Y-%m')",$link_id);
if($Row7=mysql_fetch_array($Result_7)){
   $j=0;
   do{
         $Number=$Row7["Number"];
         $Month=$Row7["Month"]==""?0:$Row7["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row7["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row7["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row7=mysql_fetch_array($Result_7));
}
else $ItemName[7]="未结付退税收益";

//******************************8开发费用
$Result_8=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,'8' AS Number,'开发费用' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cwdyfsheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms $TempMonth",$link_id);
if($Row8=mysql_fetch_array($Result_8)){
   $j=0;
   do{
         $Number=$Row8["Number"];
         $Month=$Row8["Month"]==""?0:$Row8["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row8["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row8["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row8=mysql_fetch_array($Result_8));
}
else $ItemName[8]="开发费用";




//******************************9总务采购费用

$Result_9=mysql_query("SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount,'9' AS Number,'总务采购费用' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month  
FROM $DataIn.zw3_purchases M $Terms $TempMonth",$link_id);
if($Row9=mysql_fetch_array($Result_9)){
   $j=0;
   do{
         $Number=$Row9["Number"];
         $Month=$Row9["Month"]==""?0:$Row9["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row9["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row9["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row9=mysql_fetch_array($Result_9));
}
else $ItemName[9]="总务采购费用";


//******************************10行政费用
$Result_10=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'10' AS Number,'行政费用' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.hzqksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms $TempMonth",$link_id);
if($Row10=mysql_fetch_array($Result_10)){
   $j=0;
   do{
         $Number=$Row10["Number"];
         $Month=$Row10["Month"]==""?0:$Row10["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row10["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row10["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row10=mysql_fetch_array($Result_10));
}
else $ItemName[10]="行政费用";

//******************************11员工薪资
$Result_11=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'11' AS Number,'员工薪资' AS ItemName ,M.Month
FROM $DataIn.cwxzsheet M $Terms GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row11=mysql_fetch_array($Result_11)){
   $j=0;
   do{
         $Number=$Row11["Number"];
         $Month=$Row11["Month"]==""?0:$Row11["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row11["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row11["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row11=mysql_fetch_array($Result_11));
}
else $ItemName[11]="员工薪资";

//******************************12假日加班费：2013-05之前有效，之后就合并到薪资中
$Result_12=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'12' AS Number,'假日加班费' AS ItemName,M.Month
FROM $DataIn.hdjbsheet M $Terms AND Month<'2013-05' GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row12=mysql_fetch_array($Result_12)){
   $j=0;
   do{
         $Number=$Row12["Number"];
         $Month=$Row12["Month"]==""?0:$Row12["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row12["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row12["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row12=mysql_fetch_array($Result_12));
}
else $ItemName[12]="假日加班费";

//******************************13社保费用
$Result_13=mysql_query("SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'13' AS Number,'社保费用' AS ItemName,M.Month 
FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=1 GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row13=mysql_fetch_array($Result_13)){
   $j=0;
   do{
         $Number=$Row13["Number"];
         $Month=$Row13["Month"]==""?0:$Row13["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row13["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row13["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row13=mysql_fetch_array($Result_13));
}
else {
        $ItemName[13]="社保费用";
      }

//******************************14节日奖金
$Result_14=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'14' AS Number,'节日奖金' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.cw11_jjsheet M $Terms $TempMonth",$link_id);
if($Row14=mysql_fetch_array($Result_14)){
   $j=0;
   do{
         $Number=$Row14["Number"];
         $Month=$Row14["Month"]==""?0:$Row14["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row14["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row14["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row14=mysql_fetch_array($Result_14));
}
else{
        $ItemName[14]="节日奖金";
       }

//******************************15Forward费用
$Result_15=mysql_query("SELECT IFNULL(SUM(M.Amount*$HKDRate),0) AS Amount,'15' AS Number,'Forward费用' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month
FROM $DataIn.ch3_forward M 
LEFT JOIN $DataPublic.freightdata B ON B.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency $Terms $TempMonth ",$link_id);
if($Row15=mysql_fetch_array($Result_15)){
   $j=0;
   do{
         $Number=$Row15["Number"];
         $Month=$Row15["Month"]==""?0:$Row15["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row15["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row15["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row15=mysql_fetch_array($Result_15));
}
else $ItemName[15]="Forward费用";


//******************************16中港运费
$Result_16=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'16' AS Number,'中港运费' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month FROM (
SELECT F.Estate,F.Amount,C.Date 
     FROM $DataIn.ch4_freight_declaration F 
     LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id 
     LEFT JOIN $DataIn.ch1_shipmain C ON C.Id=I.chId
     WHERE  F.Estate='3'  AND F.TypeId='1' AND F.PayType='0' GROUP BY F.Id 
 UNION ALL 
 SELECT F.Estate,F.Amount,C.DeliveryDate AS Date 
     FROM $DataIn.ch4_freight_declaration F 
     LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id 
     LEFT JOIN $DataIn.ch1_deliverymain C ON C.Id=I.chId 	 
     WHERE  F.Estate='3'  AND F.TypeId='2' AND F.PayType='0' GROUP BY F.Id)M       
$Terms $TempMonth",$link_id);

if($Row16=mysql_fetch_array($Result_16)){
   $j=0;
   do{
         $Number=$Row16["Number"];
         $Month=$Row16["Month"]==""?0:$Row16["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row16["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row16["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row16=mysql_fetch_array($Result_16));
}
else $ItemName[16]="中港运费";

//******************************17入仓费
/*
$Result_17=mysql_query("SELECT IFNULL(SUM(M.depotCharge*$HKDRate),0) AS Amount,'17' AS Number,'入仓费' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.ch4_freight_declaration M $Terms $TempMonth",$link_id);
*/
$Result_17=mysql_query("SELECT IFNULL(SUM(M.depotCharge),0) AS Amount,'17' AS Number,'入仓费' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month  FROM (
SELECT F.Estate,F.depotCharge,C.Date 
     FROM $DataIn.ch4_freight_declaration F 
     LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id 
     LEFT JOIN $DataIn.ch1_shipmain C ON C.Id=I.chId
     WHERE  F.Estate='3'  AND F.TypeId='1' AND F.PayType='0' GROUP BY F.Id 
 UNION ALL 
 SELECT F.Estate,F.depotCharge,C.DeliveryDate AS Date 
     FROM $DataIn.ch4_freight_declaration F 
     LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id 
     LEFT JOIN $DataIn.ch1_deliverymain C ON C.Id=I.chId 	 
     WHERE  F.Estate='3'  AND F.TypeId='2' AND F.PayType='0' GROUP BY F.Id)M       
$Terms $TempMonth",$link_id);
if($Row17=mysql_fetch_array($Result_17)){
   $j=0;
   do{
         $Number=$Row17["Number"];
         $Month=$Row17["Month"]==""?0:$Row17["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row17["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row17["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row17=mysql_fetch_array($Result_17));
}
else $ItemName[17]="入仓费";

//******************************18报关/商检费用
$Result_18=mysql_query("SELECT IFNULL(SUM(M.declarationCharge+M.checkCharge+M.carryCharge+M.xyCharge+M.wfqgCharge+M.ccCharge+M.djCharge+M.stopcarCharge+M.expressCharge+M.otherCharge),0) AS Amount,'18' AS Number,'报关/商检费用' AS ItemName ,DATE_FORMAT(M.Date,'%Y-%m') AS Month
FROM $DataIn.ch4_freight_declaration M $Terms $TempMonth",$link_id);
if($Row18=mysql_fetch_array($Result_18)){
   $j=0;
   do{
         $Number=$Row18["Number"];
         $Month=$Row18["Month"]==""?0:$Row18["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row18["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row18["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row18=mysql_fetch_array($Result_18));
}
else $ItemName[18]="报关/商检费用/其它";

//******************************19快递费用
$Result_19=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'19' AS Number,'快递费用' AS ItemName ,DATE_FORMAT(M.Date,'%Y-%m') AS Month
FROM $DataIn.ch9_expsheet M $Terms $TempMonth",$link_id);
if($Row19=mysql_fetch_array($Result_19)){
   $j=0;
   do{
         $Number=$Row19["Number"];
         $Month=$Row19["Month"]==""?0:$Row19["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row19["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row19["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row19=mysql_fetch_array($Result_19));
}
else $ItemName[19]="快递费用";

//******************************20寄样费用SendDate
$Result_20=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'20' AS Number,'寄样费用' AS ItemName ,DATE_FORMAT(M.SendDate,'%Y-%m') AS Month
FROM $DataIn.ch10_samplemail M $Terms GROUP BY DATE_FORMAT(M.SendDate,'%Y-%m') ORDER BY DATE_FORMAT(M.SendDate,'%Y-%m')",$link_id);
if($Row20=mysql_fetch_array($Result_20)){
   $j=0;
   do{
         $Number=$Row20["Number"];
         $Month=$Row20["Month"]==""?0:$Row20["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row20["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row20["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row20=mysql_fetch_array($Result_20));
}
else $ItemName[20]="寄样费用";
//******************************21其他收入PayDate

$Result_21=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate*-1),0) AS Amount,'21' AS Number,'其他收入' AS ItemName ,DATE_FORMAT(M.PayDate,'%Y-%m') AS Month
FROM $DataIn.cw4_otherinsheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
WHERE M.Estate=3 GROUP BY DATE_FORMAT(M.PayDate,'%Y-%m')  ORDER BY DATE_FORMAT(M.PayDate,'%Y-%m')",$link_id);
if($Row21=mysql_fetch_array($Result_21)){
   $j=0;
   do{
         $Number=$Row21["Number"];
         $Month=$Row21["Month"]==""?0:$Row21["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row21["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row21["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row21=mysql_fetch_array($Result_21));
}
else $ItemName[21]="其他收入";
//******************************22模具退回费
$Result_22=mysql_query("SELECT IFNULL(SUM(M.OutAmount*-1),0) AS Amount,'22' AS Number,'模具退回费' AS ItemName ,DATE_FORMAT(M.Date,'%Y-%m') AS Month
FROM $DataIn.cw16_modelfee M $Terms $TempMonth",$link_id);
if($Row22=mysql_fetch_array($Result_22)){
   $j=0;
   do{
         $Number=$Row22["Number"];
         $Month=$Row22["Month"]==""?0:$Row22["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row22["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row22["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row22=mysql_fetch_array($Result_22));
}
else $ItemName[22]="模具退回费";

//******************************23体检费用
$Result_23=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'23' AS Number,'体检费用' AS ItemName ,M.Month
FROM $DataIn.cw17_tjsheet M $Terms GROUP BY M.Month",$link_id);
if($Row23=mysql_fetch_array($Result_23)){
   $j=0;
   do{
         $Number=$Row23["Number"];
         $Month=$Row23["Month"]==""?0:$Row23["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row23["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row23["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row23=mysql_fetch_array($Result_23));
}
else $ItemName[23]="体检费用";


//******************************24住房公积金
$Result_24=mysql_query("SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'24' AS Number,'住房公积金' AS ItemName,M.Month 
FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=2 GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row24=mysql_fetch_array($Result_24)){
   $j=0;
   do{
         $Number=$Row24["Number"];
         $Month=$Row24["Month"]==""?0:$Row24["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row24["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row24["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row24=mysql_fetch_array($Result_24));
}
else $ItemName[24]="住房公积金";

//******************************25住房公积金
$Result_25=mysql_query("SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'25' AS Number,'意外险' AS ItemName,M.Month 
FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=3 GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row25=mysql_fetch_array($Result_25)){
   $j=0;
   do{
         $Number=$Row25["Number"];
         $Month=$Row25["Month"]==""?0:$Row25["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row25["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row25["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row25=mysql_fetch_array($Result_25));
}
else $ItemName[25]="意外险";

//******************************26 非BOM采购货款
$Result_26=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,'26' AS Number,'非BOM采购货款' AS ItemName,M.Month 
FROM $DataIn.nonbom11_qksheet M 
LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms  GROUP BY M.Month ORDER BY M.Month",$link_id);
if($Row26=mysql_fetch_array($Result_26)){
   $j=0;
   do{
         $Number=$Row26["Number"];
         $Month=$Row26["Month"]==""?0:$Row26["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row26["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row26["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
    	 }while($Row26=mysql_fetch_array($Result_26));
	}
else{
	$ItemName[26]="非BOM采购货款";
	}

//******************************27 车辆费用
$Result_27=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,'27' AS Number,'车辆费用' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.carfee M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month",$link_id);
if($Row27=mysql_fetch_array($Result_27)){
   $j=0;
   do{
         $Number=$Row27["Number"];
         $Month=$Row27["Month"]==""?0:$Row27["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row27["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row27["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
    	 }while($Row27=mysql_fetch_array($Result_27));
	}
else{
	$ItemName[27]="车辆费用";
	}

//******************************28 员工离职补助
$Result_28=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,'28' AS Number,'员工离职补助' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month",$link_id);
if($Row28=mysql_fetch_array($Result_28)){
   $j=0;
   do{
         $Number=$Row28["Number"];
         $Month=$Row28["Month"]==""?0:$Row28["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row28["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row28["Amount"]);
         $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
    	 }while($Row28=mysql_fetch_array($Result_28));
	}
else{
	$ItemName[28]="员工离职补助";
	}
	
//******************************9供应商货款返利
$Result_29=mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount,'29' AS Number,'供应商货款返利' AS ItemName,DATE_FORMAT(M.Date,'%Y-%m') AS Month  
FROM $DataIn.cw2_hksheet M $Terms $TempMonth",$link_id);
if($Row29=mysql_fetch_array($Result_29)){
   $j=0;
   do{
         $Number=$Row29["Number"];
         $Month=$Row29["Month"]==""?0:$Row29["Month"];
         $subMonth[$Number][$j]=$Month;
		 $ItemName[$Number]=$Row29["ItemName"];
         $Amount[$Number][$Month]=sprintf("%.2f",$Row29["Amount"]);
          $SumAmount[$Month]+=$Amount[$Number][$Month];
         $j++;
     }while($Row29=mysql_fetch_array($Result_29));
}
else $ItemName[29]="供应商货款返利";

//########################
ChangeWtitle("$SubCompany 审核通过未结付金额统计");
$tableWidth=1200;
$subTableWidth=1150;
$maxSum=11;//统计月份数量
$totalNumber=count($ItemName);//总共统计的项目
$tablelow=$maxSum+7;//加上项目,HKD,RMB,USD列
$CheckMonth=date("Y-m");		//当前月第一天
$sub_M=$maxSum-1;
$StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" >审核通过未结付金额统计(本页面的其他收入需更新代码)</td>
    </tr>
	<tr>
   	 <td align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="30" height="25"class="A1111" align="center">序号</td>
    <td class="A1100" width="90" >&nbsp;项&nbsp;&nbsp;目</td>
    <td class="A1101" width="30" >&nbsp;</td>
     <?php 
         for($i=0;$i<$maxSum;$i++){
               $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
              if ($SumAmount[$tmpMonth]!=0){
	             echo "<td width='70' class='A1101' align='center'>$tmpMonth</td>";
	          }
         }
    ?>
	<td width="70" class="A1101" align="center">未付TWD</td>
    <td width="70" class="A1101" align="center">未付HKD</td>
	<td width="70" class="A1101" align="center">未付USD</td>
	<td width="70" class="A1101" align="center">未付RMB</td>
	<td width="70" class="A1101" align="center">合计(RMB)</td>
  </tr>
</table>

<?php
$totalAmount1=0;$totalAmount2=0;$totalAmount3=0; $totalAmount4=0;
for($i=1;$i<=$totalNumber;$i++){
    $TempAmount1="A".strval($i)."_1";
	$TempAmount2="A".strval($i)."_2";
	$TempAmount3="A".strval($i)."_3";
	$TempAmount4="A".strval($i)."_4";
	$TempItem="ItemName_".strval($i);
	//百分比计算
	
	$totalAmount1+=$$TempAmount1;
	$totalAmount2+=$$TempAmount2;
	$totalAmount3+=$$TempAmount3;
	$totalAmount4+=$$TempAmount4;
	
	$FK=$$TempAmount1+$$TempAmount2*$Rate_2+$$TempAmount3*$Rate_3+$$TempAmount4*$Rate_4;
	$RmbAmount += $FK;
	$TempPC=sprintf("%.1f",($FK/$SumRMB)*100);
	
	$TempPC=$TempPC==0?"&nbsp;":"<span class='greenB'>".$TempPC."%</span>";
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
        <td width="30" height="25"class="A0111" align="center"><?php echo $i?></td>
         <td class="A0100" width="90" ><?php  echo $ItemName[$i]?></td>
            <td class="A0101" width="30" align="right"><?php echo $TempPC ?></td>

<?php 
       $DivNum="a";
        for($j=0;$j<$maxSum;$j++){
               $tmpMonth=date("Y-m",strtotime(" $StratMonth +$j month"));
          if ($SumAmount[$tmpMonth]!=0){
               $TempId="$tmpMonth|$DivNum$i";
               $onClickStr=$Amount[$i][$tmpMonth]==0?"":"onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_outfee_$i\",\"desktask\");'";
               $tmpAmount=$Amount[$i][$tmpMonth]==0?"&nbsp;":number_format($Amount[$i][$tmpMonth],0);
               if($Amount[$i][$tmpMonth]>=500000)$tmpAmount="<span class='redB' title='超过50万'>$tmpAmount</span>";
               else{
                       if($Amount[$i][$tmpMonth]>=100000)$tmpAmount="<span class='yellowB' title='10-50万'>$tmpAmount</span>";
                      }
	           echo "<td width='70' class='A0101' align='right' $onClickStr>$tmpAmount</td>";
               $HideTableHTML="<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				      <tr bgcolor='#B7B7B7'><td class='A0111' height='30'>
						    <br>
							    <div id='HideDiv_$DivNum$i' width='$subTableWidth' align='center'>&nbsp;</div>
						    <br>
					     </td>
				      </tr>
			      </table>";
          }
       }
?>
			<td class="A0101" width="70" align="right"><?php echo zerotospace(number_format($$TempAmount4,0))?></td><!-- TWD-->
            <td class="A0101" width="70" align="right"><?php echo zerotospace(number_format($$TempAmount3,0))?></td><!-- HKD-->
			<td class="A0101" width="70" align="right"><?php echo zerotospace(number_format($$TempAmount2,0))?></td><!-- USD-->
			<td class="A0101" width="70" align="right"><?php echo zerotospace(number_format($$TempAmount1,0))?></td><!-- RMB-->
			<td class="A0101" width="70" align="right"><?php echo zerotospace(number_format($FK,0))?></td><!-- 合计-->
		</tr>
	</table>
<?php
echo $HideTableHTML;
}
?>	
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr bgcolor='#EEEEEE'>
    <td width="30" height="25" class="A0110" >&nbsp;</td>
    <td class="A0100"  width="90" align="center" >合  计</td>
    <td class="A0101" width="31" >&nbsp;</td>
    <?php
         $tablecols=0;
	   	for($i=0;$i<$maxSum;$i++){
	   	    $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
	   	    if ($SumAmount[$tmpMonth]!=0){
               echo "<td width='70' class='A0101' align='right'>" . number_format($SumAmount[$tmpMonth],0) . "</td>";
               $tablecols++;
             }
          }
          $tablecols+=8;
	    ?>		
	<td class="A0101" width="70" align="right"><?php  echo number_format($totalAmount4,0); ?></td><!-- 预收货款-->
    <td class="A0101" width="70" align="right"><?php  echo number_format($totalAmount3,0); ?></td><!-- 预收货款-->
	<td class="A0101" width="70" align="right"><?php  echo number_format($totalAmount2,0);?></td><!-- USD-->
	<td class="A0101" width="70" align="right"><?php  echo number_format($totalAmount1,0); ?></td><!-- RMB-->
	<td class="A0101" width="70" align="right"><?php  echo number_format($RmbAmount,0); ?></td><!-- 合计-->
  </tr>
<tr  height="25" class=''><td class="A0111" colspan="<?php echo  $tablecols?>">合 计: 转RMB总额约<?php echo number_format($SumRMB)."元。各月份金额结算货币RMB" ?></td></tr>
</table>	
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="30" height="25"class="A0111" align="center">序号</td>
    <td class="A0100" width="90" >&nbsp;项&nbsp;&nbsp;目</td>
    <td class="A0101" width="30" >&nbsp;</td>
     <?php 
         for($i=0;$i<$maxSum;$i++){
              $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
             if ($SumAmount[$tmpMonth]!=0){
              //$tmpMonth=$subMonth[$k][$i];
                 $mAmount[$tmpMonth]=0;
                 $monthTotal[$tmpMonth]=0;
	            echo "<td width='70' class='A0101' align='center'>$tmpMonth</td>";
	         }
         }
    ?>
	<td width="70" class="A0101" align="center">未付TWD</td>
    <td width="70" class="A0101" align="center">未付HKD</td>
	<td width="70" class="A0101" align="center">未付USD</td>
	<td width="70" class="A0101" align="center">未付RMB</td>
	<td width="70" class="A0101" align="center">合计(RMB)</td>
  </tr>
</table>
</form>
</body>
</html>
<script language="JavaScript" type="text/JavaScript">
function SandH(divNum,RowId,f,TempId,ToPage,FromDir){
	     var e=eval("HideTable_"+divNum+RowId);
//alert("HideTable_"+divNum+RowId)
	    e.style.display=(e.style.display=="none")?"":"none";
		if(TempId!=""){	
		  var url="../desktask/outfee/"+ToPage+".php?TempId="+TempId+"&RowId="+RowId;
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
	}
</script>