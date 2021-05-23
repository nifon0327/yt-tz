<?php
function cancel_project_all($num)
{
    $mysql = "delete from trade_drawing where TradeId = $num";
    mysql_query($mysql);
    $mysql = "delete from trade_drawing_hole where TradeId = $num";
    mysql_query($mysql);
    $mysql = "delete from trade_embedded where TradeId = $num";
    mysql_query($mysql);
    $mysql = "delete from trade_embedded_data where TradeId = $num";
    mysql_query($mysql);
    $mysql = "delete from trade_steel where TradeId = $num";
    mysql_query($mysql);
    $mysql = "delete from trade_steel_data where TradeId = $num";
    mysql_query($mysql);
}