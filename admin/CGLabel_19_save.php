<?php 
//代码共享、数据库共享-EWEN 2012-11-02
session_start();
echo " $KORDERNO <Br>  --------- ";

$_SESSION["KORDERNO"]=$KORDERNO; 
$_SESSION["KPackingList"]=$KPackingList; 
$_SESSION["CAddress"]=$CAddress; 
$_SESSION["Volume"]=$Volume; 
$_SESSION["KDestination"]=$KDestination; 
$_SESSION["KStyle"]=$KStyle; 
$_SESSION["Materal"]=$Materal; 
$_SESSION["KColor"]=$KColor; 
$_SESSION["Drop"]=$Drop; 
$_SESSION["Tone"]=$Tone; 

echo "$Tone  -------------";


?>

