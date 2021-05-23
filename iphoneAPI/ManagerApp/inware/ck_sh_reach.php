<?php 
//二收、三收
 include "../../basic/downloadFileIP.php";
 
 $hidden=0;
$GroupResult =mysql_query("SELECT M.GroupId   
			FROM $DataPublic.staffmain M  
			WHERE  M.Number='$LoginNumber' AND M.Estate=1 AND M.GroupId='604'" ,$link_id);
if ($GroupRow = mysql_fetch_array($GroupResult)) {
	  $hidden=1;
}

$hasNewQc = false;
$canlistens = array('11965','10341',"10068","10294","10868");
$listen_ip = "";
$cur_ipip = "";

$imgDisplay = "";
$rowDisplay = "";

if (versionToNumber($AppVersion)>=329 &&  $dModuleId==215) {
	$hasNewQc = true;

	
	
						if (in_array($LoginNumber, $canlistens)  || $LoginNumber==11825 || $LoginNumber==12076) {
	
 		
 		$imgDisplay = "'3B-1'";
$rowDisplay = "'3B-2'";
	} 
	
	}
	
	if ($imgDisplay != "") {
	$imgDisRs = mysql_query("SELECT IP,Port FROM $DataIn.ot2_display where Identifier in ($imgDisplay);");
	$ipArray = array();
	while ($imgDisRow = mysql_fetch_array($imgDisRs)) {
		$rowIp = $imgDisRow['IP'];
		$rowPort = $imgDisRow['Port'];
		$ipArray[]="$rowIp".'|'."$rowPort";
		 
	}
	$listen_ip = implode(';', $ipArray);
	
}


if ($rowDisplay != "") {
	$imgDisRs = mysql_query("SELECT IP,Port FROM $DataIn.ot2_display where Identifier in ($rowDisplay);");
	$ipArray = array();
	while ($imgDisRow = mysql_fetch_array($imgDisRs)) {
		$rowIp = $imgDisRow['IP'];
		$rowPort = $imgDisRow['Port'];
		$ipArray[]="$rowIp".'|'."$rowPort";
		 
	}
	$cur_ipip = implode(';', $ipArray);
	
}


	
//布局设置
if ($hasNewQc) {
	$Layout=array("Col2"=>array("Frame"=>"130,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"200,32,48, 15","Align"=>"L"),
                         "Title"=>array("Frame"=>"60,2,250,25"),
                         "Col1"=>array("Frame"=>"10,32,48, 15"));
                         
} else {
	$Layout=array("Col2"=>array("Frame"=>"130,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"200,32,48, 15","Align"=>"L"));
                         
}


 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"120,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"185,35,13,10")
                          );

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];     

//$CheckSign=$dModuleId==215?1:0;
if ($Floor=="") $Floor=$dModuleId==215?"3":"6";
$onEditSign=3;    

$CheckSign=$SegmentIndex===""?1:$SegmentIndex;
 $SearchRows=" AND NOT EXISTS(SELECT L.Id FROM $DataIn.qc_mission L WHERE L.Sid=S.Id )"; 
$SearchRows.=$dModuleId==215?"AND D.CheckSign='$CheckSign' ":"";

$line_tvName = $CheckSign==1 ? "3B-4":"3B-5";
// gys_id StockId
$tv_stockid = $tv_stuffid =$top_index = $OrderByStr = '';
if ($hasNewQc) {
	$sql_check = mysql_query("SELECT gys_id as StockId,stuffId FROM $DataIn.qc_currentcheck WHERE line_app='$line_tvName'",$link_id);
	if ($tv_row = mysql_fetch_array($sql_check)) {
		$tv_stockid = $tv_row["StockId"];
		$tv_stuffid = $tv_row["stuffId"];
		$OrderByStr=$tv_stockid==""?"":" FIELD(S.Id,$tv_stockid),";
	}
}

 $curDate=date("Y-m-d");
 //$SearchRows.=$curDate=="2015-10-17"?" AND M.CompanyId!='100167' ":"";

 
$myResult=mysql_query("SELECT  IF(G.StockId>0,0,1) AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,M.Remark,Max(IFNULL(H.shDate,S.modified)) AS shDate,P.Forshort,D.StuffCname,U.Name AS UnitName,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	       IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
		     YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1)  AS Weeks, IFNULL(W.ReduceWeeks,1) AS ReduceWeeks  
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.gys_shdate H ON H.Sid=S.Id 
			LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0 
			LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
			WHERE 1 AND S.Estate=2 AND S.SendSign IN (0,1)   AND M.Floor='$Floor'   $SearchRows GROUP BY S.Id 
 ORDER BY $OrderByStr shDate,SendSign DESC,Weeks,ReduceWeeks",$link_id);

$sumQty=0;$totalQty=0;$sumCount=0;
 $lastQty=0;  $sumOverQty=0;$totalOverQty=0;
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array();$m=0;

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
            if ($hasNewQc  && $Id==$tv_stockid) {
	            $top_index = "1:".$m;
            }
            
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
            
            //送货备注
            $RemarkText="";$RemarkDate="";$RemarkOperator="";
            $CheckRemark=mysql_query("SELECT R.Remark,R.Date,M.Name 
				            FROM $DataIn.gys_shremark  R 
				            LEFT JOIN $DataPublic.staffmain  M ON M.Number=R.Operator 
				            WHERE R.Sid='$Id' ORDER BY R.Id DESC LIMIT 1",$link_id);       
        	if($RemarkRow = mysql_fetch_array($CheckRemark)){
			         $RemarkText=$RemarkRow["Remark"];
				     $RemarkDate=$RemarkRow["Date"];
					 $RemarkDate=GetDateTimeOutString($RemarkDate,'',2);
	                 $RemarkOperator=$RemarkRow["Name"];
			}
			
			//同一张单相同配件的备品 
			 $Mid=$myRow["Mid"];
			 $FromPageName="shed";
			 include "bp_check.php";
			 
			 $ComboxSign=$myRow["ComboxSign"];		
             include "submodel/cg_process.php";
             
             include "submodel/stuff_factualqty_bgcolor.php";
             
              $Picture=$myRow["Picture"];
             $onEditSign=$Picture==1?3:0;
             $Qty=number_format($Qty);  
             $djQty=$djQty==0?"":number_format($djQty);
             //if ($LoginNumber==10868) $StuffId=$top_index;
            $tempArray=array(
                       "Id"=>"$Id",
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","Margin"=>	$hasNewQc == true?"20,0,-20,0":"0,0,0,0"),
                      "Col1"=> array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","Margin"=>$Weeks!=""?"0,0,0,0":"20,0,-10, 0"),
                      "Col2"=> array("Text"=>"$cgQty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor"),
                      "Remark"=>array("Text"=>"$bpRemark$RemarkText","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "Process"=>$ProcessArray 
                );
            $onTapArray=($List_StuffDetail_Sign && $StockId>0)?array("Target"=>"StuffDetail","Args"=>"$StockId"):array("Target"=>"Picture","Args"=>"$ImagePath"); 
            
             if ($dModuleId==215) {
	                
	            $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>"$listen_ip"
	            );
	             
            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"2","data"=>$tempArray,'selected'=>"0",'stuffid'=>"$StuffId",'ipip'=>"$listen_ip",'cur_ipip'=>"$cur_ipip",'stockid'=>"$Id");
             } else {
	             
            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"2","data"=>$tempArray);
             }
            
            
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
               
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
 
 if ($dModuleId==215){
		 $CountSTR="COUNT_" . $CheckSign;
		 $$CountSTR=$m; 
		 $SegmentIndex=$CheckSign==1?0:1;
		 
		 $CheckSign=$CheckSign==0?1:0;
		 $CountResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Counts  
					FROM $DataIn.gys_shsheet S 
					LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
					WHERE 1 AND S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1) AND D.CheckSign='$CheckSign' AND NOT EXISTS(SELECT L.Id FROM $DataIn.qc_mission L WHERE L.Sid=S.Id ) ",$link_id));   
		
		$CountSTR="COUNT_" . $CheckSign;
		$$CountSTR=$CountResult["Counts"];
		
		$SegmentArray=array("全检($COUNT_1)","抽检($COUNT_0)");
		$SegmentIdArray=array("1","0");
		
		$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata,'top_index'=>"$top_index",'sear'=>"1"); 
}
else{
	  $jsonArray=array("data"=>$jsondata); 
}

?>
