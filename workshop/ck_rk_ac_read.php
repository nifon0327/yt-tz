<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $SearchRows="";
      switch($Floor){
         case "3A":
         case "6":    $Floor=6; $Line="D"; break;
         case "17":    $Floor=17; break;
         default:     $Floor=3;$Line=$Line==""?"A":$Line; break;
      }
      
      $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      $SearchRows=" AND  H.LineId='$LineId' ";
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";   

 $m=0;

$TotalQty=0; $TotalCount=0;//品检总数
$CurQty=0;  $CurCount=0;//品检中
$QcedQty=0;  $QcedCount=0;//待处理

$WaitQty=0; $WaitCount=0;   
 //品检任务
$mySql = "SELECT * FROM
        (
        SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,S.LineNo,
                S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$curWeek',1,0) AS OveSign,U.Decimals,'1' as stateTag
        FROM (
            SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit,L.LineNo
            FROM qc_cjtj  C 
            INNER JOIN gys_shsheet S ON C.Sid=S.Id
            INNER JOIN gys_shmain M ON S.Mid=M.Id 
            INNER JOIN qc_badrecord B ON B.Sid=S.Id  
            LEFT JOIN cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN qc_mission H ON H.Sid=S.Id 
            LEFT JOIN qc_scline L On L.Id = H.LineId
            WHERE  C.Estate=1  AND M.Floor='$Floor'  AND D.ComboxSign!=1 
            GROUP BY S.Id
            )S 
        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
        LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
        LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
        LEFT JOIN  stuffunit U ON U.Id=S.Unit 
        WHERE  1  

        UNION ALL
        SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,S.LineNo,
                S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$curWeek',1,0) AS OveSign,U.Decimals,'1' as stateTag  
        FROM (
            SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit,L.LineNo
            FROM qc_cjtj  C 
            INNER JOIN gys_shsheet S ON C.Sid=S.Id 
            INNER JOIN gys_shmain M ON S.Mid=M.Id 
            INNER JOIN qc_badrecord B ON B.Sid=S.Id  
            LEFT JOIN cg1_stuffcombox MB ON MB.StockId=S.StockId 
            LEFT JOIN cg1_stocksheet G ON G.StockId=MB.mStockId 
            LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN qc_mission H ON H.Sid=S.Id  
            LEFT JOIN qc_scline L On L.Id = H.LineId
            WHERE  C.Estate=1 AND M.Floor='$Floor' AND C.Qty>0 AND D.ComboxSign=1 
            GROUP BY S.Id
        )S 
        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
                LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
        LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
        LEFT JOIN  stuffunit U ON U.Id=S.Unit

        UNION ALL
        SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,SUM(CJ.Qty) as scQty,MAX(CJ.Date) as scDate,L.LineNo,
                  N.LineId,D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$curWeek',1,0) AS OveSign,U.Decimals,'2' as stateTag
        FROM  gys_shsheet S  
        INNER JOIN qc_mission N ON N.Sid=S.Id
        LEFT JOIN qc_scline L On L.Id = N.LineId 
        LEFT JOIN qc_cjtj CJ ON CJ.Sid = S.Id
        LEFT JOIN gys_shmain M ON M.Id=S.Mid 
        LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
        LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
        LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
        LEFT JOIN  stuffunit U ON U.Id=D.Unit
        WHERE M.Floor='$Floor' AND S.Estate='2' AND S.SendSign<2 AND D.ComboxSign!=1 
        GRoup by S.Id
        HAVING scQty > 0

        UNION ALL
        SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,SUM(CJ.Qty) as scQty,MAX(CJ.Date) as scDate,L.LineNo,
                  N.LineId,D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$curWeek',1,0) AS OveSign,U.Decimals,'2' as stateTag
        FROM  gys_shsheet S  
        INNER JOIN qc_mission N ON N.Sid=S.Id
        LEFT JOIN qc_scline L On L.Id = N.LineId  
        LEFT JOIN qc_cjtj CJ ON CJ.Sid = S.Id
        LEFT JOIN gys_shmain M ON M.Id=S.Mid 
        LEFT JOIN cg1_stuffcombox MB ON MB.StockId=S.StockId 
        LEFT JOIN cg1_stocksheet G ON G.StockId=MB.mStockId 
        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
        LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
        LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
        LEFT JOIN  stuffunit U ON U.Id=D.Unit
        WHERE M.Floor='$Floor' AND S.Estate='2' AND S.SendSign<2 AND D.ComboxSign=1 
        GRoup by S.Id
        HAVING scQty > 0) T
        ORDER By T.stateTag desc,T.scDate";
