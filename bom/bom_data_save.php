<?php
//步骤1
include "../model/modelhead.php";
//步骤2：

$type = $_POST["type"];
if (!$type) {
    $type = $_GET["type"];
}

$fromWebPage = "bom_data_add";
$Log_Item="构件数据";
$nowWebPage="bom_data_save"; //$funFrom."_save"
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&type=$type";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//$Month="2011-04";
$x=0;

if(empty($_FILES['ExcelFile']['tmp_name'])) {
    //没有选择构件资料
    echo "请选择导入资料";
    include "../model/logpage.php";
    return;
    //echo "<script >alert('请选择构件资料(EXCEL)');</script></html>";
}

if ($_POST["lossChk"] || $_POST["mouldChk"] ){
} else {
    //没有选择构件资料
    echo "请选择要导入的信息";
    include "../model/logpage.php";
    return;
}

$cmptNumber =  $_POST["cmptNumber"];
//取项目编号
$proId = $_POST["tradeChoose"];
if ($proId == null) {
    echo "请选择客户项目";
    include "../model/logpage.php";
    return;
}

$mySql="SELECT a.Id, a.Forshort, b.Estate, c.TradeNo FROM $DataIn.trade_object a
INNER JOIN $DataIn.bom_object b on a.Id = b.TradeId 
INNER JOIN $DataIn.trade_info c on a.Id = c.TradeId 
 where a.Id = $proId";

$myResult = mysql_query($mySql, $link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    $TradeNo = $myRow["TradeNo"]; 
    $Estate = $myRow["Estate"];
    if ($Estate == 0 || $Estate == 3 || $Estate == 4) {
    } else {
        echo "项目状态不能导入信息";
        include "../model/logpage.php";
        return;
    }
} else {
    echo "项目资料出错";
    include "../model/logpage.php";
    return;
}
        
//步骤3：需处理
//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/excelfile/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['ExcelFile']['tmp_name'];
$imgname = $_FILES["ExcelFile"]["name"]; //获取上传的文件名称
$filetype = pathinfo($imgname, PATHINFO_EXTENSION);//获取后缀

