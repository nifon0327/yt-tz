<?php 	//模板，不需处理
//电信-EWEN
//代码-EWEN
include "../../basic/chksession.php" ;
include_once "../../basic/parameter.inc";
include "../modelfunction.php";

if(!$_SESSION["SearchRows"]){
	$_SESSION["SearchRows"];
	}
$VoiceTypeStr=$TypeId==""?"":"&TypeId=".$TypeId;
$ReEstateStr=$ReEstate==""?"":"&Estate=".$ReEstate;//状态传递
$cwSignSTR=$cwSign==""?"":"&cwSign=".$cwSign;//系统来源传递
$ReLinkmanStr=$ReLinkman==""?"":"&ComeFrom=".$ComeFrom."&Type=".$Type;
$uTypeStr=$uType==""?"":"&uType=".$uType;
$_SESSION["SearchRows"]=SearchCheck($table,$Field,$fun,$value,$types,$DateArray);
$scTypeSTR=$scType==""?"":"&scType=".$scType;
$OrderActionStr=$OrderAction==""?"":"&OrderAction=".$OrderAction;
//查询来自于共享目录还是独立目录
echo "<meta http-equiv=\"Refresh\" content='0;url=../../$FromDir/".$fromWebPage.".php?From=slist$ReEstateStr$ReLinkmanStr$cwSignSTR$scTypeSTR$VoiceTypeStr$uTypeStr$OrderActionStr'>";

function SearchCheck($table,$Field,$fun,$value,$types,$DateArray){
	$tempStr="";
	$Lengths=count($value);
	$y=-1;
	for($i=0;$i<$Lengths;$i++){
		//判断字段类型
		$y=$types[$i]=="isDate"?$y+1:$y;
		if($value[$i]!=""){
			switch($types[$i]){
				case"isNum":		//数字					
					$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i].$value[$i];
				break;
				case"isStr":		//字符串
					if($fun[$i]=="LIKE"){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]." '%".$value[$i]."%'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]."'".$value[$i]."'";
						}
				break;
				case"isDate":		//日期
					if($DateArray[$y]!=""){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." between '".$value[$i]."' and '".$DateArray[$y]."'";
						}
					else{
						if($fun[$i]=="LIKE"){
							$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]." '".$value[$i]."%'";
							}
						else{
							$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]." '".$value[$i]."'";
							}
						}
				break;
				case"isSign":		//空与非空
					if($value[$i]==1){
						$tempStr.=" and ".$table[$i].".".$Field[$i]."!=''";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i]."=''";
						}
					break;
			case"isMonth":				
				if($MonthArray[$y]!=""){//之间
						$tempStr.=" and ".$table[$i].".".$Field[$i]." between '".$value[$i]."' and '".$MonthArray[$y]."'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]."'".$value[$i]."'";
					}
			break;
			case "isDateTime":
					if($DateTimeArray[$y]!=""){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." between '".$value[$i]."' and '".$DateTimeArray[$y]."'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i]." LIKE '".$value[$i]."%'";
						}
			break;
				}//end switch($types[$i])
			}//end if($value[$i]!="")
		}//end for($i=0;$i<$Lengths;$i++)
		return $tempStr;
	}
?>