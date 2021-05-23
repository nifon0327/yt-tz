<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

include "../model/characterset.php";

$pandsS="";
for($i=0;$i<11;$i++){
	//判断字段类型
	if($value[$i]!=""){
		switch($types[$i]){
			case"isYandN":
				if($value[$i]==0){
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i]."=''";
					}
				else{
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i]."!=''";
					}
			break;
			case"isNum":
				$pandsS=$pandsS." and ".$table[$i].".".$Field[$i].$fun[$i].$value[$i];
			break;
			case"isDate":
				if($LastDate!=""){
					$pandsS=$pandsS." and ".$table[$i].".Date between '".$value[$i]."' and '".$LastDate."'";
					}
				else{
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
					}
			break;
			case"pDate":
				if($pLastDate!=""){
					$pandsS=$pandsS." and ".$table[$i].".Date between '".$value[$i]."' and '".$pLastDate."'";
					}
				else{
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
					}
			break;
			case"isStr":
				if($fun[$i]=="LIKE"){
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i]." ".$fun[$i]." '%".$value[$i]."%'";
					}
				else{
					$pandsS=$pandsS." and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
					}
			break;
			}
		}
	}
//if(!(session_is_registered("pandsS"))) {
if(!($_SESSION["pandsS"])) {	
	//session_register("pandsS");
	$_SESSION["pandsS"] = $pandsS;
	
	}
echo "<meta http-equiv=\"Refresh\" content='0;url=pands_s1.php?Action=$Action&Tid=$Tid'>";
ChangeWtitle("$SubCompany 产品查询");
?>