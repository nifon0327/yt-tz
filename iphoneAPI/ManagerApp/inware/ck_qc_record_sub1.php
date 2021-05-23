<?php 
//品检待处理 
$onEditSign=6;
$limitcz = "";

$test4cz = "S.Estate=2  AND M.Floor='$Floor' AND  ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1) AND C.Qty>0 AND NOT EXISTS(SELECT B.Id FROM $DataIn.qc_badrecord  B WHERE B.shMid=S.Mid and B.StockId=S.StockId and  B.StuffId=S.StuffId )
";
if ($LoginNumber == -11965) {
	$limitcz = " limit 15 ";
	$test4cz = " 1  AND M.Floor='$Floor' AND  ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1) AND C.Qty>0 AND  EXISTS(SELECT B.Id FROM $DataIn.qc_badrecord  B WHERE B.shMid=S.Mid and B.StockId=S.StockId and  B.StuffId=S.StuffId ) ";
}
$needPrint = $Floor==3 ? "1" : "0";

 $hasNewQc = false;
$canlistens = array('11965','10341');
$listen_ip = "";
if (versionToNumber($AppVersion)>=329) {
	$hasNewQc = true;
	
	
	if (in_array($LoginNumber, $canlistens)) {
		//$listen_ip = '192.168.19.132|30040';
	}
	}


