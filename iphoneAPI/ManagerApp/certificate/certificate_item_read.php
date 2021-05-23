<?php 
//读取证书信息
$today=date("Y-m-d");
$mySql="SELECT D.Id,D.Caption,D.Attached,D.EndDate 
		FROM $DataIn.zw2_hzdoc D 
		WHERE  D.Id IN (366,1178,1179,1180,1197,1251,1263) ORDER BY  Field(Id,1178,1180,1179,1263,366,1251,1197)";
		$myResult = mysql_query($mySql,$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			do{
				$Id=$myRow["Id"];		
				$Caption=$myRow["Caption"];
				$Attached=$myRow["Attached"];
		
				if($Attached!=""){
					$Attached="download/hzdoc/".$Attached;
					}
				else{
					$Attached="";
				 }
				 
				 $ImagePath="certificate/image/EN_".$Id."_s.png";
				 $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
				 //用英文显示
				 switch($Id){
					 case 1251:  $Caption="D&B";  break;
					 case 1197:  $Caption="Work safety management";  break;
					 default:$Caption=str_replace("证书", "", $Caption);break;
				 }
				 
				$EndDate=$myRow["EndDate"];
				$DateColor=$EndDate<$today?"#FF0000":"";
			    $jsondata[] = array("Id"=>"$Id","Caption"=>"$Caption","Expdate"=>"$EndDate","DateColor"=>"$DateColor","FilePath"=>"$Attached","Icon"=>"1","Date"=>"$img_mtime");
				}while ($myRow = mysql_fetch_array($myResult));
				$jsonArray=array("data"=>$jsondata);
	}

  ?>