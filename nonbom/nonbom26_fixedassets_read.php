<?php
include "nobom_config.inc";
include "../model/modelhead.php";

$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=23;		
$sumCols="12,14,15,16,17";			//求和列,需处理		
$tableMenuS=600;
ChangeWtitle("$SubCompany 固定资产折旧列表");
$funFrom="nonbom26_fixedassets";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|资产ID|60|条码|100|资产名称|350|类别|80|使用部门|70|使用<br>情况|40|入帐日期|70|增加<br>方式|40|折旧方法|80|折旧期数|60|原值|80|每期折旧|70|本年折旧|70|累计折旧|70|累计减值<br>准备|70|净值|60|残值率|40|预计净残值|70|状态|40|发票|40|合同|40|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	
			$Estate=$Estate==""?0:$Estate;
			$EstateStr="Estatestr".$Estate;
			$$EstateStr="selected";   
			echo "<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>";
			echo "<option value='0' $Estatestr0>全部</option>";
			echo "<option value='1' $Estatestr1>正常</option>";
			echo "<option value='2' $Estatestr2>未审核</option>";
			echo "</select>";	
			
			if ($Estate>0){
					$SearchRows=" AND S.Estate=$Estate";
			}
			
			//使用情况
	
			echo "<select name='UseEstate' id='UseEstate' onchange='ResetPage(this.name)'>";
			echo "<option value='' selected>全部</option>";
			$EstateStrs = $APP_CONFIG['NOBOM_FIXEDASSET_ESTATE'];
		    while(list($_key,$_val)= each($EstateStrs))
             {
                  
                  if ($_key==$UseEstate && strlen(''.$UseEstate)>0){
	                   echo "<option value='$_key' selected>$_val</option>";  
	                   $SearchRows.=" AND C.Estate=$UseEstate ";
                  }else{
	                  echo "<option value='$_key'>$_val</option>";  
                  }
             }
             echo "</select>";
             
             //供应商
			$checkResult = mysql_query("SELECT D.CompanyId,D.Letter,D.Forshort 
									   FROM $DataIn.nonbom7_code C
									   INNER JOIN $DataPublic.nonbom7_fixedassets S ON S.BarCode=C.BarCode  
									   LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=C.GoodsId 
									   LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId 
									   WHERE D.CompanyId>0 $SearchRows GROUP BY D.CompanyId ORDER BY D.Letter,D.Forshort",$link_id);
			   
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
				echo"<option value='' selected>全部供应商</option>";
				do{
					$CompanyIdTemp=$checkRow["CompanyId"];
					$ForshortTemp=$checkRow["Forshort"];
					$Letter=$checkRow["Letter"];
					if($CompanyId==$CompanyIdTemp){
						echo"<option value='$CompanyIdTemp' selected>$Letter-$ForshortTemp</option>";
						$SearchRows.=" AND G.CompanyId='$CompanyIdTemp'";	
						}
					else{
						echo"<option value='$CompanyIdTemp'>$Letter-$ForshortTemp</option>";
						}
					}while ($checkRow = mysql_fetch_array($checkResult));
				echo"</select>&nbsp;";
				}
				
				//入帐日期
			$checkResult = mysql_query("SELECT DATE_FORMAT(S.PostingDate,'%Y-%m') AS Month 
						FROM  $DataIn.nonbom7_code C 
						INNER JOIN $DataPublic.nonbom7_fixedassets S ON S.BarCode=C.BarCode 
						LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=C.GoodsId
						LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId 
						WHERE  1 $SearchRows  GROUP BY  DATE_FORMAT(S.PostingDate,'%Y-%m') ORDER BY Month DESC ",$link_id);
			   
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<select name='Month' id='Month' onchange='ResetPage(this.name)'>";
				echo"<option value='' selected>全部</option>";
				do{
					$MonthTemp=$checkRow["Month"];
					if($Month==$MonthTemp){
						echo"<option value='$MonthTemp' selected>$MonthTemp</option>";
						$SearchRows.=" AND DATE_FORMAT(S.PostingDate,'%Y-%m')='$MonthTemp' ";	
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

$SearchRows.= '  AND  (F.TypeId IN (' .  $APP_CONFIG['NOBOM_FIXEDASSET_TYPEID'] . ')  OR  A.AssetType=2)  '; 
$mySql="SELECT   C.Id,C.BarCode,C.GoodsNum,C.TypeSign,C.rkId,C.Date,C.Operator,C.Estate AS UseEstate,C.Picture,
			A.Attached,A.GoodsId,A.GoodsName,A.Salvage,D.Forshort,D.CompanyId,A.DepreciationId,DP.Depreciation,B.Price,B.Mid AS cgMid,F.Name AS TypeName,
			S.Id AS sId,S.BranchId,S.PostingDate,S.AddType,S.DepreciationType,S.Amount,S.DepreciationId AS S_DepreciationId,
			S.Depreciation AS S_Depreciation,S.Salvage AS S_Salvage ,S.Remark,S.InvoiceFile,S.ContractFile,
			S.Estate,S.Locks,S.Operator  AS S_Operator,E.Name AS BranchName,H.Value AS AddTaxValue
	FROM $DataIn.nonbom7_code  C 
	LEFT JOIN $DataPublic.nonbom7_fixedassets S ON S.BarCode=C.BarCode  
	LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
	LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
	LEFT JOIN $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
	LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=T.FirstId  
	LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId
	LEFT JOIN $DataPublic.nonbom6_depreciation DP  ON DP.Id=A.DepreciationId 
	LEFT JOIN $DataPublic.nonbom7_insheet  R ON R.Id=C.rkId
	LEFT JOIN $DataPublic.nonbom6_cgsheet B ON B.Id=R.cgId  
	LEFT JOIN $DataIn.provider_addtax H ON H.Id = B.AddTaxValue 
	LEFT JOIN $DataPublic.branchdata E ON E.Id=S.BranchId 
	WHERE  1  $SearchRows  AND A.GoodsId>0 ORDER BY S.Estate DESC,S.PostingDate DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
		$DirCode=anmaIn("download/nonbombf/",$SinkOrder,$motherSTR);
		$Dir1=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
		$Dir2=anmaIn("download/nonbom_contract/",$SinkOrder,$motherSTR);
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
	    $rkId=$myRow["rkId"];
       
		if ($myRow["sId"]>0){
			  $AddType =$myRow["AddType"]; 
			  $DepreciationType = $myRow["DepreciationType"]; 
			  $Depreciation = $myRow["S_Depreciation"];
			  $Amount = $myRow["Amount"]; 
			  $PostingDate= $myRow["PostingDate"];
			  $Operator=$myRow["S_Operator"];
			  $BranchName=$myRow["BranchName"];
			  $Salvage = $myRow["S_Salvage"];
			  $AddTaxValue =$myRow["AddTaxValue"];
		}else{
			 $AddType = 1;
		     $DepreciationType=1;
		     $DepreciationId = $myRow["DepreciationId"];
		     $Depreciation = $myRow["Depreciation"];
		     $Amount = $myRow["Price"];
		     $PostingDate= $myRow["Date"];
		     $Operator=$myRow["Operator"];
		     $BranchName = "&nbsp;";
		     $Salvage =$myRow["Salvage"];
		     $Depreciation=$Depreciation==""?1:0;
		     
		     //添加新记录
		     $AddTaxValue =$myRow["AddTaxValue"];
		     $Amount = $Amount/(1+$AddTaxValue);
		     
		     $inRecode = "INSERT INTO $DataIn.nonbom7_fixedassets  (BarCode,PostingDate,BranchId,AddType,DepreciationType,DepreciationId,Depreciation,Salvage,Amount,Devalue,Remark,Estate,Date,Operator,creator,created) VALUES ('$BarCode','$PostingDate','0','$AddType','$DepreciationType','$DepreciationId','$Depreciation','$Salvage','$Amount','0','新增','2',CURDATE(),'$Operator','$Operator',NOW())";

		      $inAction=@mysql_query($inRecode);
              if ($inAction && mysql_insert_id()>0){ 
                     echo "ID 为$BarCode 的记录新增成功!<br>"; 
                }
               
		  }

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
        
        $LockRemark = "";
         if ($myRow["sId"]>0)  {        
		         $Estate=$myRow["Estate"];

		         $ColbgColor =$rkId==0?"bgcolor='#FFDEAD' ":"";
		         switch($Estate){
		           case "3":
		                 $ReasonRow= mysql_fetch_array(mysql_query("SELECT Reason FROM $DataPublic.nonbom7_fixedassets_audit   
		          WHERE Sid='" . $myRow["sId"] . "' AND Estate=3 ORDER BY Id DESC LIMIT 1",$link_id));
	                    $ReturnReasons = $ReasonRow['Reason']; 
	                    
						$Estate="<div align='center' class='redB' title='审核退回! 原因:$ReturnReasons'>×.</div>";
						$Locks=1; $ColbgColor = " bgcolor='#FF0000' ";
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
        }
        else{
	           $Estate="<div align='center' class='redB' title='未加入固定资产折旧'>错误</div>";
				$Locks=0;
				$ColbgColor = " bgcolor='#FF0000' ";
        }
        
         $chargeValue =$Depreciation!=0?number_format(round($Amount*(1-$Salvage)/$Depreciation,2),2):"&nbsp;";
         $SalvageValue = number_format($Amount * $Salvage,2);
         
         $curYear =date('Y') . '-01';
         $chargeRow= mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts,IFNULL(SUM(Amount),0) AS Amount,SUM(IF(Month>='$curYear',Amount,0)) AS YearAmount FROM $DataPublic.nonbom7_depreciationcharge   
		       WHERE BarCode='$BarCode' ",$link_id));
		  $DepreciationCharge = $chargeRow['Amount'];
		  $YearCharge = $chargeRow['YearAmount']==0?"&nbsp;":number_format($chargeRow['YearAmount'],2); 
		  
		  $theDefaultColor=$Depreciation>0 && $chargeRow['Counts']==$Depreciation?"#99FFCC":"#FFFFFF";
		  
		  $NetValue  = number_format($Amount-$DepreciationCharge,2);
		  $DepreciationCharge = $DepreciationCharge==0?"&nbsp;": number_format($DepreciationCharge,2); 
		  
         $Amount = number_format($Amount,2);
         $Salvage=$Salvage*100 . "%";
         
         //发票、合同信息
         $InvoiceFileSTR=$ContractFileSTR="&nbsp;";
         
         $cgMid = $myRow["cgMid"];
         if ($cgMid>0){
	            $fileRow=mysql_fetch_array(mysql_query("SELECT M.Attached,I.InvoiceFile 
	            FROM $DataIn.nonbom6_cgmain M 
	            LEFT JOIN $DataIn.nonbom6_invoice   I ON I.cgMid=M.Id 
		        WHERE M.Id='$cgMid' ",$link_id));
		        
		        if ($fileRow['InvoiceFile']!=""){
		              $InvoiceFile=$fileRow['InvoiceFile'];
			          $Attached1=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
					  $InvoiceFileSTR="<span onClick='OpenOrLoad(\"$Dir1\",\"$Attached1\",\"\",\"pdf\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		        }
		        
		        if ($fileRow['Attached']==1){
		              $ContractFile=$cgMid . '.pdf';
			          $Attached2=anmaIn($ContractFile,$SinkOrder,$motherSTR);
					  $ContractFileSTR="<span onClick='OpenOrLoad(\"$Dir2\",\"$Attached2\",\"\",\"pdf\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		        }
		        
		        
         }else{
	           $InvoiceFile =  $myRow["InvoiceFile"];
				$ContractFile =  $myRow["ContractFile"];
				
				if($InvoiceFile!=""){
					$Attached1=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
					$InvoiceFileSTR="<span onClick='OpenOrLoad(\"$Dir1\",\"$Attached1\",\"\",\"pdf\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			    }
			    
			    if($ContractFile!=""){
					$Attached2=anmaIn($ContractFile,$SinkOrder,$motherSTR);
					$ContractFileSTR="<span onClick='OpenOrLoad(\"$Dir2\",\"$Attached2\",\"\",\"pdf\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			    }
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
			array(0=>$chargeValue,	1=>"align='right'"),
			array(0=>$YearCharge,	1=>"align='right'"),
			array(0=>$DepreciationCharge,	1=>"align='right'"),
			array(0=>"&nbsp;",	1=>"align='right'"),
			array(0=>$NetValue,	1=>"align='right'"),
			array(0=>$Salvage,	1=>"align='center'"),
			array(0=>$SalvageValue,	1=>"align='right'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$InvoiceFileSTR,	1=>"align='center'"),
			array(0=>$ContractFileSTR,	1=>"align='center'"),
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