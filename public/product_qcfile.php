<?php   //产品QC检验标准图电信---yang 20120801
$qc_result = mysql_query("SELECT 0 as Id,D.Picture FROM $DataIn.qcstandardimg Q  
                   LEFT JOIN $DataIn.qcstandarddata D ON Q.QcId=D.Id WHERE Q.StuffId=$StuffId 
               UNION
                   SELECT 1 as Id,Picture FROM $DataIn.qcstandarddata WHERE   TypeId=$TypeId AND IsType=1 
                   order by Id LIMIT 1",$link_id);
if ($qcimgRow= mysql_fetch_array($qc_result)){
       $QCImage=$qcimgRow["Picture"];
       $QCImage=anmaIn($QCImage,$SinkOrder,$motherSTR);
       $Dir="download/QCstandard/";
       $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
       $QCImage="<span onClick='OpenOrLoad(\"$Dir\",\"$QCImage\")' style='CURSOR: pointer;color:#F00;'>View</span>";
    }
else{
       $QCImage="&nbsp;";
}
 

?>