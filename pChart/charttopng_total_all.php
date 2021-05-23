<?php 
/////////////////////////////////////////////////
//按产品分类设置
include "../basic/chksession.php";
 header("Content-Type:text/html;charset=utf-8");
include "../basic/parameter.inc";
$StratDate="2011-09-01";//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";	
//$CheckMonths=6;

 //取得产品分类
$m=0;$IdArray=array();$NameArray=array();$ColorArray=array();
$all_Serie=array();
$sum_chSerie=0;$sum_odSerie=0;$Sum_wcCost=0;

//$TypeSql=mysql_query("SELECT TypeId,TypeName FROM $DataIn.producttype WHERE Estate=1 AND TypeId IN (SELECT TypeId FROM $DataIn.chart3_color)ORDER BY SortId",$link_id);
$TypeSql=mysql_query("SELECT T.TypeId,T.TypeName,C.ColorCode FROM $DataIn.chart3_color C 
                     LEFT JOIN $DataIn.producttype T  ON C.TypeId=T.TypeId 
                     WHERE T.Estate=1 ORDER BY T.SortId",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
    do {
       $IdArray[$m]=$TypeRow["TypeId"];
       $NameArray[$m]=$TypeRow["TypeName"]; 
       $ColorArray[$m]=$TypeRow["ColorCode"]; 
       $m++;
    }while($TypeRow=mysql_fetch_array($TypeSql)); 
}

for($j=0;$j<$m;$j++){
   $TypeId=$IdArray[$j];
   $all_Serie[$j]=array();
   
    //下单金额 
       $odAmountSql=mysql_fetch_array(mysql_query("
			SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount 
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 $TJB AND P.TypeId='$TypeId'",$link_id));
        $odAmount=$odAmountSql["Amount"];
        $odAmount=$odAmount==""?0:sprintf("%.0f",$odAmount);
        
        array_push($all_Serie[$j],sprintf("%.0f",$odAmount/10000));  
        $sum_odSerie+=$odAmount;
        
   //出货金额
   $chAmountSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign),0) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                        LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA AND P.TypeId='$TypeId'",$link_id)); 
    $chAmount=$chAmountSql["Amount"];
    $chAmount=$chAmount==""?0:sprintf("%.0f",$chAmount);
     
     array_push($all_Serie[$j],sprintf("%.0f",$chAmount/10000));  
     $sum_chSerie+=$chAmount;
   
        
  //未出金额       
   $sc_Amount=$odAmount-$chAmount;
   array_push($all_Serie[$j],sprintf("%.0f",$sc_Amount/10000));
   
  //未出订单毛利总额 
 $GrossProfit=0;
 $CostResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate),0) AS Amount
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 AND P.TypeId='$TypeId'",$link_id));
                  $CostAmount=$CostResult["Amount"];
                  $CostAmount=$CostAmount==""?0:sprintf("%.0f",$CostAmount);
		
  $GrossProfit=$sc_Amount-$CostAmount;
  $Sum_wcCost+=$GrossProfit;
  array_push($all_Serie[$j],sprintf("%.0f",$GrossProfit/10000)); 
}

//未出总金额
$sum_scSerie=$sum_odSerie-$sum_chSerie;


// Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");
 
 // Dataset definition 
 $DataSet = new pData;
for($i=0;$i<$m;$i++){
    $Serie="Serie" . $i;
    $DataSet->AddPoint($all_Serie[$i], $Serie); 
}

 $DataSet->AddPoint(array("下单总金额","已出总金额","未出总金额","未出毛利"),"SerieName");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("SerieName");
 $DataSet->SetAbsciseLabelSerie("SerieName");

 for($i=0;$i<$m;$i++){
    $Serie="Serie" . $i;
    $TypeName=$NameArray[$i];
    $DataSet->SetSerieName($TypeName,$Serie);  
  }
  
 $DataSet->SetYAxisName("金额");
 $DataSet->SetYAxisUnit("万");
 
 $Width=600;
 $Height=600;
 $Test = new pChart($Width,$Height);
 //$Test->drawGraphAreaGradient(132,173,131,50,TARGET_BACKGROUND); //绘制背景颜色
 $Test->drawFilledRoundedRectangle(7,7,$Width-7,$Height-7,5,240,240,240);   
 $Test->drawRoundedRectangle(5,5,$Width-5,$Height-5,5,230,230,230);  
 
 $Test->setFontProperties("Fonts/simhei.ttf",18);
 
 // Draw the title
 $Title = "下单、出货总金额统计图";
 $Test->drawTextBox(10,10,$Width-10,50,$Title,0,255,255,255,ALIGN_CENTER,TRUE,0,0,0,30);
 
 $Test->setFontProperties("Fonts/simhei.ttf",8);
  //设置绘图区
 $Test->setGraphArea(70,60,$Width-150,$Height-30);

 $Test->drawGraphArea(255,255,255,TRUE);

 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,0,0,0,TRUE,0,2,TRUE);

 $Test->drawGraphAreaGradient(255,255,255,50);
 $Test->drawGrid(4,TRUE,230,230,230,20);  //绘制网格


 $Test->setFontProperties("Fonts/tahoma.ttf",9,255,0,0);  
 $Test->drawPosStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),90,0.5,0);//绘制柱形

 // Draw the legend
 $Test->setFontProperties("Fonts/simhei.ttf",9);
 $Test->drawLegend($Width-140,$Height/2,$DataSet->GetDataDescription(),236,238,240,52,58,82);
 // Render the picture
 //$Test->addBorder(1);
 //$Test->Render("test.png");
 $Test->Stroke();

?>