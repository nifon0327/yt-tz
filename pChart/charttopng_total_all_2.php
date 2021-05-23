<?php 
/////////////////////////////////////////////////
//按产品分类设置
//关闭当前页面的PHP警告及提示信息
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
include "../basic/chksession.php";
 header("Content-Type:text/html;charset=utf-8");
include "../basic/parameter.inc";
$StratDate="2011-12-01";//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";	

 //取得产品分类
$m=0;$IdArray=array();$NameArray=array();$ColorArray=array();
$Sum_wcCost=0;$sum_wcSerie=0;

$TypeSql=mysql_query("SELECT T.TypeId,T.TypeName,C.ColorCode FROM $DataIn.chart3_color C 
                     LEFT JOIN $DataIn.producttype T  ON C.TypeId=T.TypeId 
                     WHERE T.Estate=1  AND C.Estate=1 ORDER BY T.SortId",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
    do {
       $IdArray[$m]=$TypeRow["TypeId"];
       $NameArray[$m]=$TypeRow["TypeName"]; 
       $ColorArray[$m]=$TypeRow["ColorCode"]; 
       $m++;
    }while($TypeRow=mysql_fetch_array($TypeSql)); 
}

$wc_Serie=array();$wcCost_Serie=array();$MaxValue=0;
for($j=0;$j<$m;$j++){
   $TypeId=$IdArray[$j];
   
    //未出金额 
       $wcAmountSql=mysql_fetch_array(mysql_query("
			SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount 
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 $TJB  AND S.Estate>0 AND P.TypeId='$TypeId'",$link_id));
        $wcAmount=$wcAmountSql["Amount"];
        $wcAmount=$wcAmount==""?0:sprintf("%.0f",$wcAmount);
        
        array_push($wc_Serie,sprintf("%.0f",$wcAmount)); 
        //array_push($all_Serie[$j],sprintf("%.0f",$odAmount/10000)); 
        if ($MaxValue<$wcAmount) $MaxValue=$wcAmount;
        $sum_wcSerie+=$wcAmount;
        
   
  //未出订单毛利总额 
 $CostAmount=0;
 $CostResult=mysql_fetch_array(mysql_query("SELECT            IFNULL(SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate),0) AS Amount
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 AND P.TypeId='$TypeId'",$link_id));
                  $CostAmount=$CostResult["Amount"];
                  $CostAmount=$CostAmount==""?0:sprintf("%.0f",$CostAmount);
		
  $GrossProfit=$wcAmount-$CostAmount;
  $Sum_wcCost+=$GrossProfit;
  array_push($wcCost_Serie,sprintf("%.0f",$GrossProfit));
  //array_push($all_Serie[$j],sprintf("%.0f",$GrossProfit/10000)); 
   
}


// Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");
 
 

 // Dataset definition 
 $DataSet = new pData;

 //以未出订单金额排序
 arsort($wc_Serie);  
 $k=0;$SerieName=array();$wcCost_Sort=array();
 foreach ( $wc_Serie as $Key => $Values ){
    $SerieName[$k]=$NameArray[$Key];  
    $wcCost_Sort[$k]=$wcCost_Serie[$Key];
    $k++;
 }
     
 $DataSet->AddPoint($wc_Serie, "Serie0"); 
 $DataSet->AddPoint($SerieName,"SerieName");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("SerieName");
 $DataSet->SetAbsciseLabelSerie("SerieName");

 $TypeName="订单总额(比例)：" . number_format($sum_wcSerie) . "元";
 $DataSet->SetSerieName($TypeName,"Serie0"); 
 $TypeName="毛利总额(毛利率)：" . number_format($Sum_wcCost) . "元";
 $DataSet->SetSerieName($TypeName,"Serie1");
 
  
 $DataSet->SetXAxisName("金额(万元)");
 //$DataSet->SetYAxisUnit("万");
 
 $Width=620;
 //$Height=400;
 $Height=count($SerieName)*50;
 $Height=$Height<400?400:$Height;
 
 $Test = new pChart($Width,$Height);
 //$Test->drawGraphAreaGradient(132,173,131,50,TARGET_BACKGROUND); //绘制背景颜色
 $Test->drawFilledRoundedRectangle(7,7,$Width-7,$Height-7,5,240,240,240);   
 $Test->drawRoundedRectangle(5,5,$Width-5,$Height-5,5,230,230,230);  
 
 $Test->setFontProperties("Fonts/simhei.ttf",18);
 
 // Draw the title
 $Title = "未出订单总额/毛利统计图";
 $Test->drawTextBox(10,10,$Width-10,50,$Title,0,255,255,255,ALIGN_CENTER,TRUE,0,0,0,30);
 
 $Test->setFontProperties("Fonts/simhei.ttf",9);
  //设置绘图区
 $Test->setGraphArea(120,60,$Width-50,$Height-50);

 $MaxValue=$MaxValue/10000;
 $Test->drawGraphArea(255,255,255,TRUE);
// $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,0,0,0,TRUE,0,2,TRUE);
$Test->drawXScale($DataSet->GetData(),$DataSet->GetDataDescription(),$MaxValue,10,0,0,0,0,0);

 $Test->drawGraphAreaGradient(255,255,255,50);
 $Test->drawXGrid(4,TRUE,230,230,230,20);  //绘制网格


 $Test->setFontProperties("Fonts/tahoma.ttf",9,20,83,251);
 $Test->setColorPalette(0,20,83,251);
 $Test->drawXBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),FALSE,80,0.4,-0.2,10000,FALSE,TRUE,TRUE,1,255,255,255);
 
 $DataSet->removeAllSeries();
 $DataSet->removeAllData(); 
 
 $DataSet->AddPoint($wcCost_Sort, "Serie1"); 
 $Test->setColorPalette(1,0,169,251);
 $DataSet->AddAllSeries();
 $Test->setFontProperties("Fonts/tahoma.ttf",9,0,169,251);  
 $Test->drawXBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),FALSE,80,0.4,0.2,10000,FALSE,TRUE,TRUE,0);
 //$Test->drawXSortStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.5,0);//绘制柱形

 // Draw the legend
 $Test->setFontProperties("Fonts/simhei.ttf",10);
 $Test->drawLegend($Width-280,70,$DataSet->GetDataDescription(),236,238,240,52,58,82);
 // Render the picture
 //$Test->addBorder(1);
 //$Test->Render("test.png");
 $Test->Stroke(); 


?>