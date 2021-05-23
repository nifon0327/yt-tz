<?php
$CheckStuffResult =  mysql_query("SELECT  B.CompanyId,B.BuyerId,D.TypeId,D.Unit,D.Pjobid,D.PicNumber,D.JobId,D.GicNumber,D.GcheckNumber,D.Cjobid,
D.SendFloor,D.CheckSign,D.ForcePicSpe,D.jhDays,D.DevelopState
FROM $DataIn.stuffdata D 
LEFT JOIN $DataIn.bps B ON B.StuffId =D.StuffId
WHERE D.StuffId=$StuffId",$link_id);
if($CheckStuffRow = mysql_fetch_array($CheckStuffResult)){

	  $mainCompanyId = $CheckStuffRow["CompanyId"];
	  $mainBuyerId = $CheckStuffRow["BuyerId"];
	  $mainTypeId = $CheckStuffRow["TypeId"];
	  $mainUnit = $CheckStuffRow["Unit"];
	  $mainPjobid = $CheckStuffRow["Pjobid"];
	  $mainPicNumber = $CheckStuffRow["PicNumber"];
	  $mainJobId = $CheckStuffRow["JobId"];
	  $mainGicNumber = $CheckStuffRow["GicNumber"];
	  $mainGcheckNumber = $CheckStuffRow["GcheckNumber"];
	  $mainCjobid = $CheckStuffRow["Cjobid"];
	  $mainSendFloor = $CheckStuffRow["SendFloor"];
	  $mainCheckSign = $CheckStuffRow["CheckSign"];
	  $mainForcePicSpe = $CheckStuffRow["ForcePicSpe"];
	  $mainjhDays = $CheckStuffRow["jhDays"];
	  $mainDevelopState = $CheckStuffRow["DevelopState"];
	  
	  $UpdateSql = "UPDATE  $DataIn.stuffdata  D  
	  LEFT JOIN $DataIn.bps B ON B.StuffId =D.StuffId
	  LEFT JOIN $DataIn.stuffcombox_bom  S ON S.StuffId =D.StuffId
	  SET B.CompanyId ='$mainCompanyId',B.BuyerId='$mainBuyerId',D.TypeId ='$mainTypeId',
	      D.Unit='$mainUnit',D.Pjobid='$mainPjobid',D.PicNumber='$mainPicNumber',
	      D.JobId='$mainJobId',D.GicNumber='$mainGicNumber',D.GcheckNumber='$mainGcheckNumber',
	      D.Cjobid='$mainCjobid',D.SendFloor='$mainSendFloor',D.CheckSign='$mainCheckSign',
	      D.ForcePicSpe='$mainForcePicSpe',D.jhDays='$mainjhDays',D.DevelopState='$mainDevelopState'
	  WHERE S.mStuffId=$StuffId";
	  $UpdateResult = @mysql_query($UpdateSql);
	     
      $DelPropertySql = "DELETE P FROM $DataIn.stuffproperty P
                         LEFT JOIN $DataIn.stuffcombox_bom B ON B.StuffId = P.StuffId WHERE B.mStuffId=$StuffId AND Property!=10 ";
      $DelPropertyResult = @mysql_query($DelPropertySql);
      $InPropertySql = "INSERT INTO $DataIn.stuffproperty SELECT NULL, B.StuffId, P.Property, P.Estate, P.Locks, P.PLocks, P.creator, P.created, 
      P.modifier, P.modified, P.Date, P.Operator FROM $DataIn.stuffproperty P 
      LEFT JOIN  $DataIn.stuffcombox_bom B ON B.mStuffId = P.StuffId
      WHERE P.StuffId=$StuffId AND P.Property!=9";
      $InPropertyResult = @mysql_query($InPropertySql);
}

?>