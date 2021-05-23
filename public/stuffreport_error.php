<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=14;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 配件分析");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|40|序号|40|配件Id|50|配件名称|210|参考买价|60|单位|45|初始库存|60|在库|60|可用库存|60|订单总数|60|采购总数|60|入库总数|60|领料总数|60|备品入库|60|报废总数|60|退换总数|60|补仓总数|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,S.StuffCname,S.Price,U.Name AS UnitName,K.dStockQty,K.tStockQty,K.oStockQty,U.Decimals 
FROM $DataIn.stuffdata S
LEFT JOIN $DataPublic.stuffunit U ON U.Id=S.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id=T.mainType 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 and S.Estate=1 AND TM.blSign=1  $SearchRows 
ORDER BY S.Id";
//echo $mySql;
$SumoStockQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$dStockQty=$myRow["dStockQty"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$decimals=$myRow["Decimals"];
		
		$SumoStockQty=$SumoStockQty+$oStockQty;
		
		//检查是否为子配件
         $subResult=mysql_query("SELECT mStuffId,Relation FROM $DataIn.stuffcombox_bom WHERE StuffId='$StuffId' LIMIT 1 ",$link_id);  
         if($subRow = mysql_fetch_array($subResult))	{
         $CheckGRow=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.OrderQty,0)) AS orderQty,SUM(IFNULL(A.cgQty,0)) AS cgQty,
					SUM(IFNULL(A.rkQty,0)) AS rkQty,SUM(IFNULL(A.llQty,0)) AS llQty,SUM(IFNULL(A.bpQty,0)) AS bpQty,
					SUM(IFNULL(A.bfQty,0)) AS bfQty,SUM(IFNULL(A.thQty,0)) AS thQty,SUM(IFNULL(A.bcQty,0)) AS bcQty 
			FROM (
	                  SELECT SUM(OrderQty) AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
	                  FROM $DataIn.cg1_stuffcombox WHERE StuffId='$StuffId' 
	          UNION ALL 
	                 SELECT 0 AS orderQty,SUM(FactualQty+AddQty) AS cgQty,0 AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
	                  FROM $DataIn.cg1_stuffcombox WHERE StuffId='$StuffId' 
	         UNION ALL
					 SELECT 0 AS orderQty,0 AS cgQty,SUM(Qty) AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					 FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId'AND Type=1 
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,SUM(Qty) AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type IN (1,5)
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,SUM(Qty) AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND Type=2  
		    UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,SUM(Qty) AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type=2 
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,SUM(Qty) AS thQty,0 AS bcQty
					FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type=3 
			UNION ALL 
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,0 AS thQty,SUM(Qty) AS bcQty 
					FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND Type=3
		   )A ",$link_id));
         }
         else{
		
			$CheckGRow=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.OrderQty,0)) AS orderQty,SUM(IFNULL(A.cgQty,0)) AS cgQty,
					SUM(IFNULL(A.rkQty,0)) AS rkQty,SUM(IFNULL(A.llQty,0)) AS llQty,SUM(IFNULL(A.bpQty,0)) AS bpQty,
					SUM(IFNULL(A.bfQty,0)) AS bfQty,SUM(IFNULL(A.thQty,0)) AS thQty,SUM(IFNULL(A.bcQty,0)) AS bcQty 
			FROM (
	                  SELECT SUM(OrderQty) AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
	                  FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' 
	          UNION ALL 
	                 SELECT 0 AS orderQty,SUM(FactualQty+AddQty) AS cgQty,0 AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
	                  FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' 
	          UNION ALL
					 SELECT 0 AS orderQty,0 AS cgQty,SUM(Qty) AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					 FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId'AND Type=1 
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,SUM(Qty) AS llQty,0 AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type IN (1,5,6) 
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,SUM(Qty) AS bpQty,0 AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND Type=2  
		    UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,SUM(IF(Type=6,-Qty,Qty)) AS bfQty,0 AS thQty,0 AS bcQty 
					FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type IN (2,6)
			UNION ALL
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,SUM(Qty) AS thQty,0 AS bcQty
					 FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId' AND Type=3 
			UNION ALL 
					SELECT 0 AS orderQty,0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,0 AS thQty,SUM(Qty) AS bcQty 
					FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND Type=3 
		   )A ",$link_id));
	   }
	   
	  
	   $orderQty=$CheckGRow["orderQty"];
       $cgQty=$CheckGRow["cgQty"];
       $rkQty=$CheckGRow["rkQty"];
       $llQty=$CheckGRow["llQty"];
       $bpQty=$CheckGRow["bpQty"];
       $bfQty=$CheckGRow["bfQty"];
       $thQty=$CheckGRow["thQty"];
       $bcQty=$CheckGRow["bcQty"];
       
       //检查是否为母配件
       $mStuffResult=mysql_query("SELECT StuffId,Relation FROM $DataIn.stuffcombox_bom WHERE mStuffId='$StuffId' LIMIT 1 ",$link_id);  
       if($mStuffRow = mysql_fetch_array($mStuffResult)){
          //备品转入数据
          $mStuffSign=1;
	      $UnionSql="SELECT SUM(IFNULL(A.bpQty,0)) AS bpQty,SUM(IFNULL(A.bfQty,0)) AS bfQty
	       FROM (
			   SELECT ROUND(Min(A.Qty/A.Relation)) AS bpQty,0 as bfQty FROM(
					SELECT S.StuffId,S.Relation,SUM(IFNULL(B.Qty,0)) AS Qty 
					FROM $DataIn.stuffcombox_bom S 
					LEFT JOIN $DataIn.ck1_rksheet B ON S.StuffId=B.StuffId AND  B.Type=2  
					WHERE S.mStuffId='$StuffId'   GROUP BY S.StuffId
				)A 
		      UNION ALL
	             SELECT  0 AS bpQty,ROUND(Min(A.Qty/A.Relation)) AS bfQty FROM(
					SELECT S.StuffId,S.Relation,SUM(IFNULL(B.Qty,0)) AS Qty 
					FROM $DataIn.stuffcombox_bom S 
					LEFT JOIN $DataIn.ck8_bfsheet B ON S.StuffId=B.StuffId AND  (B.Estate=0 OR B.Estate=3) 
					WHERE S.mStuffId='$StuffId'  GROUP BY S.StuffId
				 )A
			 )A";
		 $CheckMReslut=mysql_query($UnionSql,$link_id); 
		 
		 if($CheckMRow = mysql_fetch_array($CheckMReslut)){
		     $bpQty=$CheckMRow["bpQty"];
             $bfQty=$CheckMRow["bfQty"];
		 }   									
      }

        
		$tValue=round($dStockQty+$rkQty+$bpQty-$llQty-$bfQty-$thQty+$bcQty,$decimals);		//初始库存+入库数量+备品转入-领料数量-报废数量-退换数量+补仓数量
		$oValue=round($dStockQty+$cgQty+$bpQty-$orderQty-$bfQty,$decimals);
		
		$tStockQty=round($tStockQty,$decimals);
		$oStockQty=round($oStockQty,$decimals);
		$OrderSignColor="";
		
		
		if($tValue!=$tStockQty || $oValue!=$oStockQty){    
            
            if($tValue>=0){
	            
	            $updateSql = "UPDATE $DataIn.ck9_stocksheet SET tStockQty='$tValue' WHERE StuffId ='$StuffId'";
	            $updateResult = mysql_query($updateSql);
            }
            
            if($oValue>=0){
	            $updateSql = "UPDATE $DataIn.ck9_stocksheet SET oStockQty='$oValue' WHERE StuffId ='$StuffId'";
	            $updateResult = mysql_query($updateSql);
            }
            
		    $WarnRemark="$tValue!=$tStockQty （实）或 $oValue!=$oStockQty （订） ";
			$OrderSignColor="bgcolor='#FF6633'";
			$myOpration="<a href='stuffreport_result.php?Idtemp=$StuffId&Nametemp=$StuffCname' target='_blank'>分析</a>";
			include"../model/subprogram/stuff_Property.php";//配件属性
			$ChooseOut="N";
			$ValueArray=array(
				array(0=>$StuffId,
						 1=>"align='center'",2=>" title='$WarnRemark' "),
				array(0=>$StuffCname),
				array(0=>$Price,
						 1=>"align='right'"),
				array(0=>$UnitName,
						 1=>"align='center'"),				
				array(0=>$dStockQty,
						 1=>"align='right'"),
				array(0=>$tStockQty,					
						 1=>"align='right'"),
				array(0=>$oStockQty,
						 1=>"align='right'"),
				array(0=>$orderQty,
						 1=>"align='right'"),
				array(0=>$cgQty,
						 1=>"align='right'"),
				array(0=>$rkQty,
						 1=>"align='right'"),
				array(0=>$llQty,
						 1=>"align='right'"),
				array(0=>$bpQty,
						 1=>"align='right'"),
				array(0=>$bfQty,
						 1=>"align='right'"),
			array(0=>$thQty,
					 1=>"align='right'"),
			array(0=>$bcQty,
					 1=>"align='right'"), 
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
if ($i==1){
	noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>