<?php 
	include "../../basic/parameter.inc";
	include("getStaffNumber.php");
	
	$Num = $_POST["idNum"];
	if(strlen($Num) != 5)
	{
		$Num = getStaffNumber($Num, $DataPublic);
	}
	
	$getComeInSql = sprintf("Select ComeIn from $DataPublic.staffmain where Number = '%s'",$Num);
	
	if($ComeInfResult = mysql_fetch_assoc(mysql_query($getComeInSql)))
	{
		$startYear = substr($ComeInfResult["ComeIn"],0,4);
	}
	
	$totleDx = 0;
	$totleGljt = 0;
	$totleGwjt = 0;
	$totleJj = 0;
	$totleShbz = 0;
	$totleZsbz = 0;
	$totleJbf = 0;
	$totleYxbz = 0;
	$totleJtbz = 0;
	$totleTaxbz = 0;
	
	$totleJz = 0;
	$totleSb = 0;
	$totleGjj = 0;
	$totleKqkk = 0;
	$totleRandP = 0;
	$totleOtherkk = 0;
	$totleAmount = 0;

	$endYear = date("Y");
	$wageArray = array();
	
	$wagesSql = sprintf("Select S.Month,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jbf,S.Yxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount, S.Gjj,S.Jtbz,S.taxbz,S.Estate,M.ComeIn From $DataIn.cwxzsheet S Left Join $DataPublic.staffmain M ON M.Number=S.Number Where 1 And S.Number='$Num' Order By S.Month Desc");
		
		$wageResult = mysql_query($wagesSql);
		while($wagesRow = mysql_fetch_assoc($wageResult))
		{
		
			$monArray = array($wagesRow["Month"],$wagesRow["Amount"],$wagesRow["Dx"],$wagesRow["Gljt"],$wagesRow["Gwjt"],$wagesRow["Jj"],$wagesRow["Shbz"],$wagesRow["Zsbz"],$wagesRow["Jbf"],$wagesRow["Yxbz"],$wagesRow["Jtbz"],$wagesRow["taxbz"] ,$wagesRow["Jz"],$wagesRow["Sb"],$wagesRow["Gjj"],$wagesRow["Kqkk"],$wagesRow["RandP"],$wagesRow["Otherkk"]);
			$yearArray[] = $monArray;
			
			$totleDx = $totleDx + $wagesRow["Dx"];
			$totleGljt = $totleGljt + $wagesRow["Gljt"];
			$totleGwjt = $totleGwjt + $wagesRow["Gwjt"];
			$totleJj = $totleJj + $wagesRow["Jj"];
			$totleShbz = $totleShbz + $wagesRow["Shbz"];
			$totleZsbz = $totleZsbz + $wagesRow["Zsbz"];
			$totleJbf = $totleJbf + $wagesRow["Jbf"];
			$totleYxbz = $totleYxbz + $wagesRow["Yxbz"];
			$totleJtbz = $totleJtbz + $wagesRow["Jtbz"];
			$totleTaxbz = $totleTaxbz + $wagesRow["taxbz"];
			
			$totleJz = $totleJz + $wagesRow["Jz"];
			$totleSb = $totleSb + $wagesRow["Sb"];
			$totleGjj = $totleGjj + $wagesRow["Gjj"];
			$totleKqkk = $totleKqkk + $wagesRow["Kqkk"];
			$totleRandP = $totleRandP + $wagesRow["RandP"];
			$totleOtherkk = $totleOtherkk + $wagesRow["Otherkk"];
			$totleAmount = $totleAmount + $wagesRow["Amount"];
		}
		
		$totleDx = sprintf("%.2f",$totleDx);
		$totleGljt = sprintf("%.2f",$totleGljt);
		$totleGwjt = sprintf("%.2f",$totleGljt);
		$totleJj = sprintf("%.2f",$totleJj);
		$totleShbz = sprintf("%.2f",$totleShbz);
		$totleZsbz = sprintf("%.2f",$totleZsbz);
		$totleJbf = sprintf("%.2f",$totleJbf);
		$totleYxbz = sprintf("%.2f",$totleYxbz);
		$totleJtbz = sprintf("%.2f",$totleJtbz);
		$totleTaxbz = sprintf("%.2f",$totleTaxbz);
		
		$totleJz = sprintf("%.2f",$totleJz);
		$totleSb = sprintf("%.2f",$totleSb);
		$totleGjj = sprintf("%.2f",$totleGjj);
		$totleKqkk = sprintf("%.2f",$totleKqkk);
		$totleRandP = sprintf("%.2f",$totleRandP);
		$totleOtherkk = sprintf("%.2f",$totleOtherkk);
		$totleAmount = sprintf("%.2f",$totleAmount);
		
		if(!$yearArray)
		{
			$yearArray = array();
		}
		
		$totleArray[] = array("总发","$totleAmount","$totleDx","$totleGljt","$totleGwjt","$totleJj","$totleShbz","$totleZsbz","$totleJbf","$totleYxbz","$totleJtbz", "$totleTaxbz","$totleJz","$totleSb", "$totleGjj","$totleKqkk","$totleRandP","$totleOtherkk");
		
		$wageArray = array_merge($totleArray,$yearArray);
		
	//print_r($wageArray);
		
	echo json_encode($wageArray);
	
?>