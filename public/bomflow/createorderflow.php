<?php   
//传入参数：$POrderId ,$ProductId 
$outImageSign=true;

if ($outImageSign){
   Header("Content-type: image/png"); //输出一个PNG 图片文件
   include "../../basic/chksession.php";
}
//echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

include "../../basic/parameter.inc";
include "../../basic/config.inc";

include  "pChart/pChart.class";
include  "flowfunction.php"; 

$FontName="../../Fonts/simhei.ttf";
$FontSize=10;

$LineWidth=1.5;
$LineColor="#DDDDDD";
$RecLineColor="#DDDDDD";
$RecbgColor="#EEEEEE";
$TitleColor="#000000";

$StuffIdArray=array();
$UniteIdArray=array();
$RelationArray=array();
$UsedIdArray=array();


$StuffIdList="";$UniteIdList="";$RelationList="";
	$StuffResult=mysql_query("SELECT StuffId FROM $DataIn.cg1_stocksheet  WHERE POrderId='$POrderId' ",$link_id);
	while($StuffRow=mysql_fetch_array($StuffResult)){
		$StuffId=$StuffRow["StuffId"];
		$StuffIdArray[]=$StuffId;
		
		$UniteId="";$Relation="";
		$UniteResult=mysql_query("SELECT U.uStuffId,U.Relation FROM $DataIn.cg1_stuffunite U  WHERE  U.POrderId='$POrderId' AND U.StuffId='$StuffId' AND U.uStuffId!='$StuffId' ",$link_id);
		while($UniteRow = mysql_fetch_array($UniteResult)){
		       $UniteId.=$UniteId==""?$UniteRow["uStuffId"]:"," . $UniteRow["uStuffId"];
		       $Relation.=$Relation==""?$UniteRow["Relation"]:"," . $UniteRow["Relation"];
		 }
		 $UniteIdArray[]=$UniteId;
		 $RelationArray[]=$Relation;
	}

$counts=count($StuffIdArray);

$rowTotal=0;
$dataArray=array();

