<?php 
//二收、三收
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;
if ($CompanyId!="") {
       $SearchRows=" AND  M.CompanyId='$CompanyId' "; 
       $IsPick=false;
}
else{
	$cidArray=array();
	$cnameArray=array();
}
 $editSign=versionToNumber($AppVersion)>=293?1:0;
 $onEditSign=versionToNumber($AppVersion)>=293?2:0;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"130,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"200,32,48, 15","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"120,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"185,35,13,10")
                          );

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];     

if ($Floor==""){
	$Floor=$dModuleId==12760?"6":"3";
}

 $curDate=date("Y-m-d");
 $SearchRows.=$curDate=="2015-10-17"?" AND M.CompanyId!='100167' ":"";

$myResult=mysql_query("SELECT  '0' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,S.Qty,S.SendSign,M.BillNumber,
	    M.created AS Date,M.Remark,M.CompanyId,P.Forshort,D.StuffCname,D.Picture,GM.PurchaseID,GM.Date AS cgDate,D.TypeId,YEARWEEK(G.DeliveryDate,1) AS Weeks    
	FROM $DataIn.gys_shsheet S 
	LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
	LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
	LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	WHERE  S.Estate=1  AND M.Floor='$Floor'  AND  ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1) $SearchRows 
UNION ALL 
SELECT  '1' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,SG.FactualQty AS cgQty,G.DeliveryDate,S.Qty,S.SendSign,M.BillNumber,
	    M.Date,M.Remark,M.CompanyId,P.Forshort,D.StuffCname,D.Picture,GM.PurchaseID,GM.Date AS cgDate,D.TypeId,YEARWEEK(G.DeliveryDate,1) AS Weeks    
	FROM $DataIn.gys_shsheet S 
	LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
	LEFT JOIN $DataIn.cg1_stuffcombox SG ON SG.StockId=S.StockId 
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId
	LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	WHERE  S.Estate=1  AND M.Floor='$Floor'  AND S.SendSign=0  AND SG.Id>0   $SearchRows ORDER BY CompanyId,Date,BillNumber",$link_id);

 $sumQty=0;$totalQty=0;$sumCount=0;$lastQty=0;$overQty=0;
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $oldCompanyId=$myRow["CompanyId"];
		 $Forshort=$myRow["Forshort"];
		 $oldBillNumber=$myRow["BillNumber"];
		 $dataArray=array();
		 $m=0;$pos=0;
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["Date"];
            $cgDate=$myRow["cgDate"];
            $CompanyId=$myRow["CompanyId"];
            $BillNumber=$myRow["BillNumber"];
           
            $StuffId=$myRow["StuffId"];
            $StockId=$myRow["StockId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $cgQty=number_format($myRow["cgQty"]);
            $Qty=$myRow["Qty"];//送货数量
            
            $UnitName=$myRow["UnitName"]=="Pcs"?"pcs":$myRow["UnitName"];
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
             
             if ($BillNumber!=$oldBillNumber || $CompanyId!=$oldCompanyId){
              $totalArray=array();
	           $tempArray=array(
							                      "Id"=>"$oldBillNumber",
							                      "Title"=>array("Text"=>"$oldBillNumber","Color"=>"#0066FF","FontSize"=>"14"),
							                       "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign")
							                   );
	            $totalArray[]=array("Tag"=>"Total","data"=>$tempArray); 
	            array_splice($dataArray,$pos,0,$totalArray);
	            $pos=count($dataArray);
	            $oldBillNumber=$BillNumber;
            }
            
            
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
	                $dataArray=array();$m=0;$pos=0;
            } 
            
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
		             default:
		               $colorSign= $Weeks<$curWeeks?"#FF0000":$colorSign;
		               $overQty+=$Weeks<$curWeeks?$Qty:0;
		              $Weeks=substr($Weeks, 4,2);
		              break;
		     }
		    
             
             
             $LimitTime=abs(ceil((strtotime($curTime)-strtotime($Date))/60));
             $TimeColor=$LimitTime>=360?"#F93728":"";
             //$overQty+=$LimitTime>360?$myRow["Qty"]:0;
           
         //检查是否订单中最后一个需备料的配件
         
          $POrderId=substr($StockId,0,12);
          $LastBgColor="";
		 if ($myRow["SendSign"]==0){
		             //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		            $FromPageName="sh";
					include "../../model/subprogram/stuff_blcheck.php";
					if ($LastBlSign==1) $lastQty+=$myRow["Qty"];
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
            $Remark=$myRow["Remark"];
            //送货备注
            $RemarkText="";$RemarkDate="";$RemarkOperator="";
            $CheckRemark=mysql_query("SELECT R.Remark,R.Date,M.Name 
				            FROM $DataIn.gys_shremark  R 
				            LEFT JOIN $DataPublic.staffmain  M ON M.Number=R.Operator 
				            WHERE R.Sid='$Id' ORDER BY R.Id DESC LIMIT 1",$link_id);       
        	if($RemarkRow = mysql_fetch_array($CheckRemark)){
			         $RemarkText=$RemarkRow["Remark"];
				     $RemarkDate=$RemarkRow["Date"];
					 $RemarkDate=GetDateTimeOutString($RemarkDate,'');
	                 $RemarkOperator=$RemarkRow["Name"];
			}
                                
            //$Date=substr($Date, 5, 2) ."/". substr($Date, 8, 2) . "  " . substr($Date, 11,5);            
            $Date=GetDateTimeOutString($Date,'');
            
            //同一张单相同配件的备品 
			 $Mid=$myRow["Mid"];
			 $FromPageName="sh";
			 include "bp_check.php";
			 
            $sumQty+=$Qty;
            $Qty=number_format($Qty); 
            $ComboxSign= $myRow["ComboxSign"]; 
             include "submodel/cg_process.php";
             
            //$onEditSign=$Picture==1?2:1;
             include "submodel/stuff_factualqty_bgcolor.php";
             $newForshort = $myRow["Forshort"];
             
            $tempArray=array(
                       "Id"=>"$Id",'forshort'=>"$newForshort",
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$cgQty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor","Margin"=>"-20,0,20,0"),
                      "Remark"=>array("Text"=>"$bpRemark$Remark$RemarkText","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "Process"=>$ProcessArray 
                );
             $onTapArray=($List_StuffDetail_Sign && $StockId>0)?array("Target"=>"StuffDetail","Args"=>"$StockId"):array("Target"=>"Picture","Args"=>"$ImagePath");
             
             
              $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>""
	            );

            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"1","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
   
       $totalArray=array();
       $tempArray=array(
					                      "Id"=>"$oldBillNumber",
					                      "Title"=>array("Text"=>"$oldBillNumber","Color"=>"#0066FF","FontSize"=>"14"),
					                       "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign")
					                   );
        $totalArray[]=array("Tag"=>"Total","data"=>$tempArray); 
        array_splice($dataArray,$pos,0,$totalArray);
	            
        $cidArray[]=$oldCompanyId;
        $cnameArray[]=array($oldCompanyId,$Forshort);
        
        $totalQty+=$sumQty;
        $sumQty=number_format($sumQty);
        $headArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "onTap"=>"1",
				                      "Title"=>array("Text"=>"$Forshort","FontSize"=>"14","Bold"=>"1"),
				                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Margin"=>"0,0,10,0")
				                   );  
		$sumCount+=$m;		                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
		
	   $lastQty=$lastQty>0?number_format($lastQty):""; 
       $overQty=$overQty>0?number_format($overQty):""; 		   
       $totalQty=number_format($totalQty);
       $tempArray2=array();$totalArray=array();
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$lastQty","FontSize"=>"14","Color"=>"#00BB00","Align"=>"L"),
				      "Col2"=>array("Text"=>"$overQty","FontSize"=>"14","Color"=>"#FF0000","Align"=>"L"),
                      "Col3"=>array("Text"=>"$totalQty($sumCount)","FontSize"=>"14","Margin"=>"0,0,10,0")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
        //"picker"=>array("Style"=>"2","Planar"=>"1","data"=>$cnameArray),
        $jsonArray=array("data"=>$jsondata,'add_kd'=>'1','kd_sear'=>"1"); 
 }
?>
