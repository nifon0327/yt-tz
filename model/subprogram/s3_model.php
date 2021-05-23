<?php 	//模板，不需处理
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";

//if(!(session_is_registered("sSearch"))){
if(!($_SESSION["sSearch"])){	
	//session_register("sSearch");
	$_SESSION["sSearch"] = $sSearch;
	}	
$sSearch=SearchCheck($table,$Field,$fun,$value,$types,$DateArray);
$AddParameter="";
if($ReturnParameter!=""){
	$ReturnFields=explode("|",$ReturnParameter);   //这几个要带过去，也就是要带到点搜索带回cg_cgdmain_s1.php ,可见cg_cgdmain_s2.php,加入,可见cg_cgdmain_s1.php 
	$CountReturn=count($ReturnFields);
	for ($i=0;$i<$CountReturn;$i++){
		$AddParameter.="&".$ReturnFields[$i]."=".$ReturnFields[$i+1];  // &CompanyId=$CompanyId&BuyerId=BuyerId;
		$i=$i+1;
		}
}


echo "<meta http-equiv=\"Refresh\" content='0;url=../../$FromDir/".$tSearchPage."_s1.php?From=slist&tSearchPage=$tSearchPage&fSearchPage=$fSearchPage&SearchNum=$SearchNum&Action=$Action&uType=$uType&Bid=$Bid&Jid=$Jid&Kid=$Kid&Month=$Month$AddParameter'>";
function SearchCheck($table,$Field,$fun,$value,$types,$DateArray){
	$tempStr="";
	$Lengths=count($value);
	for($i=0;$i<$Lengths;$i++){
		//判断字段类型
		if($value[$i]!=""){
			switch($types[$i]){
				case"isNum":		//数字
					$tempStr.=" and ".$table[$i].".".$Field[$i].$fun[$i].$value[$i];
				break;
				case"isStr":		//字符串
					if($fun[$i]=="LIKE"){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." ".$fun[$i]." '%".$value[$i]."%'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
						}
				break;
				case"isDate":		//操作日期
					if($DateArray["isDate"]!=""){
						$tempStr.=" and ".$table[$i].".Date between '".$value[$i]."' and '".$DateArray["isDate"]."'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
						}
				break;
				case"isStartDate":	//起始日期
					if($DateArray["isStartDate"]!=""){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." between '".$value[$i]."' and '".$DateArray["isStartDate"]."'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
						}
				break;
				case"isEndDate":	//结束日期
					if($DateArray["isEndDate"]!=""){
						$tempStr.=" and ".$table[$i].".".$Field[$i]." between '".$value[$i]."' and '".$DateArray["isEndDate"]."'";
						}
					else{
						$tempStr.=" and ".$table[$i].".".$Field[$i].$fun[$i]."'".$value[$i]."'";
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
				}//end switch($types[$i])
			}//end if($value[$i]!="")
		}//end for($i=0;$i<$Lengths;$i++)
		return $tempStr;
	}
?>