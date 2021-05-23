<?php 
	include "../../basic/parameter.inc";
	$date = date("Y-m-d");
	
	$cSign = $_POST["cSign"];
	
	$noticeArray = array();

	$tmpSql = sprintf("(SELECT Id, Title, Content, DATE,TYPE 
						FROM $DataPublic.msg1_bulletin 
						WHERE (cSign =  '$cSign' OR cSign =  '0') AND TYPE = '0' Order by DATE Desc limit 20)
						union
						(SELECT Id, Title, Content, DATE,TYPE 
						FROM $DataPublic.msg1_bulletin 
						WHERE (cSign =  '$cSign' OR cSign =  '0') AND TYPE =  '1' And DATE='$date' Order By Id)
						Order by Date desc, Id desc");
						
	$todayResult = mysql_query($tmpSql);
	while($tmpMsg = mysql_fetch_assoc($todayResult))
	{
		$picStr = "";
		$MsgPictureSql = sprintf("Select Picture From $DataPublic.msg1_picture Where 1 and Mid = '%s'",$tmpMsg["Id"]);
		$msgPictureResutl = mysql_query($MsgPictureSql);
		$picArray = array();
		while($msgPictureRow = mysql_fetch_assoc($msgPictureResutl))
		{
			$picArray[] = $msgPictureRow["Picture"];
		}
		$picStr = implode("^", $picArray);
		if($picStr != "")
		{
			$tmpContent = $tmpMsg["Content"]."^".$picStr;
		}
		else
		{
			$tmpContent = $tmpMsg["Content"];
		}
		$noticeArray[] = array("Title"=>$tmpMsg["Title"],"Content"=>$tmpContent,"Date"=>$tmpMsg["DATE"]);
	}
	
	echo json_encode($noticeArray);
	
?>