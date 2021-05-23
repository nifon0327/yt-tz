<?php   
/////////////////////////////////////////////////
//按产品分类设置
include "../basic/chksession.php";
header("Content-Type:text/html;charset=utf-8");
include "../basic/parameter.inc";
$CheckMonths=$M==""?6:$M;				//要计算的月份数
$M=$CheckMonths;
$CheckMonth=date("Y-m-01");		//当前月第一天
$StratDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";	
$MonthArray=array();
for($i=0;$i<=$CheckMonths;$i++){
     $theMonth=date("Y-m",strtotime("$StratDate +$i month"));//计算的起始日期
     $MonthArray[$i]=$theMonth;
     $sum_chSerie[$i]=0;
     $sum_odSerie[$i]=0;
 	}
 //取得产品分类
$m=0;$IdArray=array();$NameArray=array();$ColorArray=array();
$TypeSql=mysql_query("SELECT PM.Id AS TypeId,PM.Name AS TypeName,PM.Color AS ColorCode
	 FROM $DataIn.productmaintype PM WHERE PM.Estate=1",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
    do {
		$IdArray[$m]=$TypeRow["TypeId"];
       	$NameArray[$m]=$TypeRow["TypeName"]; 
       	$ColorArray[$m]=substr($TypeRow["ColorCode"],1); 
       	$m++;
    	}while($TypeRow=mysql_fetch_array($TypeSql)); 
	}
$ch_Serie=array();$od_Serie=array();
for($j=0;$j<$m;$j++){
	$TypeId=$IdArray[$j];
   	$ch_Serie[$j]=array();
   	$od_Serie[$j]=array();
   	//出货金额
   	for($i=0;$i<=$CheckMonths;$i++){
		$theMonth=$MonthArray[$i]; 
       	$chAmountSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty*A.Price*A.YandN*E.Rate*B.Sign),0) AS Amount 
			FROM $DataIn.ch1_shipsheet A 
			LEFT JOIN $DataIn.ch1_shipmain B ON B.Id=A.Mid 
			LEFT JOIN $DataIn.productdata C ON C.ProductId=A.ProductId
            LEFT JOIN $DataIn.producttype T ON T.TypeId=C.TypeId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=B.CompanyId 
			LEFT JOIN $DataPublic.currencydata E ON E.Id=D.Currency 
			WHERE B.Estate=0 AND DATE_FORMAT(B.Date,'%Y-%m')='$theMonth' AND T.mainType='$TypeId'  AND D.CompanyId='$CompanyId'
            GROUP BY DATE_FORMAT(B.Date,'%Y-%m')",$link_id)); 
		$chAmount= $chAmountSql["Amount"];
     	$chAmount=$chAmount==""?0:sprintf("%.0f",$chAmount/10000);
     	array_push($ch_Serie[$j],$chAmount);  
     	$sum_chSerie[$i]=$sum_chSerie[$i]+$chAmount;
   		}
  //下单金额 
	for($i=0;$i<=$CheckMonths;$i++){
    	$theMonth=$MonthArray[$i];
       	$odAmountSql=mysql_fetch_array(mysql_query("
			SELECT SUM(A.Qty*A.Price*E.Rate) AS Amount,DATE_FORMAT(B.OrderDate,'%Y-%m') AS Date 
			FROM $DataIn.yw1_ordersheet A  
			LEFT JOIN $DataIn.yw1_ordermain B ON B.OrderNumber=A.OrderNumber
			LEFT JOIN $DataIn.productdata C ON C.ProductId=A.ProductId
            LEFT JOIN $DataIn.producttype T ON T.TypeId=C.TypeId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=B.CompanyId 
			LEFT JOIN $DataPublic.currencydata E ON E.Id=D.Currency 
			WHERE 1 AND DATE_FORMAT(B.OrderDate,'%Y-%m')='$theMonth'  AND T.mainType='$TypeId' AND D.CompanyId='$CompanyId'
            GROUP BY DATE_FORMAT(B.OrderDate,'%Y-%m')",$link_id));
        $odAmount=$odAmountSql["Amount"];
        $odAmount=$odAmount==""?0:sprintf("%.0f",$odAmount/10000);
        array_push($od_Serie[$j],$odAmount);  
        $sum_odSerie[$i]=$sum_odSerie[$i]+$odAmount;
  		}
	}
//车间薪资
/*$hzAmount=array();
for($i=0;$i<=$CheckMonths;$i++){
	$theMonth=$MonthArray[$i];
    $hzAmount[$i]=0;
   	//当月车间薪资
   	$hzSql=mysql_query("SELECT IFNULL(SUM(Amount),0) AS Amount FROM(
		SELECT SUM(S.Amount+S.Sb+S.Jz+S.RandP+S.Otherkk) AS Amount FROM $DataIn.cwxzsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=6
    	UNION ALL
	  	SELECT SUM(S.cAmount) AS Amount FROM $DataIn.sbpaysheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=6
    	UNION ALL
	  	SELECT SUM(S.Amount) AS Amount FROM $DataIn.hdjbsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=6
        )E",$link_id);
	if($hzRow=mysql_fetch_array($hzSql)){
     	$Amount=$hzRow["Amount"];
      	$hzAmount[$i]=sprintf("%.0f",$Amount/10000); 
	  	}
	}*/
include("pChart/pData.class");
include("pChart/pChart.class"); 
$ch_Max=max($sum_chSerie);
$od_Max=max($sum_odSerie);
 $DataSet = new pData;
if ($ch_Max>=$od_Max) {
	for($i=0;$i<$m;$i++){
		$Serie="Serie" . $i;
    	$DataSet->AddPoint($ch_Serie[$i],$Serie); 
    	} 
	}
else{
	for($i=0;$i<$m;$i++){
		$Serie="Serie" . $i;
    	$DataSet->AddPoint($od_Serie[$i],$Serie); 
    	}  
	}
 $DataSet->AddPoint($MonthArray,"Month");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Month");
 $DataSet->SetAbsciseLabelSerie("Month");
for($i=0;$i<$m;$i++){
	$Serie="Serie" . $i;
    $TypeName=$NameArray[$i];
    $DataSet->SetSerieName($TypeName,$Serie);  
  	}
 $DataSet->SetYAxisName("金额");
 $DataSet->SetYAxisUnit("万");
if ($M<12){
	$Width=$M*120;
   	}
else{
    $Width=$M*100; 
	}
$Height=680;
$Test = new pChart($Width,$Height);
$Test->drawFilledRoundedRectangle(7,7,$Width-7,$Height-7,5,240,240,240);   
$Test->drawRoundedRectangle(5,5,$Width-5,$Height-5,5,230,230,230);  
$Test->setFontProperties("Fonts/simhei.ttf",18);
// Draw the title
$Title = $Forshort." 客户每月下单、出货金额条形图";
$Test->drawTextBox(10,10,$Width-10,50,$Title,0,255,255,255,ALIGN_CENTER,TRUE,0,0,0,30);
$Test->setFontProperties("Fonts/simhei.ttf",8);
//设置绘图区
$Test->setGraphArea(70,60,$Width-150,$Height-70);
$Test->drawGraphArea(255,255,255,TRUE);
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,0,0,0,TRUE,0,2,TRUE);
$Test->drawGraphAreaGradient(255,255,255,50);
$Test->drawGrid(4,TRUE,230,230,230,20);  //绘制网格
//设置颜色
for($i=0;$i<$m;$i++){
	$rg1=hexdec(substr($ColorArray[$i],0,2));
    $rg2=hexdec(substr($ColorArray[$i],2,2));
    $rg3=hexdec(substr($ColorArray[$i],4,2));
    $Test->setColorPalette($i,$rg1,$rg2,$rg3); 
	} 
 // Draw the bar chart
