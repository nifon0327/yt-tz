<?php
    
    include_once "../../basic/parameter.inc";
    
    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/public/kqClass/Kq_dailyItem.php");
    include_once('../../FactoryCheck/FactoryClass/AttendanceDatetype.php');
    
    //$theGysId = $_POST["CompanyId"];
    //$theGysId = "2016";
    $CheckSign = $_POST["CheckSign"];
    $checkLine = $_POST['line']==''?'1':$_POST['line'];

    
    $checkBill = array();
    $checkBillSql = "SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,T.AQL,(G.AddQty+G.FactualQty) AS cgQty,M.Date,D.TypeId,Y.ProductId,G.POrderId,M.CompanyId,P.Forshort, H.shDate, M.BillNumber, PI.Leadtime, G.DeliveryDate, SUM(cj.Qty) as checkedQty
                     FROM ( SELECT  B.Qty as shQty, A.Sid, C.Date,  A.LineId
                            From $DataIn.qc_mission A
                            LEFT JOIN $DataIn.gys_shsheet B On A.Sid = B.Id
                            LEFT JOIN (SELECT max(Date) as Date, Sid From $DataIn.qc_cjtj Group by Sid) C On C.Sid = A.Sid
                            WHERE ((A.Estate = 0) OR (C.Date <= DATE_SUB(NOW(), INTERVAL 30 MINUTE))) and B.Estate = 2
                            Group by A.Sid
                     ) Q
                     INNER JOIN $DataIn.qc_scline L On L.Id = Q.LineId
                     INNER JOIN $DataIn.gys_shsheet S ON S.Id = Q.Sid
                     LEFT JOIN $DataIn.qc_cjtj cj ON cj.StockId = S.StockId
                     LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                     LEFT JOIN $DataIn.stuffdata D ON D.StuffId= S.StuffId 
                     LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                     Left Join $DataIn.yw1_ordersheet Y On Y.POrderId = G.POrderId
                     LEFT JOIN $DataIn.trade_object P ON P.CompanyId = M.CompanyId
                     Left Join $DataIn.gys_shdate H On H.Sid = S.Id
                     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
                     WHERE Q.LineId = $checkLine
                     Group By S.StockId
                     ORDER BY H.shDate, S.StuffId";

    $billResult = mysql_query($checkBillSql);
    if(mysql_num_rows($billResult)>0)
    {
        
        $tmpLine = array();
        while($billRows = mysql_fetch_assoc($billResult))
        {
            $Id = $billRows["Id"];                  //??????ID
            //$Date = $billRows["Date"];                //?????????????????????
            $StockId = $billRows["StockId"];            //?????????????????????
            $StuffId = $billRows["StuffId"];            //??????ID
            $TypeId = $billRows["TypeId"];    //????????????
            $StuffCname = $billRows["StuffCname"];  //????????????
            //$CheckSign = $billRows["CheckSign"];   //???????????????0????????????1?????????
            $cgQty = $billRows["cgQty"];                //????????????
            $Qty = $billRows["Qty"];                    //?????????????????????
            $Picture = $billRows["Picture"];            //????????????
            $AQL = $billRows["AQL"];
            $SendSign = $billRows["SendSign"];
            $SignString="";
            $ProductId = $billRows["ProductId"];
            //???????????????????????????
            $shDate = $billRows["shDate"];
            $shDate = substr($shDate, 0, 16);
            
            $sProperty = "0";
            $stuffPropertySql = mysql_query("Select Property From $DataIn.stuffproperty Where StuffId = $StuffId and Property in (1,2)");
            if($stuffPropertyRow = mysql_fetch_assoc($stuffPropertySql)){
                $sProperty = $stuffPropertyRow['Property'];
            }
            
            //$factoryCheck = "on";
            if($factoryCheck == "on"){
                $staffNumberSql = mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE JobId = 39 AND GroupId = 604 Limit 1");
                $staffNumberResult = mysql_fetch_assoc($staffNumberSql);
                /************????????????***************/
                $Number = $staffNumberResult['Number'];
                $sheet = new WorkScheduleSheet($Number, substr($shDate, 0, 10), $attendanceTime['start'], $attendanceTime['end']);
                $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
                $datetype = $datetypeModle->getDatetype($Number, substr($shDate, 0, 10), $sheet);
                if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
                    continue;
                }
                $shDate = substr($shDate, 0, 10);
            }
            
            $POrderId = $billRows["POrderId"];
            //???????????????
            $companyId = $billRows["CompanyId"];
            $companyName = $billRows["Forshort"];
            $billNumber = $billRows["BillNumber"];
            $lock = $billRows["Locks"];
            $ywLock = $billRows["ywLock"];
            
            $piDate = str_replace("*", "", $billRows["DeliveryDate"]);
            $piDate = date("Y-m-d", strtotime($piDate));
            $piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
            $leadTime = $piWeekResult["Week"];
            
            //??????BOM????????????????????????
            $checkcgLockSql=mysql_query("SELECT A.Locks
                                         FROM $DataIn.cg1_lockstock A
                                         WHERE 
                                         A.Locks = '0'
                                         And A.StockId = $StockId",$link_id);
                                         
            /*
echo "SELECT A.Locks
                                         FROM $DataIn.cg1_lockstock A
                                         WHERE 
                                         A.Locks =0
                                         And A.StockId = $StockId"."<br>";
*/
            //??????
            $checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2 LIMIT 1",$link_id);
            if(mysql_num_rows($checkcgLockSql) > 0 && $POrderId != ""){
                continue;           
            }
                    
            //????????????
            $CheckGSql=mysql_query("SELECT IFNULL(SUM(FactualQty+AddQty),0) AS cgQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id);
            $historyOrderRow = mysql_fetch_assoc($CheckGSql);
            $historyOrder = $historyOrderRow["cgQty"];
                    
            //???????????????????????????
            $hasError = "no";
            $errorCaseSql = "Select * From $DataIn.casetoproduct Where ProductId = '$ProductId'";
            $errorResult = mysql_query($errorCaseSql);
            if(mysql_num_rows($errorResult) > 0){
                $hasError = "yes";
            }
                                
            $lockMark = "no";
            
            //if ($SendSign==1) // SendSign: 0?????????1??????, 2?????? 
            switch ($SendSign){
                case 1:{
                    $thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  
                                        FROM $DataIn.ck2_thmain M                      
                                        LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
                                        WHERE M.CompanyId = '$CompanyId' 
                                        AND S.StuffId = '$StuffId' ",$link_id);
                    $thQty=mysql_result($thSql,0,"thQty");
                
                    //??????????????? add by zx 2011-04-27
                    $bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  
                                        FROM $DataIn.ck3_bcmain M 
                                        LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
                                        WHERE M.CompanyId = '$CompanyId' 
                                        AND S.StuffId = '$StuffId' ",$link_id);
                    $bcQty=mysql_result($bcSql,0,"bcQty");  
                    $cgQty=$thQty-$bcQty;
                    $noQty=$cgQty;
                    $SignString="(??????)";
                    $StockId="????????????";
                }
                break;
                case 2:{
                    $cgQty=0;
                    $noQty=0;
                    $SignString="(??????)";
                    $StockId="????????????";
                }
                break;
                default :{
                    $rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty 
                                         FROM $DataIn.ck1_rksheet R 
                                         LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
                                         WHERE R.StockId='$StockId'",$link_id);
                    $rkQty=mysql_result($rkTemp,0,"Qty");   //????????????
                    $noQty=$cgQty-$rkQty;   
                }           
                break;
            }   
            
            if($noQty <= 0 && $SendSign !=2){
                $lockMark = "???????????????";
            }       
            else if($noQty < $Qty && $SendSign != 2){
                $lockMark = "?????????????????????????????????";
            }
                    
            $Picture = $billRows["Picture"];
            switch($Picture){
                case 0:{
                    $LockRemark = "??????????????????";
                }
                break;
                case 2:{
                    $LockRemark = "?????????";
                }
                break;
                case 3:{
                    $LockRemark = "??????????????????";
                }
                break;
                case 4:{
                    $LockRemark = "??????????????????";
                }
                break;
            }

            
            $Remark = "";
            $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
            if($remarkRow=mysql_fetch_array($remarkSql)){
                $Remark=$remarkRow["Remark"];
            }
                    
            //???????????????????????????????????????????????????
            $isLastBgColor = "0";
            if(!$POrderId == ""){
                $FromPageName = "sh";
                include "../../model/subprogram/stuff_blcheck.php";
            }else{
                $LastBgColor = "";
            }
            
            if($LastBgColor != ""){
                $isLastBgColor = "1";
            }


            
            $addBuyTag = "no";
            $checkAddBuySql = "Select * From $DataIn.yw1_ordersheet Where POrderId = '$POrderId'";
            $checkAddResult = mysql_query($checkAddBuySql);
            if(mysql_num_rows($checkAddResult) == 0){
                $addBuyTag = "yes";
            }
    
            $cgQty = intval($cgQty);
            $cgQtyCount = number_format($cgQty);
                
            $noQty = intval($noQty);
            $noQtyCount = number_format($noQty);
            
            $recordQtySql = "SELECT SUM(Qty) as Qty FROM $DataIn.qc_cjtj WHERE Sid = $Id";
            $recordQtyResult = mysql_fetch_assoc(mysql_query($recordQtySql));
            $recordQty = $recordQtyResult['Qty'] == ''?'0':$recordQtyResult['Qty'];
            $Qty = intval($Qty);
            //$QtyCount = number_format($Qty);
            $billCount += $Qty;
            $historyOrder = intval($historyOrder);
            $historyOrderCount = number_format($historyOrder);

            $tmpLine[] = array("stockId"=>"$StockId", "stuffCname"=>"$StuffCname", "cgQtyCount"=>"$cgQty", "noQtyCount"=>"$noQtyCount", "qtyCount"=>"$Qty", "note"=>"$Remark", "AQL"=>"$AQL", "Id"=>"$Id", "stuffId"=>"$StuffId", "productId"=>"$ProductId", "hasError"=>"$hasError", "lockMark"=>"$lockMark", "picture"=>"$Picture", "shDate"=>"$shDate", "isLast"=>"$isLastBgColor", "history"=>"$historyOrder", "companyId"=>"$companyId", "companyName" => "$companyName", "billNumber"=>"$billNumber", "addBuyTag" => "$addBuyTag", "TypeId" => "$TypeId", "piDate"=>"$piDate", "piWeek"=>"$leadTime", "property" => "$sProperty", "LastBgColor"=>"$LastBgColor", 'recordCount'=>"$recordQty");
            
        }
            
    }
        
    echo json_encode($tmpLine);
    
?>