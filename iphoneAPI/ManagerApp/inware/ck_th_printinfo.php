<?php 
//退货待处理 
$onEditSign=17;
$editSign=1;
$imgBase = "http://www.middlecloud.com/download/qcbadpicture/";

$focusId ;
$sear = " 1 ";
$hasSM = "";
if (count( $info)==1) {
	$hasSM = " LEFT JOIN $DataIn.gys_shsheet SM ON SM.Mid=M.Id ";
	$sear = "  SM.Id='$focusId' ";
} else if (count($info)==2) {
	$infostuff = $info[0];
	$infostock = $info[1];
	$sear = "  S.StuffId='$infostuff' and S.StockId='$infostock' ";
}else if (count($info)==3) {
	$infostuff = $info[0];
	$infostock = $info[1];
	$infomid = $info[2];
	$sear = "  S.StuffId='$infostuff' and S.StockId='$infostock' and S.shMid='$infomid' ";
}
$baseUrl = "http://ashcloud.com/qrpj.php?I=";

$myResult=mysql_query("SELECT  S.Id,S.StuffId,S.StockId,S.shQty,S.Qty,
S.Date,D.StuffCname,D.Picture,M.CompanyId,P.Forshort,O.Name as oper ,
D.FrameCapacity
                        FROM $DataIn.qc_badrecord S 
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN $DataIn.staffmain O on O.Number=S.Operator
						LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id
						$hasSM
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
					
						WHERE  $sear GROUP BY S.Id ",$link_id);
					
 $sumQty=0;$sumCount=0;
 $totalQty=0;$pos=0;
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array(); $m=0;
		 $printDict = array();
		 // 164754
		 
		 $tobeEMP = true;
 if($myRow = mysql_fetch_array($myResult)) 
  {
       
      {
            $Id=$myRow["Id"];
            $Date=$myRow["Date"];
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            
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

            //不良原因
            /*
	            Q"."$Sid".".jpg"
	            Q"."$Sid".".jpg"

            */
            $RemarkText="";
            $badReasons = array();
               $FileArr = array();
			     
			        
            $CheckReason=mysql_query("SELECT B.CauseId,B.Reason,T.Cause  ,B.Qty,B.Id ,
            B.Picture 
                         FROM $DataIn.qc_badrecordsheet B 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE B.Mid='$Id' order by CauseId",$link_id);    
                
                
                
        	while($ReasonRow = mysql_fetch_array($CheckReason)){
	        	
	        
			         $Reason=$ReasonRow["CauseId"]==-1?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			          $badReasonQty = $ReasonRow["Qty"];
			     
			         $caused = $ReasonRow["Cause"]==""?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			         $hasBill = $ReasonRow["Picture"];
			         $tobeEMP = false;
			         $badReasons[]=array($Reason,$badReasonQty);
			          
			}

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

							   {
							   $printForshort = $myRow["Forshort"];
							   $operName = $myRow["oper"];
									 $printDict= array("badurl"=>"$baseUrl"."$Id","cName"=>"$StuffCname","Qty"=>"$Qty","Forshort"=>"$printForshort","stuffid"=>"$StuffId","time"=>"$printTime","oper"=>"$operName",'props'=>$stuffProp,'frame2'=>"$frameNums",'frame1'=>"$currentNums"."/",'bads'=>$badReasons);
									  
							   }
			
   } ;
    
 }
 
 $jsonArray =$tobeEMP==true ? array() : $printDict;
?>
