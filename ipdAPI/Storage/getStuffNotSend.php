<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$myCompanyId = $_POST["companyId"];
	$stuffs = array();
	//$myCompanyId = "2745";

	$SearchRows.=" and S.CompanyId='$myCompanyId'";
	
	$mySql="SELECT S.Mid,S.StuffId,A.StuffCname,S.StockId,S.PorderId,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,S.Id,A.TypeId ,U.Name AS UnitName
			FROM $DataIn.cg1_stocksheet S
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
			LEFT JOIN $DataPublic.stuffunit U ON U.Id= A.Unit
			LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId= A.StuffId AND OP.Property=2		
			WHERE  1 
			AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) 
			$SearchRows
			GROUP BY S.StuffId";
	//AND S.rkSign>0 
	//echo $mySql;

	$stuffNotSendResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($stuffNotSendResult))
	{
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$picture = $myRow["Picture"];
		
		//已购总数
		$cgTemp=mysql_query("SELECT SUM(OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId= A.StuffId AND OP.Property=2
			WHERE 1 
			$SearchRows 
			AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) 
			and S.StuffId='$StuffId'",$link_id);

		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		$odQty=mysql_result($cgTemp,0,"odQty");
		$odQty=$odQty==""?0:$odQty;
		
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
		WHERE R.StuffId='$StuffId' $SearchRows",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		
		//待送货数量
		$shSql=mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
									   LEFT JOIN $DataIn.gys_shmain S ON S.Id=G.Mid
									   WHERE 1 AND G.SendSign=0 AND G.Locks=1 AND G.StuffId=$StuffId $SearchRows ",$link_id);
		
		$shQty=mysql_result($shSql,0,"Qty");
		$shQty=$shQty==""?0:$shQty;
		$noQty=$cgQty-$rkQty-$shQty;
		
		//退货的总数量 add by zx 2011-04-27
		$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
									   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		$thQty=mysql_result($thSql,0,"thQty");
		$thQty=$thQty==""?0:$thQty;
	    //补货的数量 add by zx 2011-04-27
		$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
									   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		$bcQty=mysql_result($bcSql,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;
		
		$bcshSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
							LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
							WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Locks=1 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id);  
		$bcshQty=mysql_result($bcshSql,0,"Qty");
		$bcshQty=$bcshQty==""?0:$bcshQty;
		
		//未补数量
		$webQty=$thQty-$bcQty-$bcshQty; 
		
		if($noQty>0 || $webQty>0)
		{
			$stuffs[] = array($StuffCname, $odQty, $cgQty, $rkQty, $shQty, $noQty, $webQty, $StuffId, $picture);
		}
	}
	
	echo json_encode($stuffs);
	
?>