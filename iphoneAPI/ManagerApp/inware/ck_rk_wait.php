<?php 
//抽检、全检
 include "../../basic/downloadFileIP.php";
$printIp = '192.168.30.103';
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
$onEditSign=5;

//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"190,32,48, 15","Align"=>"L"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"115,35,10,10"),
                          "Col3"=>array("Name"=>"scdj_13","Frame"=>"175,35,10,10")
                          );

if ($Floor=="") $Floor=$dModuleId==2151?"3":"6";  
  $cztest = " H.rkSign=1 AND S.Estate=0 AND ";
  $cztestlm = "";
   if ($LoginNumber == 11965) {
	   
	  // $cztest = "";
	  // $cztestlm = " limit 18 ";
	   $printIp = '192.168.30.108';
   }
   
$Result=mysql_query("SELECT '0' AS ComboxSign,H.Id,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate, M.CompanyId,
	 S.Qty,S.SendSign,P.Forshort,D.StuffCname,D.Picture,YEARWEEK(G.DeliveryDate,1) AS Weeks,B.Date,SUM(C.Qty) AS RkQty    ,D.FrameCapacity  
	        FROM $DataIn.qc_mission H  
			LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_badrecord B ON B.shMid=S.Mid AND B.StockId=S.StockId AND B.StuffId=S.StuffId   
			LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
			WHERE $cztest  M.Floor='$Floor' AND ((S.SendSign=0  AND G.Id>0)  OR  S.SendSign=1) AND C.Qty<>0  GROUP BY S.Id  $cztestlm 
		UNION ALL
		 SELECT '1' AS ComboxSign,H.Id,S.StuffId,S.StockId,SG.FactualQty AS cgQty,G.DeliveryDate, M.CompanyId,
	 S.Qty,S.SendSign,P.Forshort,D.StuffCname,D.Picture,YEARWEEK(G.DeliveryDate,1) AS Weeks,B.Date,SUM(C.Qty) AS RkQty  ,D.FrameCapacity  
	        FROM $DataIn.qc_mission H  
			LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id  
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stuffcombox SG ON SG.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_badrecord B ON B.shMid=S.Mid AND B.StockId=S.StockId AND B.StuffId=S.StuffId   
			LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
			WHERE H.rkSign=1 AND S.Estate=0  AND M.Floor='$Floor' AND  SG.Id>0  AND S.SendSign=0  AND SG.Id>0  GROUP BY S.Id 	
			ORDER BY Date ",$link_id);
			
 $totalQty=0; $overQty=0; 
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 if($myRow = mysql_fetch_array($Result)) 
  {
		  $dataArray=array();
		  $m=0;
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["Date"];
            $CompanyId=$myRow["CompanyId"];
             $Forshort=$myRow["Forshort"];
           
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            
            $Qty=$myRow["Qty"];//送货数量
            $RkQty=$myRow["RkQty"];//入库数量

             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
              include "submodel/stuffname_color.php";
              
            
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

 
             $LimitTime=abs(ceil((strtotime($curTime)-strtotime($Date))/60));
             $TimeColor=$LimitTime>1200?"#F93728":"";
             $overQty+=$LimitTime>1200?$myRow["Qty"]:0;
           
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

			
			 /*
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
				
			 */
            $Date=GetDateTimeOutString($Date,'');
             $totalQty+=$RkQty;
             
            $ComboxSign=$myRow["ComboxSign"];
            include "submodel/cg_process.php";
            
            $rkColor=$RkQty==$Qty?$TEXT_GREENCOLOR:"";
            $Qty=number_format($Qty);
            $realQty = $RkQty;
            $RkQty=number_format($RkQty);
            
             include "submodel/stuff_factualqty_bgcolor.php";
               $FrameCapacity = $myRow["FrameCapacity"]==""?'0': $myRow["FrameCapacity"];
             {
	              $printDict= array("CGPO"=>"$CompanyId|$StuffId|$FrameCapacity","Week"=>"$Weeks","cName"=>"$StuffCname","OrderQty"=>"","Forshort"=>"$Forshort","GXQty"=>"$FrameCapacity","stuffid"=>"$StuffId","time"=>"","oper"=>"系统",'props'=>$stuffProp,"way"=>"抽");

             }
            $tempArray=array(
                      "Id"=>"$Id","PrintDic"=>$printDict,
                      "RowSet"=>array("bgColor"=>"$LastBgColor"),
                      "Index"=>array("Text"=>"$Weeks","bgColor"=>"$colorSign","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR"),
                      "Col2"=> array("Text"=>"$Qty"),
                      "Col3"=>array("Text"=>"$RkQty","Color"=>"$rkColor"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$TimeColor","Margin"=>"-20,0,20,0"),
                      "Process"=>$ProcessArray 
                );
              
            $dataArray[]=array("Tag"=>"data",
            "onTap"=>array("Target"=>"Picture","Args"=>"$ImagePath"),
            "onEdit"=>"$onEditSign","data"=>$tempArray,"stuffid"=>"$StuffId",'realqty'=>"$realQty",'frameqty'=>"$FrameCapacity");
            $m++;
   } while($myRow = mysql_fetch_array($Result));
  		                   
	   $jsondata[]=array("IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
				   
       $totalQty=number_format($totalQty);
       $lastQty=$lastQty>0?number_format($lastQty):""; 
       $overQty=$overQty>0?number_format($overQty):""; 
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
				      "Col2"=>array("Text"=>"$overQty","FontSize"=>"14","Color"=>"#FF0000","Align"=>"L"),
                      "Col3"=>array("Text"=>"$totalQty($m)","Margin"=>"0,0,10,0","FontSize"=>"14")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
      
         $jsonArray=array("data"=>$jsondata,'printIp'=>'192.168.30.103'); 
 }
?>
