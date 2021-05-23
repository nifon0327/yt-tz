<?php    //取得产品类型的生产楼层

$type0=explode("/",$TypeName);
if($type0[0]!=""){
    /*$type1=explode(")",$type0[1]);
    $AddressName=$type1[0];
    if (strlen($AddressName)<2) $AddressName=$AddressName . "F";*/
    $AddressName=$type0[0];
    }
else $AddressName="&nbsp;";
?>