//查找成品配件
$idStr=implode(",", $StuffIdArray);
$mainResult=mysql_query("SELECT G.StockId,S.StuffId,S.StuffCname,P.Forshort,A.cName 
            FROM $DataIn.cg1_stocksheet G 
            LEFT JOIN $DataIn.stuffdata S ON G.StuffId=S.StuffId   
            LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
            LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
            LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
            LEFT JOIN $DataIn.productdata A ON A.ProductId=Y.ProductId 
            WHERE G.POrderId='$POrderId' AND G.Level=1 AND T.mainType NOT IN(". $APP_CONFIG['STATISTICS_MAINTYPE'] .") ORDER BY Field(T.mainType,". $APP_CONFIG['PANDS_FINAL_MAINTYPE'] .") DESC ",$link_id);

$k=0; $n=0;
$mStuffId="";$Mid=""; $uniteArray=array();
while($mainRow=mysql_fetch_array($mainResult)){
  if ($Mid==""){
	  $Mid=strtoupper ( md5 ( uniqid ( rand (), true ) ) );
	  $mStuffId=$mainRow["StuffId"];
	  $Forshort=strstr($mainRow["StuffCname"], "(",true);
	  $rootArray=array();
	  $size=getFontSize($mainRow["StuffCname"],$FontName,$FontSize);
	  $rootArray[]=array('mStuffId'  =>"$mStuffId",
	                   'StuffId'   =>$mainRow["StuffId"],
	                   'StuffCname'=>$mainRow["cName"],
	                   'Forshort'  =>$Forshort,
	                   'Width'=>$size['w'],
	                   'Id'=>$Mid,
	                   'StockId'=>$mainRow["StockId"],
			           'Mid'=>"");
		
		$dataArray[$k]=$rootArray;
		$dataArray[$k]['ChildNodes']=mysql_num_rows($mainResult)-1;
	}
	else{
	    $size=getFontSize($mainRow["StuffCname"],$FontName,$FontSize);
        $Id= strtoupper ( md5 ( uniqid ( rand (), true ) ) );
        $UniteArray[$n]=array('mStuffId'  =>$mStuffId,
                         'StuffId'   =>$mainRow["StuffId"],
                         'StuffCname'=>$mainRow["StuffCname"],
                         'Forshort'  =>$mainRow["Forshort"],
                         'Width'=>$size['w'],
                         'Id'=>$Id,
                         'StockId'=>$mainRow["StockId"],
                         'Mid'=>$Mid);
                         
        $n++;                   
	}
}

if (count($UniteArray)>0){
    $k++;
	$dataArray[$k]=$UniteArray;
}


do{
  // print_r($dataArray[$k]);
   $uniteArray=getSemifinishedStuff($dataArray[$k],$StuffIdArray,$UniteIdArray,$DataIn,$link_id,$FontName,$FontSize,$APP_CONFIG['PANDS_FINAL_MAINTYPE'],$APP_CONFIG['STATISTICS_MAINTYPE']);
   if (count($uniteArray)>0){
      $k++;
	  $dataArray[$k]=$uniteArray;
   }
}while(count($uniteArray)>0);


$TotalCols=$k+1; //总列数
$maxRows=0; //最大行数
$maxColNo=0;//最大行数的列号 从右至左 0列开始

//取得最大行数及列号
for($i=0,$counts=count($dataArray);$i<$counts;$i++){
     $subCounts=count($dataArray[$i]);
     if ($subCounts>$maxRows){
	     $maxRows=$subCounts;
	     $maxColNo=$i;
     }
}

//print_r ($maxRows . "/" . $maxColNo);

//获取每列的最大宽度
$widthArray=array();
$TotalWidth=0;
for($i=0,$counts=count($dataArray);$i<$counts;$i++){
     $colWidth=0;
     foreach ($dataArray[$i] as $valueArray){
        $colWidth=$valueArray['Width']>$colWidth?$valueArray['Width']:$colWidth;
     }
     $widthArray[$i]=$colWidth;
     $TotalWidth+=$colWidth;
}

//按权重调整最大列及左边列的配件顺序关系
for ($col=$maxColNo;$col<$TotalCols;$col++){
    $rowArray=arrayValueSort($dataArray,$dataArray[$col],$col);
    
    $rowCounts=count($rowArray);
    
    $maxRows=$rowCounts>$maxRows?$maxRows+($rowCounts-$maxRows)*2:$maxRows;
    
    $dataArray[$col]=$rowArray;
}

/*
//对最大行数的列数据进行排序
$rowArray=arrayValueSort($dataArray,$dataArray[$maxColNo],$col);

$dataArray[$maxColNo]=$rowArray;
*/

if (!$outImageSign){
     print_r($dataArray);
}

//创造一个画布并赋予尺寸
$distance=30;
$colwidth=400;
$offsetX=60;

$im_w=$TotalWidth+100+$offsetX*$TotalCols*3;
//$im_w=$colwidth*$TotalCols+100+$offsetX*$TotalCols;
$hscale= $maxRows<10?2:1.25;
$im_h=$distance*$maxRows*$hscale;

$im = new pchart($im_w,$im_h);
$x=50;$y=50;//开始位置

//画最大行数的列
$prePosition=array();
$rowArray=$dataArray[$maxColNo];

$x1=$x+getColumnX($widthArray,$maxColNo,$offsetX);
//$x1=$x+($TotalCols-$maxColNo-1)*$colwidth;


$y1=$y;
for($i=0,$counts=count($rowArray);$i<$counts;$i++){
   $StuffCname=$rowArray[$i]['StuffCname'];
   $ChildNodes  =$rowArray[$i]['ChildNodes'];
   $StockId=$rowArray[$i]['StockId'];
   $bgColor=getNameBgColor($StockId,$DataIn,$link_id);
       
   $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$bgColor","$RecLineColor");
   $prePosition[]=$position;
   //$maxX=$position['x']>$maxX?$position['x']:$maxX;
   $y1+=$distance;
}

//左边画列
$tmp_Mid="";
$rowPosition=array();
$colPosition=array();

$colPosition[$maxColNo]=$prePosition;

for ($col=$maxColNo+1;$col<$TotalCols;$col++){
	
	$rowArray=$dataArray[$col];
	//$x1=$x+($TotalCols-$col-1)*$colwidth;
	$x1=$x+getColumnX($widthArray,$col,$offsetX);
	$y1=$y;
	$colPosition[$col]=array();
	$endColWidth=getColumnX($widthArray,$col-1,$offsetX)+$x;
	//$endColWidth=$colwidth*($TotalCols-$col);
	for($i=0,$counts=count($rowArray);$i<$counts;$i++){
	   $StuffCname=$rowArray[$i]['StuffCname'];
	   $mStuffId  =$rowArray[$i]['mStuffId'];
	   $Mid  =$rowArray[$i]['Mid'];
	   $StockId=$rowArray[$i]['StockId'];
       $bgColor=getNameBgColor($StockId,$DataIn,$link_id);
       
	   //计算位置
	   if ($tmp_Mid!=$Mid && $Mid!="" && $StuffCname!=""){
	   
	       $rows=count($rowPosition);
		   if ($rows>0){
			   //画延长线
			  $position=$rowPosition[0];
			  $x2=$position['x'];
			  $y2=$position['y'];
			  $len=$endColWidth-$x2-$offsetX*2;
			  if ($rows>0){
				 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
				 
				if ($rows>1){
					 $position=$rowPosition[$rows-1];
					 $x2=$position['x'];
					 $y2=$position['y'];
					 $len=$endColWidth-$x2-$offsetX*2;
					 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
					
					
					//画垂直封密线
					$x2=$endColWidth-$offsetX*2;
					$y2=$rowPosition[0]['y'];
					$len=$rowPosition[$rows-1]['y']-$y2;
					drawLineArrow($im,$x2,$y2,$len,$LineColor,'v',$LineWidth,0,false);
					$y2=$y2+$len/2;
				}
				else{
					$x2=$endColWidth-$offsetX*2;
				}
				
				//画带箭头延长线
				drawLineArrow($im,$x2,$y2,$offsetX/2,$LineColor,'h',$LineWidth,0,true);
				
				$x2=$x2+$offsetX/2;$y2=$y2-10;
				
				$rowNo=getStuffIdPositon($dataArray[$col-1],$tmp_Mid,0);
				$tempArray=$dataArray[$col-1][$rowNo];
				$Forshort=$tempArray['Forshort']==""?"内部加工":$tempArray['Forshort'];
				$FontColor=$Forshort=="关联配件"?"#FF8100":"#004F8A";
				
			    $position=RoundedRectangle($im,$x2,$y2,$Forshort,$FontName,$FontSize,"$FontColor","$RecbgColor","$RecLineColor");

			    $x2=$position['x'];
				$y2=$position['y'];
                $len=$endColWidth-$x2;
				 //画带箭头延长线
			     drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,true); 

			  }	
				$rowPosition=array();
				$y1=$y;
		   }
		   
		   
		   $rowNo=getStuffIdPositon($dataArray[$col-1],$Mid,0);
		   $mCounts=getmStuffIdCounts($rowArray,$Mid);
		   if (count($colPosition[$col-1])>0){
			   $y1=$colPosition[$col-1][$rowNo]['y'];
			   $y1-=($mCounts/2-0.15) * $distance;
		   }
		   else{
		       $y1+=$distance;
		       $y1-=$rowNo==0?($mCounts/2-1.5) * $distance:($mCounts/2-0.5) * $distance;
		   }
		   //$y1=$y1<$y?$y:$y1;
	
		   $tmp_Mid=$Mid;
		   
	   }
	   
	   $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$bgColor","$RecLineColor");
	   if ($StuffCname!="") {
	       $rowPosition[]=$position;
	   }
	   $colPosition[$col][]=$position;

	   $y1+=$distance;
	}
	   
	   $rows=count($rowPosition);
	   if ($rows>0){
		   //画延长线
		  $position=$rowPosition[0];
		  $x2=$position['x'];
		  $y2=$position['y'];
		  $len=$endColWidth-$x2-$offsetX*2;
		  if ($rows>0){
			 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
		
		  if ($rows>1){	 
			 $position=$rowPosition[$rows-1];
			 $x2=$position['x'];
			 $y2=$position['y'];
			 $len=$endColWidth-$x2-$offsetX*2;
			 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
			
			//画垂直封密线
			$x2=$endColWidth-$offsetX*2;
			$y2=$rowPosition[0]['y'];
			$len=$rowPosition[$rows-1]['y']-$y2;
			drawLineArrow($im,$x2,$y2,$len,$LineColor,'v',$LineWidth,0,false);
			
			$y2=$y2+$len/2;
			}
			else{
				$x2=$endColWidth-$offsetX*2;
			}

			//画带箭头延长线
			drawLineArrow($im,$x2,$y2,$offsetX/2,$LineColor,'h',$LineWidth,0,true);
			
			$tempArray=$dataArray[$col-1][$rowNo];
			$Forshort=$tempArray['Forshort']==""?"内部加工":$tempArray['Forshort'];
			$FontColor=$Forshort=="关联配件"?"#FF8100":"#004F8A";
				
			$x2=$x2+$offsetX/2;$y2=$y2-10;
		    $position=RoundedRectangle($im,$x2,$y2,$Forshort,$FontName,$FontSize,"$FontColor","$RecbgColor","$RecLineColor");

		    $x2=$position['x'];
			$y2=$position['y'];
            $len=$endColWidth-$x2;
			 //画带箭头延长线
		    drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,true); 
		}		
	 }
	 
	 $rowPosition=array();
	 $tmp_Mid="";

}

