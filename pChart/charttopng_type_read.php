<?php 
/////////////////////////////////////////////////
//按产品分类设置
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
include "../basic/chksession.php";
header("Content-Type:text/html;charset=utf-8");
include "../basic/parameter.inc";
$CheckMonths=$M==""?6:$M;				//要计算的月份数
$M=$CheckMonths;
$TypeId=$TypeId==""?8050:$TypeId;
$DataIn="d3";

$CheckMonth=date("Y-m-01");		//当前月第一天
$StratDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";	
//$CheckMonths=6;
$MonthArray=array();
for($i=0;$i<=$CheckMonths;$i++){
     $theMonth=date("Y-m",strtotime("$StratDate +$i month"));//计算的起始日期
     $MonthArray[$i]=$theMonth;
 }

 //取得产品分类
$m=0;$ch_Serie=array();$od_Serie=array();$SUM_ch=0;$SUM_od=0;
$TypeSql=mysql_query("SELECT T.TypeId,T.TypeName,C.ColorCode FROM $DataIn.chart3_color C 
                     LEFT JOIN $DataIn.producttype T  ON C.TypeId=T.TypeId 
                     WHERE T.Estate=1 AND C.Estate=1 AND T.TypeId='$TypeId' ORDER BY T.SortId",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
  
       $TypeName=$TypeRow["TypeName"]; 
       $TypeColor=$TypeRow["ColorCode"]; 
       //每月出货数量
    for($i=0;$i<=$CheckMonths;$i++){
       $theMonth=$MonthArray[$i]; 
       $chQtySql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty),0) AS Qty 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
            LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND P.TypeId='$TypeId' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')",$link_id)); 
     $chQty=$chQtySql["Qty"];
     $chQty=$chQty==""?0:$chQty;

     array_push($ch_Serie,$chQty);  
     $SUM_ch+=$chQty;
     //每月下单数量
     $odQtySql=mysql_fetch_array(mysql_query("
			SELECT IFNULL(SUM(S.Qty),0) AS Qty 
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth'  AND P.TypeId='$TypeId' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')",$link_id));
        $odQty=$odQtySql["Qty"];
        $odQty=$odQty==""?0:$odQty;
        
        array_push($od_Serie,$odQty);
        $SUM_od+=$odQty;
  }
  
}
// Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");
 

$ch_Max=max($ch_Serie);
$od_Max=max($od_Serie);

 // Dataset definition 
 $DataSet = new pData;
 
if ($ch_Max>=$od_Max) {
     $DataSet->AddPoint($ch_Serie,"Serie1"); 
    }
else{
     $DataSet->AddPoint($od_Serie,"Serie1");  
}
 
 $DataSet->AddPoint($MonthArray,"Month");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Month");
 $DataSet->SetAbsciseLabelSerie("Month");
 

 $DataSet->SetYAxisName("数量");
//$DataSet->SetYAxisUnit("元");


 if ($M<12){
    $Width=$M*120;
   }
 else{
    $Width=$M*100; 
 }
 $Height=380;
 

 $Test = new pChart($Width,$Height);
 
 $Test->drawFilledRoundedRectangle(7,7,$Width-7,$Height-7,5,240,240,240);   
 $Test->drawRoundedRectangle(5,5,$Width-5,$Height-5,5,230,230,230);  

 $Test->setFontProperties("Fonts/simhei.ttf",18);

 // Draw the title
 $Title = $TypeName . "每月下单、出货数量统计图";
$Test->drawTextBox(10,10,$Width-10,50,$Title,0,255,255,255,ALIGN_CENTER,TRUE,0,0,0,30);
 

 $Test->setFontProperties("Fonts/simhei.ttf",8);
  //设置绘图区
 $Test->setGraphArea(70,60,$Width-150,$Height-30);

 $Test->drawGraphArea(255,255,255,TRUE);

 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,0,0,0,TRUE,0,2,TRUE);

 $Test->drawGraphAreaGradient(255,255,255,50);
 $Test->drawGrid(4,TRUE,230,230,230,20);  //绘制网格
 

  //设置颜色

 if ($ch_Max>=$od_Max) { 
       $Test->setColorPalette(0,20,83,251);
    }
 else{
    //$Test->setColorPalette(0,$rg1,$rg2,$rg3); 
     $Test->setColorPalette(0,0,169,251); 
 }
  //$Test->setColorPalette(2,0,169,251);
  
 // Draw the bar chart
//$Test->drawMPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),70,array(0.3,0.3,0.2),array(0.1,0.3,0.7));//绘制柱形
 if ($ch_Max>=$od_Max) {
    $Test->setFontProperties("Fonts/tahoma.ttf",9,0,0,255);  
    $Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),60,0.25,0.15);//绘制柱形
 }else{
    $Test->setFontProperties("Fonts/tahoma.ttf",9,255,0,0);
    $Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),80,0.25,-0.15);//绘制柱形 
 }
 //$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),FALSE,80);
 $DataSet->removeAllSeries();
 $DataSet->removeAllData(); 
 
 if ($ch_Max<$od_Max) {
     $DataSet->AddPoint($ch_Serie,"Serie1"); 
    }
else{
     $DataSet->AddPoint($od_Serie,"Serie1");  
}

 $DataSet->AddAllSeries();
 
 if ($ch_Max<$od_Max) { 
      $Test->setColorPalette(0,20,83,251);
    }
 else{
    //$Test->setColorPalette(0,$rg1,$rg2,$rg3);
    $Test->setColorPalette(0,0,169,251); 
 }
 
if ($ch_Max<$od_Max) {
    $Test->setFontProperties("Fonts/tahoma.ttf",9,0,0,255);
    $Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),60,0.25,0.15);//绘制柱形
 }else{
    $Test->setFontProperties("Fonts/tahoma.ttf",9,255,0,0);
    $Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),80,0.25,-0.15);//绘制柱形 
 }
 
// Draw the legend
 $DataSet->SetSerieName("下单","Serie1");  
 $DataSet->SetSerieName("出货","Serie2"); 
 $Test->setColorPalette(1,20,83,251);
 //$Test->setColorPalette(0,$rg1,$rg2,$rg3); 
 $Test->setColorPalette(0,0,169,251); 
 
$Test->setFontProperties("Fonts/simhei.ttf",9);
$Test->drawLegend($Width-120,60,$DataSet->GetDataDescription(),236,238,240,52,58,82);

//画月平均线
 $sDate="2011-09-01";$tmpM=$M;
 if ($StratDate<$sDate){
     $tmpY=substr($sDate,0,4)-substr($StratDate,0,4);
     $tmpM1=floor(substr($sDate,5,2));
     $tmpM2=floor(substr($StratDate,5,2));
     if ($tmpM1>$tmpM2) $tmpM=$M-($tmpY*12+$tmpM1-$tmpM2);
        else $tmpM=$M-(($tmpY-1)*12+$tmpM1+12-$tmpM2);
 }
 $tmpM=$tmpM+1;
 
 $ch_Average=sprintf("%.0f",$SUM_ch/$tmpM);
 $od_Average=sprintf("%.0f",$SUM_od/$tmpM);
 $Test->setFontProperties("Fonts/simhei.ttf",9,255,255,255);
 //$Test->setLineStyle(2);
 $Test->drawAverageLine($ch_Average,193,66,33,"出货均线：" . $ch_Average,FALSE);
 $Test->drawAverageLine($od_Average,0,232,232,"接单均线：" . $od_Average,FALSE);

 // Render the picture
 //$Test->addBorder(1);
 //$Test->Render("test.png");
 $Test->Stroke();

?>