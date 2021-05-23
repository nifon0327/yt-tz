<?php
    include "../model/modelhead.php";
    $From=$From==""?"read":$From;
    //需处理参数
    $ColsNumber=10;             
    $tableMenuS=600;
    ChangeWtitle("$SubCompany 员工排班设置");
    $funFrom="rs_tdb";
    $nowWebPage=$funFrom."_read";
    $Th_Col="选项|50|序号|50|班次名称|60|上午上班时间|100|上午下班时间|100|下午上班时间|100|下午下班时间|100|加班开始时间|100";
    $Pagination=$Pagination==""?0:$Pagination;
    $Page_Size = 100;
    $ActioToS="1,2,3,4,7,8";

    //步骤3：
    include "../model/subprogram/read_model_3.php";
    //步骤4：需处理-条件选项
    echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
        $CencalSstr";
    //步骤5：
    include "../model/subprogram/read_model_5.php";
    //步骤6：需处理数据记录处理
    $i=1;
    $j=($Page-1)*$Page_Size+1;
    List_Title($Th_Col,"1",0);

    
?>