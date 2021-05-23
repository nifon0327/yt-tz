<?php
$tempk=1;
$tempj =$j-1;
$CheckComResult =mysql_query("SELECT  CG.StuffId,A.Gfile,A.GfileDate,A.Gstate,A.Gremark,A.StuffCname,SUM(CG.OrderQty) AS ComOrderQty,SUM(CG.FactualQty) AS ComFactualQty,SUM(CG.AddQty) AS ComAddQty,U.Name AS UnitName
FROM $DataIn.cg1_stuffcombox   CG 
LEFT JOIN $DataIn.cg1_stocksheet  S  ON S.StockId = CG.mStockId 
LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId 
INNER JOIN $DataIn.stuffdata A ON A.StuffId=CG.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
WHERE  CG.mStuffId =$StuffId  and S.Mid>0 AND ( K.StockId IS NULL  OR S.rkSign>0)  AND S.CompanyId='$myCompanyId'  $WeekSearch GROUP BY CG.StuffId",$link_id);
/*
LEFT JOIN (
       SELECT D.StockId,D.DeliveryDate 
       FROM $DataIn.cg1_deliverydate D 
       LEFT JOIN  $DataIn.cg1_stocksheet S ON D.StockId=S.StockId 
       WHERE S.rkSign>0 AND S.StuffId =$StuffId GROUP BY StockId 
       ) D ON D.StockId=S.StockId  AND D.DeliveryDate<>S.DeliveryDate 
*/
$TotalRecords=mysql_num_rows($CheckComResult);

while($CheckComRow = mysql_fetch_array($CheckComResult)){
          $m=1;$LockRemark=""; $theDefaultColor=""; $DisabledSTR="";
         $StuffId = $CheckComRow["StuffId"];
        $StuffCname = $CheckComRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$Gfile=$myRow["Gfile"];
		$Gremark=$myRow["Gremark"];
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		//加密
		$Gstate=$myRow["Gstate"];

		$ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
		include "../model/subprogram/stuffimg_model.php";			
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
         //配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";
        //配件品检报告qualityReport
        include "../model/subprogram/stuff_get_qualityreport.php";
       $ComOrderQty    = $CheckComRow["ComOrderQty"];
       $ComAddQty      = $CheckComRow["ComAddQty"];
       $ComFactualQty = $CheckComRow["ComFactualQty"];
		//已收货总数
		$ComrkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stuffcombox CG ON CG.StockId=R.StockId
        LEFT JOIN $DataIn.cg1_stocksheet  S  ON S.StockId = CG.mStockId 
        LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId  
		WHERE R.StuffId='$StuffId' and S.Mid>0 AND ( K.StockId IS NULL  OR S.rkSign>0)   $SearchRowsA $WeekSearch",$link_id);
		$ComrkQty=mysql_result($ComrkTemp,0,"Qty");
		$ComrkQty=$ComrkQty==""?0:$ComrkQty;

		//待送货数量
		$ComshSql=mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
									   LEFT JOIN $DataIn.gys_shmain M ON M.Id=G.Mid
									   LEFT JOIN $DataIn.cg1_stuffcombox CG ON CG.StockId=G.StockId AND CG.StuffId=G.StuffId 
        							   LEFT JOIN $DataIn.cg1_stocksheet  S  ON S.StockId = CG.mStockId 
									   WHERE 1 AND G.SendSign=0 AND G.Estate>0 AND G.StuffId='$StuffId' $SearchRowsA $WeekSearch",$link_id);  		
		  $ComshQty=mysql_result($ComshSql,0,"Qty");
		  $ComshQty=$ComshQty==""?0:$ComshQty;
		
		  $ComnoQty=$ComAddQty+$ComFactualQty-$ComrkQty-$ComshQty;
		  //echo "$ComnoQty=$ComAddQty+$ComFactualQty-$ComrkQty-$ComshQty";
		  
		  $ComnotSendResult=mysql_query("SELECT SUM(CG.FactualQty) AS Qty 
                 FROM $DataIn.cg1_stuffcombox   CG 
                  LEFT JOIN $DataIn.cg1_stocksheet  S  ON S.StockId = CG.mStockId 
                 WHERE 1 $SearchRowsA and S.Mid>0 and S.rkSign>0 and CG.StuffId='$StuffId' AND YEARWEEK(S.DeliveryDate,1)>'$nextWeek'",$link_id);
		  $ComnotSendQty=mysql_result($ComnotSendResult,0,"Qty");
		  $ComnotSendQty=$ComnotSendQty>0?$ComnotSendQty:0;
		
		  $ComnotDateResult=mysql_query("SELECT SUM(CG.FactualQty) AS Qty 
            FROM $DataIn.cg1_stuffcombox   CG 
            LEFT JOIN $DataIn.cg1_stocksheet  S  ON S.StockId = CG.mStockId 
           WHERE 1 $SearchRowsA and S.Mid>0 and S.rkSign>0 and CG.StuffId='$StuffId' and S.DeliveryDate='0000-00-00'",$link_id);
           
		  $ComnotDateQty=mysql_result($ComnotDateResult,0,"Qty");
		  $ComnotSendQty+=$ComnotDateQty>0?$ComnotDateQty:0;

		  $ComokSendQty=$IsWeeks==""?$ComnoQty-$ComnotSendQty:$ComnoQty;
		  $ComokSendQty=$IsWeeks>$nextWeek?0:$ComokSendQty;
		  $ComokSendQty=$ComokSendQty<=0?0:$ComokSendQty;


	      //退货的总数量
		  $ComthSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
									   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		  $ComthQty=mysql_result($ComthSql,0,"thQty");
		  $ComthQty=$ComthQty==""?0:$ComthQty;
	      //补货的数量 
		  $CombcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
									   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
									   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
		  $CombcQty=mysql_result($CombcSql,0,"bcQty");
		  $CombcQty=$CombcQty==""?0:$CombcQty;
		
		  $CombcshSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
							LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
							WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Estate>0 AND S.StuffId='$StuffId' AND (S.StockId='-1' or S.SendSign='1')",$link_id);  
		
		   $CombcshQty=mysql_result($CombcshSql,0,"Qty");
		   $CombcshQty=$CombcshQty==""?0:$CombcshQty;			
		   $ComwebQty=$ComthQty-$CombcQty-$CombcshQty; //未补数量

           include"../model/subprogram/stuff_Property.php";//配件属性   
			$ComSendQty="<input name='sendQty[]' type='text' id='sendQty$i' style='width:50px' value='' onclick='checkSendWay()' class='I0000C' $DisabledSTR>";
			$ComBS_Qty="<input name='BSQty[]' type='text' id='BSQty$i' style='width:50px' value='' onclick='checkSendWay()' class='I0000C' $DisabledSTR>"; 
			$ComBP_Qty="<input name='BPQty[]' type='text' id='BPQty$i' style='width:50px'  value='' onclick='checkSendWay()' class='I0000C' $DisabledSTR>";  
			$subValueArray=array(
				array(0=>$StuffId,		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Gfile, 		1=>"align='center'"),
				array(0=>$GfileDate, 	1=>"align='center'"),
                 array(0=>$QCImage, 	1=>"align='center'"),
				array(0=>$qualityReport, 1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$ComFactualQty, 		1=>"align='right'"),
				array(0=>$ComrkQty, 		1=>"align='right'"),
				array(0=>"<div class='redB'>".$ComnoQty."</div>", 1=>"align='right'"),
				array(0=>"<div class='greenB'>".$ComokSendQty."</div>", 1=>"align='right'"),
				array(0=>$ComSendQty, 	1=>"align='center'"),
				array(0=>"<div class='redB'>".$ComwebQty."</div>", 1=>"align='right'"),
				array(0=>$ComBS_Qty, 	1=>"align='center'"),
				array(0=>$ComBP_Qty, 	1=>"align='center'")
				);
			$TotalRecords--;
			$checkidValue=$i."-".$StuffId."-1";
			include "../model/subprogram/read_model_8.php";
}

?>