//右边画列

for ($col=$maxColNo-1;$col>=0;$col--){
	
	$rowArray=$dataArray[$col];
	//$x1=$x+($TotalCols-$col)*$colwidth;
	$x1=$x+getColumnX($widthArray,$col,$offsetX);
	$y1=$y;$tmpPosition=array();
	$noUniteArray=array();

	$preCol=$col+1;
	
	$endColWidth=getColumnX($widthArray,$col,$offsetX)+$x;
	
	for($i=0,$counts=count($rowArray);$i<$counts;$i++){
	   $StuffCname=$rowArray[$i]['StuffCname'];
	   $StuffId   =$rowArray[$i]['StuffId'];
	   $Forshort  =$rowArray[$i]['Forshort']==""?"内部加工":$rowArray[$i]['Forshort'];
	   $FontColor=$Forshort=="关联配件"?"#FF8100":"#004F8A";
	   $Id   =$rowArray[$i]['Id'];
	   
	   $StockId=$rowArray[$i]['StockId'];
       $bgColor=getNameBgColor($StockId,$DataIn,$link_id);
       
	   $mCounts=getmStuffIdCounts($dataArray[$preCol],$Id);
	   if ($mCounts>0){
	      $rowNo=getStuffIdPositon($dataArray[$preCol],$Id,1);
		  $y1=$rowNo*$distance; 
		  $y1-=($mCounts/2-0.5) * $distance;
		  $y1=$y1<$y?$y:$y1;
		  
		   $rows=count($prePosition);
		   if ($rows>0){
			   //画延长线
			  $position=$prePosition[$rowNo];
			  $x2=$position['x'];
			  //$endColWidth=$colwidth*($TotalCols-$col);
			  $y2=$position['y'];
			  $len=$endColWidth-$x2-$offsetX*2;
			  if ($rows>1){
			     
				 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
				 
				 $lastNo=$rowNo+$mCounts-1;
				 $position=$prePosition[$lastNo];
				 $x2=$position['x'];
				 $y2=$position['y'];
				 $len=$endColWidth-$x2-$offsetX*2;
				 
				 drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,false);
				
				//画垂直封密线
				 //$x2=$colwidth*($TotalCols-$col);
				 $x2=$endColWidth-$offsetX*2;
				 $y2=$prePosition[$rowNo]['y'];
				 $len=$prePosition[$lastNo]['y']-$y2;
				 drawLineArrow($im,$x2,$y2,$len,$LineColor,'v',$LineWidth,0,false);
				
				$y2=$y2+$len/2;
				
			}	
				//画带箭头延长线
				drawLineArrow($im,$x2,$y2,$offsetX/2,$LineColor,'h',$LineWidth,0,true);
				
				$x2=$x2+$offsetX/2;$y2=$y2-10;
			    $position=RoundedRectangle($im,$x2,$y2,$Forshort,$FontName,$FontSize,"$FontColor","$RecbgColor","$RecLineColor");
	
			    $x2=$position['x'];
				$y2=$position['y'];
	            $len=$endColWidth-$x2;
				 //画带箭头延长线
			    drawLineArrow($im,$x2,$y2,$len,$LineColor,'h',$LineWidth,0,true); 
		    
				$x1=$x2+$len;
				$y1=$y2-11;
		 }
		  $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$bgColor","$RecLineColor");
	      $tmpPosition[]=$position;
	      $maxX=$position['x']>$maxX?$position['x']:$maxX;
	      $y1+=$distance;
		   
	   }
	   else{
		   $noUniteArray[]=$rowArray[$i];
	   }
	}
	
	//绘制无关联配件
	//$x1=$x+($TotalCols-$col)*$colwidth;
	$x1=$x+getColumnX($widthArray,$col,$offsetX);
	for($k=0,$counts=count($noUniteArray);$k<$counts;$k++){
	   $StuffCname=$noUniteArray[$k]['StuffCname'];
	   $StuffId   =$noUniteArray[$k]['StuffId'];
       $position=RoundedRectangle($im,$x1,$y1,$StuffCname,$FontName,$FontSize,"$textcolor","$RecbgColor","$RecLineColor");
       $tmpPosition[]=$position;
       $maxX=$position['x']>$maxX?$position['x']:$maxX;
       $y1+=$distance;
	}
	
	$prePosition=array();
	$prePosition=$tmpPosition;

}

