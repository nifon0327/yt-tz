<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 非BOM 配件固定资产保养记录");
$funFrom="nonbom18";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|条码|100|资产编号|130|资产名称|350|保养日期|70|内部保养人|70|外部保养公司|80|凭证|60|原因|250|更新日期|70|操作人|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
include "../model/subprogram/read_model_3.php";
if($tempBarCode!=""){
     $SearchRows.=" AND D.BarCode='tempBarCode'";
      }
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
$mySql="SELECT D.Id,D.ByReason,D.Picture,D.Estate,D.Date,D.Operator,D.ByNumber, D.ByCompanyId,D.ByDate,
A.Attached,A.GoodsId,C.BarCode,C.GoodsNum,A.GoodsName,C.Picture AS BarPicture
FROM $DataIn.nonbom7_care  D 
LEFT JOIN $DataIn.nonbom7_code  C  ON D.BarCode=C.BarCode
LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId
WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
$DirCode=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
	do{
		$m=1;
   		$Id=$myRow["Id"];
		$GoodsName= $myRow["GoodsName"];
		$GoodsId= $myRow["GoodsId"];
		$BarCode= $myRow["BarCode"];
		$GoodsNum= $myRow["GoodsNum"];
		$ByReason= $myRow["ByReason"]==""?"&nbsp;":$myRow["ByReason"];
		$ByNumber= $myRow["ByNumber"];
        if($ByNumber>0){
                   $CheckByNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber",$link_id));
                   $ByName=$CheckByNumberResult["Name"];
         }
        else{
                $ByName="&nbsp;";
              }
		$ByCompanyId= $myRow["ByCompanyId"];
        if($ByCompanyId>0){
                 $CheckByCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId",$link_id));
                  $ByForshort=$CheckByCompanyResult["Forshort"];
             }
          else{
                $ByForshort="&nbsp;";
               }
		$ByDate= $myRow["ByDate"];
		$Date= $myRow["Date"];
        $Picture= $myRow["Picture"];
        $Attached= $myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
        include"../model/subprogram/good_Property.php";//非BOM配件属性
          $BarPicture=$myRow["BarPicture"];
          $BarCodeStr="";
            if($BarPicture!="") {
			      $BarPicture=anmaIn($BarPicture,$SinkOrder,$motherSTR);
                $BarCodeStr="<span onClick='OpenOrLoad(\"$DirCode\",\"$BarPicture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
              }
       else $BarCodeStr=$BarCode;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";   

		$Picture=$myRow["Picture"];
		$Dir18=anmaIn("download/nonbom18/",$SinkOrder,$motherSTR);
		if($Picture==1){
			$Picture="C".$Id.".jpg";
			$Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
			$PictureStr="<span onClick='OpenOrLoad(\"$Dir18\",\"$Picture\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$PictureStr="-";
			}
		$ValueArray=array(
			array(0=>$BarCodeStr,	1=>"align='center'"),
			array(0=>$GoodsNum,	1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$ByDate,	1=>"align='center'"),
			array(0=>$ByName,	1=>"align='center'"),
			array(0=>$ByForshort,	1=>"align='center'"),
			array(0=>$PictureStr,	1=>"align='center'"),
			array(0=>$ByReason,	1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
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