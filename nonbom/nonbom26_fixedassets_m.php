<?php
include "nobom_config.inc";
include "../model/modelhead.php";

$From=$From==""?"m":$From;
//需处理参数
$ColsNumber=18;		
$sumCols="12,13,14,15,17";			//求和列,需处理		
$tableMenuS=600;
ChangeWtitle("$SubCompany 资产折旧审核");
$funFrom="nonbom26_fixedassets";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|30|资产ID|60|条码|100|资产名称|350|类别|80|使用部门|70|使用<br>情况|40|入帐日期|70|增加<br>方式|40|折旧方法|80|折旧期数|70|原值|80|累计折旧|70|累计减值准备|70|净值|60|残值率|40|预计净残值|70|修改原因|200|状态|40|修改人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,17,15";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
  
$searchtable="nonbom4_goodsdata|A|GoodsName|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
 include "../model/subprogram/QuickSearch.php";

//步骤5：
echo "&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\">";

include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$SearchRows.= ' AND  (F.TypeId IN (' .  $APP_CONFIG['NOBOM_FIXEDASSET_TYPEID'] . ') OR  A.AssetType=2)'; 

$mySql="SELECT   S.Id,C.BarCode,C.GoodsNum,C.TypeSign,C.CkId,C.Date,C.Operator,C.Estate AS UseEstate,C.Picture,
			A.Attached,A.GoodsId,A.GoodsName,A.Salvage,D.Forshort,D.CompanyId,A.DepreciationId,DP.Depreciation,B.Price,F.Name AS TypeName,
			S.BranchId,S.PostingDate,S.AddType,S.DepreciationType,S.Amount,S.DepreciationId AS S_DepreciationId,
			S.Depreciation AS S_Depreciation,S.Salvage AS S_Salvage ,S.Remark,
			S.Estate,S.Locks,S.Modifier  AS S_Operator,E.Name AS BranchName  
	FROM $DataPublic.nonbom7_fixedassets S
	LEFT JOIN $DataIn.nonbom7_code  C  ON S.BarCode=C.BarCode  
	LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
	LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
	LEFT JOIN $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
	LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=T.FirstId  
	LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId
	LEFT JOIN $DataPublic.nonbom6_depreciation DP  ON DP.Id=A.DepreciationId 
	LEFT JOIN $DataPublic.nonbom7_insheet  R ON R.Id=C.rkId
	LEFT JOIN $DataPublic.nonbom6_cgsheet B ON B.Id=R.cgId  
	LEFT JOIN $DataPublic.branchdata E ON E.Id=S.BranchId 
	WHERE  S.Estate=2  AND S.Amount>0 AND S.Depreciation>0 $SearchRows   ORDER BY S.modified DESC, S.Id";
//echo $SearchRows;
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
		$Forshort= $myRow["Forshort"];
		$CompanyId= $myRow["CompanyId"];
        $EstateSign=$myRow["UseEstate"];
		$Locks=$myRow["Locks"];
	    $TypeName = $myRow["TypeName"];
        $Remark = $myRow["Remark"];
	   $AddType =$myRow["AddType"]; 
	   $DepreciationType = $myRow["DepreciationType"]; 
	   $Depreciation = $myRow["S_Depreciation"];
	   $Amount = $myRow["Amount"]; 
	   $PostingDate= $myRow["PostingDate"];
	   
	   $Operator=$myRow["S_Operator"];
	   $BranchName=$myRow["BranchName"];
	   $SalvageValue = number_format($myRow["Amount"] * $myRow["S_Salvage"],2);
       $Salvage=$myRow["S_Salvage"]*100 . "%";
       
         $curYear =date('Y') . '-01';
         $chargeRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS Amount,SUM(IF(Month>='$curYear',Amount,0)) AS YearAmount FROM $DataPublic.nonbom7_depreciationcharge   
		       WHERE BarCode='$BarCode' ",$link_id));
		  $DepreciationCharge = $chargeRow['Amount'];
		  $YearCharge = $chargeRow['YearAmount']==0?"&nbsp;":number_format($chargeRow['YearAmount'],2); 
		  
		  $NetValue  = number_format($Amount-$DepreciationCharge,2);
		  $DepreciationCharge = $DepreciationCharge==0?"&nbsp;": number_format($DepreciationCharge,2); 
		   $Amount = number_format($Amount,2);
		   
         include "../model/subprogram/staffname.php";
        
		 $AddType = $APP_CONFIG['NOBOM_FIXEDASSET_ADDTYPE'][$AddType];
		 $DepreciationType = $APP_CONFIG['NOBOM_FIXEDASSET_DEPRECIATIONTYPE'] [$DepreciationType];
		
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
           
         $EstateStr =  $APP_CONFIG['NOBOM_FIXEDASSET_ESTATE'][$EstateSign];
         switch($EstateSign){
                case 1:
                        $EstateStr="<span class='greenB'>$EstateStr</span>";  break;
                case 2:
                        $EstateStr="<span class='blueB'>$EstateStr</span>";  break;
                case 0:
                        $EstateStr="<span class='redB'>$EstateStr</span>";  break;
                      }
        
         $Estate=$myRow["Estate"];
         $ColbgColor = "";
         switch($Estate){
           case "3":
				$Estate="<div align='center' class='rebB' title='审核退回!'>√×</div>";
				$Locks=1;
				break;
           case "2":
				$Estate="<div align='center' class='yellowB' title='未审核!'>√.</div>";
				$Locks=1;
				break;
		   case "1":
				 $Estate="<div align='center' class='greenB' title='审核通过!'>√</div>";
				break;
			default:
				$Estate="<div align='center' class='redB' title='已报废'>×</div>";
				 $LockRemark="已报废";
				 $Locks=0;
				break;
			}
        
		$ValueArray=array(
			array(0=>$GoodsIdStr, 	1=>"align='center'"),
			array(0=>$BarCodeStr,	1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$TypeName,	1=>"align='center'"),
			array(0=>$BranchName,	1=>"align='center'"),
			array(0=>$EstateStr,	1=>"align='center'"),
			array(0=>$PostingDate,	1=>"align='center'"),
			array(0=>$AddType,	1=>"align='center'"),
			array(0=>$DepreciationType,	1=>"align='center'"),
			array(0=>$Depreciation,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$DepreciationCharge,	1=>"align='right'"),
			array(0=>"&nbsp;",	1=>"align='center'"),
			array(0=>$NetValue,	1=>"align='right'"),
			array(0=>$Salvage,	1=>"align='center'"),
			array(0=>$SalvageValue,	1=>"align='right'"),
			array(0=>$Remark),
			array(0=>$Estate,	1=>"align='center'"),
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