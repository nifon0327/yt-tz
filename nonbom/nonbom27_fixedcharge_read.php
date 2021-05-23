<?php
include "nobom_config.inc";
include "../model/modelhead.php";

$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=17;		
$sumCols="11,12,15,17";			//求和列,需处理		
$tableMenuS=600;
ChangeWtitle("$SubCompany 固定资产折旧费用列表");
$funFrom="nonbom27_fixedcharge";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|折旧月份|60|资产ID|60|条码|100|资产名称|350|入帐日期|70|折旧方法|80|折旧<br>期数|35|原值|80|每期折旧|75|本月折旧|75|本年折旧|75|累计折旧|75|累计减值<br>准备|70|净值|80|残值率|40|预计净残值|75|登记日期|65";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量

$ActioToS="";
//$ActioToS="1,2,3,4";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){				
				//入帐日期
			$checkResult = mysql_query("SELECT S.Month 
						FROM  $DataIn.nonbom7_depreciationcharge S 
						WHERE  1 GROUP BY  S.Month  ORDER BY Month DESC ",$link_id);
			   
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<select name='Month' id='Month' onchange='ResetPage(this.name)'>";
				//echo"<option value='' selected>全部</option>";
				do{
					$MonthTemp=$checkRow["Month"];
					$Month=$Month==""?$MonthTemp:$Month;
					if($Month==$MonthTemp){
						echo"<option value='$MonthTemp' selected>$MonthTemp</option>";
						$SearchRows=" AND  L.Month='$MonthTemp' ";	
						}
					else{
						echo"<option value='$MonthTemp'>$MonthTemp</option>";
						}
					}while ($checkRow = mysql_fetch_array($checkResult));
				echo"</select>&nbsp;";
				}
}
   
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
  
 $searchtable="nonbom4_goodsdata|A|GoodsName|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
 include "../model/subprogram/QuickSearch.php";
 
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT  L.Id,L.Amount AS MonthCharge,L.Date,L.Month,
            C.BarCode,C.GoodsNum,C.TypeSign,C.rkId,C.Operator,C.Estate AS UseEstate,C.Picture,
			A.Attached,A.GoodsId,A.GoodsName,A.Salvage,D.Forshort,D.CompanyId,A.DepreciationId,DP.Depreciation,B.Price,B.Mid AS cgMid,
			S.Id AS sId,S.BranchId,S.PostingDate,S.AddType,S.DepreciationType,S.Amount,S.DepreciationId AS S_DepreciationId,
			S.Depreciation AS S_Depreciation,S.Salvage AS S_Salvage ,S.Remark,S.InvoiceFile,S.ContractFile,
			S.Estate,S.Locks,S.Operator  AS S_Operator,E.Name AS BranchName,H.Value AS AddTaxValue
    FROM  $DataIn.nonbom7_depreciationcharge L 
	LEFT JOIN $DataIn.nonbom7_code C ON  L.BarCode=C.BarCode 
	LEFT JOIN $DataPublic.nonbom7_fixedassets S ON S.BarCode=C.BarCode  
	LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
	LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
	LEFT JOIN $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
	LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId
	LEFT JOIN $DataPublic.nonbom6_depreciation DP  ON DP.Id=A.DepreciationId 
	LEFT JOIN $DataPublic.nonbom7_insheet  R ON R.Id=C.rkId
	LEFT JOIN $DataPublic.nonbom6_cgsheet B ON B.Id=R.cgId  
	LEFT JOIN $DataIn.provider_addtax H ON H.Id = B.AddTaxValue 
	LEFT JOIN $DataPublic.branchdata E ON E.Id=S.BranchId 
	WHERE  1  $SearchRows  ORDER BY  L.Month DESC,L.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
		$DirCode=anmaIn("download/nonbombf/",$SinkOrder,$motherSTR);
		$Dir1=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
		$Dir2=anmaIn("download/nonbom_contract/",$SinkOrder,$motherSTR);
		$TotalAmount=$TotalAmount0=$TotalAmount1=$TotalAmount2=$TotalAmount3=0;
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsName= $myRow["GoodsName"];
		$GoodsId= $myRow["GoodsId"];
		$BarCode= $myRow["BarCode"];
		$Forshort= $myRow["Forshort"];
		$CompanyId= $myRow["CompanyId"];
		$Date=$myRow["Date"];
        $EstateSign=$myRow["UseEstate"];
		$Locks=$myRow["Locks"];
	    $TypeName = $myRow["TypeName"];
	    $rkId=$myRow["rkId"];
        $Month=$myRow["Month"];
        
	  $AddType =$myRow["AddType"]; 
	  $DepreciationType = $myRow["DepreciationType"]; 
	  $Depreciation = $myRow["S_Depreciation"];
	  $Amount = $myRow["Amount"]; 
	  $monthCharge = $myRow["MonthCharge"]; 
	  $PostingDate= $myRow["PostingDate"];
	  $Operator=$myRow["S_Operator"];
	  $BranchName=$myRow["BranchName"];
	  $Salvage = $myRow["S_Salvage"];
	  $AddTaxValue =$myRow["AddTaxValue"];

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
          
        
        $LockRemark = "";

        
         $chargeValue =$Depreciation!=0?number_format(round($Amount*(1-$Salvage)/$Depreciation,2),2):"&nbsp;";
         $SalvageValue = number_format($Amount * $Salvage,2);
         
         $curYear =date('Y') . '-01';
         $chargeRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS Amount,SUM(IF(Month>='$curYear',Amount,0)) AS YearAmount FROM $DataPublic.nonbom7_depreciationcharge   
		       WHERE BarCode='$BarCode' ",$link_id));
		  $DepreciationCharge = $chargeRow['Amount'];
		  $YearCharge = $chargeRow['YearAmount']==0?"&nbsp;":number_format($chargeRow['YearAmount'],2); 
		  
		 $NetValue  =$Amount-$DepreciationCharge;
		  
		 $TotalAmount+=$Amount;
		 $TotalAmount0+=$monthCharge;
         $TotalAmount1+=$YearCharge;
         $TotalAmount2+=$DepreciationCharge;
         $TotalAmount3+=$NetValue;
         
          $NetValue  = number_format($NetValue,2);
		  $DepreciationCharge = $DepreciationCharge==0?"&nbsp;": number_format($DepreciationCharge,2); 
         $Amount = number_format($Amount,2);
         $monthCharge= number_format($monthCharge,2);
         $Salvage=$Salvage*100 . "%";
         

		$ValueArray=array(
		    array(0=>$Month, 	1=>"align='center'"),
			array(0=>$GoodsIdStr, 	1=>"align='center'"),
			array(0=>$BarCodeStr,	1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$PostingDate,	1=>"align='center'"),
			array(0=>$DepreciationType,	1=>"align='center'"),
			array(0=>$Depreciation,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$chargeValue,	1=>"align='right'"),
			array(0=>"<span style='color:#0000FF'>" .$monthCharge . '</span>',	1=>"align='right'"),
			array(0=>$YearCharge,	1=>"align='right'"),
			array(0=>$DepreciationCharge,	1=>"align='right'"),
			array(0=>"&nbsp;",	1=>"align='right'"),
			array(0=>$NetValue,	1=>"align='right'"),
			array(0=>$Salvage,	1=>"align='center'"),
			array(0=>$SalvageValue,	1=>"align='right'"),
			array(0=>$Date,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		
		$m=1;
		    $TotalAmount= number_format($TotalAmount,2);
		    $TotalAmount0= number_format($TotalAmount0,2);
		    $TotalAmount1= number_format($TotalAmount1,2);
		    $TotalAmount2= number_format($TotalAmount2,2);
		    $TotalAmount3= number_format($TotalAmount3,2);
		    
			$ValueArray=array(
				    array(0=>"&nbsp;"	),
					array(0=>"&nbsp;"	),
					array(0=>"&nbsp;"	),
					array(0=>"&nbsp;"	),
					array(0=>"&nbsp;"	),
					array(0=>"&nbsp;"	),
				    array(0=>"&nbsp;"	),
					array(0=>$TotalAmount,	1=>"align='right'"),
					array(0=>"&nbsp;"	),
					array(0=>"<span style='color:#0000FF'>" .$TotalAmount0 . '</span>',	1=>"align='right'"),
					array(0=>$TotalAmount1,	1=>"align='right'"),
					array(0=>$TotalAmount2,	1=>"align='right'"),
					array(0=>"&nbsp;"),
					array(0=>$TotalAmount3,	1=>"align='right'"),
					array(0=>"&nbsp;"),
					array(0=>"&nbsp;"),
					array(0=>"&nbsp;")
			);	
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";	
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