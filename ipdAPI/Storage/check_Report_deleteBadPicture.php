<?php
	
	include_once "../../basic/parameter.inc";
	
	$ImgName = $_POST["ImgName"];
	$Bid = $_POST["Bid"];
	
	$tag = "N";
	$FilePath="../../download/qcbadpicture/".$ImgName;
	
	$delSql="update $DataIn.qc_badrecordsheet  set Picture=0  WHERE Id='$Bid'";
	$result1 = mysql_query($delSql);
	if($result1)
	{
		$tag = "Y";
    	if(file_exists($FilePath))
    	{
	    	unlink($FilePath);
	    }
	}
	
	echo $tag;
	
?>