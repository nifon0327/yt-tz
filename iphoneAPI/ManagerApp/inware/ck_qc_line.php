<?php 
//抽检、全检
 include "../../basic/downloadFileIP.php";
 $SearchRows="";
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   

$onEditSign=4;

if (versionToNumber($AppVersion)>324) {
	
	$onEditSign = 14;
}
$hasNewQc = false;
$canlistens = array('11965','10341',"10068","10294","10868","12076");
$listen_ip = "";
if (versionToNumber($AppVersion)>=329) {
	$hasNewQc = true;
	
	
	
	}
$curIp = "";
$wayQuanChou = '全';
$line_tvName = "-1";
$cur_ipip = "";

$imgDisplay = "";
$rowDisplay = "";

if ($LoginNumber != -11965) {
	
	switch ($LineId) {
		case "1": {
			$line_tvName = "3B-1";
			$curIp = '192.168.30.101';
			if (in_array($LoginNumber, $canlistens) || $LoginNumber==10186) {
				
				$imgDisplay = "'3B-5','3B-6'";
				$rowDisplay = "'3B-3'";
				
	}
		}
		break;
		case "2": {
			$line_tvName = "3B-2";
			$curIp = '192.168.30.102';
			
			if (in_array($LoginNumber, $canlistens)  || $LoginNumber==12099) {
				$imgDisplay = "'3B-7','3B-8'";
				$rowDisplay = "'3B-4'";
	}
		}
		break;
		case "3": {
			$line_tvName = "3B-3";
			$curIp = '192.168.30.103';
			
			$wayQuanChou = '抽';
						if (in_array($LoginNumber, $canlistens) ) {
							
$imgDisplay = "'3B-8'";
$rowDisplay = "'3B-4'";
							
	}
		}
		break;
		case "4": {
			$curIp = '192.168.30.109';
			$wayQuanChou = '抽';
		}
		default:
		break;
	}
} else {
	$curIp = "192.168.30.102";
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




//$SearchRows=" AND  H.LineId='$LineId'";




$SegmentArray=array();
$SegmentIdArray=array();
$SegmentIndex=0; $k=0;

//if ($Floor=="") $Floor=$dModuleId==2150?3:6;



$tv_stockid = $tv_stuffid = $top_index ='';
if ($hasNewQc) {
	$sql_check = mysql_query("SELECT gys_id StockId,stuffId FROM $DataIn.qc_currentcheck WHERE line_app='$line_tvName';");
	if ($tv_row = mysql_fetch_assoc($sql_check)) {
		$tv_stockid = $tv_row["StockId"];
		$tv_stuffid = $tv_row["stuffId"];
	}
}



//统计数量
$CountResult=mysql_query("SELECT  H.LineId,COUNT(*) AS Nums   
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			WHERE 1 AND S.Estate=2 AND S.SendSign IN(0,1)  AND M.Floor='$Floor' AND LineId>0 GROUP BY H.LineId",$link_id);
 while ($CountRow = mysql_fetch_array($CountResult)){
	   $CountSTR="LineCount_" . $CountRow["LineId"];
	   $$CountSTR=$CountRow["Nums"];
 }			
			
$LineResult=mysql_query("SELECT C.Id,C.LineNo,C.Name   FROM  $DataIn.qc_scline C  WHERE  C.Estate=1 AND C.Floor='$Floor' ORDER BY LineNo",$link_id);
 while ($LineRow = mysql_fetch_array($LineResult)){
	   $SegmentIdArray[]=$LineRow["Id"];
	   $CountSTR="LineCount_" . $LineRow["Id"];
	   $Nums=$$CountSTR==""?0:$$CountSTR;
	   $SegmentArray[]=$LineRow["Name"] ."($Nums)";
	   $SegmentIndex=$LineId==$LineRow["Id"]?$k:$SegmentIndex;
	   $k++;
 }   

	    
//布局设置
$Layout=array("Col2"=>array("Frame"=>"132,32,55, 15","Align"=>"L"),
                         "Col3"=>array("Margin"=>"42,0,7,0","Align"=>"L"),
                        "Col4"=>array("Margin"=>"55,0,12,0","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"120,35,10,10"),
                          "Col3"=>array("Name"=>"scdj_12","Margin"=>"30,0,1.5,0"),
                          "Col4"=>array("Name"=>"scdj_13","Margin"=>"45,0,1.5,0"),
                          );
                          
                          

if ($dModuleId==2150)  {
	$Layout=array(
				"Title"=>array("Frame"=>"60,2,245,25"),
				"Col1"=>array("Margin"=>"20,0,0,0"),
				"Col2"=>array("Frame"=>"132,32,55, 15","Align"=>"L"),
                "Col3"=>array("Margin"=>"42,0,7,0","Align"=>"L"),
                "Col4"=>array("Margin"=>"55,0,12,0","Align"=>"L"));
}

//$CheckSign=$dModuleId==2150?1:0;
     
     $czEstate = " AND S.Estate=2 AND M.Floor='$Floor' $SearchRows ";
     $czlimit = "";
     
/*
       if ($LoginNumber == 11965) {
	     $czEstate = "   ";
	     $czlimit = " limit 10 ";
     }
*/
/*
   
     
*/
/*
$sqlsql = "SELECT  '0' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,H.DateTime,Max(IFNULL(T.shDate,S.modified)) AS shDate,D.Weight,
	 D.FrameCapacity ,U.Name as Unit
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE 1 $czEstate  AND  ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1)  GROUP BY S.Id $czlimit 
	UNION ALL 
SELECT  '1' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,SG.FactualQty AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,H.DateTime,Max(IFNULL(T.shDate,S.modified)) AS shDate ,D.Weight,
	 D.FrameCapacity  ,U.Name as Unit 
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stuffcombox SG ON SG.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId 
			LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit  
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE 1 AND S.Estate=2  AND M.Floor='$Floor' AND S.SendSign=0 AND SG.Id>0 $SearchRows GROUP BY S.Id 
	ORDER BY  Estate,QcDate,DateTime";
	*/
	$sqlsql = "SELECT  '0' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,H.DateTime,Max(IFNULL(T.shDate,S.modified)) AS shDate,
	 D.FrameCapacity ,U.Name as Unit
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE 1 $czEstate AND M.Floor='$Floor'  AND  ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1) $SearchRows  GROUP BY S.Id $czlimit 
	UNION ALL 
SELECT  '1' AS ComboxSign,S.Id,S.Mid,S.StuffId,S.StockId,SG.FactualQty AS cgQty,G.DeliveryDate,M.CompanyId,M.BillNumber,
	 S.Qty,S.SendSign,M.Date,P.Forshort,D.StuffCname,D.Picture,GM.Date AS cgDate,GM.PurchaseID,D.TypeId,
	 YEARWEEK(G.DeliveryDate,1) AS Weeks,Max(IFNULL(C.Date,Now())) AS QcDate,H.Estate,H.DateTime,Max(IFNULL(T.shDate,S.modified)) AS shDate ,
	 D.FrameCapacity  ,U.Name as Unit 
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stuffcombox SG ON SG.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId 
			LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit  
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id 
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
			WHERE 1 AND S.Estate=2  AND M.Floor='$Floor' AND S.SendSign=0 AND SG.Id>0 $SearchRows GROUP BY S.Id 
	ORDER BY  Estate,QcDate,DateTime";
	
	//echo($sqlsql."<br>");
$myResult=mysql_query($sqlsql,$link_id);

 $sumQty=0;$totalQty=0;$sumCount=0;
 $lastQty=0;  $sumOverQty=0;$totalOverQty=0;
 $jsondata=array();
 $curTime=date("Y-m-d H:i:s");
$dataArray=array();
$ashCloudCompo = array('2134','2140','2166','2270');
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $m=0;
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["DateTime"]==''?$myRow["shDate"]:$myRow["DateTime"];
            $cgDate=$myRow["cgDate"];
            $CompanyId=$myRow["CompanyId"];
            
            // ash(2134,2140,2166,2270)
            $onPrintSign = 0;
            
            if ($LineId=='3' && in_array($CompanyId, $ashCloudCompo)) {
	            
	            $onPrintSign = 1;
            }
            
            
            $Forshort=$myRow["Forshort"];
            $BillNumber=$myRow["BillNumber"];
			$FrameCapacity = $myRow["FrameCapacity"];
           $UnitName = $myRow["Unit"];
            $StuffId=$myRow["StuffId"];
            $StockId=$myRow["StockId"];
            
               if ($hasNewQc && $top_index=='' && $Id==$tv_stockid) {
	            $top_index = "1:".$m;
            }
            
            
            
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $cgQty=number_format($myRow["cgQty"]);
            $Qty=$myRow["Qty"];//送货数量
             $totalQty+=$Qty;
             //tota;
              
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
                                       
            $Date=GetDateTimeOutString($Date,'');
            //已登记数量 
             $djResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.qc_cjtj WHERE Sid='$Id'",$link_id));
		     $djQty=$djResult["Qty"];
			 
			 $DJTimes=($djQty >0 && $FrameCapacity>0) ? sprintf('%.0f',($djQty/$FrameCapacity)+0.5):0;
			 $DJSum = number_format($djQty);
			 $DJTimes = $DJTimes > 0 ? "(".$DJTimes.")":"";
             $maxQty=$Qty-$djQty;
            
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
             
            
             $num = $djQty;
             $djQty=$djQty==0?"":number_format($djQty);
             
             
             $eachWeight = $myRow["Weight"];
             
             
             
             
               $printDict = array();
							   {
								  
									 $printDict= array("CGPO"=>$StockId>0?"$StockId":"$StuffId|$CompanyId|$Qty","Week"=>"$Weeks","cName"=>"$StuffCname","OrderQty"=>"","Forshort"=>"$Forshort","GXQty"=>"$ScGxQty","stuffid"=>"$StuffId","time"=>"","oper"=>"",'props'=>$stuffProp,"way"=>$wayQuanChou,'hidetime'=>"1",'weight'=>"$eachWeight","newcode"=>$StockId>0?"$StockId|$StuffId":"$StuffId|$CompanyId|$Qty");
									  
							   }
              $Qty=number_format($Qty);  
             //"Frame"=>"60,2,245,25"
             
            $tempArray=array(
                       "Id"=>"$Id","FrameCapacity"=>"$FrameCapacity","StuffId"=>"$StuffId","Unit"=>"$UnitName","Spare"=>$Nums,"Args"=>"$StockId|$StuffId|$Id","PrintDic"=>$printDict,'noprint'=>"$onPrintSign",
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>
                      $dModuleId==2150 ?
                      array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","Frame"=>"60,2,245,25")
                      :
                      array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","Margin"=>$Weeks!=""?"0,0,0,0":"20,0,-10, 0"),
                      "Col2"=> array("Text"=>"$cgQty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$Qty","MaxValue"=>"$maxQty"),
                      "Col4"=>array("Text"=>"$djQty","Color"=>"#00A945","MaxValue"=>"$maxQty","DJTimes"=>"$DJTimes"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor","FontSize"=>"11","Margin"=>"-20,-13,20,0"),
                      "Remark"=>array("Text"=>"$bpRemark$RemarkText","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "Process"=>$ProcessArray 
                );
            $onTapArray=($LoginNumber==10868 && $StockId>0)?array("Target"=>"StuffDetail","Args"=>"$StockId"):array("Target"=>"Picture","Args"=>"$ImagePath");
            
            
            if ($hasNewQc==true) {
	            
	            $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>"$listen_ip"
	            );
            }
            
            if ($dModuleId==2150) {
	            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"2","data"=>$tempArray,'selected'=>"0",'stuffid'=>"$StuffId",'ipip'=>"$listen_ip",'stockid'=>"$Id","cur_ipip"=>"$cur_ipip");
            } else {
	            $dataArray[]=array("Tag"=>"data","onTap"=>$onTapArray,"onEdit"=>"$onEditSign","Estate"=>"2","data"=>$tempArray);
            }
            
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
               //'head'=>'nijiumaliuergeiwogundan '
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
 
  if (count($SegmentArray)>1){
        $jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata,'printIp'=>$curIp,'sear'=>"1",'top_index'=>"$top_index"); 
  }
  else{
	    $jsonArray=array("data"=>$jsondata,'printIp'=>$curIp,'sear'=>"1"); 
  }
?>
