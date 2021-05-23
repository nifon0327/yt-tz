<?php   
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache"); 
$Date=date("Y-m-d");
switch($ActionId){
         case 2:
                 if($Qty>0){
                      $UpdateSql="UPDATE $DataIn.skech_deliverysheet SET Qty=$Qty  WHERE Id=$Sid";
                      $UpdateResult=@mysql_query($UpdateSql);
                  }
               else{
                        $CheckMid=mysql_fetch_array(mysql_query("SELECT Mid FROM $DataIn.skech_deliverysheet WHERE Id=$Sid",$link_id));
                        $Mid=$CheckMid["Mid"];
                        $DelSql="DELETE FROM $DataIn.skech_deliverysheet WHERE Id=$Sid";
                        $DelResult=@mysql_query($DelSql);
                        if($DelResult&& mysql_affected_rows()>0){ //最后一个删除主表
                                $DelmainSql="DELETE A FROM skech_deliverymain  A 
                                LEFT JOIN $DataIn.skech_deliverysheet B ON B.Mid=A.Id 
                                WHERE A.Id=$Mid AND B.Id IS NULL";
                               $DelmainResult=@mysql_query($DelmainSql);
                             }
                      }
         break;
}