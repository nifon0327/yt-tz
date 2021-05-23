<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$fromWebPage = "cg_cgdsheet_dely_add";
$Log_Item="采购信息数据";
$nowWebPage="cg_cgdsheet_dely_save"; //$funFrom."_save"
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
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

//步骤3：需处理
//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/expressfile/";
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
        
        $rs = $data -> getExcelResult();
        
        $RowNum=$data->sheets[0]['numRows'];
        $ColNum=$data->sheets[0]['numCols'];
        
        $x = 0;
        for($i=2;$i<=$RowNum;$i++){
            
            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
                //PO
                $OrderPO = trim($rs[$i][1]);
                //项目名称
                $TradeName = trim($rs[$i][2]);
                //配件ID
                $StuffId = trim($rs[$i][3]);
                //配件编码
                $StuffNo = trim($rs[$i][4]);
                //含税价
                $Price = trim($rs[$i][5]);
                //税率
                $Tax = trim($rs[$i][6]);
                //配件名称
                $StuffCname = trim($rs[$i][7]);
                //供应商
                $CompanyId = trim($rs[$i][8]);
                //品牌
                $Brand = trim($rs[$i][9]);
                //采购员
                $BuyerId = trim($rs[$i][10]);
                //采购流水号
                $StockId = trim($rs[$i][11]);
                //预定交期
                //$DeliveryDate = trim($rs[$i][12]);



                //供应商
                $providerSql= mysql_query("select a.CompanyId, a.Letter, a.Forshort
                        from $DataIn.trade_object a 
                        where a.ProviderType = 0
                        ORDER BY a.Letter",$link_id);
                if($providerRow = mysql_fetch_array($providerSql)){

                    do{
                        $Letter=$providerRow["Letter"];
                        $Forshort=$providerRow["Forshort"];
                        //$Forshort=$Letter.'-'.$Forshort;
                        $thisCompanyId=$providerRow["CompanyId"];


                        if ($Forshort==$CompanyId){
                            $sgCompanyId = $thisCompanyId;
                        }

                    }while ($providerRow = mysql_fetch_array($providerSql));
                }

                //采购员
                $staffSql= mysql_query("SELECT M.Number,M.Name as staffname FROM $DataPublic.staffmain M  
		                  WHERE M.Estate>0 AND M.BranchId IN (4,110)",$link_id);
                if($staffRow = mysql_fetch_array($staffSql)){

                    do{
                        $pNumber=$staffRow["Number"];
                        $PName=$staffRow["staffname"];
                        if ($PName==$BuyerId){
                            $sgNumber = $pNumber;
                        }

                    }while ($staffRow = mysql_fetch_array($staffSql));

                }




                
                /*$updateSql="update $DataIn.cg1_stocksheet a, $DataIn.stuffdata b
                set a.Price = '$Price', a.CompanyId='$CompanyId', a.BuyerId='$BuyerId', 
                    a.DeliveryDate='$DeliveryDate', b.remark = '$Brand'
                where a.StuffId = b.stuffid and a.StockId = '$StockId' ";*/

                $updateSql = "update $DataIn.cg1_stocksheet set BuyerId='$sgNumber', CompanyId='$sgCompanyId', Price='$Price' where StockId = '$StockId'";
//                $Log.=$updateSql;
                echo $updateSql;
                $InRecode=@mysql_query($updateSql);
                if($InRecode){
                    $x++;
                }
            }
        }
        $Log.="$x 条记录更新cg1_stocksheet表";
    }
}
else{
    echo "加载EXCEL文件失败！";
}

//步骤4：
include "../model/logpage.php";
?>