if ($outImageSign){
	$im->AntialiasQuality = 0;

	$outFileName="../../download/orderflow/" . $POrderId . ".png";
	$im->Render($outFileName);
	$im->Stroke();
}

function getbgColor($sign)
{
   switch($sign){
	    case 1: return "#4EC4D8"; break;//蓝色
	    case 2: return "#69DA87"; break;//绿色
	    case 4: return "#C71585"; break;//紫色
	   default: return "#EEEEEE"; break;
     }
}

//获取状态颜色
function getNameBgColor($StockId,$DataIn,$link_id){
   $sign=0;
   $checkResult=mysql_query("SELECT G.Mid,IF(K.tStockQty>=G.OrderQty,1,0) AS KblSign,IF(L.llQty>=G.OrderQty,1,0) AS YblSign 
		                FROM $DataIn.cg1_stocksheet G 
		                LEFT JOIN $DataIn.ck9_stocksheet K  ON K.StuffId=G.StuffId 
		                LEFT JOIN (
		                    SELECT StockId,IFNULL(SUM(Qty),0) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' 
		                )L ON G.StockId=L.StockId 
		                WHERE G.StockId='$StockId'",$link_id);
   if($checkRow = mysql_fetch_array($checkResult)){
       //$sign=$checkRow['Mid']>0?1:$sign;
       $sign=$checkRow['KblSign']>0?1:$sign;
       $sign=$checkRow['YblSign']>0?2:$sign;
   }              
   return getbgColor($sign);
}


//获取半成品配件列表
function getSemifinishedStuff(&$dataArray,$StuffIdArray,$UniteIdArray,$DataIn,$link_id,$FontName,$FontSize,$mianType,$noType)
{
    $n=0; $UniteArray=array(); 
    for($i=0,$counts=count($dataArray);$i<$counts;$i++){ 
      $subArray=$dataArray[$i];
      $mStuffId=$subArray['StuffId'];
      $mStockId=$subArray['StockId'];
	  $UniteResult=mysql_query("SELECT M.Id,M.StockId,S.StuffId,S.StuffCname,S.Picture,P.Forshort,T.mainType 
		                FROM $DataIn.cg1_semifinished  M 
		                LEFT JOIN $DataIn.stuffdata S  ON S.StuffId=M.StuffId 
		                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
		                LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
                        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
		                WHERE M.mStockId='$mStockId' AND M.mStuffId='$mStuffId' AND T.mainType NOT IN ($noType) ORDER BY M.Id",$link_id);
	  if($UniteRow = mysql_fetch_array($UniteResult)){
	     $m=0;
	     do{
	         if ($UniteRow["mainType"]==$mianType){
		        $dataArray[$i]['Forshort']=strstr($UniteRow["StuffCname"], "(",true);
	         }
	         else{
	            $size=getFontSize($UniteRow["StuffCname"],$FontName,$FontSize);
	           
	            $Id=strtoupper ( md5 ( uniqid ( rand (), true ) ) );
	           
	            $UniteArray[$n]=array('mStuffId'  =>$mStuffId,
	                                 'StuffId'   =>$UniteRow["StuffId"],
	                                 'StuffCname'=>$UniteRow["StuffCname"],
	                                 'Forshort'  =>$UniteRow["Forshort"],
	                                 'Width'=>$size['w'],
	                                 'Id'=>$Id,
	                                 'StockId'=>$UniteRow["StockId"],
	                                 'Mid'=>$subArray['Id']); 
	           $n++;$m++;
	         }
	     }while($UniteRow = mysql_fetch_array($UniteResult));
	     $dataArray[$i]['ChildNodes']=$m;
	  }
	  
	  else{
		 $Unite2Array=getUniteStuff($subArray,$StuffIdArray,$UniteIdArray,$DataIn,$link_id,$FontName,$FontSize);
		 $m=0;
		 for($j=0,$iCounts=count($Unite2Array);$j<$iCounts;$j++){
		    $UniteArray[$n]=$Unite2Array[$j];
		    $n++;$m++;
		 }
		 if ($m>0){
		    $dataArray[$i]['ChildNodes']=$m;
			$dataArray[$i]['Forshort']="关联配件";
		 }
	  } 
	              
	}
	return $UniteArray;               	                
}

function getUniteStuff($subArray,$StuffIdArray,$UniteIdArray,$DataIn,$link_id,$FontName,$FontSize)
{

    $UniteArray=array(); 
    $n=0;
    for($j=0,$iCounts=count($StuffIdArray);$j<$iCounts;$j++){
	     if ($StuffIdArray[$j]==$subArray['StuffId']){
		    $UniteId=$UniteIdArray[$j];
		    $dataArray[$i]['ChildNodes']=$UniteId!=""?count(explode(",", $UniteId)):0;
		    if ($UniteId!=""){
			  $UniteResult=mysql_query("SELECT S.StuffId,S.StuffCname,S.Picture,P.Forshort   
                FROM $DataIn.stuffdata S    
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
                LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
                LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
                WHERE S.StuffId IN ($UniteId) ORDER BY T.mainType",$link_id);
                while($UniteRow = mysql_fetch_array($UniteResult)){
                   $size=getFontSize($UniteRow["StuffCname"],$FontName,$FontSize);
                   $Id= strtoupper ( md5 ( uniqid ( rand (), true ) ) );
                   $UniteArray[$n]=array('mStuffId'  =>$subArray['StuffId'],
                                         'StuffId'   =>$UniteRow["StuffId"],
                                         'StuffCname'=>$UniteRow["StuffCname"],
                                         'Forshort'  =>$UniteRow["Forshort"],
                                         'Width'=>$size['w'],
                                         'Id'=>$Id,
                                         'Mid'=>$subArray['Id']);
                   $n++;
                }
		    }
		    break; 
	     }  
	}
	return $UniteArray;
}

/*
function getUniteStuff(&$dataArray,$StuffIdArray,$UniteIdArray,$DataIn,$link_id,$FontName,$FontSize)
{

    $UniteArray=array(); 
    $n=0;
    for($i=0,$counts=count($dataArray);$i<$counts;$i++){ 
            $subArray=$dataArray[$i];
		    for($j=0,$iCounts=count($StuffIdArray);$j<$iCounts;$j++){
			     if ($StuffIdArray[$j]==$subArray['StuffId']){
				    $UniteId=$UniteIdArray[$j];
				    $dataArray[$i]['ChildNodes']=$UniteId!=""?count(explode(",", $UniteId)):0;
				    if ($UniteId!=""){
					  $UniteResult=mysql_query("SELECT S.StuffId,S.StuffCname,S.Picture,P.Forshort   
		                FROM $DataIn.stuffdata S    
		                LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
		                LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
                        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
		                WHERE S.StuffId IN ($UniteId) ORDER BY T.mainType",$link_id);
		                while($UniteRow = mysql_fetch_array($UniteResult)){
		                   $size=getFontSize($UniteRow["StuffCname"],$FontName,$FontSize);
		                   $Id= strtoupper ( md5 ( uniqid ( rand (), true ) ) );
		                   $UniteArray[$n]=array('mStuffId'  =>$subArray['StuffId'],
		                                         'StuffId'   =>$UniteRow["StuffId"],
		                                         'StuffCname'=>$UniteRow["StuffCname"],
		                                         'Forshort'  =>$UniteRow["Forshort"],
		                                         'Width'=>$size['w'],
		                                         'Id'=>$Id,
		                                         'Mid'=>$subArray['Id']);
		                   $n++;
		                }
				    }
				    break; 
			     }  
			}
	}
	return $UniteArray;
}
*/

function getStuffIdPositon($rowArray,$Mid,$mSign=0)
{
    $rowNo=0;
	for($i=0,$counts=count($rowArray);$i<$counts;$i++){
	   $Id=$mSign==1?$rowArray[$i]['Mid']:$rowArray[$i]['Id'];
	   if($Mid  ==$Id && isset($rowArray[$i]['StuffCname'])){
		   $rowNo=$i;
		   break;
	   } 
	}
	return $rowNo;
}

function getmStuffIdCounts($rowArray,$Mid)
{
    $rowCounts=0;
    foreach ($rowArray as $valueArray){
	   if($Mid  ==$valueArray['Mid']){
		   $rowCounts++;
	   }  
    }
    $counts=count($rowArray);
    $kCounts=0;
    for ($i=0;$i<$counts;$i++){
       if ($Mid  ==$rowArray[$i]['Mid']){
         if (isset($rowArray[$i]['StuffCname'])){
		       break;
	     }
	     $kCounts++;
       }
    }
    
    for ($i=$counts-1;$i>0;$i--){
       if ($Mid  ==$rowArray[$i]['Mid']){
	       if (isset($rowArray[$i]['StuffCname'])){
		       break;
	       }
	       $kCounts++;
       }
       
    }
    
	return $rowCounts-$kCounts;
}

//获取列($col)的起始位置
function getColumnX($widthArray,$col,$offsetX){
  $sx=0;
  for($i=$col+1,$counts=count($widthArray);$i<$counts;$i++){
     $sx+=$widthArray[$i];
     $sx+=$offsetX*3;
  }
  return $sx;
}

function getMaxChildNodes($dataArray,$col,$StuffId)
{
    $Nodes=0;
	for ($i=$col+1,$counts=count($dataArray);$i<$counts;$i++){
	
	     $colArray=$dataArray[$i];
		 foreach ($colArray as $valueArray){
		 
			 if($StuffId  ==$valueArray['mStuffId']){
				   $Nodes=$valueArray['ChildNodes']>$Nodes?$valueArray['ChildNodes']:$Nodes;
				   $subNodes=isset($valueArray['StuffId'])?getMaxChildNodes($dataArray,$i,$valueArray['StuffId']):0;
				   $Nodes=$subNodes>$Nodes?$subNodes:$Nodes;
			 } 
			 
	   }   
	}
	return $Nodes;
}

function arrayValueSort($dataArray,$rowArray,$col){
    $n=0;$oldStuffId="";
	$rowdatas=array();
    
	for($i=0,$counts=count($rowArray);$i<$counts;$i++){
	    $mStuffId=$rowArray[$i]['mStuffId'];
	    $maxNodes=getMaxChildNodes($dataArray,$col,$rowArray[$i]['StuffId']);
	    $ChildNodes=$maxNodes>$rowArray[$i]['ChildNodes']?$maxNodes:$rowArray[$i]['ChildNodes'];
	    $rowArray[$i]['ChildNodes']=$ChildNodes;
	    
	    if ($oldStuffId!=$mStuffId){
		    $oldStuffId=$mStuffId;
		    if ($i>0) $n++;
	    }
	    $rowdatas[$n][]= $rowArray[$i];
	}
	
	$rowArray=array();$m=0;
	for($i=0,$counts=count($rowdatas);$i<$counts;$i++){
	    $values=$rowdatas[$i];
	    
	    $nodes=array();
	    foreach ($values as $value) {
	         $nodes[] = $value['ChildNodes']  . '-' . $value['Mid'];
	    }
	    array_multisort($nodes, SORT_ASC, $values);
	
	    $blanks=0;
	    for($j=0,$lens=count($values);$j<$lens;$j++){
	        $ChildNodes=$values[$j]["ChildNodes"];
	        if ($ChildNodes==0){
		        $blanks++;
		        $rowArray[$m]=$values[$j];
	        }
	        else{
	            $newRow=$ChildNodes;//-$blanks
		        if ($newRow>0){
			        for ($n=0;$n<$newRow;$n++){
				       $rowArray[$m]=array('Mid'  =>$values[$j]["Mid"],'mStuffId'  =>$values[$j]["mStuffId"]);
				       $m++;
			        }
			        $rowArray[$m]=$values[$j];
			        
			        if ($i<$counts-1 && $j>0){
				        for ($n=0;$n<=$newRow;$n++){
				           $m++;
					       $rowArray[$m]=array('Mid'  =>$values[$j]["Mid"],'mStuffId'  =>$values[$j]["mStuffId"]);
				        }
			        }
			        
		        }
		        else{
			       if ($newRow<0){
				       $rowArray[$m]=$rowArray[$m+$newRow];
				       $rowArray[$m+$newRow]=$values[$j];   
			       } 
			       else{
				       $rowArray[$m]=$values[$j];
			       }
		        }
		        $blanks=0;
	        }
	       $m++;
	    } 
	}
	return $rowArray;
}

 
?>