<?php
//模具信息 生成 非bom配件资料
$mySql = "select DISTINCT a.Id, a.TradeId, a.MouldCat, a.MouldNo, a.ProQty, a.Ratio,
a.Length, a.Width, b.TradeNo, c.Forshort
from $DataIn.bom_mould a
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
where a.TradeId = $proId ";

$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    
    $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort limit 1",$link_id);
    if ($CompanyRow = mysql_fetch_array($CompanySql)){
        $CompanyId=$CompanyRow["CompanyId"];
    }
    if ($CompanyId == "") $CompanyId="60001";
    $TypeId=22;

    do {
        $TradeNo = $myRow['TradeNo'];
        $MouldNo = $myRow['MouldNo'];
        
        $GoodsName=FormatSTR($TradeNo."-".$MouldNo);
        $chinese=new chinese;
        $Letter=substr($chinese->c($GoodsName),0,1);
        
        //echo $GoodsName, "<br>";
        
        $checkResult = mysql_query("select GoodsId FROM $DataPublic.nonbom4_goodsdata where GoodsName='$GoodsName' limit 1");
        if($checkRow = mysql_fetch_array($checkResult)){
            //已经存在配件

        } else {

            $maxSql = @mysql_query("SELECT MAX(GoodsId) AS GoodsId FROM $DataPublic.nonbom4_goodsdata ORDER BY GoodsId DESC LIMIT 1",$link_id);
            $GoodsId=@mysql_result($maxSql,0);
            if($GoodsId){
                $GoodsId=$GoodsId+1;
            } else{
                $GoodsId=70001;
            }
            
            $inRecode="INSERT INTO $DataPublic.nonbom4_goodsdata (Id,TradeId,GoodsId,GoodsName,BarCode,TypeId,Attached,Price,Unit,CkId,nxId,pdDate,
            ByNumber,ByCompanyId,WxNumber,WxCompanyId,AssetType,DepreciationId,Salvage,ReturnReasons,Remark,GetSign,GetQty,Date,Estate,Locks,Operator) VALUES (
             NULL,'$proId','$GoodsId','$GoodsName','','$TypeId','0','0','套','0','0','0',
             '0','0','0','0','3','0','0.05','','','0','0','$Date','2','0','$Operator')";
            $inAction=@mysql_query($inRecode);
            //echo $inRecode, "<br>";
            $Id=mysql_insert_id();
            if ($inAction){ 
                //属性
                //$inSql3="INSERT INTO $DataPublic.nonbom4_goodsproperty(Id,GoodsId,Property)VALUES(NULL,'$GoodsId','$Property[$k]')";
                //$inRes3=@mysql_query($inSql3);
                
                //写入库存表
                $inRecode2="INSERT INTO $DataPublic.nonbom5_goodsstock (Id,GoodsId,wStockQty,lStockQty,oStockQty,mStockQty,CompanyId) VALUES 
                (NULL,'$GoodsId','0','0','0','0','$CompanyId')";
                $inRes2=@mysql_query($inRecode2);
            }
        }
    } while ($myRow = mysql_fetch_array($result));
}