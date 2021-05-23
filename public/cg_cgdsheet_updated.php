<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.ck9_stocksheet
*/

if (@$ActionId>700){
   include "../basic/chksession.php";
   include "../basic/parameter.inc"; 
    }
else{
    include "../model/modelhead.php";


$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$upDataSheet="$DataIn.cg1_stocksheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
}
switch($ActionId){
	case 17:
		$Log_Funtion="审核"; $SetStr="Estate=0,ywOrderDTime=NOW() ";				
		include "../model/subprogram/updated_model_3d.php";		
		//include "../admin/swapdata/cg_updatePrice_topt.php";
		break;
	case 21://如果原来为加急，再点时则取消加急
		$Log_Funtion="加急";
		$Type=7;
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.cg2_orderexpress E LEFT JOIN $upDataSheet S ON S.StockId=E.StockId WHERE S.Id='$Id' AND E.Type='$Type'",$link_id);
				if($Check7Row=mysql_fetch_array($Check7Sql)){//取消加急
					$DelSql="DELETE FROM $DataIn.cg2_orderexpress WHERE Type='$Type' AND StockId=(SELECT StockId FROM $upDataSheet WHERE Id='$Id')";
					$DelResult=mysql_query($DelSql);
					if($DelResult){
						$Log.="&nbsp;&nbsp;需求单(ID为 $Id)取消加急状态.</br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;需求单(ID为 $Id)取消加急状态失败. $DelSql </div></br>";
						$OperationResult="N";
						}
					}
				else{//加急
					$inRecode= $DataIn !== 'ac' ? "INSERT INTO $DataIn.cg2_orderexpress SELECT NULL,StockId,'$Type','$DateTime','$Operator' FROM $upDataSheet WHERE Id='$Id'" : 
					                              "INSERT INTO $DataIn.cg2_orderexpress SELECT NULL,StockId,'$Type','$DateTime','$Operator',1,0,0,'$Operator','$DateTime','$Operator','$DateTime' FROM $upDataSheet WHERE Id='$Id'";
					$inResult=@mysql_query($inRecode);
					if($inResult){
						$Log.="&nbsp;&nbsp;需求单(ID为 $Id)设为加急.</br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;需求单(ID为 $Id)设为加急失败. $inRecode </div></br>";
						$OperationResult="N";
						}
					}
				}
			}
		break;
	case 22:	//拆分
		$Log_Funtion="拆分";
		$StockResult = mysql_query("SELECT POrderId,StuffId,BuyerId FROM $upDataSheet WHERE Id='$Id' LIMIT 1",$link_id);
		if ($myrow = mysql_fetch_array($StockResult)) {
			$POrderId=$myrow["POrderId"];
			$StuffId=$myrow["StuffId"];
			$BuyerId=$myrow["BuyerId"];
			$maxSql = mysql_query("SELECT MAX(StockId) AS maxStockId FROM $upDataSheet WHERE POrderId='$POrderId'",$link_id);
			$maxStockId=mysql_result($maxSql,0,"maxStockId");
		//拆分
			$Field1=explode("~",$ListSTR);
			$Count1=count($Field1);
			for($i=0;$i<$Count1;$i++){
				$ListSTRtemp=$Field1[$i];
				$Field2=explode("|",$ListSTRtemp);
				$Price=$Field2[0];			//配件价格
				$OrderQty=$Field2[1];		//订单数量
				$StockQty=$Field2[2];		//使用库存数量
				$FactualQty=$Field2[3];		//需求数量
				$AddQty=$Field2[4];			//增购数量
				$CompanyId=$Field2[5];		//供应商ID
				if($x==1){//更新
					$sql = "UPDATE $upDataSheet SET Price='$Price',OrderQty='$OrderQty',StockQty='$StockQty',AddQty='$AddQty',FactualQty='$FactualQty',CompanyId='$CompanyId',Estate='1',AddRemark=concat(AddRemark,'(采购拆分的需求单)'),modifier='$Operator',modified=NOW() WHERE Id='$Id'";
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
					//$StockId=$maxStockId+1;
					$getNewStockId = mysql_fetch_array(mysql_query("SELECT  getNewStockId('$POrderId',2) AS StockId",$link_id));
					$StockId = $getNewStockId['StockId'];
					
					$IN_recode3="INSERT INTO $upDataSheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES (NULL,'0','$StockId','$POrderId','$StuffId','$Price','$OrderQty','$StockQty','$AddQty','$FactualQty','$CompanyId','$BuyerId','0000-00-00','','采购拆分的需求单','1','0')";
					//echo $IN_recode3;
					$res3=@mysql_query($IN_recode3);
					if($res3){
						$Log.="第 $x 个拆分子单加入成功！ $IN_recode3 <br>";
					               $CheckComResult = mysql_query("SELECT M.Relation, M.StuffId  FROM $DataIn.stuffcombox_bom M   WHERE  M.mStuffId=$StuffId",$link_id);
                                   $newComStockId =substr($StockId, 2, 12);
                                   $newComStockId=$newComStockId."01";
					               while($CheckComRow = mysql_fetch_array($CheckComResult)){
                                              $ComStockId = $ComStockId==""?$newComStockId:$ComStockId+1;
					                          $ComRelation = $CheckComRow["Relation"];
					                          $ComStuffId     = $CheckComRow["StuffId"];
					                          $ComOrderQty  = $OrderQty*$ComRelation;
				                              $ComStockQty   = $StockQty*$ComRelation;
					                          $ComFactualQty  = ($AddQty+$FactualQty)*$ComRelation;
                                              if($ComOrderQty>0){
					                                  $IN_recode4="INSERT INTO $DataIn.cg1_stuffcombox (Id,POrderId,mStockId,StockId,mStuffId,StuffId,Relation,OrderQty,StockQty,FactualQty,Date,Operator)VALUES 						                             (NULL,'$POrderId','$StockId','$ComStockId','$StuffId','$ComStuffId','$ComRelation','$ComOrderQty','$ComStockQty','$ComFactualQty','$DateTime','$Operator')";
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
						$Log.="<div class=redB>第 $x 个拆分子单加入失败！</div><br>";
						$OperationResult="N";
						}
					}
				$x++;
				}//end for
			}//end if
		else{
			$Log="<div class='redB'>读取资料失败!</div>";
			$OperationResult="N";
			}
		break;
	case 26:	//重置		OK
		       include "cg_cgdsheet_reset.php";
		break;
                
         case 701://更新采购备注
                $upSql="UPDATE $DataIn.cg1_stocksheet SET StockRemark='$Remark' WHERE Id='$Id'";
                $upSheet=@mysql_query($upSql);
                if ($upSheet){
                   $Log="Y"; 
                }else{
                   $Log="N";  
                }
              break;
		case 702://更新外箱承重
			$Log="N";
			$DateTime=date("Y-m-d H:i:s");
			$Weight=$Weight==""?0:$Weight;
		   if ($Sid!=""){
				$InSql="REPLACE INTO $DataIn.stuff_loadbearing (Id,StuffId,Weight1,Weight2,Date,Operator) SELECT NULL, StuffId,'$Weight','0','$DateTime','$Login_P_Number' FROM $DataIn.stuffdata WHERE StuffId IN($Sid)";
				$InResult=mysql_query($InSql,$link_id);	
				if($InResult){
					   $Log="Y"; 
				   }
               }
            break;              
	 case 134://更新采购备注
		$Log_Funtion="采购解锁";
		$sheetResult = mysql_query("SELECT Y.StockId FROM $DataIn.cg1_stocksheet  Y  WHERE Y.Id='$Id' ",$link_id);
		if($sheetRow = mysql_fetch_array($sheetResult)){
			///////读取原订单资料///////////
			$StockId=$sheetRow["StockId"];		//原订单流水号
			$sql = "SELECT StockId FROM $DataIn.cg1_unlockstock  WHERE StockId='$StockId'";
			$result = mysql_query($sql,$link_id);
			if($stockRow = mysql_fetch_array($result)){
				 $InSql = "delete  FROM $DataIn.cg1_unlockstock  WHERE StockId='$StockId'";
			     $result = mysql_query($InSql,$link_id);
				 $Log=" 需求单 $StockId 重新采购锁定成功.<br>";
				}
			else{
				$InSql="INSERT INTO $DataIn.cg1_unlockstock (Id,StockId,Estate,Locks,Date,Operator)
								VALUES ( NULL, '$StockId','1','0','$Date','$Operator' )";
				//echo "$InSql";		
				$InResult=mysql_query($InSql,$link_id);				
				$Log="<div class=redB>需求单 $StockId 采购解锁设置定成功.</div><br>";
				$OperationResult="N";
				}	
				
		}
	break;
		  
              
	default:
	      $Log_Funtion="采购单更新";
          $MyPDOEnabled=1;
		  include "../basic/parameter.inc";
		  $newAddQty=$newAddQty==""?0:$newAddQty;
		  $newFactualQty =$newFactualQty==""?0:$newFactualQty;
		  
		  $myResult=$myPDO->query("CALL proc_cg1_stocksheet_updated('$Id','$newFactualQty','$newAddQty','$newPrice',
		  '$BuyerId','$CompanyId','$AddRemark','$Operator');");
		  $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		  $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
		  $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
		  $myRow = null;
		  $myResult = null;
	      include "cg_cgdsheet_updated_costprice.php";
		  $Number=$BuyerId;
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId&Number=$Number";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
if ($ActionId>700){
    echo $Log;
    }
else{
    include "../model/logpage.php";
}
?>