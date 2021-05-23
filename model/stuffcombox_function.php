<?php
/*子母配件函数*/

//检查是否为母配件
function check_stuffbox($StockId,$StuffId,$DataIn,$link_id){
	 if ($StockId!=""){
		  $checkResult=mysql_query("SELECT StockId,StuffId,Relation FROM $DataIn.cg1_stuffcombox WHERE mStockId='$StockId' LIMIT 1",$link_id);
	 }
	 else{
		  $checkResult=mysql_query("SELECT StuffId,Relation FROM $DataIn.stuffcombox_bom WHERE mStuffId='$StuffId' LIMIT 1",$link_id);  
	 }
	 return  mysql_num_rows($checkResult)==1?true:false;
}

//检查是否为子配件
function check_stuffbox_sub($StockId,$StuffId,$DataIn,$link_id){
	 if ($StockId!=""){
		  $checkResult=mysql_query("SELECT StockId,StuffId,Relation FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId' LIMIT 1",$link_id);
	 }
	 else{
		 $checkResult=mysql_query("SELECT StuffId,Relation FROM $DataIn.stuffcombox_bom WHERE StuffId='$StuffId' LIMIT 1 ",$link_id);  
	 }
	 return  mysql_num_rows($checkResult)==1?true:false;
}

//子母配件备料保存
function stuffcombox_bl_save($StockId,$Pid,$DataIn,$link_id,$Operator,&$Log){

	  if (check_stuffbox_sub($StockId,"",$DataIn,$link_id)){
		     stuffcombox_bl($StockId,$Pid,$DataIn,$link_id,$Operator,$Log);
      }
      else{
          if (check_stuffbox($StockId,"",$DataIn,$link_id)){
	         stuffcombox_subbl($StockId,$Pid,$DataIn,$link_id,$Operator,$Log);
	      }
      }
}

//子母配件备料删除
function stuffcombox_bl_delete($StockId,$Qty,$DataIn,$link_id,$Operator,&$Log){
       if (check_stuffbox_sub($StockId,"",$DataIn,$link_id)){
		          stuffcombox_bl_del($StockId,$Qty,$DataIn,$link_id,$Operator,$Log);
	      }
	      else{
	         if (check_stuffbox($StockId,"",$DataIn,$link_id)){
		          stuffcombox_subbl_del($StockId,$Qty,$DataIn,$link_id,$Operator,$Log);
		      }
	      }
}


