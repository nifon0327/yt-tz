<?php 
//电信-ZX  2012-08-01
//步骤1 $DataIn.sbpaymain 二合一已更新
include "../model/modelhead.php";
//步骤2：//需处理
$upDataMain="$DataIn.cw18_workhurtmain";
ChangeWtitle("$SubCompany 更新员工体检费用");
include "subprogram/upmain_model.php";
?>