<?php 
//电信-zxq 2012-08-01
/*header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul201105:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
 * 
 */
//include "../../basic/parameter.inc";
//include "../model/modelhead.php";
include "subprogram/createXML.php";
//获取供应商公司资料
 //$PurchaseID='20110106';$CompanyId="2270";
if ($PurchaseID!="" && $CompanyId!=""){
    $outResult=mysql_query("SELECT ClientId,IP,toUrl,dataUrl,LinkId FROM $DataIn.cg_outlink WHERE CompanyId='$CompanyId' AND Sign='out' AND Estate=1 LIMIT 1",$link_id); 
    if($outRow = mysql_fetch_array($outResult)){
       $ClientId=$outRow["ClientId"];
       $toIP=$outRow["IP"];
       $toUrl=$outRow["toUrl"];
       $dataUrl=$outRow["dataUrl"];
       $LinkId=$outRow["LinkId"];
       
       $cgSql=mysql_query("SELECT M.PurchaseID,M.CompanyId,S.StockId,S.StuffId,F.StuffCname,S.POrderId AS Pid,P.cName AS Pname,S.FactualQty AS Qty,S.Price,A.Name AS Buyer,M.Date   
            FROM $DataIn.cg1_stocksheet S 
            LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
            LEFT JOIN $DataIn.stuffdata F ON F.StuffId=S.StuffId 
            LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
            LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
            LEFT JOIN $DataPublic.staffmain A ON A.Number=M.BuyerId  
            WHERE M.PurchaseID='$PurchaseID'",$link_id); 
      if ($cgRow=mysql_fetch_assoc($cgSql)){
          $stocks=array();
       do{
          $stocks[]=$cgRow;
        }while($cgRow=mysql_fetch_assoc($cgSql));
        
        $xmlFileName=$CompanyId . "_" . $PurchaseID;
        $filePath="../" . $dataUrl;
        if(!file_exists($filePath)){
	    makedir($filePath);
	}
        $filePath=$filePath . $xmlFileName . ".xml";
        $retResult=createXML($stocks,'MC_ORDER|Order',$filePath);//创建XML文件
        
        if ($retResult){
            $url="http://" . $toIP . "/" . $toUrl;
            $url.="?Fsource=" . $dataUrl . $xmlFileName  . "&CId=" .  $ClientId . "&LinkId=" .  $LinkId; 
            for ($n=0;$n<3;$n++){
                $html=file_get_contents(iconv("UTF-8","GB2312",$url));
                if ($html){break;}
                sleep(2);
             }  
            if ($html){$Log.=$html;}else{$Log.="<div style='color:#F00;'>给客户系统下采购单失败！网络连接有误！</div></br>";}
            }
        else{
             $Log.="<div style='color:#F00;'>创建XML文件($filePath)失败!" . "</div></br>";
           }
      }
     // echo $Log;
   }
}
?>
