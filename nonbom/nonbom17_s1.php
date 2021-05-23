<?php
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|60|序号|30|资产ID|60|条码|100|资产编号|130|资产名称|350|入库地点|80|入库日期|70|使用年限|60|内部维修人|80|外部维修公司|80|内部保养人|80|外部保养公司|80|状态|40|销售商|120|操作员|60";
$ColsNumber=100;
$tableMenuS=600;
$Page_Size = 100;
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
echo"<select name='Pagination' id='Pagination' onchange='RefreshPage(\"nonbom17_s1\")'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT   A.Attached,A.GoodsId,C.BarCode,C.GoodsNum,C.TypeSign,C.CkId,A.GoodsName,C.Id,C.Date AS rkDate,K.Name AS rkName,C.Operator,C.Estate,D.Forshort,D.CompanyId,C.Picture,A.ByNumber,A.ByCompanyId,A.WxNumber,A.WxCompanyId
FROM $DataIn.nonbom7_code  C 
LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId";

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
        $WxNumber= $myRow["WxNumber"];
         if($WxNumber>0){
                     $CheckWxNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber",$link_id));
                     $WxName=$CheckWxNumberResult["Name"];
          }
        else{
             $WxName="";
              }
        $WxCompanyId= $myRow["WxCompanyId"];
      if($WxCompanyId>0){
                    $CheckWxCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId",$link_id));
                     $WxForshort=$CheckWxCompanyResult["Forshort"];
            }
        else{
                 $WxForshort="";
             }

		$ByNumber= $myRow["ByNumber"];
        if($ByNumber>0){
                   $CheckByNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber",$link_id));
                   $ByName=$CheckByNumberResult["Name"];
         }
        else{
                $ByName="";
              }
		$ByCompanyId= $myRow["ByCompanyId"];
        if($ByCompanyId>0){
                 $CheckByCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId",$link_id));
                  $ByForshort=$CheckByCompanyResult["Forshort"];
             }
          else{
                $ByForshort="";
               }
     switch($Action){
          case "7":
		$checkidValue=$GoodsName."^^".$BarCode."^^".$GoodsNum."^^".$ByNumber."^^".$ByName."^^".$ByCompanyId."^^".$ByForshort;
         break;
        case "8"://."^^".$WxNumber."^^".$WxName."^^".$WxCompanyId."^^".$WxForshort
		$checkidValue=$GoodsName."^^".$BarCode."^^".$GoodsNum."^^".$WxNumber."^^".$WxName."^^".$WxCompanyId."^^".$WxForshort;
           break;
        }
//echo $checkidValue;
		$Forshort= $myRow["Forshort"];
		$CompanyId= $myRow["CompanyId"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
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



         switch($EstateSign){
                case 0:
                       $EstateStr="<span class='greenB'>在库</span>";  break;
                case 1:
                       $EstateStr="<span class='blueB'>领用</span>";  break;
                case 2:
                       $EstateStr="<span class='redB'>报废</span>";  break;
                        }

       //$CareStr="<a href='nonbom18_read.php?tempBarCode=$BarCode' target='_blank'>View</a>";
      // $RepairStr="<a href='nonbom19_read.php?tempBarCode=$BarCode' target='_blank'>View</a>";
		$ValueArray=array(
			array(0=>$GoodsIdStr, 	1=>"align='center'"),
			array(0=>$BarCodeStr,	1=>"align='center'"),
			array(0=>$GoodsNum,	1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$rkName,	1=>"align='center'"),
			array(0=>$rkDate,	1=>"align='center'"),
			array(0=>$nxFrequency,	1=>"align='center'"),
			array(0=>$WxName,1=>"align='left'"),
			array(0=>$WxForshort,1=>"align='left'"),
			array(0=>$ByName,1=>"align='left'"),
			array(0=>$ByForshort,1=>"align='left'"),
			array(0=>$EstateStr,	1=>"align='center'"),
			array(0=>$Forshort,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);

		    include "../model/subprogram/s1_model_6.php";
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