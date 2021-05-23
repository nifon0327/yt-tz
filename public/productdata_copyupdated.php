<?php 
/*电信---yang 20120801
$DataIn.productdata
$DataIn.pands
$DataIn.yw1_ordersheet 
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_copyupdated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品资料";//需处理
$Log_Funtion="批量复制资料";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;

$targetProductSql = "SELECT * FROM $DataIn.productdata WHERE Id in ($targetid)";
$targetProdcutResult = mysql_query($targetProductSql);
while($targetRow = mysql_fetch_assoc($targetProdcutResult)){
    $id = $targetRow['id'];
    $cName=trim($targetRow['cName']);//去连续空格,去首尾空格
    $eCode=trim($targetRow['eCode']);
    $Price=FormatSTR($targetRow['Price']);
    $Unit=FormatSTR($targetRow['Unit']);
    $Remark=FormatSTR($targetRow['Remark']);
    $pRemark=FormatSTR($targetRow['pRemark']);
    $Description=FormatSTR($targetRow['Description']);
    $Code=Chop(trim($targetRow['Code']));
    $Date=date("Y-m-d"); 
    $TypeId = $targetRow['TypeId'];
    $ProductId = $targetRow['ProductId'];
    $MainWeight = $targetRow['MainWeight'];
    $Weight = $targetRow['Weight'];
    $PackingUnit = $targetRow['PackingUnit'];

    $maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $DataIn.productdata",$link_id);
    $theProductId=mysql_result($maxSql,0,"Mid");
    if($theProductId){
        $theProductId=$theProductId+1;
    }
    else{
        $theProductId=80001;
    }

    $inRecode="INSERT INTO $DataIn.productdata
                (Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,MainWeight,Weight,CompanyId,Description,Remark,pRemark,bjRemark,LoadQty,TestStandard,Img_H,Date,PackingUnit,dzSign,Estate,Locks,Code,Operator, productsize, ReturnReasons)           
                VALUES(NULL,'$theProductId','$cName','$eCode','$TypeId','$Price','$Unit','0','$MainWeight','$Weight','$CompanyId7','$Description','$Remark','$pRemark','$bjRemark','0','0','0','$Date','$PackingUnit','0','1','0','$Code','$Operator', '$productsize', '$ReturnReasons')";
    $inAction=@mysql_query($inRecode);
    //exit();
    //解锁表
    //$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
    if ($inAction){ 
        $Log.="产品基本资料($ProductId -> $theProductId)复制成功!<br>";
    } 
    else{ 
        $Log.="<div class=redB>产品基本资料($ProductId -> $theProductId)复制失败(检查中文名是否有重复)! $inRecode</div><br>";
        $OperationResult="N";
        break;
    } 
    ////////////////////////////////////////////////////////////////ProductId
    //复制BOM表
    $inRecode="INSERT INTO $DataIn.pands SELECT NULL,'$theProductId',StuffId,Relation,Diecut,Cutrelation,bpRate,Date,Operator,1,0,0,Operator,NOW(),Operator,NOW() FROM $DataIn.pands WHERE ProductId='$ProductId'";
    $inAction=@mysql_query($inRecode);
    if ($inAction){ 
        $Log.="产品BOM($ProductId -> $theProductId)复制成功!<br>";
    } 
    else{ 
        $Log.="<div class=redB>产品BOM($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
        $OperationResult="N";
    }
    //复制标准图
    if($TestStandard==1){
        $OldFile="T".$ProductId.".jpg";
        $NewFile="T".$theProductId.".jpg";
        if (!copy("../data$Login_cSign/teststandard/$OldFile", "../data$cSign/teststandard/$NewFile")) {
            $Log.="<div class=redB>产品标准图($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
            $OperationResult="N";
        }
        else{
            $Log.="产品标准图($ProductId -> $theProductId)复制成功";
        }
    }

}


//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&CompanyId=$CompanyId&TypeId=$TypeId&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>