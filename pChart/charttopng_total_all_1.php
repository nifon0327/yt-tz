<?php 
//皮套专用-EWEN 2012-08-17
//按产品分类设置
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
include "../basic/chksession.php";
 header("Content-Type:text/html;charset=utf-8");
include "../basic/parameter.inc";
$StratDate="2011-12-01";//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";	

 //取得产品分类
$m=0;$IdArray=array();$NameArray=array();$ColorArray=array();
$all_Serie=array();
$sum_chSerie=0;$sum_odSerie=0;$Sum_wcCost=0;
$TypeSql=mysql_query("SELECT T.TypeId,T.TypeName,C.ColorCode FROM $DataIn.chart3_color C 
                     LEFT JOIN $DataIn.producttype T  ON C.TypeId=T.TypeId 
                     WHERE T.Estate=1 AND C.Estate=1 ORDER BY T.SortId",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
    do {
       $IdArray[$m]=$TypeRow["TypeId"];
       $NameArray[$m]=$TypeRow["TypeName"]; 
       $ColorArray[$m]=$TypeRow["ColorCode"]; 
       $m++;
    }while($TypeRow=mysql_fetch_array($TypeSql)); 
}

$ch_Serie=array();$od_Serie=array();$MaxValue=0;
for($j=0;$j<$m;$j++){
   $TypeId=$IdArray[$j];
    //下单金额 
       $odAmountSql=mysql_fetch_array(mysql_query("
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 $TJB AND P.TypeId='$TypeId'",$link_id));
        $odAmount=$odAmountSql["Amount"];
        $odAmount=$odAmount==""?0:sprintf("%.0f",$odAmount);
        
        array_push($od_Serie,sprintf("%.0f",$odAmount)); 
        //array_push($all_Serie[$j],sprintf("%.0f",$odAmount/10000)); 
        if ($MaxValue<$odAmount) $MaxValue=$odAmount;
        $sum_odSerie+=$odAmount;
   //出货金额
   $chAmountSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                        LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA AND P.TypeId='$TypeId'",$link_id)); 
    $chAmount=$chAmountSql["Amount"];
    $chAmount=$chAmount==""?0:sprintf("%.0f",$chAmount);
     array_push($ch_Serie,sprintf("%.0f",$chAmount)); 
     if ($MaxValue<$chAmount) $MaxValue=$chAmount;
     $sum_chSerie+=$chAmount;
}

// Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");
 $DataSet = new pData;

 //以出货金额排序
 arsort($ch_Serie);  
 $k=0;$SerieName=array();$od_Sort=array();
 foreach ( $ch_Serie as $Key => $Values ){
    $SerieName[$k]=$NameArray[$Key];  
    $od_Sort[$k]=$od_Serie[$Key];
    $k++;
 }
     
 $DataSet->AddPoint($ch_Serie, "Serie0"); 
 $DataSet->AddPoint($SerieName,"SerieName");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("SerieName");
 $DataSet->SetAbsciseLabelSerie("SerieName");

 $TypeName="出货总额：" . number_format($sum_chSerie) . "元";
 $DataSet->SetSerieName($TypeName,"Serie0"); 
 $TypeName="接单总额：" . number_format($sum_odSerie) . "元";
 $DataSet->SetSerieName($TypeName,"Serie1");

 $DataSet->SetXAxisName("金额(万元)");

 $Width=720;
 $Height=count($SerieName)*50;
 $Height=$Height<400?400:$Height;

 $Test = new pChart($Width,$Height);

 //$Test->drawGraphAreaGradient(132,173,131,50,TARGET_BACKGROUND); //绘制背景颜色
 $Test->drawFilledRoundedRectangle(7,7,$Width-7,$Height-7,5,240,240,240);   
 $Test->drawRoundedRectangle(5,5,$Width-5,$Height-5,5,230,230,230);  
 
 $Test->setFontProperties("Fonts/simhei.ttf",18);

 // Draw the title
 $Title = "下单、出货总金额统计图 $MaxValue";
 $Test->drawTextBox(10,10,$Width-10,50,$Title,0,255,255,255,ALIGN_CENTER,TRUE,0,0,0,30);
 
 $Test->setFontProperties("Fonts/simhei.ttf",9);
  //设置绘图区
 $Test->setGraphArea(120,60,$Width-30,$Height-50);
  
 $MaxValue=$MaxValue/10000;
 $Test->drawGraphArea(255,255,255,TRUE);
//$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,0,0,0,TRUE,0,2,TRUE);
$Test->drawXScale($DataSet->GetData(),$DataSet->GetDataDescription(),$MaxValue,10,0,0,0,0,0);

 $Test->drawGraphAreaGradient(255,255,255,50);
 $Test->drawXGrid(4,TRUE,230,230,230,20);  //绘制网格


 $Test->setFontProperties("Fonts/tahoma.ttf",9,20,83,251);
 $Test->setColorPalette(0,20,83,251);
 $Test->drawXBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),FALSE,80,0.4,0.2,10000,FALSE,TRUE,TRUE,1,255,255,255);
 
 $DataSet->removeAllSeries();
 $DataSet->removeAllData(); 
 
 $DataSet->AddPoint($od_Sort, "Serie1"); 
 $Test->setColorPalette(1,0,169,251);
 $DataSet->AddAllSeries();
 $Test->setFontProperties("Fonts/tahoma.ttf",9,0,169,251);  
 $Test->drawXBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),FALSE,80,0.4,-0.2,10000,FALSE,TRUE,TRUE,0);
 $Test->setFontProperties("Fonts/simhei.ttf",10);
 $Test->drawLegend($Width-250,70,$DataSet->GetDataDescription(),236,238,240,52,58,82);

 $Test->Stroke();
?>