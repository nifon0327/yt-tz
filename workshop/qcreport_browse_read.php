<?php
    include_once "tasks_function.php";
    include "../basic/parameter.inc";

    $stuffId = $_GET['stuffId']==''?$StuffId:$_GET['stuffId'];
    //$stuffId = '164971';

    $ListSTR="";

    //总统计
    $qcbadrecordStaticSql =  "SELECT  sum(record.shQty) as shQty, sum(record.checkQty) as checkQty, sum(record.Qty) as Qty,count(*) as count, stuff.StuffCname
                        FROM $DataIn.qc_badrecord AS record
                        LEFT JOIN $DataIn.gys_shmain as sh ON record.shMid = sh.Id
                        LEFT JOIN $DataIn.trade_object as trade ON sh.CompanyId = trade.CompanyId
                        LEFT JOIN $DataIn.stuffdata as stuff ON stuff.stuffId = record.stuffId
                        WHERE record.StuffId= '$stuffId' Order By record.Date Desc";
    $qcBadStaticResult= mysql_query($qcbadrecordStaticSql);
    $qcBadSattic = mysql_fetch_assoc($qcBadStaticResult);  
    $qsumBadQty = $qcBadSattic['Qty'];
    $qsumShQty = $qcBadSattic['shQty'];
    $qsumCount = $qcBadSattic['count'];
    $stuffname = $qcBadSattic['StuffCname'];

    //获取配件qc数据
    $qcbadrecordSql =  "SELECT  record.Id,record.shQty, record.checkQty, record.Qty,record.Date,trade.Forshort, stuff.StuffCname
                        FROM $DataIn.qc_badrecord AS record
                        LEFT JOIN $DataIn.gys_shmain as sh ON record.shMid = sh.Id
                        LEFT JOIN $DataIn.trade_object as trade ON sh.CompanyId = trade.CompanyId
                        LEFT JOIN $DataIn.stuffdata as stuff ON stuff.stuffId = record.stuffId
                        WHERE record.StuffId= '$stuffId' Order By record.Date Desc Limit 10";
    //echo $qcbadrecordSql;
    $qc_badrecordResult = mysql_query($qcbadrecordSql);

    $sumBadQty = 0;
    $sumShQty = 0;
    $sumCount = 0;
    $totleCount = mysql_num_rows($qc_badrecordResult);
    while($qc_rows= mysql_fetch_assoc($qc_badrecordResult)){
        $Id=$qc_rows['Id'];
        $shQty = $qc_rows['shQty'];
        $checkQty = $qc_rows['checkQty'];
        $qcDate = substr($qc_rows['Date'], 0, 10);
        $distanceDay = (strtotime(date('Y-m-d'))-strtotime($qcDate))/(24*3600);
        $companyName = $qc_rows['Forshort'];

        $qc_checkBadTotleSql = "SELECT sum(Qty) as Qty From $DataIn.qc_badrecordsheet Where Mid='$Id'";
        $qc_checkBadResult = mysql_query($qc_checkBadTotleSql);
        $qc_checkBadRow = mysql_fetch_assoc($qc_checkBadResult);
        $qcBadTotle = $qc_checkBadRow['Qty'];

        $qc_badrecordSheetSql = "SELECT sheet.Qty, sheet.Reason, sheet.CauseId, type.Cause, file.Picture, sheet.Id
                                 FROM $DataIn.qc_badrecordsheet AS sheet
                                 LEFT JOIN $DataIn.qc_causetype AS type ON type.Id = sheet.CauseId
                                 LEFT JOIN $DataIn.qc_badrecordfile AS file ON file.Mid = sheet.Id
                                 WHERE sheet.Mid = $Id";

        $qc_badrecordSheetResult = mysql_query($qc_badrecordSheetSql);
        $reasonList = "";
        $totleBadQty = 0;
        while($qc_sheetRow = mysql_fetch_assoc($qc_badrecordSheetResult)){
            $sheetId = $qc_sheetRow['Id'];
            $badQty = $qc_sheetRow['Qty'];
            //$causeReason = $qc_sheetRow['CauseId']=="-1"?$qc_sheetRow['Cause']:$qc_sheetRow['Reason'];

            $causeReason = '-';
            if ($qc_sheetRow['CauseId']=='-1') {
                $causeReason = $qc_sheetRow['Reason'];
            }else if($qc_sheetRow['CauseId'] != ''){
                $causeReason = $qc_sheetRow['Cause'];
            }

            $fileName = $qc_sheetRow['Picture']==''?"Q$sheetId.jpg":$qc_sheetRow['Picture'];
            $imagePath = "../download/qcbadpicture/$fileName";
            if(!file_exists($imagePath)){
                $imagePath = 'image/nonImage.png';
            }


            $totleBadQty += $badQty;
            $reasonList.="<div class='float_left' style='margin-right:20px;padding-top:30px;'><img src='$imagePath' width='228' height='190' style='transform: rotate(90deg)' ><div style='background-color:#308CC0;position:relative; margin-top:-30px;padding-bottom:10px;color:#FFFFFF;text-align:center;font-size:30px;width:190px;font-family:SimHei;padding-top:5px;margin-left:19px;'>$causeReason($badQty)</div></div>";
            $sumCount++;
        } 

        $sumBadQty += $totleBadQty;
        $sumShQty += $shQty;

        if($reasonList == ''){
            $lineHeight = '40px';
        }else{
            $lineHeight = '270px';
        }
        $reasonList = "<tr>
                        <td><div class='float_left' style='background-color:#888888;width:2px;height:$lineHeight;margin-left:87px;'></div></td>
                        <td style='width:100px; border-bottom: 25px solid #E5F1F7;padding-bottom:10px;' colspan='2'>$reasonList</td>
                       </tr>";

        //<span class='numberIcon'>".$totleCount."</span>
        $badRate = number_format(($totleBadQty/$shQty),2)*100;
        $ListSTR .= "<table id='List$m' name='List[]' class='recordTable' cellspacing=0px; style='color:#333333;'>
                        <tr >
                            <td width='180' class='blue_color' style='font-size:25px;text-align:center;font-family:SimHei;padding-top:30px;margin_top:10px;' valign='middle'>
                                <div>
                                    $companyName
                                </div>
                                <div style='text-align:left;padding-left:15px;padding-top:20px;'>
                                    <img src='image/timeicon.png' width='30' height='30'>
                                    <span class='numberFont' style='font-size:40px;'>".$distanceDay."d</span>
                                </div>
                            </td>
                            <td width='450' style='font-size:50px;text-align:center;' valign='middle'>
                                <div class='float_left numberFont' style='width:444px;margin-top:30px;'>
                                    <img src='image/checkicon.png' width='40' height='40'>
                                    <span style='margin-left:10px'>".number_format($shQty)."</span>
                                </div>
                                <div class='float_left' style='background-color:#333333;width:3px;height:45px;margin-top:30px;'></div>
                            </td>
                            <td width='450' style='font-size:50px;text-align:center;'>
                                <div class='numberFont' style='width:444pxpx;margin-top:30px;'>
                                    <img src='image/badrateicon.png' width='40' height='40''>
                                    <span class='red_color'>".number_format($totleBadQty)."</span>(".$badRate."%)
                                </div>
                            </td>
                        </tr>
                        $reasonList
                    </table>";

    $totleCount=$totleCount-1;

    }





?>
 
<div id='headdiv' style='height:210px;'>
    <div class='title'>
        <table style='background-color:#E5F1F7;'>
            <tr>
                <td class='blue_color numberFont' style='height:100px;padding-top:20px;' valign="top"><?php echo $stuffId.'-'?></td>
                <td style='height:100px;font-family:SimHei;padding-top:20px' valign="top"><?php echo $stuffname?></td>
            </tr>
        </table>

       <!--  <div class='blue_color float_left' style='margin-top:5px'><?php echo $stuffId.'-'?></div>
        <div><?php echo $stuffname?></div> -->
    </div>
    <div style='margin_top:20px'>
         <div class='float_right qcCount'><?php echo $qsumCount?></div>
         <div class='float_right qcShQty'><?php echo $qsumShQty ?></div>
         <div class='float_right' style='background-color:#888888;width:5px;height:40px;margin-top:10px;margin-right:10px;'>
        <div class='float_right qcBad'><?php echo $qsumBadQty ?></div>
    </div>
</div>
<div id='listdiv' style='overflow: hidden;height:1740px;width:1080px;'>
<?php echo $ListSTR;?>
</div>