<?php   
//输出检验标准图
$FileName="T".$ProductId.".jpg";
$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
$td=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\",$ProductId)' style='CURSOR: pointer;' class='blueB'>$cName</span>";
?>