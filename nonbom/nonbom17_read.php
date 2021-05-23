<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=19;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 非BOM 配件固定资产明细");
$funFrom="nonbom17";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|资产ID|60|条码|100|资产编号|130|资产名称|350|入库地点|60|入库日期|70|使用年限|60|预计报废日期|80|最终报废日期|80|盘点时限|70|下次盘点时间|80|领用时间|70|领用人|70|分析|60|保养记录|70|维修记录|70|状态|40|销售商|120|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT   A.Attached,A.GoodsId,C.BarCode,C.GoodsNum,C.TypeSign,C.CkId,A.GoodsName,C.Id,C.Date AS rkDate,K.Name AS rkName,N.Frequency AS nxFrequency,X.Frequency AS pdFrequency,C.Operator,C.Estate,D.Forshort,D.CompanyId,C.Picture,N.days AS nxdays,X.days  AS pddays
FROM $DataIn.nonbom7_code  C 
LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId
LEFT JOIN $DataPublic.nonbom6_nx  N ON N.Id=A.nxId
LEFT JOIN $DataPublic.nonbom6_nx X  ON X.Id=A.pdDate
WHERE  1  $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
$DirCode=anmaIn("download/nonbombf/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsName= $myRow["GoodsName"];
		$GoodsId= $myRow["GoodsId"];
		$BarCode= $myRow["BarCode"];
		$GoodsNum= $myRow["GoodsNum"];
		$rkDate= $myRow["rkDate"];
		$rkName= $myRow["rkName"];
		$nxFrequency= $myRow["nxFrequency"];
		$pdFrequency= $myRow["pdFrequency"];
		$Forshort= $myRow["Forshort"];
		$CompanyId= $myRow["CompanyId"];
        $EstateSign=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
        include"../model/subprogram/good_Property.php";//非BOM配件属性
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//配件分析
		$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";

             $Picture=$myRow["Picture"];
            $PictureStr="";
            if($Picture!="") {
			      $Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
                $BarCodeStr="<span onClick='OpenOrLoad(\"$DirCode\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
              }
           else  $BarCodeStr=$BarCode;
       //**********************************领用记录
        $nxdays=$myRow["nxdays"];
        $pddays=$myRow["pddays"];
        $CheckLyManResult=mysql_query("SELECT  O.Date AS LyDate,M.Name AS LyName,O.Remark AS LyRemark    FROM $DataIn.nonbom8_outfixed  O  
        LEFT JOIN $DataPublic.staffmain M ON M.Number=O.LyMan
         WHERE O.BarCode=$BarCode AND O.Estate=1",$link_id);
         if($CheckLyManRow=mysql_fetch_array($CheckLyManResult)){
                   $LyDate=$CheckLyManRow["LyDate"];
                   $LyName=$CheckLyManRow["LyName"];
                   $LyRemark=$CheckLyManRow["LyRemark"];
                   $LyDate=substr($LyDate, 0, 10);
             }
         else{
                   $LyDate="";$LyName="";$LyRemark="";
                 }
         if($pddays>0 && $LyDate!=""){
                    $LastpdDate=date("Y-m-d",strtotime("$LyDate  +$pddays day"));   
                   if($LastpdDate<=date("Y-m-d"))$LastpdDate="<span class='redB'>$LastpdDate</span>";
                }
         else{
                  $LastpdDate="&nbsp;";
                  if($LyDate=="")$LyDate="&nbsp;";
               }
       //**********************************报废记录
        $CheckBfResult=mysql_query("SELECT  O.Date AS bfDate,O.Remark AS bfRemark    FROM $DataIn.nonbom10_bffixed  O  
         WHERE O.BarCode=$BarCode ",$link_id);
         if($CheckBfRow=mysql_fetch_array($CheckBfResult)){
                   $bfDate=$CheckBfRow["bfDate"];
                   $bfRemark=$CheckBfRow["bfRemark"];
                   $bfDate=substr($bfDate, 0, 10);
             }
         else{
                 $bfDate="&nbsp;";$bfRemark="";
                 }

       if($nxdays>0 && $bfDate!=""){
                    $yjbfDate=date("Y-m-d",strtotime("$rkDate  +$nxdays day"));   
                     if($bfDate>$yjbfDate)$bfDate="<span class='redB'>$bfDate</span>";
                    else if($bfDate==$yjbfDate)$bfDate="<span class='greenB'>$bfDate</span>";
                }
         else{
                 $yjbfDate="&nbsp;"; $bfDate="&nbsp;";
               }
         switch($EstateSign){
                case 1:
                        $EstateStr="<span class='greenB'>在库</span>";  break;
                case 2:
                        $EstateStr="<span class='blueB'>领用</span>";  break;
                case 0:
                        $EstateStr="<span class='redB'>报废</span>";  break;
                      }

     $CareResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.nonbom7_care WHERE BarCode=$BarCode",$link_id));
      $CareId=$CareResult["Id"];
     if($CareId>0){
                  $CareStr="<a href='nonbom18_read.php?tempBarCode=$BarCode' target='_blank'>View</a>";
             }
       else{
              $CareStr="";
              }

       $RepairResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.nonbom7_repair WHERE BarCode=$BarCode",$link_id));
      $RepairId=$RepairResult["Id"];
       if($RepairId>0){
            $RepairStr="<a href='nonbom19_read.php?tempBarCode=$BarCode' target='_blank'>View</a>";
       }
     else{
          $RepairStr="";
         }
      $Analyse="<a href='nonbom17_analyse.php?tempBarCode=$BarCode' target='_blank'>分析</a>";

		$ValueArray=array(
			array(0=>$GoodsIdStr, 	1=>"align='center'"),
			array(0=>$BarCodeStr,	1=>"align='center'"),
			array(0=>$GoodsNum,	1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$rkName,	1=>"align='center'"),
			array(0=>$rkDate,	1=>"align='center'"),
			array(0=>$nxFrequency,	1=>"align='center'"),
			array(0=>$yjbfDate,	1=>"align='center'"),
			array(0=>$bfDate,	1=>"align='center'"),
			array(0=>$pdFrequency,	1=>"align='center'"),
			array(0=>$LastpdDate,	1=>"align='center'"),
			array(0=>$LyDate,	1=>"align='center'"),
			array(0=>$LyName,	1=>"align='center'"),
			array(0=>$Analyse,	1=>"align='center'"),
			array(0=>$CareStr,	1=>"align='center'"),
			array(0=>$RepairStr,	1=>"align='center'"),
			array(0=>$EstateStr,	1=>"align='center'"),
			array(0=>$Forshort,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>