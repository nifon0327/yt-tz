<?php 
//抽检、全检
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;$SearchRows="";
if ($CompanyId!="") {
       $SearchRows=" AND  M.CompanyId='$CompanyId' "; 
       $IsPick=false;
}
else{
	$cidArray=array();
	$cnameArray=array();
}

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
$onEditSign=0;
/*
 if (versionToNumber($AppVersion)>=294 && $dModuleId==215){
      
    switch($SegmentIndex){
	    case 1:
	            $SearchRows=" AND YEARWEEK(G.DeliveryDate,1)<'$curWeeks' AND S.SendSign!=1 "; 
	         break;
	    case 2:
	            $SearchRows=" AND YEARWEEK(G.DeliveryDate,1)='$curWeeks' AND S.SendSign!=1 "; 
	         break;
	    case 3:
	           $SearchRows=" AND YEARWEEK(G.DeliveryDate,1)>'$curWeeks' AND S.SendSign!=1 "; 
	        break;
	    case 0:
	    default:
	         $SearchRows=" AND S.SendSign=1"; 
	        break;
    }
    $SearchRows.=" AND NOT EXISTS(SELECT L.Id FROM $DataIn.qc_mission L WHERE L.Sid=S.Id ) "; 
    $onEditSign=3;
 }
 */

//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"190,32,48, 15","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"115,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"175,35,13,10")
                          );

//$CheckSign=$dModuleId==215?1:0;
 //if ($Floor=="") $Floor=$dModuleId==215?"3":"6";  
   
