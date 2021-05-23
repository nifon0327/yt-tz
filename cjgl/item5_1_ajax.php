<?php
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="送货单审核";			//需处理
$Log_Funtion="状态更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$DateTemp = date("Ymd");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
        case 44: //批量备注
            $Ids = explode(',',$Ids);
            $Date=date("Y-m-d");
            foreach ($Ids as $v) {
                $checkSql=mysql_query("SELECT Id FROM $DataIn.ck6_shremark WHERE ShId='$v' LIMIT 1",$link_id);
                if($checkRow=mysql_fetch_array($checkSql)){//更新
                    $updateSQL = "UPDATE $DataIn.ck6_shremark SET Remark='$Remark',Date='$Date',Operator='$Login_P_Number' WHERE ShId='$v'";
                    $updateResult = mysql_query($updateSQL);
                    if ($updateResult && mysql_affected_rows()>0) echo"Y";
                }
                else{
                    $addRecodes="INSERT INTO $DataIn.ck6_shremark (Id, ShId, Remark, Date, Operator) VALUES (NULL, '$v', '$Remark', '$Date', '$Login_P_Number')";
                    $addAction=@mysql_query($addRecodes);
                    if($addAction)  echo "Y";
                }
            }
        break;
        case 5: //仓管备注
           $Date=date("Y-m-d");
           $checkSql=mysql_query("SELECT Id FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
	     if($checkRow=mysql_fetch_array($checkSql)){//更新
                $updateSQL = "UPDATE $DataIn.ck6_shremark SET Remark='$Remark',Date='$Date',Operator='$Login_P_Number' WHERE ShId='$Id'";
	            $updateResult = mysql_query($updateSQL);
		        if ($updateResult && mysql_affected_rows()>0) echo"Y";
          }
         else{
              $addRecodes="INSERT INTO $DataIn.ck6_shremark (Id, ShId, Remark, Date, Operator) VALUES (NULL, '$Id', '$Remark', '$Date', '$Login_P_Number')";
	          $addAction=@mysql_query($addRecodes);
	          if($addAction)  echo "Y";
           }
            break;
	case 17://审核通过，Estate=2
		$updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Q.Estate=2,Q.Locks=0 WHERE Q.Id in ($Id) ";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			  echo "Y";
	          $Log="<div class=greenB>送货单:" . $Id . "审核成功!</div><br>";
              $In_Sql="INSERT INTO $DataIn.gys_shdate (Id,Sid,shDate)values(NULL,'$Id','$DateTime')";
              $In_result=mysql_query($In_Sql);
              $shIds=$Id;
			}
		else{
		     echo "N";
  	         $Log="<div class=redB>送货单:" . $Id . "审核失败!</div><br>";
	         $OperationResult="N";
	        }
		break;
       case 15://审核退回
           //读取送货资料
		   $CheckSql= mysql_query("SELECT S.Mid,S.sPOrderId,S.StockId,S.StuffId,S.Qty,
		                M.BillNumber,M.CompanyId,M.Date AS SendDate 
                        FROM $DataIn.gys_shsheet S 
                        LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
                        WHERE S.Id in ($Id) ",$link_id);
		   if($CheckRow = mysql_fetch_array($CheckSql)){
                    $Mid=$CheckRow["Mid"];
                    $StockId=$CheckRow["StockId"];
                    $StuffId=$CheckRow["StuffId"];
                    $Qty=$CheckRow["Qty"];
                    $CompanyId=$CheckRow["CompanyId"];
                    $BillNumber=$CheckRow["BillNumber"];
                    $SendDate=$CheckRow["SendDate"];
                    $sPOrderId=$CheckRow["sPOrderId"];

                    $InsertSql="INSERT INTO $DataIn.gys_shback (Id, CompanyId, BillNumber,sPOrderId, StockId, SendDate, StuffId, Qty, remark, Estate, Locks, Date, Operator) VALUES (NULL, '$CompanyId', '$BillNumber','$sPOrderId', '$StockId', '$SendDate', '$StuffId', '$Qty', '$Remark', '2', '0', '$Date', '$Operator');";
                    $InsertRresult = mysql_query($InsertSql);

                    $delSql = "DELETE FROM $DataIn.gys_shsheet WHERE Id in ($Id) ";
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                            echo "Y";
                            $Log.="配件 $StuffId 的需求单 $StockId 待送货记录删除成功!<br>";
                            //主入库单
                            $CheckMidResult = mysql_query("SELECT Mid FROM $DataIn.gys_shsheet WHERE Mid = $Mid",$link_id);

                            if(!mysql_fetch_array($CheckMidResult)){
		                        $delMainSql = "DELETE FROM $DataIn.gys_shmain WHERE Id=$Mid";
	                            $delMianRresult = mysql_query($delMainSql);
	                            if($delMianRresult && mysql_affected_rows()>0){
	                                    $Log.="主入库单已经没有内容，清除成功!<br>";
	                             }
                            }
                     }
                     else{
                        echo "N";
                        $Log="<div class=redB>送货单:" . $Id . "退回失败!</div><br>";
                     }
                }
           break;
    case 80:
        $Mid=0;$j=1;
        $checkArray=explode("|",$AddIds);
        $Lens=count($checkArray);

        $MaxBillNumberResult  = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS MaxBillNumber FROM $DataIn.gys_shmain WHERE BillNumber LIKE'$DateTemp%'",$link_id));
        $MaxBillNumber  = $MaxBillNumberResult["MaxBillNumber"];
        if($MaxBillNumber){
	        $MaxBillNumber = $MaxBillNumber+1;
        }else{
	        $MaxBillNumber = $DateTemp."0001";
        }

        for($i=0;$i<$Lens;$i++){
	        $ValueArray=explode("_",$checkArray[$i]);
	        $StuffId=$ValueArray[0];
	        $QtyArray=explode("^^",$ValueArray[1]);


	        $SumQty=$QtyArray[0];

	        //获取配件送货楼层
	        $floorResult = mysql_fetch_array(mysql_query("SELECT SendFloor 
	        FROM $DataIn.stuffdata WHERE StuffId='$StuffId' LIMIT 1",$link_id));
	        $floor = $floorResult["SendFloor"]==""?0:$floorResult["SendFloor"];

	        if($SumQty>0){
	         //检查该配件全部未收货的记录
		        $checkSql=mysql_query("select * from(SELECT  S.Id,S.StockId,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryWeek 
		        FROM $DataIn.cg1_stocksheet S
	           	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId	
		        WHERE 1 
		        AND S.StuffId='$StuffId' 
		        AND S.rkSign>0 
		        AND S.CompanyId='$CompanyId2' 
	            AND (S.Mid>0   AND (S.FactualQty  + S.AddQty) >0 )
	            UNION ALL 
	            SELECT S.Id,M.StockId,  (M.AddQty + M.FactualQty) AS Qty ,S.DeliveryWeek
			    FROM  $DataIn.cg1_stuffcombox  M 
                LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StockId =M.mStockId
			    WHERE 1 
			    AND M.StuffId ='$StuffId'
			    AND S.CompanyId='$CompanyId2' 
			    AND (S.Mid>0  AND  (M.FactualQty  + M.AddQty) >0)
			    AND S.rkSign >0	) as E	   
		        ORDER BY if(E.DeliveryWeek=0,'999999',E.DeliveryWeek)",$link_id);
	           if($checkRow=mysql_fetch_array($checkSql)){
		        do{

			      $StockId=$checkRow["StockId"];
			      $Qty=$checkRow["Qty"];
			       //已收货总数
			      $rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet 
			      WHERE StuffId='$StuffId' AND StockId=$StockId",$link_id);
			      $rkQty=mysql_result($rkTemp,0,"Qty");
			      $rkQty=$rkQty==""?0:$rkQty;

			       //待送货数量
			      $shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet 
			      WHERE 1 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId",$link_id);
			      $shQty=mysql_result($shSql,0,"Qty");
			      $shQty=$shQty==""?0:$shQty;

			      $NoQty=$Qty-$rkQty-$shQty;  //减掉未送货的单，省得出错
			      if($NoQty>0 && $SumQty>0){//该单未送完货
				      if($Mid==0){//如果没生成主送货单就先生成主送货单
					     $inRecode="INSERT INTO $DataIn.gys_shmain  
					     (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,creator,created) 
 VALUES (NULL,'$MaxBillNumber','$TempGysNumber','$CompanyId2','1','$Date','$Remark','$floor','$Login_P_Number','$DateTime')";
					      $inAction=@mysql_query($inRecode);
					      $Mid=mysql_insert_id();
				       }
				      if($Mid>0){
						   //分析：送货数量与该数量的比较
						   if($SumQty>=$NoQty ){//可以全部送货
							    $SumQty-=$NoQty;
							    echo "$j - 全部送货 $StockId - $NoQty <br>";
							    $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks) 
							    VALUES (NULL,'$Mid','','$StockId','$StuffId','$NoQty','0','1','1')";
							    $addAction=@mysql_query($addRecodes);
							    }
						    else{//部分送货
							    $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks) 
							    VALUES (NULL,'$Mid','','$StockId','$StuffId','$SumQty','0','1','1')";
							    $addAction=@mysql_query($addRecodes);
							    break;//当该送货数量已经分配完，则跳出
							    }
						  }
					  }
			        $j++;
			      }while($checkRow=mysql_fetch_array($checkSql));
		        }
	         }



	     //*****************************************************取得补货总数
	      $tmpBSQty=$QtyArray[1];
	      if($tmpBSQty>0 ) {
		      //退货的总数量
		      $thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
							   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
						       WHERE M.CompanyId = '$CompanyId2' AND S.StuffId = '$StuffId'",$link_id);
		      $thQty=mysql_result($thSql,0,"thQty");
		      $thQty=$thQty==""?0:$thQty;
		      //补货的数量
		      $bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
						    LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
							WHERE M.CompanyId = '$CompanyId2' AND S.StuffId = '$StuffId' ",$link_id);
		      $bcQty=mysql_result($bcSql,0,"bcQty");
		      $bcQty=$bcQty==""?0:$bcQty;
		      //待送货数量
		      $shQty=0;

			  $shSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
			  LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
			  WHERE 1 AND M.CompanyId = '$CompanyId2' 
			  AND S.Estate>0 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id);

		      $shQty=mysql_result($shSql,0,"Qty");
		      $shQty=$shQty==""?0:$shQty;

		      $webQty=$thQty-$bcQty-$shQty; //未补数量
		      if($tmpBSQty>$webQty){
			      $tmpBSQty=$webQty;  //最多只能送未补数量
	          }

		     if($Mid==0){//如果没生成主送货单就先生成主送货单
			    $inRecode="INSERT INTO $DataIn.gys_shmain 
			    (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,creator,created) 
				VALUES (NULL,'$MaxBillNumber','$TempGysNumber','$CompanyId2','1','$Date','',$floor,'$Login_P_Number','$DateTime')";
			    $inAction=@mysql_query($inRecode);
			    $Mid=mysql_insert_id();
		      }
		   if($Mid!=0){//可以全部补货
			     $addRecodes="INSERT INTO $DataIn.gys_shsheet  
			     (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks)
			    VALUES (NULL,'$Mid','','-1','$StuffId','$tmpBSQty','1','1','1')";   //SendSign: 1补货
			     $addAction=@mysql_query($addRecodes);
		       }
	    }



	    //*****************************************************取得备品总数
	    $tmpBPQty=$QtyArray[2];

	    if($tmpBPQty>0) {
		    if($Mid==0){
			  $inRecode="INSERT INTO $DataIn.gys_shmain 
			  (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,creator,created) 
		      VALUES (NULL,'$MaxBillNumber','$TempGysNumber','$CompanyId2','1','$Date','',$floor,'$Login_P_Number','$DateTime')";
			  $inAction=@mysql_query($inRecode);
			  $Mid=mysql_insert_id();
		     }

		    if($Mid!=0){
			    $addRecodes="INSERT INTO $DataIn.gys_shsheet  
			    (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks)
			    VALUES (NULL,'$Mid','','-2','$StuffId','$tmpBPQty','2','1','1')";   //2备品
			    $addAction=@mysql_query($addRecodes);
		       }
	        }
        }

        $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
         //上传文件
        $filename=$_FILES["fileinput"]["name"];
        if($filename!=""){//有上传文件
                $FileType=".jpg";
                $FilePath="../download/ckshbill/";
                if(!file_exists($FilePath)){
                        makedir($FilePath);
                        }
                $PreFileName="S".$Mid.$FileType;
                $copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath" . "$PreFileName");
                if($copymes){
                        $alertLog.= ";送货单据上传成功";
                        $Log.="&nbsp;&nbsp;送货单据上传成功！<br>";
                        }
                else{
                        $alertErrLog.= ";送货单据上传失败";
                        $Log.="<div class=redB>&nbsp;&nbsp;送货单据上传失败！</div><br>";
                        $upFileResult="N";
                   }
         }
	 break;
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
if ($ActionId==80){
	if ($OperationResult=="N"){
        echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
       }
   else{
	    echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog' );parent.closeWinDialog();parent.ResetPage(1,5);</script>";
       }
 }
?>