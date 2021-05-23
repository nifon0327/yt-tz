<?php
	
	include_once("../../basic/parameter.inc");
	
	$ProductId = $_GET["productId"];
	//$ProductId = "85698";
	$imageType = $_GET["type"];
	//$imageType = "3";
	
	switch($imageType)
	{
		case "0":
		{
			$ImageFile="http://10.0.10.1/download/teststandard/" . "T".$ProductId.".jpg";
			echo "<image src= '$ImageFile' />";
		}
		break;
		case "1":
		{
			$ImagePath="http://10.0.10.1/download/QCstandard/";
			$ImageFile="";
			$OperaStr="";
			$n=0;
			
			$imgResult=mysql_query("SELECT Q.Picture,Q.Date,Q.Operator FROM $DataIn.qcstandardimg D
									LEFT JOIN $DataIn.qcstandarddata Q ON Q.Id=D.QcId
									WHERE D.ProductId='$ProductId' AND Q.Estate=1 AND Q.IsType=0",$link_id); 
						
			if($imgRow=mysql_fetch_array($imgResult))
			{
				do
				{
					$Picture=$imgRow["Picture"];
					$ImageFile=$ImagePath . $Picture;
					echo "<img id='qcImg$n' src='$ImageFile' /><br />";
				}
				while($imgRow=mysql_fetch_array($imgResult));
			}
			else
			{
				$typeSql = mysql_query("SELECT TypeId FROM $DataIn.productdata where ProductId='$ProductId'",$link_id);
				
				$TypeId=mysql_result($typeSql,0,"TypeId");
				$typeResult=mysql_query("select * from $DataIn.qcstandarddata where TypeId='$TypeId' AND Estate=1 AND IsType=1",$link_id);
				while ($typeRow=mysql_fetch_array($typeResult))
				{   
					$Picture=$typeRow["Picture"];
					$ImageFile=$ImagePath . $Picture;
					$Operator=$typeRow["Operator"];
					$Date=$typeRow["Date"];
				
					$n+=1;  
				 	$OperaStr=$OperaStr . "||" . "$Operator#$Date";
				  	echo "<img id='qcImg$n' src='$ImageFile' /><br />";
			    }
			}
		}
		break;
		case "2":
		{
			$mastakeResult = mysql_query("select E.Id,E.Title,E.Picture,E.Owner,E.Operator,E.Date   
										  FROM $DataIn.casetoproduct C 
										  LEFT JOIN $DataIn.errorcasedata E ON E.Id=C.cId 
										  WHERE C.ProductId='$ProductId' AND E.Estate=1 ",$link_id); 
			
			if($mastakeRow=mysql_fetch_array($mastakeResult)) 
			{
				$n=0;$OperaStr="";
				do 
				{
					$Picture=$mastakeRow["Picture"];
					$Owner=$mastakeRow["Owner"];
					$Operator=$mastakeRow["Operator"];
					$Date=$mastakeRow["Date"];
					$cId=$mastakeRow["Id"];
					$ImageFile ="http://10.0.10.1/download/errorcase/".$Picture;
					$n+=1;
					$OperaStr=$OperaStr . "||" . "$cId#$Owner#$Operator#$Date";
					echo "<img id='caseImg$n' src='$ImageFile' /><br />";
				} 
				while ($mastakeRow = mysql_fetch_array($mastakeResult));
			}
		}
		break;
	}
	
?>