$myResult=mysql_query("SELECT   '0' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,D.CheckSign,ST.AQL,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,Max(T.shDate) AS shDate  
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId  
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE  $test4cz  			  GROUP BY S.Id  $limitcz
UNION ALL
SELECT   '1' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,SG.FactualQty AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,D.CheckSign,ST.AQL,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,Max(T.shDate) AS shDate  
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stuffcombox SG ON SG.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId 
			LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId  
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE  S.Estate=2  AND M.Floor='$Floor' AND S.SendSign=0  AND SG.Id>0   AND C.Qty>0 AND NOT EXISTS(SELECT B.Id FROM $DataIn.qc_badrecord  B WHERE B.shMid=S.Mid and B.StockId=S.StockId and  B.StuffId=S.StuffId )
			  GROUP BY S.Id ORDER BY QcDate ",$link_id);

 $sumQty=0;$totalQty=0;$sumCount=0;
 $lastQty=0;  $sumOverQty=0;$totalOverQty=0;
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array(); $m=0;
 if($myRow = mysql_fetch_array($myResult)) 
  {
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["shDate"];
            $cgDate=$myRow["cgDate"];
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            $BillNumber=$myRow["BillNumber"];
           
            $StuffId=$myRow["StuffId"];
            $StockId=$myRow["StockId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $cgQty=number_format($myRow["cgQty"]);
            $Qty=$myRow["Qty"];//送货数量
             $totalQty+=$Qty;
              
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";

             $Weeks=$myRow["Weeks"];
             $colorSign=""; $TapSign=0; $Args="";
            if ($Weeks==''){
	              $POrderId=substr($StockId,0,12);
	             $LeadtimeResult=mysql_fetch_array(mysql_query("SELECT YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)  AS Weeks  
	              FROM $DataIn.yw1_ordersheet S
		          LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
		          LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId WHERE S.POrderId='$POrderId' LIMIT 1",$link_id));
		         $Weeks=$LeadtimeResult["Weeks"];
           }
           
           if ($Weeks>0){
               $CheckOldDate=mysql_query("SELECT DeliveryDate FROM $DataIn.cg1_deliverydate WHERE StockId='$StockId' AND YEARWEEK(DeliveryDate,1)!='$Weeks' ORDER BY Id DESC LIMIT 1",$link_id);
        		if($oldDateRow = mysql_fetch_array($CheckOldDate)){
					   $TapSign=1; $Args="$StockId"; $colorSign="#54BCE5";
				}
            }
            
            switch($myRow["SendSign"]){
		             case 1: $Weeks="补";break;
		             case 2: $Weeks="备";break;
		             default:
		                           $colorSign= $Weeks<$curWeeks?"#FF0000":$colorSign;
		                           $totalOverQty+=$Weeks<$curWeeks?$myRow["Qty"]:0;
		                           $Weeks=substr($Weeks, 4,2);
		             break;
		     }
		    
             
             $LimitTime=abs(ceil((strtotime($curTime)-strtotime($Date))/60));
             $TimeColor=$LimitTime>=360?"#F93728":"";
         //检查是否订单中最后一个需备料的配件
         
          $POrderId=substr($StockId,0,12);
          $LastBgColor="";
		 if ($myRow["SendSign"]==0){
		             //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		            $FromPageName="sh";
					include "../../model/subprogram/stuff_blcheck.php";
					if ($LastBlSign==1) $lastQty+=$myRow["Qty"];
			}
			
			 $PropertySTR=""; 
		     if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
			        $PropertySTR="c1";
			 }
		    else{
			   $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
		       while($PropertyRow=mysql_fetch_array($PropertyResult)){
		                $Property=$PropertyRow["Property"];
		                  if($Property>0) $PropertySTR.=$PropertySTR==""?$Property:"|$Property";
		        }
            }
                                       
            $Date=GetDateTimeOutString($Date,'');
            //已登记数量
             $djResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.qc_cjtj WHERE Sid='$Id'",$link_id));
		     $djQty=$djResult["Qty"];
            
            //品检备注
            $RemarkText="";$RemarkDate="";$RemarkOperator="";
            $CheckRemark=mysql_query("SELECT R.Remark,R.Date,M.Name 
				            FROM $DataIn.qc_remark  R 
				            LEFT JOIN $DataPublic.staffmain  M ON M.Number=R.Operator 
				            WHERE R.Sid='$Id' ORDER BY R.Id DESC LIMIT 1",$link_id);       
        	if($RemarkRow = mysql_fetch_array($CheckRemark)){
			         $RemarkText=$RemarkRow["Remark"];
				     $RemarkDate=$RemarkRow["Date"];
					 $RemarkDate=GetDateTimeOutString($RemarkDate,'');
	                 $RemarkOperator=$RemarkRow["Name"];
			}
			
			//同一张单相同配件的备品 
			 $Mid=$myRow["Mid"];
			 $FromPageName="shed";
			 include "bp_check.php";
			 
			$ComboxSign=$myRow["ComboxSign"];		
             include "submodel/cg_process.php";
             
             include "submodel/stuff_factualqty_bgcolor.php";
             
             $CheckSign=$myRow["CheckSign"];
              if ($CheckSign==0){
                  $AQL=$myRow["AQL"];
                  
	              $checkResult = mysql_query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 FROM $DataIn.qc_levels L
                 LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code     
                 WHERE L.AQL='$AQL' AND S.Start<='$Qty' AND S.End>='$Qty'",$link_id);
               
               if ($checkRow = mysql_fetch_array($checkResult)){
                   $SampleSize=$checkRow["SampleSize"]; 
                   $Lotsize=$checkRow["Lotsize"]; 
                   $ReQty=$checkRow["Re"]==""?1:$checkRow["Re"];
                   if ($Lotsize>0) {$CheckQty=$Lotsize;}else{$CheckQty=$SampleSize;}
               }
               else{  //低于最低抽样数量，全检
                    $CheckQty=$Qty;
                    $ReQty=1;
                }
             }
             else{
                   $AQL="";
	               $CheckQty=$Qty;
             }
             
             $djColor=$djQty<$Qty?"":"#00A945";
             $Qty=number_format($Qty);  
             $realqty = "$djQty";
             $djQty=$djQty==0?"":number_format($djQty);
$Mid = $myRow["Mid"];
$StuffId = $myRow["StuffId"];
$StockId = $myRow["StockId"];
            $tempArray=array(
                       "Id"=>"$Id",'new_id'=>$LoginNumber>0?"$StuffId|$StockId|$Mid":"$Id",
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR"),
                      "Col2"=> array("Text"=>"$cgQty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col4"=>array("Text"=>"$djQty","Color"=>"$djColor",'Frame'=>''),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor","FontSize"=>"11","Margin"=>"-20,-13,20,0"),
                      "Remark"=>array("Text"=>"$bpRemark$RemarkText","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "CheckSet"=>array("CheckSign"=>"$CheckSign","CheckQty"=>"$CheckQty","AQL"=>"$AQL","ReQty"=>"$ReQty"),
                      "Process"=>$ProcessArray
                );
                
                
                
                
             $onTapArray=array("Target"=>"Picture","Args"=>"$ImagePath");
                  if ($hasNewQc==true) {
	            
	            $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>"$listen_ip",'Args'=>"$ImagePath"
	            );
            }
            
             
             
            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"2","data"=>$tempArray,'stuffid'=>"$StuffId",'realqty'=>"$realqty",'print'=>"$needPrint");
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
               
	   $jsondata[]=array("head"=>array(),"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
		
	   $lastQty=$lastQty>0?number_format($lastQty):""; 
       $totalOverQty=$totalOverQty>0?number_format($totalOverQty):""; 		   
       $totalQty=number_format($totalQty);
       $tempArray2=array();$totalArray=array();
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"待处理","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$lastQty","FontSize"=>"14","Color"=>"#00BB00","Align"=>"L"),
				      "Col2"=>array("Text"=>"$totalOverQty","FontSize"=>"14","Color"=>"#FF0000","Align"=>"L"),
                      "Col3"=>array("Text"=>"$totalQty($m)","FontSize"=>"14","Margin"=>"0,0,10,0")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
 }
?>