if ($tmpFile!=""){
    $str_time=time();
    $tmpXmlFile=$Login_P_Number . "_" . $str_time .".".$filetype;
    $PreFileName=$FilePath .$tmpXmlFile;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    //echo iconv("UTF-8","gb2312",$PreFileName);
    if($uploadInfo!=""){
        require_once "phpExcelReader/Excel/reader.php";
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');
        $data->read($PreFileName);
        error_reporting(E_ALL ^ E_NOTICE);
        //echo $data->sheets[0]['cells'][2][3];
        // print_r($rs);

        //损耗信息
        if ($_POST["lossChk"]) {
            $rs = $data -> getExcelResult();

            $RowNum=$data->sheets[0]['numRows'];
            $ColNum=$data->sheets[0]['numCols'];

            for($i=2;$i<=$RowNum;$i++){
                if($rs[$i][2]!=NULL || $rs[$i][2]!=""){
                    $x++;
                }
            }
            
            if ($x != $cmptLossNumber) {
                echo "$cmptLossNumber 损耗数据和设定的数量不一致";
                include "../model/logpage.php";
                return;
            }
            $x = 0;
            //判断构件类型是否存在
            $cmptTypeArr = array();
            for($i=2;$i<=$RowNum;$i++){
                
                if($rs[$i][3]!=NULL || $rs[$i][3]!=""){
                    
                    //构件类别
                    $CmptType = trim($rs[$i][3]);
                    
                    if ($CmptType && $CmptType != "所有") {
                        $typeSql = mysql_query("SELECT TypeId FROM $DataIn.producttype where TypeName='$CmptType' limit 1",$link_id);
                        $TypeId=@mysql_result($typeSql,0,"TypeId");
                        
                        if ($TypeId) {
                            $cmptTypeArr[$CmptType] = $TypeId;
                        } else {
                            echo "损耗导入失败,构件类型($CmptType)不存在";
                            include "../model/logpage.php";
                            return;
                        }
                    } else {
                        $cmptTypeArr[$CmptType] = 0;
                    }
                }
            }
            
            
            //判断配件分类是否存在
            /*
            $stuffTypeArr = array();
            for($i=2;$i<=$RowNum;$i++){
                
                if($rs[$i][3]!=NULL || $rs[$i][3]!=""){
                    
                    //配件分类
                    $StuffType = trim($rs[$i][4]);
                    
                    $typeSql = mysql_query("SELECT TypeId FROM $DataIn.stufftype where TypeName='$StuffType' limit 1",$link_id);
                    $TypeId=@mysql_result($typeSql,0,"TypeId");
                    
                    if ($TypeId) {
                        $stuffTypeArr[$StuffType] = $TypeId;
                    } else {
                        echo "损耗导入失败,配件分类($StuffType)不存在";
                        include "../model/logpage.php";
                        return;
                    }
                }
            }
            */
            //更改为按照物料编码
            $stuffTypeArr = array();
            for($i=2;$i<=$RowNum;$i++){

                if($rs[$i][3]!=NULL || $rs[$i][3]!=""){

                    //配件分类
                    $StuffEname = trim($rs[$i][4]);

                    $typeSql = mysql_query("SELECT StuffEname FROM $DataIn.Stuffdata where StuffEname='$StuffEname' limit 1",$link_id);
                    $TypeId=@mysql_result($typeSql,0,"StuffEname");

                    if ($TypeId) {
                        $stuffTypeArr[$StuffType] = $TypeId;
                    } else {
                        echo "损耗导入失败,配件编码(StuffEname)不存在";
                        include "../model/logpage.php";
                        return;
                    }
                }
            }

            for($i=2;$i<=$RowNum;$i++){

                if($rs[$i][3]!=NULL || $rs[$i][3]!=""){
                    //构件类别
                    $CmptType = trim($rs[$i][3]);
                    $CmptTypeId = $cmptTypeArr[$CmptType];
                    //配件分类
                    $StuffType = trim($rs[$i][4]);
                    //$StuffTypeId = $stuffTypeArr[$StuffType];
                    $StuffIdSql = mysql_query("SELECT StuffId FROM $DataIn.Stuffdata where StuffEname='$StuffType' limit 1",$link_id);
                    $StuffTypeId=@mysql_result($StuffIdSql,0,"StuffId");
                    //单位
                    //$Unit = trim($rs[$i][5]);
                    $Unit = "";
                    //本次标准
                    $ThisStd = trim($rs[$i][6]);
                    //PC定额标准
                    $PcStd = trim($rs[$i][7]);

                    $InSql="INSERT INTO $DataIn.bom_loss(TradeId,
                                CmptTypeId,
                                CmptType,
                                StuffTypeId,
                                StuffType,
                                Unit,
                                ThisStd,
                                PcStd,
                                creator,
                                created
                        )VALUES($proId,
                                '$CmptTypeId',
                                '$CmptType',
                                '$StuffTypeId',
                                '$StuffType',
                                '$Unit',
                                '$ThisStd',
                                '$PcStd',
                                '$Operator',
                                '$DateTime')";
                    //echo $InSql;
                    
                    $InRecode=@mysql_query($InSql);
                    if($InRecode){
                        $x++; 
                        
                        //计算损耗
                        $b=preg_match('/[-+]?[0-9]*\.?[0-9]+/',$ThisStd,$arr);
                        $std = $arr[0];
                        if (strpos($ThisStd, "%")) {
                            $std = $std / 100;
                        }
                        //echo $CmptTypeId,'  ',$StuffTypeId, '   ',$std,"<br>";
                        
                        if ($std) {
                            
                            $UpdateSql="update $DataIn.bom_info set 
                                loss = Quantity * $std
                               where TradeId = $proId
                                and (CmptTypeId = $CmptTypeId or $CmptTypeId = 0)
                                and MaterName = '$StuffTypeId'";
                            
                            //echo $UpdateSql;
                            $InRecode=@mysql_query($UpdateSql);
                        }
                    }
                }
            }
            $Log.="$x 条记录插入bom_loss表中";
            $Log.="<br/>";
        }
        
        ////////////
        //模具信息
        if ($_POST["mouldChk"]) {

            $rs = $data -> getExcelResultBySheetNo(1);
            
            $RowNum=$data->sheets[1]['numRows'];
            $ColNum=$data->sheets[1]['numCols'];
            $x = 0;
            for($i=2;$i<=$RowNum;$i++){
                if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
                    $x++;
                 }
            }
            
            if ($x != $cmptModuleNumber) {
                echo "$x 模具数据和设定的数量不一致";
                include "../model/logpage.php";
                return;
            }
            
            $x = 0;
            for($i=2;$i<=$RowNum;$i++){
                // for($j=1;$j<=$ColNum;$j++){
                
                if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
                    //模具类别
                    $MouldCat=trim($rs[$i][4]);
                    //模具编号
                    $MouldNo=trim($rs[$i][5]);
                    //制作数量
                    $ProQty=trim($rs[$i][6]);
                    //共模比
                    $Ratio=trim($rs[$i][7]);
                    //长
                    $Length=trim($rs[$i][8]);
                    //宽
                    $Width=trim($rs[$i][9]);

                    $InSql="INSERT INTO $DataIn.bom_mould(TradeId,
                    MouldCat,
                    MouldNo,
                    ProQty,
                    Ratio,
                    Length,
                    Width,
                    creator,
                    created
                    )VALUES($proId,
                    '$MouldCat',
                    '$MouldNo',
                    '$ProQty',
                    '$Ratio',
                    '$Length',
                    '$Width',
                    '$Operator',
                    '$DateTime')";
                    $InRecode=@mysql_query($InSql);
                    
                    //echo $InSql;
                    if($InRecode){
                        $x++; 
                    
                        //最大id查找
                        $maxSql = mysql_query("SELECT MAX(id) AS Mid FROM $DataIn.bom_mould",$link_id);
                        $Mid=mysql_result($maxSql,0,"Mid");
    
                        $rowspan=  $data->sheets[1]['cellsInfo'][$i][2]['rowspan'];
                        if (!$rowspan) {
                            $rowspan = 1;
                        }
                        
                        for($j=0;$j<$rowspan;$j++){
                            $BuildingNo=trim($rs[$i + $j][10]);
                            $FloorNo=trim($rs[$i + $j][11]);
                            $CmptNo=trim($rs[$i + $j][12]);
                            
                            $InSql="INSERT INTO $DataIn.bom_mould_data(MouldId,
                            BuildingNo,
                            FloorNo,
                            CmptNo,
                            creator,
                            created
                            )VALUES($Mid,
                            '$BuildingNo',
                            '$FloorNo',
                            '$CmptNo',
                            '$Operator',
                            '$DateTime')";
                            @mysql_query($InSql);
                        }
                    }
                    //echo $i,"   $rowspan<br>";
                    $i = $i + $rowspan - 1;
                    
                    /////////////////////////////////////////////////////////////
                    //扩展 非bom配件资料
                    $GoodsName=$TradeNo . "-" . $MouldNo;
                    
                    $goodsResult = mysql_query("select GoodsId FROM $DataPublic.nonbom4_goodsdata where GoodsName = '$GoodsName' limit 1");
                    if($goodsRow = mysql_fetch_array($goodsResult)){
                        //已经存在配件
                    } else {

                        // 取配件id 最大值加1
                        $maxSql = mysql_query("SELECT MAX(GoodsId) AS MGoodsId FROM $DataPublic.nonbom4_goodsdata",$link_id);
                        $GoodsId=mysql_result($maxSql,0,"MGoodsId");
                        $GoodsId=$GoodsId + 1;
                        
                        $InSql="INSERT INTO $DataPublic.nonbom4_goodsdata(
                            GoodsId,
                            GoodsName,
                            BarCode,
                            TypeId,Attached,AppIcon,Price,Unit,CkId,nxId,pdDate,AssetType,
                            ReturnReasons,Remark,Date,Operator,creator,created
                        )VALUES(
                            $GoodsId,
                            '$GoodsName',
                            '',
                            10,0,0,0.00,'套',0,0,0,3,
                            '','','$Date','$Operator','$Operator','$DateTime'
                         )";
                        @mysql_query($InSql);
                        
                        //取供应商
                        $maxSql = mysql_query("SELECT CompanyId FROM $DataPublic.nonbom3_retailermain limit 1",$link_id);
                        $CompanyId=mysql_result($maxSql,0,"CompanyId");
                        
                        if ($CompanyId) {
                            $InSql="INSERT INTO $DataPublic.nonbom5_goodsstock(
                                GoodsId,wStockQty,oStockQty,CompanyId,Date,Operator
                            )VALUES(
                                $GoodsId,0,0,$CompanyId,'$Date','$Operator'
                            )";
                            @mysql_query($InSql);
                        }
                    }
                    
                }
            }
            $Log.="$x 条记录插入bom_mould表中";
        }
    }
}
else{
    echo "加载EXCEL文件失败！";
}

//步骤4：
include "../model/logpage.php";
?>