//添加关联子配件备料记录
function stuffcombox_subbl($StockId,$Pid,$DataIn,$link_id,$Operator,&$Log)
{
		  $checkResult=mysql_query("SELECT StockId,StuffId,Relation FROM $DataIn.cg1_stuffcombox WHERE mStockId='$StockId' ",$link_id);
		  while($checkRow=mysql_fetch_array($checkResult)) {
		       $SubStockId=$checkRow["StockId"];
		       $StuffId=$checkRow["StuffId"];
		       $Relation=$checkRow["Relation"];
		       
		       //检查子母配件的领料数量
		       $m_llQty=0;$sub_llQty=0;
		       $lledResult=mysql_query("SELECT SUM(Qty) AS Qty,'1' AS llSign FROM  $DataIn.ck5_llsheet WHERE StockId='$StockId' 
												UNION ALL 
													       SELECT SUM(Qty) AS Qty,'2' AS llSign FROM  $DataIn.ck5_llsheet WHERE StockId='$SubStockId' ",$link_id);
		       while($lledRow=mysql_fetch_array($lledResult)){
		             if ($lledRow["llSign"]==1){
			                $m_llQty=$lledRow["Qty"]==""?0:$lledRow["Qty"];
		             }
		             else{
			              $sub_llQty=$lledRow["Qty"]==""?0:$lledRow["Qty"];
		             }
		       }
		       
		       $llQty=$m_llQty*$Relation; //子配件需领料数量
		       
		       if ($llQty>$sub_llQty){
			            $addQty=$llQty-$sub_llQty;
			           addStockQty($SubStockId,$StuffId,$Pid,$addQty,$DataIn,$link_id,$Log);//添加领料记录
		       }
		       /*子配件领料数量大于母配件数量时不处理*/
		}
}

//添加关联母配件备料记录
function stuffcombox_bl($StockId,$Pid,$DataIn,$link_id,$Operator,&$Log)
{
		  $checkResult=mysql_query("SELECT mStockId,mStuffId FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId' ",$link_id);
		  if($checkRow=mysql_fetch_array($checkResult)) {
		         $mStockId=$checkRow["StockId"];
		         $mStuffId=$checkRow["StuffId"];
		         
		         $lledResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM  $DataIn.ck5_llsheet WHERE StockId='$mStockId' ",$link_id));
		         $llQty=$lledResult["Qty"]==""?0:$lledResult["Qty"];
		         
		          $llResult=mysql_fetch_array(mysql_query("SELECT MIN(ROUND(A.Qty/A.Relation)) AS Qty FROM (
									SELECT S.Relation,SUM(IFNULL(L.Qty,0)) AS Qty 
									FROM $DataIn.cg1_stuffcombox S 
									LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=S.StockId 
									WHERE S.mStockId='$mStockId' GROUP BY S.StockId 
									)A ",$link_id));
				$Qty=$llResult["Qty"];//正确的备料数量
				
				if ($llQty>$Qty){
						$Log=reduceStockQty($mStockId,$mStuffId,$Qty,$DataIn,$link_id,$Log);  //减少领料记录
				 }
				 else{
					  $addQty= $Qty-$llQty;
					  if ($addQty>0){
						   $Log=addStockQty($mStockId,$mStuffId,$Pid,$addQty,$DataIn,$link_id,$Log);//添加领料记录
					  }
				 }
		}
}

//修改(添加)配件领料数量
function addStockQty($StockId,$StuffId,$Pid,$Qty,$DataIn,$link_id,&$Log)
{
        $llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,POrderId,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0', 0,'$StockId','$StuffId','$Qty','0','0')";
		$llInAction=mysql_query($llInSql,$link_id);
		if ($llInAction){
			$upSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$Qty  WHERE StuffId='$StuffId' AND tStockQty>=$Qty ";
			$upResult = mysql_query($upSql,$link_id);
			if ($upResult && mysql_affected_rows()>0){
			      $Log.="生成领料单并更新($StockId)子配件($StuffId)在库数量(-$Qty)成功!\n";
			}
			else{
				 $Log.="<div class='redB'>生成领料单但更新($StockId)子配件($StuffId)在库数量(-$Qty)失败!</div>\n";
			}
		}
}


//删除关联子配件备料记录
function stuffcombox_subbl_del($StockId,$Qty,$DataIn,$link_id,$Operator,&$Log)
{
		  $checkResult=mysql_query("SELECT StockId,StuffId,Relation FROM $DataIn.cg1_stuffcombox WHERE mStockId='$StockId' ",$link_id);
		  while($checkRow=mysql_fetch_array($checkResult)) {
		       $SubStockId=$checkRow["StockId"];
		       $StuffId=$checkRow["StuffId"];
		       $Relation=$checkRow["Relation"];
		       $OrderQty=$Qty*$Relation;//正确的备料数量
		       
		       if ($OrderQty>0){
					 reduceStockQty($SubStockId,$StuffId,$OrderQty,$DataIn,$link_id,$Log);
			   }
		}				   
}

//删除关联母配件备料记录
function stuffcombox_bl_del($StockId,$Qty,$DataIn,$link_id,$Operator,&$Log)
{
		  $checkResult=mysql_query("SELECT mStockId,mStuffId FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId' ",$link_id);
		  if($checkRow=mysql_fetch_array($checkResult)) {
		         $mStockId=$checkRow["StockId"];
		         $mStuffId=$checkRow["StuffId"];
		         
		         if ($llQty>0){
				        $llResult=mysql_fetch_array(mysql_query("SELECT MIN(ROUND(A.Qty/A.Relation)) AS Qty FROM (
									SELECT S.Relation,SUM(IFNULL(L.Qty,0)) AS Qty 
									FROM $DataIn.cg1_stuffcombox S 
									LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=S.StockId 
									WHERE S.mStockId='$mStockId' GROUP BY S.StockId 
									)A ",$link_id));
						$Qty=$llResult["Qty"];//正确的备料数量
						
						if ($Qty>0){
							reduceStockQty($mStockId,$mStuffId,$Qty,$DataIn,$link_id,$Log);
						}
				}
		}
}

//修改(减少)配件领料数量
function reduceStockQty($StockId,$StuffId,$Qty,$DataIn,$link_id,&$Log)
{
       $OPeratorSign="N";
       $ReduceQty=$Qty;
       $checkllResult=mysql_query("SELECT Id,Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND StuffId='$StuffId' ",$link_id);
	    while($llRow=mysql_fetch_array($checkllResult)) {
	           $llQty=$llRow["Qty"];
	           $Id=$llRow["Id"];
	            if ($llQty>$ReduceQty && $ReduceQty>0){
		              $upSql="UPDATE $DataIn.ck5_llsheet SET Qty=$ReduceQty WHERE Id='$Id'";  
		              $upResult = mysql_query($upSql,$link_id);
		              $OPeratorSign="Y";
	           }
	           else{
		             $delSql="DELETE FROM $DataIn.ck5_llsheet  WHERE Id='$Id'";
		             $delResult = mysql_query($delSql,$link_id);
		             if ($delResult){
			              $ReduceQty-=$llQty;
			              $OPeratorSign="Y";
		             }
	           }
	    }
	    
	    $Qty=$ReduceQty>0?$Qty-$ReduceQty:$Qty;
	    
	    if ($OPeratorSign=="Y"){
		    $upSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty  WHERE StuffId='$StuffId' ";
			$upResult = mysql_query($upSql,$link_id);
			if ($upResult && mysql_affected_rows()>0){
			      $Log.="更新($StockId)子配件($StuffId)在库数量(+$Qty)成功!\n";
			}
			else{
				 $Log.="<div class='redB'>更新($StockId)子配件($StuffId)在库数量(+$Qty)失败!</div>\n";
			}
	    }
}

//取得母配件库存
function getStuffComBoxStockQty($mStuffId,$DataIn,$link_id){
      $StockArray=array();
      $updateSTR="";
	  $checkResult=mysql_query("SELECT IFNULL(A.StuffId,0) AS StuffId,MIN(ROUND(A.oStockQty/A.Relation)) AS oStockQty,MIN(ROUND(A.tStockQty/A.Relation)) AS tStockQty,A.m_oStockQty,A. m_tStockQty 
	                   FROM (
									SELECT S.StuffId,S.Relation,K.oStockQty,K.tStockQty,K1.oStockQty AS m_oStockQty,K1.tStockQty AS m_tStockQty    
									FROM $DataIn.stuffcombox_bom S  
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
									LEFT JOIN $DataIn.ck9_stocksheet K1 ON K1.StuffId=S.mStuffId 
									WHERE S.mStuffId='$mStuffId'  
					  )A  ",$link_id);
	 if($checkRow = mysql_fetch_array($checkResult)){
               $StuffId=$checkRow["StuffId"];
               if ($StuffId>0){
	                 $oStockQty=$checkRow["oStockQty"];
	                 $tStockQty=$checkRow["tStockQty"];
	                 $m_oStockQty=$checkRow["m_oStockQty"];
	                 $m_tStockQty=$checkRow["m_tStockQty"];
	                 $StockArray=array("oStockQty"=>"$oStockQty","tStockQty"=>"$tStockQty");
	                 
	                 if ($oStockQty!=$m_oStockQty){
		                   $updateSTR=" oStockQty='$oStockQty' ";
	                 }
	                 
	                 if ($tStockQty!=$m_tStockQty){
		                  $updateSTR.=$updateSTR==""?" tStockQty='$tStockQty' ":",tStockQty='$tStockQty' ";
	                 }
               }
	 }
	 
	 if ($updateSTR!=""){
	        //更新母配件库存数据
		    $upSql="UPDATE $DataIn.ck9_stocksheet SET $updateSTR  WHERE StuffId='$mStuffId' ";
			$upResult = mysql_query($upSql,$link_id);
	 }
	 
	 return $StockArray;
}

//取得子配件备品转入
function getStuffComBoxBprkQty($mStuffId,$DataIn,$link_id){
     $bpQty=0;
     $checkResult=mysql_query("SELECT ROUND(Min(A.Qty/A.Relation)) AS bpQty FROM(
									SELECT S.StuffId,S.Relation,SUM(IFNULL(B.Qty,0)) AS Qty 
									FROM $DataIn.stuffcombox_bom S 
									LEFT JOIN $DataIn.ck7_bprk B ON S.StuffId=B.StuffId  AND B.Estate=0 
									WHERE S.mStuffId='$mStuffId' GROUP BY S.StuffId
								)A ",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)){
	     $bpQty=$checkRow["bpQty"]==""?0:$checkRow["bpQty"];
	}
	return $bpQty;						
}

//添加子母配件关系
function addCg_StuffComBox_data($mStockId,$mStuffId,$DataIn,$link_id,$Operator,&$Log)
{
       $CheckComboxRow=mysql_fetch_array(mysql_query("SELECT COUNT(G.StockId) AS sheetCount  
                          FROM  $DataIn.cg1_stuffcombox   G  
                         LEFT JOIN $DataIn.stuffcombox_bom A  ON A.StuffId =G.StuffId
                         WHERE  G.mStockId='$mStockId' AND A.mStuffId='$mStuffId' ",$link_id));  
       if($CheckComboxRow["sheetCount"]>0){
          //$Log.="$mStockId($mStuffId) 子母配件关系表已存在</br>";
	      // return false;
	      $delSql="DELETE FROM $DataIn.cg1_stuffcombox WHERE mStockId='$mStockId' AND mStuffId='$mStuffId'";
	      $delResult=mysql_query($delSql,$link_id);
	      $Log.="原 $mStockId($mStuffId) 子母配件关系表已删除</br>";
       }                  
                         
      $checkResult=mysql_query("SELECT * FROM $DataIn.cg1_stocksheet  WHERE StockId='$mStockId' AND  StuffId='$mStuffId' AND rkSign=1 ",$link_id);
      if($checkRow = mysql_fetch_array($checkResult)){
               $POrderId=$checkRow["POrderId"];
               $OrderQty=$checkRow["OrderQty"];
               $StockQty=$checkRow["StockQty"];
               $AddQty=$checkRow["AddQty"];
               $FactualQty=$checkRow["FactualQty"];
               
               //新增子母配件关系
               $n=1;   $Com_StuffId_STR="";
               $newComStockId =substr($mStockId, 2);
               
               //$newComStockId=$newComStockId."01";
               
               $CheckComResult = mysql_query("SELECT M.Relation, M.StuffId  FROM $DataIn.stuffcombox_bom M  WHERE  M.mStuffId=$mStuffId",$link_id);
               while($CheckComRow = mysql_fetch_array($CheckComResult)){
                          $addId=$n<10?'0' . $n:$n;
                          $ComStockId   = $newComStockId .$addId;
                          $ComRelation  = $CheckComRow["Relation"];
                          $ComStuffId   = $CheckComRow["StuffId"];
                          
                          $ComOrderQty = $OrderQty*$ComRelation;
                          $ComStockQty = $StockQty*$ComRelation;
                          $ComAddQty   =  $AddQty*$ComRelation;
                          $ComFactualQty = $FactualQty*$ComRelation;
                           
                         if($ComOrderQty>0 || $ComFactualQty>0){
		                          $IN_recode="INSERT INTO $DataIn.cg1_stuffcombox (Id,POrderId,mStockId,StockId,mStuffId,StuffId,Relation,OrderQty,StockQty,AddQty,FactualQty,Date,Operator,creator,created) 
		VALUES (NULL,'$POrderId','$mStockId','$ComStockId','$mStuffId','$ComStuffId','$ComRelation','$ComOrderQty','$ComStockQty','$ComAddQty','$ComFactualQty',CURDATE(),'$Operator','$Operator',NOW())";
			                      $IN_Result=@mysql_query($IN_recode);
			                      if ($IN_Result){
				                           $UP_Sql= "UPDATE $DataIn.ck9_stocksheet SET oStockQty=oStockQty-'$ComStockQty' WHERE StuffId='$ComStuffId'";
									       $UP_Result = mysql_query($UP_Sql);
			                      }
			                      $Com_StuffId_STR.=$Com_StuffId_STR==""?$ComStuffId:",$ComStuffId";
                        }
                        $n++;
                }
                if ($n>1) {
	                $Log.="$mStockId($mStuffId)  子母配件关系表(子配件:$Com_StuffId_STR)已添加成功。</br>";
	                return true;
                }
      }
}
?>