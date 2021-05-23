<?php

include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$OrderIdDate=date("Ymd");
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OrderNumber=0;
@$proId = addslashes($_POST['proId']);

$mySql="select Estate,CreateOrder from $DataIn.bom_object WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];
    $CreateOrder = $myRow["CreateOrder"];
    
    //审核
    if ($Estate != 2) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目审核状态不对'
        ));
        return;
    }
    
}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '数据错误,请重新检索'
    ));
    return;
}



//申购单
$result = mysql_query("select t.TradeNo,b.TypeId, a.MouldNo, SUM(a.ProQty) as ProQty
        from $DataIn.bom_mould a
        LEFT JOIN producttype b on b.typename = a.MouldCat
        LEFT JOIN trade_info t on a.TradeId= t.TradeId
        where a.TradeId = $proId
        group by a.MouldCat, a.MouldNo");

if($result && $myRow = mysql_fetch_array($result)){
    
    //供应商 查询第一个
    $checkResult = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
    if ($checkRow = mysql_fetch_array($checkResult)){
        $CompanyId=$checkRow["CompanyId"];
    }
    
    $Unit = "副";
    
    do {
        //$TypeId = $myRow["TypeId"]; //分类
         $TypeId = '22'; //设置为模台对应的生产分类 by.h 180420
        $MouldNo = "[".$myRow["TradeNo"]."]".$myRow["MouldNo"];
        $ProQty = $myRow["ProQty"];
        $TradeNo = $myRow["TradeNo"];
        
        //检查模具 申购商品
        $checkSql1=mysql_query("SELECT A.GoodsId,A.Price,A.Unit,A.TypeId,B.wStockQty,B.oStockQty,B.mStockQty,B.CompanyId, C.mainType,C.BuyerId,E.AddTaxValue
                FROM $DataPublic.nonbom4_goodsdata A
                LEFT JOIN $DataPublic.nonbom5_goodsstock  B ON B.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
                LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=D.CompanyId
                WHERE A.GoodsName='$MouldNo' LIMIT 1",$link_id);
        if($checkRow1=mysql_fetch_array($checkSql1)){
        } else {
            //加入 商品
            $maxSql = mysql_query("SELECT MAX(GoodsId) AS MGoodsId FROM $DataPublic.nonbom4_goodsdata",$link_id);
            $GoodsId=mysql_result($maxSql,0,"MGoodsId");
            $GoodsId=$GoodsId + 1;
            
            $InSql="INSERT INTO $DataPublic.nonbom4_goodsdata(
            GoodsId,
            GoodsName,
            BarCode,
            TypeId,
            Attached,Price,Unit,CkId,nxId,pdDate,ReturnReasons,Remark,
            Date,Operator
            )VALUES(
            $GoodsId,
            '$MouldNo',
            '',
            $TypeId,
            0,0,'$Unit',0,0,0,'','$TradeNo',
            '$Date','$Operator')";

            $InRecode=@mysql_query($InSql);
            
            //属性
            $InSql="INSERT INTO $DataPublic.nonbom5_goodsstock(GoodsId,
            wStockQty,oStockQty,CompanyId,Date,Operator
            )VALUES($GoodsId,
            0,0,$CompanyId,'$Date','$Operator')";
            
            $InRecode=@mysql_query($InSql);
        }

    }while($myRow = mysql_fetch_array($result));
}

$checkSql2=mysql_query("SELECT A.GoodsId,A.Price,A.Unit,A.TypeId,B.wStockQty,B.oStockQty,B.mStockQty,B.CompanyId, C.mainType,C.BuyerId,E.AddTaxValue
                FROM $DataPublic.nonbom4_goodsdata A
                LEFT JOIN $DataPublic.nonbom5_goodsstock  B ON B.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
                LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=D.CompanyId
                WHERE A.Remark='$TradeNo' ",$link_id);
if($checkSql2 && $checkRow2 = mysql_fetch_array($checkSql2)) {
    do {
            $GoodsId = $checkRow2["GoodsId"];
            $Price = $checkRow2["Price"];
            $Unit = $checkRow2["Unit"];
            $CompanyId = $checkRow2["CompanyId"];

            $sheetInSql = "INSERT INTO $DataIn.nonbom6_cgsheet
        (Id,Mid,fromMid,qkId,mainType,GoodsId,CompanyId,BuyerId,Qty,Price,AddTaxValue,Remark,ReturnReasons,rkSign,Estate,Locks,Date,Operator)
        VALUES
        (NULL,'0',0,'0','$TypeId','$GoodsId','$CompanyId','10024','$ProQty','0','0','$TradeNo','','1','2','1','$Date','$Operator') ";
            if ($sheetInAction = @mysql_query($sheetInSql)) {
                $OrderNumber = $OrderNumber + 1;
            }
            else
            {
                break;
            }
    } while ($checkRow2 = mysql_fetch_array($checkSql2));
}

if  ($OrderNumber>0)
{
    echo json_encode(array(
        'rlt'=> true,
        'msg'=> '模具申购数量为：'.$OrderNumber
        ) );
}
else
{
    echo json_encode(array(
    'rlt'=> false,
    'msg'=> '模具申购出错！'
) );
}



