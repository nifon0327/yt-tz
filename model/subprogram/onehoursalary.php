<?php 
/*
MC、DP共享
工价计算时设定的时薪
用到此参数的页面
生产工期统计：desk_deliverydatecount.php
车间生产统计：desk_cjtj_count.php,desk_cjtj_count_data.php
车间人工分析日统计：desk_cjrtj_read.php
*/
$checkOneHourSalaryt=mysql_fetch_array(mysql_query("SELECT Value FROM $DataPublic.cw3_basevalue WHERE ValueCode=107",$link_id));
$OneHourSalaryt=sprintf("%.2f",$checkOneHourSalaryt["Value"]);
?>