//echo $mySql;
$myResult=mysql_query($mySql,$link_id);//shDate,
while($myRow = mysql_fetch_array($myResult)) {
        $Id=$myRow["Id"];
        $Mid=$myRow["Mid"];
        $Weeks=$myRow["DeliveryWeek"];
        $Qty=$myRow["Qty"];
        $LineNo = $myRow['LineNo'];
        $targetDate = $myRow['scDate'];
        $rkSign = $myRow['rkSign'];
        //已登记数量
        $StuffId=$myRow["StuffId"];
        $DjQty=$myRow["scQty"];
        $dateColor = '#848888';
        
        $QcDateStr = GetDateTimeOutString($targetDate, '');
        $StockId=$myRow["StockId"];

        $tableClass="";
        if($myRow['stateTag'] == '1'){
            $tableClass="table_image";
            $secondLogo = "<img src='image/checkedLogo.png' style='margin-right:0px;width:42px;'/>";
            $QcedQty += $Qty;
            $QcedCount++;
        }else{
            $WaitQty += $Qty;
            $WaitCount++;
            $secondLogo = "<img src='image/$LineNo.png' style='margin-right:0px;width:42px;'/>";
        }
         
        if ($m<12){

            if ($Weeks>0){
                 $StockId=$myRow["StockId"];
                 $Week1=substr($Weeks, 4,1);
                 $Week2=substr($Weeks, 5,1);
                 $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
                 $tdColor=$curWeek>$Weeks?'red_color':'black_color';
                 $WeekClass="week";
                $WeekSTR="<div>$Week1</div><div>$Week2</div>";
            }else{
                $WeekClass="week2";
                $WeekSTR="<div>补</div>";
            }
                 
            $StuffCname=$myRow["StuffCname"];
            if(mb_strlen($StuffCname,'utf-8') > 12){
                $shortCname = mb_substr($StuffCname, 0, 12, 'utf-8').'...';
            }else{
                $shortCname = $StuffCname;
            }
            $Forshort=$myRow["Forshort"];
            $CompanyId = $myRow['CompanyId'];
            // if($CompanyId == '2270'){
            //     $innerForshortSql = "SELECT B.Name FROM yw1_scsheet A 
            //                         LEFT JOIN workshopdata B ON A.WorkShopId = B.Id
            //                         WHERE A.mStockId = $StockId";
            //     $innerForshortResult = mysql_fetch_assoc(mysql_query($innerForshortSql));
            //     $Forshort = $innerForshortResult['Name'];
            // }
            
            $Qty=number_format($Qty);
            
            if($DjQty > 0){
              $DjQty = number_format($DjQty);
              $DjQty = "<span class='green_color'>$DjQty</span>";
            }else{
              $DjQty = 0;
            }
            $Forshort="<span class='blue_color'>$Forshort</span>";

            //$bgColor = $m%2!=0?"#E5F1F7":"#fff";
            $logoName = 'image/noIogo.png';
            if(file_exists("../download/stuffIcon/$StuffId.png")){
                $logoName = "../download/stuffIcon/$StuffId.png";
            }else if(file_exists("../download/stuffIcon/$StuffId.jpg")){
                $logoName = "../download/stuffIcon/$StuffId.jpg";
            }
            $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass qc_table' style='background-color:$bgColor;height:100px;'>
                <tr height='40' style='vertical-align:top;'>
                    <td rowspan='2' width='120px' style='padding-top:20px;padding-left:10px;'><img style='width:100px;' src='$logoName'></td>
                    <td width='80' class='$WeekClass  $WeekColor' style='padding-left:15px;padding-top:5px;'>$WeekSTR</td>
                    <td colspan='4' width='600' class='title_qc_ac'>$Forshort-$shortCname</td>
                    <td class='time static_qty_ac' width='300' style='padding-top:-5px;padding-right:65px;'>$DjQty/$Qty</td>
               </tr>";
                   
            //备注 
            $Remark="";
            $RemarkResult=mysql_query("SELECT Remark  FROM $DataIn.qc_remark WHERE  Sid='$Id' ORDER BY Date DESC, Id Desc LIMIT 1",$link_id);
            if($RemarkRow = mysql_fetch_array($RemarkResult)) {
                  $Remark=$RemarkRow["Remark"];
             }

            //同一张单相同配件的备品 
            $Mid=$myRow["Mid"];
            $bpRemark="";
            $bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate=2  AND S.SendSign=2",$link_id);
             if($bpRow = mysql_fetch_array($bpResult)) {
                  $bpQty=number_format($bpRow["Qty"]);
                    $sameResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate>0  AND S.SendSign=0 ",$link_id));
                   $Nums=$sameResult["Nums"];
                   $bpRemark=$bpQty . "pcs备品($Nums);";
             }
            if ($bpRemark!="" || $Remark!=""){
                    //<td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span>&nbsp;</td>
                    $ListSTR.="<tr height='40'>
                                    <td></td>
                                    <td colspan='4' class='remark_qc' style='margin-top:-10px;'><img class='float_left' src='image/remark.png'/><div class='float_left' style='padding-bottom:0px;'>$bpRemark $Remark</div></td>
                                    <td class='time_qc float_right'><span style='font-size:30px;'><div style='color:$dateColor;margin-top:-20px;margin-right:5px;'>$QcDateStr</div></span>
                                    $secondLogo</td>
                                </tr>";
            }
            else{
                    $ListSTR.="<tr>
                                    <td colspan='4' style='height:30px;line-height:40px;'>&nbsp;</td>
                                    <td  class='remark_qc text_right'>&nbsp;</span>&nbsp;</td>
                                    <td class='time_qc float_right'>
                                        <span class='$typeColor' style='font-size:30px;'>
                                            <div style='color:$dateColor;margin-top:-20px;margin-right:5px;'>$QcDateStr</div>
                                        </span>$secondLogo
                                    </td>
                                </tr>";
            }                                        
                                      
             $ListSTR.="</table>";
             $m++;
        }
}


