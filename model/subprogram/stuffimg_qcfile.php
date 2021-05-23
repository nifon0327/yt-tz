<?php   //配件QC检验标准图电信---yang 20120801
$qc_result = mysql_query("SELECT 0 as Id,D.Picture FROM $DataIn.stuffqcimg Q  
                   LEFT JOIN $DataIn.stuffqcstandard D ON Q.QcId=D.Id WHERE Q.StuffId='$StuffId'
               UNION
                   SELECT 1 as Id,Picture FROM   $DataIn.stuffqcstandard WHERE   TypeId='$TypeId' AND IsType=1 
                   order by Id LIMIT 1",$link_id);

$LessResult=mysql_fetch_array(mysql_query("SELECT L.Id From $DataIn.stuffqcless L  
                     LEFT  JOIN $DataIn.stuffdata S ON S.StuffId=L.StuffId
                     WHERE TypeId='$TypeId' AND S.StuffId='$StuffId' limit 1",$link_id));//如果存在，则说明这个产品不能用相关类图的QC标准图
if ($qc_result && !$LessResult){
		if ($qcimgRow= mysql_fetch_array($qc_result)){
		       $QCImage=$qcimgRow["Picture"];
		       $QCImage=anmaIn($QCImage,$SinkOrder,$motherSTR);
		       $Dir="download/stuffQCstandard/";
		       $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		       $QCImage="<span onClick='OpenOrLoad(\"$Dir\",\"$QCImage\")' style='CURSOR: pointer;color:#F00;'>View</span>";
		    }
		else{
		       $QCImage="&nbsp;";
		}
 }
else{
       $QCImage="&nbsp;";
}
?>