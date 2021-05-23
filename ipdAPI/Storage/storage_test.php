<?php
    
    include_once "../../basic/parameter.inc";
    include_once "../../model/modelfunction.php";
    
    header("Content-Type: text/html; charset=utf-8");
    header("expires:mon,26jul199705:00:00gmt");
    header("cache-control:no-cache,must-revalidate");
    header("pragma:no-cache");
    
    $Log_Item="送货单审核";          //需处理
    $Log_Funtion="状态更新";
    
    $DateTime=date("Y-m-d H:i:s");
    $Date=date("Y-m-d");
    $Operator = $_POST["Login_P_Number"];
    $Operator = "11008";
    
    $CompanyId2 = $_POST["TempCompanyId"];
    $CompanyId2 = "2746";
    $TempBillNumber = $_POST["TempBillNumber"];
    $TempBillNumber = "570006872805";
    $Remark = $_POST["Remark"];
    $AddIds = $_POST["AddIds"];
    $AddIds = "148728_1500^^0^^0";
    
    $operationFlag = "Y";
    $info = "";
    
    $Mid=0;$j=1;
    $checkArray=explode("|",$AddIds);
    $Lens=count($checkArray);
    for($i=0;$i<$Lens;$i++)
    {
        $ValueArray=explode("_",$checkArray[$i]);
        $StuffId=$ValueArray[0];
        $QtyArray=explode("^^",$ValueArray[1]);
        $SumQty=$QtyArray[0];
        
        //获取配件送货楼层
        $floorResult = mysql_fetch_assoc(mysql_query("Select SendFloor From $DataIn.stuffdata Where StuffId='$StuffId'"));
        $floor = $floorResult["SendFloor"] == ""?"0":$floorResult["SendFloor"];
        
        
        
        if($SumQty>0)
        {
            //检查该配件全部未收货的记录
            $checkSql=mysql_query("SELECT S.StockId,(S.AddQty+S.FactualQty) AS Qty 
                                   FROM $DataIn.cg1_stocksheet S
                                   LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                                   LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId= A.StuffId AND OP.Property=2
                                   WHERE 1 
                                   AND S.StuffId='$StuffId' 
                                   AND S.rkSign>0 
                                   AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0)))  
                                   AND S.CompanyId='$CompanyId2' 
                                   ORDER BY S.Id",$link_id);
            

            if($checkRow=mysql_fetch_array($checkSql))
            {
                do
                {
                    $StockId=$checkRow["StockId"];
                    $Qty=$checkRow["Qty"];
                    //$floor = $checkRow["SendFloor"];
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
                    
                    //采购总数
                    //已购总数
                    $cgTemp=mysql_query("SELECT SUM(OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty 
                                         FROM $DataIn.cg1_stocksheet S
                                         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                                         LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId= A.StuffId AND OP.Property=2
                                         WHERE S.CompanyId='$CompanyId2' 
                                         AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0)))
                                         and S.StuffId='$StuffId'",$link_id);
                                         
                    $cgQty=mysql_result($cgTemp,0,"Qty");
                    $cgQty=$cgQty==""?0:$cgQty;
                    $odQty=mysql_result($cgTemp,0,"odQty");
                    $odQty=$odQty==""?0:$odQty;
                    
                    //$NoQty=$cgQty-$rkQty-$shQty;
                    $NoQty=$Qty-$rkQty-$shQty;  //减掉未送货的单，省得出错              
                    if($NoQty>0 && $SumQty>0)
                    {
                        //该单未送完货
                        if($Mid==0)
                        {
                            //如果没生成主送货单就先生成主送货单
                            $inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,CompanyId,Locks,Date,Remark,Floor) 
                            VALUES (NULL,'$TempBillNumber','$CompanyId2','1','$DateTime','$Remark','$floor')";
                            $inAction=mysql_query($inRecode);
                            $Mid=mysql_insert_id();
                         }
                         //分析：送货数量与该数量的比较
                         if($SumQty>=$NoQty && $Mid!=0)
                         {
                             //可以全部送货
                             $SumQty-=$NoQty;
                             $info = "$j - 全部送货 $StockId - $NoQty";
                             $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) 
                             VALUES (NULL,'$Mid','$StockId','$StuffId','$NoQty','0','1','1')";   //SendSign: 0送货，1补货, 2备品  
                             $addAction=mysql_query($addRecodes);
                             if(!$addAction)
                             {
                                echo addRecodes."<br>";
                                $operationFlag = "N";
                             }
                         }
                         else
                         {
                            //部分送货
                            $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) 
                            VALUES (NULL,'$Mid','$StockId','$StuffId','$SumQty','0','1','1')";  //SendSign: 0送货，1补货, 2备品  
                            $addAction=mysql_query($addRecodes);
                            if(!$addAction)
                            {
                                echo addRecodes."<br>";
                                $operationFlag = "N";
                            }

                            break;//当该送货数量已经分配完，则跳出
                         }
                    }
                    $j++;
                  }while($checkRow=mysql_fetch_array($checkSql));
                }
             }  //if($SumQty>0 )
         //*****************************************************取得补货总数
          $tmpBSQty=$QtyArray[1];  
          //退货的总数量 
          $thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
                                   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
                                   WHERE M.CompanyId = '$CompanyId2' AND S.StuffId = '$StuffId' ",$link_id);
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
          /*
          $shSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
                        LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
                        WHERE 1 AND M.CompanyId = '$CompanyId2' 
                        AND S.Locks=1 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id);  
          */
          $shSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
                        LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
                        WHERE 1 AND M.CompanyId = '$CompanyId2' 
                        AND S.Estate>0 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id); 
          
          $shQty=mysql_result($shSql,0,"Qty");
          $shQty=$shQty==""?0:$shQty;   
    
          $webQty=$thQty-$bcQty-$shQty; //未补数量  
          //echo "$webQty=$thQty-$bcQty-$shQty"; //未补数量 
          if($tmpBSQty>$webQty){
              $tmpBSQty=$webQty;  //最多只能送未补数量
                         }
         if($tmpBSQty>0 ) {     //
             if($Mid==0){//如果没生成主送货单就先生成主送货单
                  $inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,CompanyId,Locks,Date,Remark,Floor) 
                        VALUES (NULL,'$TempBillNumber','$CompanyId2','1','$DateTime','','$floor')";
                  $inAction=mysql_query($inRecode);
                  if(!$inAction)
                            {
                                $operationFlag = "N";
                            }
                  $Mid=mysql_insert_id();
              }
           if($Mid!=0){//可以全部补货
                 $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks)
                         VALUES (NULL,'$Mid','-1','$StuffId','$tmpBSQty','1','1','1')";   //SendSign: 0送货，1补货, 2备品  
                 $addAction=mysql_query($addRecodes);
                 if(!$addAction)
                            {
                                $operationFlag = "N";
                            }
               }        
        }
      //*****************************************************取得备品总数
      $tmpBPQty==$QtyArray[2];  
      if($tmpBPQty>0) {     //
        if($Mid==0){
            $inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,CompanyId,Locks,Date,Remark,Floor) 
                        VALUES (NULL,'$TempBillNumber','$CompanyId2','1','$DateTime','','$floor')";
            $inAction=mysql_query($inRecode);
            if(!$inAction)
            {
                $operationFlag =  "N";
            }
            $Mid=mysql_insert_id();
           }
        if($Mid!=0){//可以全部补货
            $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks)
                         VALUES (NULL,'$Mid','-2','$StuffId','$tmpBPQty','2','1','1')";   //SendSign: 0送货，1补货, 2备品  
            $addAction=mysql_query($addRecodes);
            if(!$addAction)
            {
                $operationFlag =  "N";
            }
          }             
       }
    }
            
        $alertLog=$Log_Item . "数据保存成功";
        $alertErrLog=$Log_Item . "数据保存失败";
        //上传文件
        $filename=$_FILES["fileinput"]["name"]; 
        if($filename!=""){//有上传文件
                $FileType=".jpg";
                $FilePath="../../download/ckshbill/";
                if(!file_exists($FilePath)){
                        makedir($FilePath);
                        }
                $PreFileName="S".$Mid.$FileType;    
                $copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath" . "$PreFileName"); 
                if($copymes){
                        $info.= ";送货单据上传成功";
                        $Log.="&nbsp;&nbsp;送货单据上传成功！<br>";
                        }
                else{
                        $info.= ";送货单据上传失败";
                        $Log.="<div class=redB>&nbsp;&nbsp;送货单据上传失败！</div><br>";
                        $upFileResult="N";          
                        }
         }

    //步骤4：
    $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES     ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res=@mysql_query($IN_recode);
   
   echo $operationFlag."&&".$info;
           
?>