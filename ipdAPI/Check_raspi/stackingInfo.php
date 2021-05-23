<?php
include "../../basic/parameter.inc";

$stuffId = $_POST['stuffId']; 
$currentId = 7;
if($stuffId != 'barcode'){
    $currentId = 8;
    $stuffId = str_replace('N', '', $stuffId);
}
$rId = $_POST['sId'];
preg_replace('/[^\d]/','',$rId);
$isLast = "N";
$LineId = '0';
if($stuffId != 'barcode'){
    $rId = str_replace('N', '', $rId);
    $getRecordSql = "SELECT A.Qty,FrameCapacity,A.LineId,A.Sid,A.stacked,A.StockId
                From qc_cjtj A
                left join stuffdata B On A.StuffId = B.StuffId
                where A.Id = $rId";
    $recordResult = mysql_query($getRecordSql);

    if($recordRow = mysql_fetch_assoc($recordResult)){
        $position = $recordRow['LineId'];
        $qty = intval($recordRow['Qty']);
        $FrameCapacity = intval($recordRow['FrameCapacity']);
        $sId = $recordRow['Sid'];
        $LineId = $recordRow['LineId'] == '2'?'0':'1';
        $StockId = $recordRow['StockId'];
        preg_replace('/[^\d]/','',$StockId);
    }    
}else{
    $LineId = '1';
    $infos = explode('|', $rId);

    $StockId = str_replace('M', '', $infos[0]);
    $getStuffId = "SELECT StuffId From gys_shsheet Where StockId=$StockId";
    $getStuffIdResult = mysql_fetch_assoc(mysql_query($getStuffId));
    $stuffId = $getStuffIdResult['StuffId'];

}

$getCurrentMisson = "SELECT * FROM qc_currentcheck Where Id = $currentId";
//echo $getCurrentMisson;
$getCurrentResult = mysql_fetch_assoc(mysql_query($getCurrentMisson));
$currentStockId = $getCurrentResult['StockId'];
$currentStuffId = $getCurrentResult['stuffId'];

preg_replace('/[^\d]/','',$currentStockId);
if($currentStuffId != $stuffId){
    $isLast = 'Y';

    $updateCurrent = "UPDATE qc_currentcheck SET StockId='$StockId',StuffId='$stuffId' Where Id=$currentId";
    mysql_query($updateCurrent);

}

echo json_encode(array('position'=>"$LineId", 'isLast'=>$isLast, 'stacked'=>"$currentStuffId:$stuffId"));

?>