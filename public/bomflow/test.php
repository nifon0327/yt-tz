<?php   
//传入参数$POrderId,$ProductId 
//电信-zxq 2012-08-01
//Header("Content-type: image/png"); //输出一个PNG 图片文件
include "../../basic/parameter.inc";

include  "pChart/pChart.class";
include  "flowfunction.php"; 

ini_set('display_errors', 'On');

$FontName="D:/website/mc/public/bomflow/Fonts/simhei.ttf";
$FontSize=10;

$LineWidth=1.5;
$LineColor="#DDDDDD";
$RecLineColor="#DDDDDD";
$RecbgColor="#BBBBBB";
$TitleColor="#000000";

$rowTotal＝0;

//$POrderId="201311140911";
//读取关联配件表
$StuffIdArray=array();
$StuffArray=array();
$UsedIdArray=array();
$scFrom=1; $PackName="";
$unBlSign=0; $ableBlSign=1;
$G_Reslut=mysql_query("SELECT Y.ProductId,Y.scFrom,Y.Estate,G.Mid,G.StuffId,SUM(G.OrderQty) AS OrderQty,SUM(G.AddQty+G.FactualQty) AS cgQty,S.StuffCname,T.mainType,T.TypeId,T.TypeName,P.Forshort,K.tStockQty  
                  FROM   $DataIn.yw1_ordersheet Y  
                  LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=Y.POrderId 
                  LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
                  LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                  LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId
                  LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
                  WHERE  Y.POrderId='$POrderId' GROUP BY G.StuffId ORDER BY T.mainType,G.Id ",$link_id);
if ($G_Row=mysql_fetch_array($G_Reslut)){
       $scFrom=$G_Row["scFrom"];
       $Estate=$G_Row["Estate"];
  do{
  		 //echo '<pre>', print_r($G_Reslut), '</pre>';
         $ProductId=$G_Row["ProductId"];
         
		 $StuffId=$G_Row["StuffId"];
		 $StuffCname=$G_Row["StuffCname"];
		 $mainType=$G_Row["mainType"];
		 $TypeName=$G_Row["TypeName"];
		 $Forshort=$G_Row["Forshort"];
		 
		 // if ($G_Row["TypeId"]=='7100') $PackName=$G_Row["StuffCname"];
		   if ($G_Row["TypeId"]=='7100') $PackName="组装加工";
		 $SendSign=0;
		 $dataArray=array();
		 $UniteResult=mysql_query("SELECT U.uStuffId,S.StuffCname,K.tStockQty,T.mainType    
		        FROM $DataIn.pands_unite U 
                LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=U.uStuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
                WHERE U.ProductId='$ProductId' AND U.StuffId='$StuffId'  ORDER BY T.mainType",$link_id);
       if($UniteRow = mysql_fetch_array($UniteResult)){
              $SendSign=2;
          do{
              $uStuffId=$UniteRow["uStuffId"];
              $tStockQty=$UniteRow["tStockQty"];
              $uMainType=$UniteRow["mainType"];
              //订单需求数量
              $QtyResult= mysql_fetch_array(mysql_query("SELECT SUM(OrderQty) AS OrderQty  FROM $DataIn.cg1_stocksheet  WHERE POrderId='$POrderId' AND StuffId='$uStuffId'",$link_id));
               $OrderQty=$QtyResult["OrderQty"]==""?0:$QtyResult["OrderQty"];
               
                //检查是否已备料
              $BlResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty  FROM $DataIn.ck5_llsheet  WHERE LEFT(StockId,12)='$POrderId' AND StuffId='$uStuffId'",$link_id));
               $BlQty=$BlResult["Qty"]==""?0:$BlResult["Qty"];
               $blSign=$BlQty==$OrderQty?2:0;
               
               if ($blSign==0){
                      $unBlSign=1;
	                  $blSign=$tStockQty>=($OrderQty-$BlQty)?1:0;
	                  if ($blSign==0) $ableBlSign=0;
               }
               
               if ($mainType<2) $UsedIdArray[]=$uStuffId;
               $dataArray[]=array("Name"=>$UniteRow["StuffCname"],"blSign"=>$blSign,"mainType"=>"$uMainType");
               
                $rowTotal++;
             }while($UniteRow = mysql_fetch_array($UniteResult));
         }
        else{
			    if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
			         $SendSign=1;
			         $SendName="1F";
			         $UsedIdArray[]=$StuffId;
			         $rowTotal++;
			    }else{
					$propertyResult=mysql_query("SELECT Property FROM  $DataIn.stuffproperty WHERE StuffId='$StuffId' AND Property=6",$link_id);
					if (mysql_num_rows($propertyResult)>0){
						 $SendSign=1;
				         $SendName="4F";
				         $UsedIdArray[]=$StuffId;
				         $rowTotal++;
					} 
			     }
		 }
         if ($SendSign>0){
	           $TypeName=$mainType<2?"外发":"";
			    if ($SendSign==2){
			          $odSign=$G_Row["Mid"]>0?2:0;
			          $rkSign=0;
			          if ($odSign==2){
				               //检查是否已收货
				              $RkResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty  FROM $DataIn.ck1_rksheet  WHERE LEFT(StockId,12)='$POrderId' AND StuffId='$StuffId'",$link_id));
				              $rkQty=$RkResult["Qty"]==""?0:$RkResult["Qty"];
				              $rkSign=$rkQty==$G_Row["cgQty"]?2:0;
			          }
			          $Forshort=$mainType<2?$Forshort:"";
				      $UniteArray[]=array("Name"=>$StuffCname,"SendName"=>$TypeName,"Forshort"=>$Forshort,
				                                        "odSign"=>$odSign,"rkSign"=>$rkSign,"data"=>$dataArray); 
			    }    
			    else{
			        $tStockQty=$G_Row["tStockQty"];
                     $OrderQty=$G_Row["OrderQty"];
                     $blSign=$tStockQty>=$OrderQty?2:0;
				     $SendArray[]=array("Name"=>$StuffCname,"SendName"=>$SendName,"blSign"=>$blSign); 
			    }
       }
       
       if ($mainType<2) {
            $tStockQty=$G_Row["tStockQty"];
            $OrderQty=$G_Row["OrderQty"];
           
            //检查是否已备料
             $BlResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty  FROM $DataIn.ck5_llsheet  WHERE LEFT(StockId,12)='$POrderId' AND StuffId='$StuffId'",$link_id));
              $BlQty=$BlResult["Qty"]==""?0:$BlResult["Qty"];
              $blSign=$BlQty==$OrderQty?2:0;
               if ($blSign==0){
                      $unBlSign=1;
	                  $blSign=$tStockQty>=($OrderQty-$BlQty)?1:0;
	                  if ($blSign==0) $ableBlSign=0;
               }
              $StuffIdArray[]=$StuffId;
              $StuffArray[$StuffId]=array("Name"=>$StuffCname,"blSign"=>$blSign); 
       }
	}while($G_Row=mysql_fetch_array($G_Reslut));
}

//去掉已使用StuffId
$StuffIdArray = array_diff($StuffIdArray,$UsedIdArray);
$rowTotal+=count($StuffIdArray);

  //创造一个画布并赋予尺寸
$distance=30;
$offsetX=50;
$im_w=1200;
$im_h=$distance*$rowTotal+20+80;
$im = new pchart($im_w,$im_h);
//$im->addBorder(2,220,220,220);
//$im->drawBackground(255,255,255);
$x=50;$y=20;//开始位置

$maxX=0;
$textcolor="#FFFFFF";

for($i=0;$i<count($UniteArray);$i++){
	$rowArray=$UniteArray[$i];
	$dataArray=$rowArray["data"];
	
	$x1=$x;$y1=$y;$len=30;
	$rowPosition=array();
	
	$nums=count($dataArray);
	for($j=0;$j<$nums;$j++){
	    $uArray=$dataArray[$j];
		$StuffCname=$uArray["Name"];
		$mainType=$uArray["mainType"];
		
		if ($j>0){
		        if ($mainType>1){
		              $position=$rowPosition[$j-1];
		              $x1=$position['x'];
		              $y2=$position['y'];
		             //画带箭头延长线
		             drawLineArrow($im,$x1,$y2,$offsetX,$LineColor,'h',$LineWidth,0,true);
		             unset($rowPosition[$j-1]);
		             $rowPosition=array_merge($rowPosition);
		             $x1+=$offsetX; $y1-=$distance;
		             //$drows++;
		        }
        }

		$RecbgColor=getbgColor($uArray["blSign"]);
		 $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor");
         $rowPosition[]=$position;
         $maxX=$position['x']>$maxX?$position['x']:$maxX;
         $y1+=$distance;
	}
	 $y=$y1+15;//换行
	
	 //画延长线
	  $rows=count($rowPosition);
	  $position=$rowPosition[0];
	  $x1=$position['x'];
	  $y1=$position['y'];
	  $len=$maxX-$x1+$offsetX;
	  if ($rows>1){
		 drawLineArrow($im,$x1,$y1,$len,$LineColor,'h',$LineWidth,0,false);
		 
		 $position=$rowPosition[$rows-1];
		  $x1=$position['x'];
		  $y1=$position['y'];
		  $len=$maxX-$x1+$offsetX;
		 drawLineArrow($im,$x1,$y1,$len,$LineColor,'h',$LineWidth,0,false);
		
		//画垂直封密线
		$x1=$maxX+$offsetX;
		$y1=$rowPosition[0]['y'];
		$len=$rowPosition[$rows-1]['y']-$y1;
		drawLineArrow($im,$x1,$y1,$len,$LineColor,'v',$LineWidth,0,false);
		
		$y1=$y1+$len/2;
	}	
		//画带箭头延长线
		drawLineArrow($im,$x1,$y1,$offsetX,$LineColor,'h',$LineWidth,0,true);

	if ($rowArray["SendName"]!=""){
	      $FileName="../../images/gys7.gif";
           $im->drawFromGIF($FileName,$x1+14,$y1-20,$Alpha=100);
		 //  drawWithText($im,$x1+10,$y1-5,$rowArray["SendName"],$FontName,$FontSize-2,"$TitleColor");
	}
	$x1=$x1+$offsetX;$y1=$y1-12;
	if ($rowArray["Forshort"]!=""){
	      $RecbgColor=getbgColor($rowArray["odSign"]);
	      $position=RoundedRectangle($im,$x1,$y1,$rowArray["Forshort"],$FontName,$FontSize,"#004F8A","$RecbgColor","$RecLineColor");
	      $x1=$position['x'];
		  $y1=$position['y'];
		  
		 //画带箭头延长线
		 drawLineArrow($im,$x1,$y1,$offsetX,$LineColor,'h',$LineWidth,0,true); 
		 $x1=$x1+$offsetX;$y1=$y1-12;
	}
	$RecbgColor=getbgColor($rowArray["rkSign"]);
	$position=RoundedRectangle($im,$x1,$y1,$rowArray["Name"],$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor");
	
}

$x1=$x;$y1=$y;
$maxX=0;
$rowPosition=array();
foreach ($StuffIdArray as $StuffId){ 
      $rowArray=$StuffArray[$StuffId];
      $StuffCname=$rowArray["Name"];
      $RecbgColor=getbgColor($rowArray["blSign"]);
      
      $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor");
      $rowPosition[]=$position;
      $maxX=$position['x']>$maxX?$position['x']:$maxX;
      $y1+=$distance;
 }
 $y=$y1;
 
 //画延长线
  $rows=count($rowPosition);
  $position=$rowPosition[0];
  $x1=$position['x'];
  $y1=$position['y'];
  $len=$maxX-$x1+$offsetX;
 drawLineArrow($im,$x1,$y1,$len,$LineColor,'h',$LineWidth,0,false);
 
 $position=$rowPosition[$rows-1];
  $x1=$position['x'];
  $y1=$position['y'];
  $len=$maxX-$x1+$offsetX;
 drawLineArrow($im,$x1,$y1,$len,$LineColor,'h',$LineWidth,0,false);

 //画垂直封密线
$x1=$maxX+$offsetX;
$y1=$rowPosition[0]['y'];
$len=$rowPosition[$rows-1]['y']-$y1;
drawLineArrow($im,$x1,$y1,$len,$LineColor,'v',$LineWidth,0,false);

//画带箭头延长线
$y1=$y1+$len/2;
drawLineArrow($im,$x1,$y1,$offsetX,$LineColor,'h',$LineWidth,0,true);

$x1=$x1+$offsetX;
$FontSize=12;

if ($PackName!=""){
	   //画带箭头延长线
	  $colorSign=$ableBlSign==1?1:0;
	  $colorSign=$unBlSign==0?2:$colorSign;
      $RecbgColor=getbgColor($colorSign); 
	 $position=RoundedRectangle($im,$x1,$y1-12," 备 料 ",$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor",0);
	  $x1=$position['x'];
      $y1=$position['y'];
	   drawLineArrow($im,$x1,$y1,$offsetX,$LineColor,'h',$LineWidth,0,true);
      $x1=$x1+$offsetX;
      
      $colorSign=$unBlSign==0?1:0;
      $colorSign=$scFrom==0?2:$colorSign;
       $RecbgColor=getbgColor($colorSign);
      $position=RoundedRectangle($im,$x1,$y1-12," 组 装 ",$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor",0);
	  $x1=$position['x'];
      $y1=$position['y'];
	   drawLineArrow($im,$x1,$y1,$offsetX,$LineColor,'h',$LineWidth,0,true);
      $x1=$x1+$offsetX;
}

$PackName=" 成品仓 ";
//$PackName=$PackName==""?"成品仓":$PackName;
$colorSign=$scFrom==0?1:0;
$colorSign=($Estate>2 || $Estate==0)?2:$colorSign;
$RecbgColor=getbgColor($colorSign);  
$position=RoundedRectangle($im,$x1,$y1-12,$PackName,$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor",0);
/*
if ($scFrom==0){
    $RecbgColor=getbgColor(2);
	$position=RoundedRectangle($im,$x1,$y1-12,$PackName,$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor",0);
}
else{
	//drawWithText($im,$x1+5,$y1+5,$PackName,$FontName,$FontSize,"$TitleColor");
}
*/
$x1=$x;$y1=$y+15;$len=50;
$FontSize=10;
$rowPosition=array();
for($i=0;$i<count($SendArray);$i++){
	$rowArray=$SendArray[$i];
	$RecbgColor=getbgColor($rowArray["blSign"]);
	$position=RoundedRectangle($im,$x1,$y1, $rowArray["Name"],$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor");
	
	drawLineArrow($im,$position['x'],$position['y'],$len,$LineColor,'h',$LineWidth,0,true);
	
	drawWithText($im,$position['x']+$len+5,$position['y']+8,$rowArray["SendName"],$FontName,$FontSize+5,"$TitleColor");
	$y1+=$distance;//换行
}


$im->AntialiasQuality = 0;

//$outFileName="../../download/orderflow/" . $POrderId . ".png";
//$im->Render('output.png');
$im->Stroke();

function getbgColor($sign)
{
   switch($sign){
	  case 1: return "#009BC9"; break;
	  case 2: return "#1BC900"; break;
	default: return "#BBBBBB"; break;
   }
}
?>