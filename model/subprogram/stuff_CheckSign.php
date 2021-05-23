<?php 
//获取品检方式
switch($CheckSign){
	  case "0":$CheckSign="抽检";break;
	  case "1":$CheckSign="<div style='color:#E00;' >全检</div>";break;
	  case "99":$CheckSign="-----";break;
}
?>