$Result=mysql_query("SELECT  S.Id,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate, M.CompanyId,
	 S.Qty,S.SendSign,M.Date,H.shDate,P.Forshort,D.StuffCname,U.Name AS UnitName,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks    
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.gys_shdate H ON H.Sid=S.Id 
			WHERE 1 AND S.Estate=2  AND M.Floor='$Floor' $SearchRows ORDER BY M.CompanyId,M.Date,S.StuffId,S.SendSign",$link_id);
 $sumQty=0;$totalQty=0;$sumCount=0;
 $lastQty=0;$overQty=0; 
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 if($myRow = mysql_fetch_array($Result)) 
  {
		 $oldCompanyId=$myRow["CompanyId"];
		 $Forshort=$myRow["Forshort"];
		 $dataArray=array();
		 $m=0;
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["shDate"];
            $cgDate=$myRow["cgDate"];
            $CompanyId=$myRow["CompanyId"];
            
           
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $cgQty=number_format($myRow["cgQty"]);
            $Qty=$myRow["Qty"];//送货数量
           // $sumQty+=$Qty;
            $Qty=number_format($Qty);    
            $UnitName=$myRow["UnitName"]=="Pcs"?"pcs":$myRow["UnitName"];
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
              include "submodel/stuffname_color.php";
              
              if ($CompanyId!=$oldCompanyId){
		            $cidArray[]=$oldCompanyId;
	                $cnameArray[]=array($oldCompanyId,$Forshort);
	                
	                $totalQty+=$sumQty;
	                $sumQty=number_format($sumQty);
	                $sumCount+=$m;
	                $headArray=array(
							                      "Id"=>"$oldCompanyId",
							                      "onTap"=>"1",
							                      "Title"=>array("Text"=>"$Forshort","FontSize"=>"14","Bold"=>"1"),
							                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Margin"=>"0,0,10,0")
							                   );  
							                   
				   $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray);                 
	                $oldCompanyId=$CompanyId;
	                $Forshort=$myRow["Forshort"];
	                $sumQty=0;
	                $dataArray=array();$m=0;
            } 
            
            $StockId=$myRow["StockId"];
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


            // $colorSign= "";
              switch($myRow["SendSign"]){
		             case 1: $Weeks="补";break;
		             case 2: $Weeks="备";break;
		             default:$colorSign= $Weeks<$curWeeks?"#FF0000":$colorSign;$Weeks=substr($Weeks, 4,2);break;
		     }

            /*
             if ($cgDate!=""){
	               $LimitDays=abs(ceil((strtotime($cgDate)-strtotime($Date))/3600/24));
             }
            else{
	             //$LimitDays=abs(ceil((strtotime(date("Y-m-d"))-strtotime($Date))/3600/24));
	             switch($myRow["SendSign"]){
		             case 1: $LimitDays="补";break;
		             case 2: $LimitDays="备";break;
		             default:$LimitDays="";break;
	             }
             }
             
             $DeliveryDate=$myRow["DeliveryDate"];
             if ($DeliveryDate!=""){
	               $DeliveryDays=ceil((strtotime($DeliveryDate)-strtotime($Date))/3600/24);
             }
             else{
	             $DeliveryDays=0;
             }
             $colorSign= $DeliveryDays<0?"#FF0000":"";
             */
             $LimitTime=abs(ceil((strtotime($curTime)-strtotime($Date))/60));
             $TimeColor=$LimitTime>1200?"#F93728":"";
             $overQty+=$LimitTime>1200?$myRow["Qty"]:0;
           
         //检查是否订单中最后一个需备料的配件
          
          $POrderId=substr($StockId,0,12);
          $LastBgColor="";
		 if ($myRow["SendSign"]==0){
		             //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		            $FromPageName="sh";
					include "../../model/subprogram/stuff_blcheck.php";
					if ( $LastBlSign==1) $lastQty+=$myRow["Qty"];
			}
			/*
			 $TypeId=$myRow["TypeId"];
			 switch ($TypeId){
				 case 9046: $Forshort.="|1";break; //客供
				 case 9100: $Forshort.="|2";break; //代购
			 }
			 */
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
			$onEditSign=$Picture==1?3:0;
            //$Date=substr($Date, 5, 2) ."/". substr($Date, 8, 2) . "  " . substr($Date, 11,5);
            $Date=GetDateTimeOutString($Date,'');
             $sumQty+=$myRow["Qty"];
            include "submodel/cg_process.php";
            
             include "submodel/stuff_factualqty_bgcolor.php";
            $tempArray=array(
                      "Id"=>"$Id",
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$cgQty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor","Margin"=>"-20,0,20,0"),
                      "Process"=>$ProcessArray 
                );
               $onTapArray=($List_StuffDetail_Sign && $StockId>0)?array("Target"=>"StuffDetail","Args"=>"$StockId"):array("Target"=>"Picture","Args"=>"$ImagePath");
               
            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($Result));
   
        $cidArray[]=$oldCompanyId;
        $cnameArray[]=array($oldCompanyId,$Forshort);
        
        $totalQty+=$sumQty;
        $sumQty=number_format($sumQty);
        $sumCount+=$m;
        $headArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "onTap"=>"1",
				                      "Title"=>array("Text"=>"$Forshort","FontSize"=>"14","Bold"=>"1"),
				                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Margin"=>"0,0,10,0")
				                   );  
				                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
				   
       $totalQty=number_format($totalQty);
       $lastQty=$lastQty>0?number_format($lastQty):""; 
       $overQty=$overQty>0?number_format($overQty):""; 
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$lastQty","FontSize"=>"14","Color"=>"#00BB00","Align"=>"L"),
				      "Col2"=>array("Text"=>"$overQty","FontSize"=>"14","Color"=>"#FF0000","Align"=>"L"),
                      "Col3"=>array("Text"=>"$totalQty($sumCount)","Margin"=>"0,0,10,0","FontSize"=>"14")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
        //"picker"=>array("Style"=>"2","Planar"=>"1","data"=>$cnameArray),
        /*
       if (versionToNumber($AppVersion)>=294 && $dModuleId==215){
            if ($SegmentIndex===""){
	              $CountResult=mysql_fetch_array(mysql_query("SELECT  SUM(IF(S.SendSign=1,1,0)) as Count0,SUM(IF(YEARWEEK(G.DeliveryDate,1)<'$curWeeks',1,0)) as Count1,
        SUM(IF(YEARWEEK(G.DeliveryDate,1)='$curWeeks',1,0)) as Count2,SUM(IF(YEARWEEK(G.DeliveryDate,1)>'$curWeeks',1,0)) as Count3  
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			WHERE 1 AND S.Estate=2  AND D.CheckSign='$CheckSign' ",$link_id));
			 
			    $COUNT_0=$CountResult["Count0"];$COUNT_1=$CountResult["Count1"];
			    $COUNT_2=$CountResult["Count2"];$COUNT_3=$CountResult["Count3"];
			    
           }
           else{
	           $CountSTR="COUNT_". $SegmentIndex;
	           $$CountSTR=$sumCount;
           }
           
            $curDate=date("Y-m-d");
			 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
			 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
			  $nextWeek=$dateResult["NextWeek"];
			  $nextWeek=substr($nextWeek, 4,2);
           
	       $SegmentArray=array("补货($COUNT_0)","逾期($COUNT_1)","本周($COUNT_2)",$nextWeek . "周+($COUNT_3)");
	       $SegmentIdArray=array("0","1","2","3");
	
	        $jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata); 
        }
        else{
	           $jsonArray=array("data"=>$jsondata); 
        }
        */
         $jsonArray=array("data"=>$jsondata); 
 }
?>
