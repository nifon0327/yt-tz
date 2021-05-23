<?php
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数电信---yang 20120801
include "../model/modelhead.php";
$funFrom = "trade_steel";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="钢筋";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

$proId = $_GET["proId"];
//步骤3：需处理，执行动作
$Ids = $_GET["Ids"];
$checkid = explode(',',$Ids);
$y=count($checkid);

$DelSql = "DELETE FROM $DataIn.trade_steel WHERE Id IN ($Ids)";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
    $Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
    
    $query=mysql_query("select count(*) from $DataIn.trade_steel where TradeId=$proId");
    if(mysql_num_rows( $query)){
        $rs=mysql_fetch_array($query);
        //统计结果
        $count=$rs[0];
    }else{
        $count=0;
    }
    
    if ($count == 0) {
        $mysql="delete from $DataIn.trade_steel_data where TradeId=$proId";
        @mysql_query($mysql);
    }
}
else{
    $Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. </div><br>";
    $OperationResult="N";
}

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&proId=$proId";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>