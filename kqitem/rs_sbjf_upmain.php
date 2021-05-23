<?php 
//电信-ZX  2012-08-01
//步骤1 $DataIn.sbpaymain 二合一已更新
include "../model/modelhead.php";
//步骤2：//需处理
$upDataMain="$DataIn.sbpaymain";
ChangeWtitle("$SubCompany 更新社保费用结付记录");
include "subprogram/upmain_model.php";
?>