<?php
include "../model/modelhead.php";
$Parameter="tSearchPage,$tSearchPage,fSearchPage,$fSearchPage,SearchNum,$SearchNum,Action,$Action,uType,$uType";
if($r!="" && isset($_SESSION['sSearch'])){
	unset($_SESSION['sSearch']);
	$sSearch="";
	}
ChangeWtitle("$SubCompany 查询");
?>