<?php   
//输出标准图
$FileName="T".$ProductId.".jpg";
$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);			
$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
?>