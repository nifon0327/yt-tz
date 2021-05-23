<?php 
//BOM未收明细
include "../../basic/downloadFileIP.php";

$SearchRows.=" AND  B.CompanyId='$CompanyId' "; 
$today=date("Y-m-d");
$lastYear=date("Y")-1;
$WeeksNow = date("W");

$ip = "192.168.30.101";
$is47_1F_ip = "192.168.30.115";

$mySql="SELECT D.StuffId,D.StuffCname,D.Picture,D.Price,D.Estate,K.tStockQty,K.oStockQty,(K.tStockQty*D.Price*C.Rate) AS Amount,C.PreChar, MAX(IFNULL(YM.OrderDate,M.Date)) AS Date,
D.CheckSign,D.FrameCapacity,B.CompanyId,P.Forshort,max(RK.Date) lastRK,IF((D.SendFloor=17 or D.SendFloor=14),1,0) is47_1F 
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
						LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StuffId=K.StuffId
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataIn.ck1_rksheet RK ON RK.StuffId=K.StuffId   
						LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			            LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
						WHERE  K.tStockQty>0 AND TM.blSign=1 $SearchRows 
						GROUP BY K.StuffId ORDER BY  Date Desc";    //AND D.Estate>0
 // echo $mySql;  
 $Result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($Result)) {
     do {
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            
            $is47_1F = $myRow["is47_1F"];
            
             $Forshort = $myRow["Forshort"];
            $FrameCapacity = $myRow["FrameCapacity"];
              $CheckSign = $myRow["CheckSign"];
                $aCompanyId = $myRow["CompanyId"];
                     $lastRK = $myRow["lastRK"];
                
            $tStockQty=$myRow["tStockQty"];
            $oStockQty=number_format($myRow["oStockQty"]);
            $Price=$myRow["Price"];
            $Amount=number_format($tStockQty*$Price);
            $tStockQty=number_format($tStockQty);
            //$Price=sprintf("%.2f",$Price);
            $Price=floatval($Price);
            $PreChar=$myRow["PreChar"];
            $Picture=$myRow["Picture"];
             $PicutreImage=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             
            include "submodel/stuffname_color.php";
            
            //配件属性$StuffProperty
             include "submodel/stuff_property.php";
             
             //最后一次采购时间
             $LastDate=date("Y-m-d",strtotime($myRow["Date"]));
              $Overtime=0;
             if ($LastDate!="1970-01-01"){
                  $checkMonths=mysql_fetch_array(mysql_query("SELECT TIMESTAMPDIFF(MONTH,'$LastDate',Now()) AS months",$link_id)); 
                  //$months=(date("Y")-date("Y",strtotime($LastDate)))*12+(date("m")-date("m",strtotime($LastDate)));
                  $months=$checkMonths["months"];
                  $LastDateColor=$months>=1+$Overtime?"#0066FF":"";
                  $LastDateColor=$months>=3+$Overtime?"#FF0000":$LastDateColor;
	             //$days = (strtotime($today)-strtotime($LastDate))/3600/24;
	             //$LastDateColor=$days>90?"#B90061":"";
	             //$LastDateColor=$days>365?"#FF0000":$LastDateColor;
             }
             else{
                 $LastDate="";
	             $LastDateColor="";
             }
             $bgColor=$myRow["Estate"]==0?"#FCE2E0":"";
             $oStockResult = mysql_fetch_array(mysql_query("SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0  AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  K.StuffId='$StuffId'
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.StuffId='$StuffId' 
												)A GROUP BY A.StuffId",$link_id));
             $OrderQty=$oStockResult["OrderQty"];
             $bgColor=$OrderQty>0?"#DAE6F1":$bgColor;
             /*
             $checkOrder=mysql_query("SELECT S.POrderId FROM $DataIn.cg1_stocksheet S 
							LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
							WHERE S.StuffId='$StuffId' AND Y.Estate=1 AND S.POrderId>0 LIMIT 1",$link_id);
		    if (mysql_num_rows($checkOrder)>0){
			    $bgColor="#DAE6F1";
		    }
		    */
             /*
             $checkLastDate=mysql_fetch_array(mysql_query("SELECT MAX(M.Date) AS Date FROM $DataIn.cg1_stocksheet S 
                                          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid WHERE S.StuffId='$StuffId' ",$link_id));                                        
			$LastDate=$checkLastDate["Date"];
			*/
			 
			$Remark="";
			$RemarkResult=mysql_query("SELECT K.Remark,K.Date,M.Name FROM $DataIn.stuffremark  K
			          LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Operator 
			          WHERE K.StuffId='$StuffId' AND K.Type=1 ORDER BY K.Id DESC LIMIT 1 ",$link_id);    
			 if($RemarkRow = mysql_fetch_array($RemarkResult)) {
			        $Remark=$RemarkRow["Remark"];
			        $RemarkDate=date("Y/m/d",strtotime($RemarkRow["Date"]));
			        $RemarkOperator=$RemarkRow["Name"];
			 }                                  
              $stuffProp  =array();
			   $PropertyResult=mysql_query("SELECT T.TypeName  FROM $DataIn.stuffproperty P 
			   left join $DataIn.stuffpropertytype T on P.Property=T.Id 
			   WHERE P.StuffId='$StuffId' ORDER BY Property",$link_id);
		       while($PropertyRow=mysql_fetch_array($PropertyResult)){
		                 
		                  $stuffProp[]=$PropertyRow['TypeName'];
		                  
		        }
		     
		          switch($CheckSign){
                  case "0":$CheckSign="抽";break;
                  case "1":$CheckSign="全";break;
                  case "99":$CheckSign="--";break;
              }
              
              	$WeeksNow = date("W",strtotime($lastRK)); 
              		$lastRK = date("Y-m   ",strtotime($lastRK));
			 	 $printDict= array("CGPO"=>"","Week"=>"$WeeksNow","cName"=>"$StuffCname","tstock"=>"$tStockQty","Forshort"=>"$Forshort","GXQty"=>"","stuffid"=>"$StuffId","datee"=>"$lastRK","oper"=>"",'props'=>$stuffProp,"way"=>$CheckSign,'Frame'=>"$FrameCapacity",'Qty'=>"$num",
			 	 'ip'=> $is47_1F == 1 ? "$is47_1F_ip":"$ip",
			 	 'companyid'=>"$aCompanyId","time"=>"");
			 
			 
			                    
              $jsonArray[]= array(
	              'printdict'=>$printDict,
					             "Id"=>"$StuffId",
					             "is47_1F"=>"$is47_1F",
					             "onEdit"=>"1",
"RowSet"=>array("Cols"=>"5","ReSet"=>"1","bgColor"=>"$bgColor"),
"onTap"=>array("Title"=>"$StuffCname","Value"=>"$Picture","Tag"=>"StuffImage","URL"=>"$PicutreImage"),
					            // "Index"=>array("Title"=>"$Days","bgColor"=>""), 
					             "Caption"=>array("Title"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","Align"=>"L",
					                                             "GysIcon"=>"$StuffProperty","Margin"=>"-30,0,30,0"),
					             "Col_A"=>array("Title"=>"$LastDate","Color"=>"$LastDateColor","Align"=>"L","Margin"=>"-30,0,0,0"),
					             "Col_B"=>array("Title"=>"$tStockQty","IconType"=>"9","Align"=>"L","Margin"=>"0,0,10,0"),
					             "Col_C"=>array("Title"=>"$oStockQty","IconType"=>"10","Align"=>"L","Margin"=>"35,0,10,0"),
					             "Col_D"=>array("Title"=>"$PreChar$Price","Align"=>"R","Margin"=>"38,0,0,0"),
					             "Col_E"=>array("Title"=>"$PreChar$Amount","Align"=>"R","Margin"=>"20,0,0,0"),
					              "Remark"=>array("Title"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator")
					          ); 
	   } while($myRow = mysql_fetch_array($Result));
 }

?>