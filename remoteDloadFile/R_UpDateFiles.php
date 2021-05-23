<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include "../remoteDloadFile/basic/parameter.inc";   //加放数据库访问
switch ($UpFileSign){  //$UpFileSign:
	case "stuff":   //stuff: 表示从stuffdata_updated.php过来的，好找从那里远程调用过来，每个尽量标示
		include "../remoteDloadFile/R_stuffdata_updated.php";
	break;

}
?>