//$WaitQty=$TotalQty-$CurQty-$QcedQty;
//$WaitCount=$TotalCount-$CurCount-$QcedCount;
//

$TotalQty=number_format($TotalQty);
$CurQty=number_format($CurQty);
$QcedQty=number_format($QcedQty);
$WaitQty=number_format($WaitQty);

include "../iphoneAPI/subprogram/worktime_read.php";
$upTime=date("H:i:s");




$realPriceSql = "SELECT SUM(S.Qty) as Vaule, '1' as sign
                FROM $DataIn.ck1_rksheet S 
                LEFT JOIN $DataIn.`stuffdata` R On R.StuffId = S.StuffId 
                Where left(S.created, 10) ='$curDate'
                AND R.SendFloor in ($Floor)";

//echo $realPriceSql ;
$realResult = mysql_fetch_assoc(mysql_query($realPriceSql));
$TodayQty = $realResult['Vaule'] == ""?0:number_format($realResult['Vaule']);


?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
<div id='headdiv_qc' style='height:200px;'>
    <div id='linediv_new' class='float_left'>入</div>
       <ul id='quantity3_qc' class='float_right'>
             <li class='text_left'><span style='color:#848888'><?php echo " / ".$QcedQty; ?></span></li>
             <li class='text_right'><span class='margin_right_15'><?php echo $TodayQty; ?></span></li>
      </ul>
      <ul id='count_qc' class=''>
            <li style='width: 540px;'>待处理 <div></div>
                <span class='float_right'>
                    <span class='red_color'><?php echo $WaitQty; ?> </span>
                    <span class='littleLi'><?php echo  "($WaitCount)"; ?> </span>
                </span>
            </li>
            <li style='width: 540px;'>待入
                <span class='float_right'>
                    <span ><?php echo $QcedQty; ?> </span>
                    <span class='littleLi'><?php echo  "($QcedCount)"; ?> </span>
                </span>
            </li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<!-- <div id='staticDiv' class='topLine' style='height:290px;width:1080px;'>
    <table style='border-collapse: 0px;border-spacing: 0px;'>
        <tr style='height:240px;' class='tableBottomLine' >
            <?php echo $staticListStr;?>
        </tr>
        <tr style='height:60px;'>
            <?php echo $dateListStr;?>
        </tr>
    </table>
</div> -->
