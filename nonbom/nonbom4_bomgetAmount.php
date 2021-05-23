<?php
// 相关刀模对应的配件下单数量pt
$StuffOrderQty="&nbsp;";$GetSignStr="&nbsp;";$BOMCompanyStr="&nbsp;";$BOMForshort="";
$GetQty=$myRow["GetQty"];
if($mainType==8){    
    //供应商款项是否扣掉     
    if($GetQty>0){
            $CheckMainResult=mysql_query("SELECT   B.CompanyId,T.Forshort  
            FROM  $DataPublic.nonbom4_bomcompany  B  
            LEFT JOIN $DataIn.trade_object  T ON T.CompanyId=B.CompanyId  
            WHERE   B.GoodsId=$GoodsId AND B.cSign=7",$link_id);
            while($CheckMainRow = mysql_fetch_array($CheckMainResult)){
		        $mCompanyId = $CheckMainRow["CompanyId"];
		        $mForshort  = $CheckMainRow["Forshort"];
		        if($mCompanyId!=""){
		        
				   $CheckmainResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(G.AddQty+G.FactualQty),0) AS Qty
				      FROM $DataIn.cut_die  D   
				      LEFT JOIN $DataIn.cg1_stocksheet   G ON G.StuffId=D.StuffId 
				      LEFT JOIN $DataIn.cg1_stockmain   M ON M.Id=G.Mid 
				      WHERE D.GoodsId=$GoodsId  AND M.CompanyId IN ($mCompanyId)",$link_id));
				   $StuffOrderQty=$CheckmainResult["Qty"]==0?"&nbsp;":$CheckmainResult["Qty"];
		           $BOMForshort.="<span class='redB'>".$mForshort."(包装)</span><span class='redB'>".$StuffOrderQty."</span><br>";
		           }     
            }        

            $CheckptResult=mysql_query("SELECT   B.CompanyId,T.Forshort  
            FROM  $DataPublic.nonbom4_bomcompany  B  
            LEFT JOIN $DataOut.providerdata  T ON T.CompanyId=B.CompanyId  
            WHERE   B.GoodsId=$GoodsId AND B.cSign=3",$link_id);   
            while($CheckptRow = mysql_fetch_array($CheckptResult)){
	            $ptCompanyId = $CheckptRow["CompanyId"];
	            $ptForshort  = $CheckptRow["Forshort"];
	            if($ptCompanyId!=""){
				   $CheckptsubResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(G.AddQty+G.FactualQty),0) AS Qty
				      FROM $DataOut.cut_die  D   
				      LEFT JOIN $DataOut.cg1_stocksheet   G ON G.StuffId=D.StuffId 
				      LEFT JOIN $DataOut.cg1_stockmain   M ON M.Id=G.Mid 
				      WHERE D.GoodsId=$GoodsId  AND M.CompanyId IN ($ptCompanyId)",$link_id));
				   $StuffOrderQty=$CheckptsubResult["Qty"]==0?"&nbsp;":$CheckptsubResult["Qty"];
	               $BOMForshort.="<span class='blueB'>".$ptForshort."(皮套)</span><span class='redB'>".$StuffOrderQty."</span><br>&nbsp;&nbsp;&nbsp;&nbsp;";
	            }
            }
            $BOMCompanyStr="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom4_BomCompany\",\"$GoodsId\")' src='../images/edit.gif' title='关联BOM采购的供应商' width='13' height='13'>$BOMForshort";

      }
  }
$GetQty=$GetQty==0?"&nbsp;":$GetQty; 

?>