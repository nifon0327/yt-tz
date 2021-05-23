<?php 
//电信-zxq 2012-08-01
/*
//传入参数：CId｜LinkId｜Fsource
*/
include "../basic/parameter.inc";
include "subprogram/createXML.php";
//步骤2：
$Log_Item="客户送货单数据远程写入";			//需处理
$Log_Funtion="保存";
$TitleSTR=$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");

$OperationResult="Y";
$Log="";
//步骤3：需处理
$outResult=mysql_query("SELECT ClientId,IP,dataUrl,LinkId FROM $DataIn.cg_outLink WHERE CompanyId='$CId' AND LinkId='$LinkId' AND Sign='in' AND Estate=1 LIMIT 1",$link_id); 
if($outRow = mysql_fetch_array($outResult)){
     $ClientId=$outRow["ClientId"];
     $getIP=$outRow["IP"];
     $dataUrl=$outRow["dataUrl"];
       
 $url="http://" . $getIP . "/"  . $Fsource . ".xml";

 $filePath="../" . $dataUrl;
 if(!file_exists($filePath)){
    makedir($filePath);
 }
 
 $fileName = end(explode('/',$Fsource));
 $fileName=$CId . "_" . $fileName . ".xml";
 $filePath=$filePath . $fileName;
 
 $arrData=readXML($url,$filePath);
 $Counts=count($arrData);
 if ($Counts>0){
   //插入数据库
  $myCompanyId=$CId;
  $Date=date("Y-m-d");
  foreach( $arrData as $data){
      $ProductId=$data['ProductId'];
      $StuffId=$data['StuffId'];
      $SumQty=$data['Qty'];
       //检查该配件全部未收货的记录
      $checkSql=mysql_query("SELECT StockId,(AddQty+FactualQty) AS Qty FROM $DataIn.cg1_stocksheet WHERE 1 AND StuffId=$StuffId AND Mid>0 AND rkSign>0 AND CompanyId=$myCompanyId ORDER BY Id",$link_id);
      if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$StockId=$checkRow["StockId"];
			$Qty=$checkRow["Qty"];
			//已收货总数
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId=$StockId",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;

			//待送货数量
			$shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId",$link_id);  
			$shQty=mysql_result($shSql,0,"Qty");
			$shQty=$shQty==""?0:$shQty;
			//$noQty=$cgQty-$rkQty-$shQty;

			$NoQty=$Qty-$rkQty-$shQty;  //减掉未送货的单，省得出错
			//$NoQty=$Qty-$rkQty;
			if($NoQty>0 && $SumQty>0){//该单未送完货
				if($Mid==0){//如果没生成主送货单就先生成主送货单
					$maxSql = mysql_query("SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.gys_shmain WHERE CompanyId=$myCompanyId",$link_id);
					$BillNumber=mysql_result($maxSql,0,"BillNumber");
					if($BillNumber){
						$BillNumber=$BillNumber+1;
						}
					else{
						$BillNumber=$myCompanyId."0001";//默认
						}
					$inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,CompanyId,Locks,Date,Remark) VALUES (NULL,'$BillNumber','$myCompanyId','1','$DateTime','')";
					$inAction=@mysql_query($inRecode);
					$Mid=mysql_insert_id();
				}
				//分析：送货数量与该数量的比较
				if($SumQty>=$NoQty && $Mid!=0){//可以全部送货
					$SumQty-=$NoQty;
					echo "$ProductId - 全部送货 $StockId - $NoQty </br>";
					$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$NoQty','0','1','1')";   //SendSign: 0送货，1补货, 2备品  
					$addAction=@mysql_query($addRecodes);
					}
				else{//部分送货
					echo "$ProductId - 全部送货 $StockId - $SumQty </br>";
					$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$SumQty','0','1','1')";  //SendSign: 0送货，1补货, 2备品  
					$addAction=@mysql_query($addRecodes);
					break;//当该送货数量已经分配完，则跳出
					}
				}else{
                                        echo "<div style='color:#F00;'>$ProductId - 送货资料有误(客户系统显示已全部收货)! </div></br>";   
                                }
			$j++;
		      }while($checkRow=mysql_fetch_array($checkSql));
	        }//if($checkRow=mysql_fetch_array($checkSql))
            else{
                echo "<div style='color:#F00;'> $ProductId - 送货资料有误(客户系统应收货的产品不存在)！</div></br>";
             }
     
         }//foreach( $arrData as $data)
    
      }//if ($Counts>0)
     else{
          echo "<div style='color:#F00;'>" . $Fsource . ".xml - 送货资料有误（文件有误）！</div></br>";
     }
 } 
?>
