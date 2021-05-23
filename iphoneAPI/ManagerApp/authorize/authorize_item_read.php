<?php 
//读取证书信息
$today=date("Y-m-d");
$mySql="SELECT D.Id,D.Caption,D.TimeLimit,D.Attached,C.CompanyId,C.Forshort 
FROM $DataIn.yw7_clientproxy D
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId 
WHERE D.Estate=1 AND C.CompanyId NOT IN(1003,1020,1057,1088) ORDER BY C.CompanyId  ";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
		      $jsondata=array();$dataArray=array();$n=0;
			do{
			   if ($n==3){
				   $jsondata[]=$dataArray;
				   $n=0;$dataArray=array();
			   }
				$Id=$myRow["Id"];
				$CompanyId=$myRow["CompanyId"];
			     $Forshort=$myRow["Forshort"];
			     
				$vowels = array("\"","“","”","-");
				$Caption=str_replace($vowels, " ",$myRow["Caption"]);
				$vowels = array("(", ")","&");
				$Caption=str_replace($vowels, "",$Caption);
				$Caption=str_replace(" ", "_",$Caption);
				
				$Attached=$myRow["Attached"];
		        $TimeLimit=$myRow["TimeLimit"];
		       
				if($Attached!=""){
					$Attached="download/clientproxy/".$Attached;
					}
				else{
					$Attached="";
					}
				$days=floor((strtotime($TimeLimit)-strtotime($today))/(24*60*60));
				$dateColor=($days<180 && $days>0)?"#FDA615":"";
				$dateColor=$days<0?"#FF0C0C":$dateColor;
				
				$overSign=$days<0?1:0;
			    $dataArray[] = array( "Id"=>"$Id","Title"=>"$Forshort","Caption"=>"$Caption",
												    "Date"=>"$TimeLimit","DateColor"=>"$dateColor","FilePath"=>"$Attached","OverSign"=>"$overSign");
				$n++;								   
				}while ($myRow = mysql_fetch_array($myResult));
				$jsondata[]=$dataArray;
				$jsonArray=array("data"=>$jsondata);
}
  ?>