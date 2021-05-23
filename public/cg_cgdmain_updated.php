<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$Log_Funtion="更新";
$upDataSheet="$DataIn.cg1_stocksheet";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 7:
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
		case 22:	//拆分
		$Log_Funtion="拆分";
	    $StockResult = mysql_query("SELECT Mid,StockId,POrderId,StuffId,BuyerId,DeliveryDate FROM $upDataSheet WHERE Id='$Id' LIMIT 1",$link_id);
		if ($myrow = mysql_fetch_array($StockResult)) {
		    $Mid=$myrow["Mid"];
			$POrderId=$myrow["POrderId"];
			$StuffId=$myrow["StuffId"];
			$BuyerId=$myrow["BuyerId"];
			$StockId=$myrow["StockId"];
			$DeliveryDate=$myrow["DeliveryDate"];
			$maxSql = mysql_query("SELECT MAX(StockId) AS maxStockId FROM $upDataSheet WHERE POrderId='$POrderId'",$link_id);
			$maxStockId=mysql_result($maxSql,0,"maxStockId");
		//拆分
			$Field1=explode("~",$ListSTR);
			$Count1=count($Field1);
			for($i=0;$i<$Count1;$i++){
				$ListSTRtemp=$Field1[$i];
				echo $i."  ".$ListSTRtemp."<br>";
				$Field2=explode("|",$ListSTRtemp);
				$Price=$Field2[0];			//配件价格
				$OrderQty=$Field2[1];		//订单数量
				$StockQty=$Field2[2];		//使用库存数量
				$FactualQty=$Field2[3];		//需求数量
				$AddQty=$Field2[4];			//增购数量
				$CompanyId=$Field2[5];		//供应商ID
				if($x==1){//更新
				$OrderQty1=$OrderQty;
		         $sql = "UPDATE $upDataSheet SET Price='$Price',OrderQty='$OrderQty',StockQty='$StockQty',
		         AddQty='$AddQty',FactualQty='$FactualQty',CompanyId='$CompanyId',
			     AddRemark=concat(AddRemark,'(采购拆分的需求单)'),modifier='$Operator',modified=NOW() WHERE Id='$Id'";
					$result = mysql_query($sql);
					if ($result){
						$Log.="第 $x 个拆分子单加入成功！ $sql <br>";
             			     $UpdateComboxSql = "UPDATE   $DataIn.cg1_stuffcombox  M   
             			     LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId =M.mStockId
             			     SET  M.OrderQty=M.Relation*G.OrderQty,M.StockQty=M.Relation*G.StockQty,M.FactualQty=M.Relation*(G.FactualQty+G.AddQty),M.Date = NOW()
              			     WHERE   G.Id=$Id";
              			     $UpdateComboxResult = @mysql_query($UpdateComboxSql);
          			          if($UpdateComboxResult ){
               			         $Log.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;母配件采购ID($Id)对应的子配件更新成功. </br>";
         			           }
           		          else{
              		             $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;母配件采购ID($Id)对应的子配件更新失败. $UpdateComboxSql </div></br>";
               		            }
						}
					else{
						$Log.="<div class=redB>第 $x 个拆分子单加入失败！退出操作!</div><br>";
						$OperationResult="N";
						break;
						}
					}
				else{//新增
				    $OrderQty2=$OrderQty;
					//$StockId2=$maxStockId+$i;
					$getNewStockId = mysql_fetch_array(mysql_query("SELECT  getNewStockId('$POrderId',2) AS StockId",$link_id));
					$StockId2 = $getNewStockId['StockId'];
					
					$IN_recode3="INSERT INTO $upDataSheet(Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,
					FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES                                        (NULL,'$Mid','$StockId2','$POrderId','$StuffId','$Price','$OrderQty','$StockQty','$AddQty',
					'$FactualQty','$CompanyId','$BuyerId','$DeliveryDate','','采购拆分的需求单','0','0')";
					//echo $IN_recode3;
					$res3=@mysql_query($IN_recode3);
					if($res3){
						$Log.="第 $x 个拆分子单加入成功！ $IN_recode3 <br>";
					               $CheckComResult = mysql_query("SELECT M.Relation, M.StuffId  FROM $DataIn.stuffcombox_bom M   WHERE  M.mStuffId=$StuffId",$link_id);
                                   $newComStockId =substr($StockId2, 2, 12);
                                   $newComStockId=$newComStockId."01";
					               while($CheckComRow = mysql_fetch_array($CheckComResult)){
                                              $ComStockId = $ComStockId==""?$newComStockId:$ComStockId+1;
					                          $ComRelation = $CheckComRow["Relation"];
					                          $ComStuffId     = $CheckComRow["StuffId"];
					                          $ComOrderQty  = $OrderQty*$ComRelation;
				                              $ComStockQty   = $StockQty*$ComRelation;
					                          $ComFactualQty  = ($AddQty+$FactualQty)*$ComRelation;
                                              if($ComOrderQty>0){
					                                  $IN_recode4="INSERT INTO $DataIn.cg1_stuffcombox (Id,POrderId,mStockId,StockId,mStuffId,StuffId,Relation,OrderQty,StockQty,FactualQty,Date,Operator)VALUES 						                             (NULL,'$POrderId','$StockId2','$ComStockId','$StuffId','$ComStuffId','$ComRelation','$ComOrderQty','$ComStockQty','$ComFactualQty','$DateTime','$Operator')";
						                              $res4=@mysql_query($IN_recode4);
						                              if($res4){								
							                                      $Log.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  母配件( $StuffId )的第 $n 个子配件($ComStuffId)的需求单添加成功;<br>";
					                                      } 
						                            else{
							                                  $Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; 母配件( $StuffId )的第 $n 个子配件($ComStuffId)的需求单添加失败.$IN_recode4<br></div>";
							                                 $OperationResult="N";
						                                 }
                                               }
					                        $n++;
					                    }       						
						}
					else{
						$Log.="<div class=redB>第 $x 个拆分子单加入失败！$IN_recode3</div><br>";
						$OperationResult="N";
						}
					}
				$x++;
				}//end for
			}//end if($myrow = mysql_fetch_array($StockResult))
		else{
			$Log="<div class='redB'>读取资料失败!</div>";
			$OperationResult="N";
			}
		break;

	case 26:
		$Log_Funtion="重置";	//条件：未做请款、未锁定，未全部收货
		
		//收货情况				
		$mySql="SELECT S.StockId,S.StuffId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,K.oStockQty
			FROM $upDataSheet S 
			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
			WHERE 1 AND S.Id='$Id' AND S.Locks=1 AND S.Estate!=1 LIMIT 1";
		$myResult = mysql_query($mySql,$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			$StockId=$myRow["StockId"];
			$StuffId=$myRow["StuffId"];
			$OrderQty=$myRow["OrderQty"];
			$StockQty=$myRow["StockQty"];
			$AddQty=$myRow["AddQty"];
			$FactualQty=$myRow["FactualQty"];
			$Qty=$FactualQty+$AddQty;
			$oStockQty=$myRow["oStockQty"];
			$semiRow= mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.cg1_semifinished 
			WHERE mStockId='$StockId'",$link_id));
			$semiId = $semiRow["Id"];
			if($semiId>0){
				$Log="<div class=redB>重置失败,需求单($StockId)为半成品,不能重置!</div><br>";
				$OperationResult="N";
				break;
			}
			
			//收货情况				
			$rkTemp= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet 
			WHERE StockId='$StockId' ORDER BY StockId",$link_id));
			$rkQty=$rkTemp["Qty"];
			$rkQty=$rkQty==""?0:$rkQty;
			if($rkQty==$Qty){
				//全部收货，不允许重置
				$Log="<div class=redB>重置失败,需求单($StockId)已全部收货!</div><br>";
				$OperationResult="N";
				}
			else{
				if($oStockQty==0){
					$Log="<div class=redB>重置失败,需求单($StockId)所用配件 $StuffId 没有可用库存!</div><br>";
					$OperationResult="N";
					}
				else{//部分或未收货，且有可用库存
					//可重置的数量
					$useQty=$Qty-$rkQty;
					if($oStockQty>=$useQty){
						$newAddQty=$AddQty-$useQty;
						if($newAddQty==0 ||$newAddQty<0){	//A:可用库存足够，除了可以重置增购,还可以重置需购和已用库存数量
							$newAddQty=0;
							$newFactualQty=$FactualQty+$AddQty-$useQty;
							$newStockQty=$OrderQty==0?0:$OrderQty-$newFactualQty;
							$newoStockQty=$oStockQty-$useQty;
							$Log="A-";
							}
						else{							//B:可用库存足够，但只能重置增购,其它不变
							$newFactualQty=$FactualQty;
							//$newStockQty=$StockQty;
							$newStockQty=$OrderQty==0?0:$StockQty;
							$newoStockQty=$oStockQty-$useQty;
							$Log="B-";
							}			
						}
					else{
						$newAddQty=$AddQty-$oStockQty;
						if($newAddQty==0 ||$newAddQty<0){	//C:OK,可用库存不足，但可以重置增购,还可以重置部分需购和已用库存数量							
							$newAddQty=0;
							$newFactualQty=$FactualQty+$AddQty-$oStockQty;
							//$newStockQty=$OrderQty-$newFactualQty;
							$newStockQty=$OrderQty==0?0:$OrderQty-$newFactualQty;
							$newoStockQty=0;
							$Log="C-";
							}
						else{									//D:OK,可用库存不足，只可以重置增购,其它不变
							$newFactualQty=$FactualQty;
							//$newStockQty=$StockQty;
							$newStockQty=$OrderQty==0?0:$StockQty;
							$newoStockQty=0;
							$Log="D-";
							}
						}
						
						
					if($newFactualQty==0){
						$Log.="重置后无需采购，此需求单将自动取消采购!<br>";
						$MidSTR=",S.Mid='0'";
						$OperationSign="DELETE";
						}
					//更新需求单和配件的可用库存					
					$upSql = "UPDATE $upDataSheet S
					SET S.StockQty='$newStockQty',S.FactualQty='$newFactualQty',S.AddQty='$newAddQty' $MidSTR 
					WHERE S.Id='$Id'";
					$upResult = mysql_query($upSql);
					if($upResult && mysql_affected_rows()>0){
						 $Log.="需求单($StockId)重置成功!<br>"; 
						 if (($newFactualQty+$newAddQty)>0)  {
							  $OperationSign="UPDATE";
							  $cgQty=$newFactualQty+$newAddQty;
						  }
						}
					else{
						$Log.="<div class='redB'>需求单($StockId)重置失败!$upSql</div><br>"; 
						$OperationResult="N";
						}
				    }
				}
	             //******************************母配件下的子配件需求更新
				$StockId=$myRow["StockId"];
				$StuffId=$myRow["StuffId"];	
	            include "../admin/subprogram/del_model_combox.php";
			}
		else{
			$Log="<div class=redB>重置失败,需求单($Id)未审核或处于锁定状态!</div><br>";
			$OperationResult="N";
			}
		
		break;
		
	case 27:
			$Log_Funtion="需求单还原";	//条件：未收货、未结付、领料总数为0
		   //检查主单下记录数，如果只有一条记录，那还原后做删除主单操作
			$CheckMid=mysql_fetch_array(mysql_query("SELECT S.StockId,M.CompanyId FROM $upDataSheet S 
			          LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.id
			          WHERE S.Id='$Id'",$link_id));
			$StockId=$CheckMid["StockId"];
	        $CompanyId=$CheckMid["CompanyId"];

		    $UpSql= "UPDATE $upDataSheet S
                       SET S.Mid='0',modifier='$Operator',modified=NOW()  WHERE S.Id='$Id' AND S.Locks='1'
                       AND NOT EXISTS(SELECT R.StockId FROM $DataIn.ck1_rksheet R WHERE R.StockId=S.StockId)
                       AND NOT EXISTS(SELECT H.StockId FROM $DataIn.gys_shsheet H WHERE H.StockId=S.StockId AND Estate>0)
                       AND NOT EXISTS(SELECT L.StockId FROM $DataIn.ck5_llsheet L WHERE L.StockId=S.StockId)";
		   $UpResult = mysql_query($UpSql);
		   if($UpResult && mysql_affected_rows()>0){
				$Log.="需求单还原成功(ID号 $Id)!<br>"; 
				$DelSql="DELETE FROM $DataIn.cg1_stockmain WHERE Id='$Mid' AND Id NOT IN(SELECT Mid FROM $upDataSheet)";
				$DelResult=mysql_query($DelSql);
				if($DelResult && mysql_affected_rows()>0){
					$Log.="主单 $Mid 下已没有明细，删除主单成功.<br>";
					}
			}
		else{
				$Log.="<div class='redB'>$x 需求单还原失败(ID号 $Id),需求单已锁定或已入库，收货、领料! $UpSql</div><br>"; 
				$OperationResult="N";
			   }
		break;
	case 923://更新单价
		$Log_Funtion="需求单配件价格更新";
		$UpSql="UPDATE $upDataSheet SET Price='$Price',AddRemark='$AddRemark',Estate='1',modifier='$Operator',modified=NOW() WHERE Id='$Id' LIMIT 1";
		$UpResult=mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
		     $insertSql= "INSERT INTO $DataIn.cg1_stocksheet_log(StockId,Opcode,Estate,Locks,Date,Operator,PLocks,creator,created) SELECT StockId,'3',Estate,Locks,'$DateTime','$Operator',0,'$Operator','$DateTime' FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
			  $insertResult= mysql_query($insertSql);
			$Log="需求单( $Id) 的配件价格更新成功.";
			}
		else{
			$Log="<div class='redB'>需求单( $Id) 的配件价格更新失败.</div>";
			$OperationResult="N";
			}
		break;
	case 933:
		$Log_Funtion="采购单内容更新";
		$chooseDate=$cgDate;
		$Remark=FormatSTR($Remark);
		$mainSql = "UPDATE $DataIn.cg1_stockmain SET Date='$cgDate',Remark='$Remark' WHERE Id='$Mid'";
		$mainResult = mysql_query($mainSql);
		if($mainResult && mysql_affected_rows()>0){
			$Log.="采购单 $Mid 主单信息更新成功!<br>"; 
			}
		else{		
			$Log.="<div class='redB'>采购单 $Mid 主单信息更新失败! $mainSql </div><br>"; 
			$OperationResult="N";
			}
		if($StockIds!=""){
			//添加的需求单明细		
			$sheetSql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid' WHERE StockId IN ($StockIds)";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult && mysql_affected_rows()>0){
				$Log.="需求单($StockIds)加入主采购单成功!<br>"; 
				}
			else{		
				$Log.="<div class='redB'>需求单($StockIds)加入主采购单失败!</div><br>"; 
				$OperationResult="N";
				}
			}
		break;
	default:
		//更新:单价、数量
		$Date=date("Y-m-d");
		//提取可用库存和原单数据，重新计算
		$checkSql=mysql_query("SELECT S.StockId,S.POrderId,S.StuffId,S.AddQty,S.FactualQty,S.Price,K.oStockQty,S.cgSign  
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
		WHERE S.Id=$Id ORDER BY S.Id DESC",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$FactualQty=$checkRow["FactualQty"];
			$AddQty=$checkRow["AddQty"];
			$Price=$checkRow["Price"];
			$oStockQty=$checkRow["oStockQty"];
			$POrderId=$checkRow["POrderId"];
			$StuffId=$checkRow["StuffId"];
			$StockId=$checkRow["StockId"];
		   $cgSign=$checkRow["cgSign"];
	       $OperationSign="";
	  	   if($cgSign==1){//特采单				
			    if($FactualQty-$newFactualQty<=$oStockQty){////数量满足条件					
				$newoStockQty=$oStockQty-($FactualQty-$newFactualQty);
				if($newFactualQty==0){//删除特采单
					$delSql = "DELETE FROM $DataIn.cg1_stocksheet WHERE Id='$Id' LIMIT 1"; 
					$delRresult = mysql_query($delSql);
					if($delRresult && mysql_affected_rows()>0){
					   $OperationSign="DELETE";
						$Log.="特采单 $StockId 更新后无需采购，资料删除成功<br>";
						}
					else{
						$Log.="<div class='redB'>特采单 $StockId 重置后无需采购，资料删除失败 $delSql </div><br>";
						$OperationResult="N";
						}
					}
				else{					//更新特采单
					$SetStr="S.AddRemark='$AddRemark',S.FactualQty='$newFactualQty',S.Price='$newPrice',S.Estate='1',S.Locks='0',modifier='$Operator',modified=NOW()";
					$upSql="UPDATE $DataIn.cg1_stocksheet S SET $SetStr WHERE S.Id=$Id ";
					$upResult = mysql_query($upSql);
					if($upResult && mysql_affected_rows()>0){
					   $Log.="特采单 $StockId 的资料更新成功<br>";	
					    $insertSql= "INSERT INTO $DataIn.cg1_stocksheet_log(StockId,Opcode,Estate,Locks,Date,Operator,PLocks,creator,created) SELECT StockId,'3',Estate,Locks,'$DateTime','$Operator',0,'$Operator','$DateTime' FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
		               $insertResult= mysql_query($insertSql);
		               	
						   if ($FactualQty!=$newFactualQty) {
						        $OperationSign="UPDATE";	
						        $cgQty=$newFactualQty;
						   }				
						}
					else{
						$Log.="<div class='redB'>特采单 $StockId 的资料更新失败</div><br>";
						$OperationResult="N";
						}
					}
				}
			else{//数量不满足条件
				$Log="<div class='redB'>可用库存不足，特采单 $StockId 的资料更新失败!</div>";
				$OperationResult="N";
				}
			}
		else{				//常单
			$newoStockQty=$oStockQty-($AddQty-$newAddQty);
			if($AddQty-$newAddQty<=$oStockQty){	//数量满足条件
				$SetStr="S.AddRemark='$AddRemark',S.AddQty='$newAddQty',S.Price='$newPrice',S.Estate='1',S.Locks='0'";
				$upSql="UPDATE $DataIn.cg1_stocksheet S SET $SetStr WHERE S.Id=$Id ";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
	                $insertSql= "INSERT INTO $DataIn.cg1_stocksheet_log(StockId,Opcode,Estate,Locks,Date,Operator,PLocks,creator,created) SELECT StockId,'3',Estate,Locks,'$DateTime','$Operator',0,'$Operator','$DateTime' FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
	               $insertResult= mysql_query($insertSql);
			       if ($AddQty!=$newAddQty)  {
			               $OperationSign="UPDATE";
			                $cgQty=$newAddQty+$FactualQty;
			         }
					$Log.="需求单 $StockId 的资料更新成功<br>";						
					}
				else{
					$Log.="需求单 $StockId 的资料更新失败<br>";
					$OperationResult="N";
					}
				}
			else{								//数量不满足条件
				$Log="<div class='redB'>可用库存不足，需求单 $StockId 的资料更新失败!</div>";
				$OperationResult="N";
				}
			}
	    }
		else{
			$Log="<div class='redB'>提取资料失败</div>";
			$OperationResult="N";
		}
		$BuyerId=$Number;
		break;
}

if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
//返回参数
$ALType="From=$From&Pagination=$Pagination&Page=$Page&BuyerId=$BuyerId&GysPayMode=$GysPayMode&CompanyId=$CompanyId&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>