if ($ch_Max>=$od_Max) {
	$Test->setFontProperties("Fonts/tahoma.ttf",9,255,0,0);  
    $Test->drawSortStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.25,0);//绘制柱形
 	}
else{
	$Test->setFontProperties("Fonts/tahoma.ttf",9,0,0,255);
    $Test->drawSortStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.25,-0.3);//绘制柱形 
 	}
$DataSet->removeAllSeries();
$DataSet->removeAllData();  
if ($ch_Max<$od_Max) {
	for($i=0;$i<$m;$i++){
		$Serie="Serie" . $i;
     	$DataSet->AddPoint($ch_Serie[$i],$Serie); 
    	} 
	}
else{
	for($i=0;$i<$m;$i++){
		$Serie="Serie" . $i;
    	$DataSet->AddPoint($od_Serie[$i],$Serie); 
    	}  
	}
$DataSet->AddAllSeries();
if ($ch_Max<$od_Max) {
	$Test->setFontProperties("Fonts/tahoma.ttf",9,255,0,0);
    $Test->drawSortStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.25,0);//绘制柱形
 	}
else{
    $Test->setFontProperties("Fonts/tahoma.ttf",9,0,0,255);
    $Test->drawSortStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.25,-0.3);//绘制柱形 
	}
// Draw the legend
$Test->setFontProperties("Fonts/simhei.ttf",9);
$Test->drawLegend($Width-140,60,$DataSet->GetDataDescription(),236,238,240,52,58,82); 
$DataSet->removeAllSeries();
$DataSet->removeAllData();  

/*$DataSet->AddPoint($hzAmount,"Serie0");
$DataSet->AddAllSeries();
$Test->setColorPalette(0,0,209,223);
$Test->setFontProperties("Fonts/tahoma.ttf",9,0,0,0);
$Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.2,0.3);//绘制柱形 */
//画月平均线
$SUM_ch=0;$SUM_od=0;
for($i=0;$i<=$CheckMonths;$i++){
	$SUM_ch+=$sum_chSerie[$i];
    $SUM_od+=$sum_odSerie[$i];
    }
$sDate="2011-09-01";$tmpM=$M;
if ($StratDate<$sDate){
	$tmpY=substr($sDate,0,4)-substr($StratDate,0,4);
    $tmpM1=floor(substr($sDate,5,2));
    $tmpM2=floor(substr($StratDate,5,2));
    if ($tmpM1>=$tmpM2) $tmpM=$M-($tmpY*12+$tmpM1-$tmpM2);
	else $tmpM=$M-(($tmpY-1)*12+$tmpM1+12-$tmpM2);
 	}
$tmpM=$tmpM+1;
$ch_Average=sprintf("%.1f",$SUM_ch/$tmpM);
$od_Average=sprintf("%.1f",$SUM_od/$tmpM);
$Test->setFontProperties("Fonts/simhei.ttf",9,255,255,255);
$Test->drawAverageLine($ch_Average,193,66,33,"出货均线：" . $ch_Average . "万",FALSE);
$Test->drawAverageLine($od_Average,0,232,232,"接单均线：" . $od_Average . "万",FALSE); 
$Test->setFontProperties("Fonts/simhei.ttf",9);
$Title = "   说明：第一柱:当月下单金额;  第二柱:当月出货总额";
$Test->drawTextBox(10,$Height-35,$Width-10,$Height-10,$Title,0,255,255,255,ALIGN_LEFT,TRUE,0,0,0,30);
$Test->Stroke();
?>