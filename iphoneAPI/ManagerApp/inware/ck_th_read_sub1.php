<?php 
//退货待处理 
$onEditSign=7;
$editSign=1;

$baseUrl = "http://ashcloud.com/qrpj.php?I=";

$myResult=mysql_query("SELECT  S.Id,S.StuffId,S.StockId,S.shQty,S.Qty,
S.Date,D.StuffCname,D.Picture,M.CompanyId,P.Forshort,L.Id AS Lid  ,O.Name as oper ,
D.FrameCapacity
                        FROM $DataIn.qc_badrecord S 
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN $DataIn.staffmain O on O.Number=S.Operator
						LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
						LEFT JOIN $DataIn.ck12_thprintlabel L ON L.Mid=S.Id 
						WHERE S.Estate=1 AND S.Qty>0 AND M.Floor='$Floor' GROUP BY S.Id ORDER BY M.CompanyId,S.Date ",$link_id);
					
 $sumQty=0;$sumCount=0;
 $totalQty=0;$pos=0;
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array(); $m=0;
 if($myRow = mysql_fetch_array($myResult)) 
  {
        $OldCompanyId=$myRow["CompanyId"];
        $Forshort=$myRow["Forshort"];
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["Date"];
            $CompanyId=$myRow["CompanyId"];
            
            
            if ($OldCompanyId!=$CompanyId){
	           $sumArray=array();
	           $sumQty=number_format($sumQty);
	           $tempArray=array(
	                      "Id"=>"$CompanyId",
	                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$sumQty($sumCount)","FontSize"=>"13","Margin"=>"20,0,0,0"),
	                      "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign")
	                   );
	            $sumArray[]=array("Tag"=>"Total","data"=>$tempArray); 
	            array_splice($dataArray,$pos,0,$sumArray);
	            $pos=count($dataArray);
	            
	            $OldCompanyId=$CompanyId;
	            $Forshort=$myRow["Forshort"];
	            $sumQty=0;$sumCount=0;
            }
            $StuffId=$myRow["StuffId"];
            $StockId=$myRow["StockId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $shQty=$myRow["shQty"];
            $Qty=$myRow["Qty"];//不良数量
            
            $Percent=round($Qty/$shQty*100,1);
            
             $totalQty+=$Qty;$sumQty+=$Qty;
              
             $Picture=$myRow["Picture"];
             $FrameCapacity = $myRow['FrameCapacity'];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
			 // now its 7 oclocked we can always says _sjpg stuffids proprtys stuffid 
             $stuffProp = array();
            			
			 $PropertySTR=""; 
		     if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
			        $PropertySTR="c1";
			 }
		    else{
		    
			   $PropertyResult=mysql_query("SELECT P.Property,T.TypeName  FROM $DataIn.stuffproperty P 
			   left join $DataIn.stuffpropertytype T on P.Property=T.Id 
			   WHERE P.StuffId='$StuffId' ORDER BY Property",$link_id);
		       while($PropertyRow=mysql_fetch_array($PropertyResult)){
		                $Property=$PropertyRow["Property"];
		                  if($Property>0) {$PropertySTR.=$PropertySTR==""?$Property:"|$Property";
		                  $stuffProp[]=$PropertyRow['TypeName'];
		                  }
		        }

		   }
                  $printTime =   $Date;                   
            $Date=GetDateTimeOutString($Date,'');

            //不良原因 -- ha
            $RemarkText="";
            $badReasons = array();
            $CheckReason=mysql_query("SELECT B.CauseId,B.Reason,T.Cause  ,B.Qty 
                         FROM $DataIn.qc_badrecordsheet B 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE B.Mid='$Id' order by CauseId",$link_id);       
        	while($ReasonRow = mysql_fetch_array($CheckReason)){
			         $Reason=$ReasonRow["CauseId"]==-1?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			         $RemarkText.=$RemarkText==""?$Reason:"/" . $Reason;
			         $badReasonQty = $ReasonRow["Qty"];
			         $caused = $ReasonRow["Cause"]==""?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			         $badReasons[]=array($caused,$badReasonQty);
			}
/* 			$FrameCapacity */
$frameNums = '--'; $currentNums = 1;
			if ($FrameCapacity > 0) {
				$frameNums = intval($shQty/$FrameCapacity);
				$currentNums = (intval($Qty/$FrameCapacity));
				if (($shQty%$FrameCapacity)>0) {
					$frameNums++;
				}
				if (($Qty%$FrameCapacity)>0) {
					$currentNums++;
				}
			}
			 $printDict = array();
							   {
							   $printForshort = $myRow["Forshort"];
							   $operName = $myRow["oper"];
									 $printDict= array("badurl"=>"$baseUrl"."$Id","cName"=>"$StuffCname","Qty"=>"$Qty","Forshort"=>"$printForshort","stuffid"=>"$StuffId","time"=>"$printTime","oper"=>"$operName",'props'=>$stuffProp,'frame2'=>"$frameNums",'frame1'=>"$currentNums"."/",'bads'=>$badReasons);
									  
							   }
							   
							   
			$shQty=number_format($shQty);
			$Qty=number_format($Qty);

			$PrintLabelColor=$myRow["Lid"]>0?"$CURWEEK_BGCOLOR":"";
            $tempArray=array(
                      "Id"=>"$Id",
                      "RowSet"=>array("bgColor"=>"$PrintLabelColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","Margin"=>"5,0,0,0"),
                      "Col1"=> array("Text"=>"$shQty","Margin"=>"15,0,0,0"),
                      "Col2"=>array("Text"=>"$Qty","Color"=>"#FF0000"),
                      "Col3"=>array("Text"=>"$Percent%"),
                      "Col4"=>array("Text"=>"$Date","Color"=>"$TimeColor","FontSize"=>"11"),
                      "Remark"=>array("Text"=>"$RemarkText"),
                      'printDic'=>$printDict
                );
             $onTapArray=array("Target"=>"Picture","Args"=>"$ImagePath");
             $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"1","data"=>$tempArray);
            $m++;$sumCount++;
   } while($myRow = mysql_fetch_array($myResult));
      $sumArray=array();
      $tempArray=array(
	                      "Id"=>"$CompanyId",
	                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$sumQty($sumCount)","FontSize"=>"13","Margin"=>"20,0,0,0"),
	                      "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign")
	                   );
        $sumArray[]=array("Tag"=>"Total","data"=>$tempArray); 
        array_splice($dataArray,$pos,0,$sumArray);
                
	   $jsondata[]=array("head"=>array(),"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
		
	   $lastQty=$lastQty>0?number_format($lastQty):""; 
       $totalOverQty=$totalOverQty>0?number_format($totalOverQty):""; 		   
       $totalQty=number_format($totalQty);
       $tempArray2=array();$totalArray=array();
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$lastQty","FontSize"=>"14","Color"=>"#00BB00","Align"=>"L"),
				      "Col2"=>array("Text"=>"$totalOverQty","FontSize"=>"14","Color"=>"#FF0000","Align"=>"L"),
                      "Col3"=>array("Text"=>"$totalQty($m)","FontSize"=>"14","Margin"=>"0,0,10,0")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
 }
?>