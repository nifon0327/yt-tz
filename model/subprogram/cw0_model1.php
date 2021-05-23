<?php 
//$DataIn.电信---yang 20120801
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";

	$monthResult = mysql_query("SELECT PayDate FROM $mainData WHERE 1  group by DATE_FORMAT(PayDate,'%Y-%m') order by PayDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows.="and  DATE_FORMAT(M.PayDate,'%Y-%m')='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and  DATE_FORMAT(M.PayDate,'%Y-%m')='$FirstValue'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
			}		
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	
	 if($funFrom=="adminicost"){ //行政费用
		$cSignResult = mysql_query("SELECT C.CShortName,M.cSign
		FROM $mainData  M 
		LEFT JOIN $DataIn.companys_group C ON C.cSign = M.cSign
		WHERE 1 AND M.cSign>0 GROUP BY M.cSign ORDER BY C.Id",$link_id);
		if($cSignRow = mysql_fetch_array($cSignResult)){
			$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
			do{
			    $cSignValue = $cSignRow["cSign"];
			    $CShortName = $cSignRow["CShortName"];
			    $cSign = $cSign==""?$cSignValue:$cSign;
				if($cSign==$cSignValue){
					$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
					$SearchRows.=" and  M.cSign ='$cSignValue'";
					}
				else{
					$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
					}
				}while($cSignRow = mysql_fetch_array($cSignResult));
			 $cSignSelect.="</select>&nbsp;";
			  echo $cSignSelect;
			}
	}
	
	if($funFrom=="ch_freight_declaration"||$funFrom=="ch_shipforward"){
	    $TypeId=$TypeId==""?1:$TypeId;
	    $selectedStr="strType".$TypeId;
	    $$selectedStr="selected";
	    echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	    echo "<option value='1' $strType1>invoice</option>
	         <option value='2' $strType2>提货单</option>
		     </select>&nbsp;";
		  }
	//月份
	echo $MonthSelect;		
    if($funFrom=="rs_sbjf"){//社保公积金
           $TypeResult=mysql_query("SELECT S.TypeId FROM $DataIn.sbpaysheet S 
         LEFT JOIN $DataIn.sbpaymain M ON M.Id=S.Mid
         WHERE 1 $SearchRows  GROUP BY S.TypeId",$link_id);
            if($TypeRow=mysql_fetch_array($TypeResult)){
            echo"<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
            do{
                   $thisTypeId =$TypeRow["TypeId"];
                       switch($thisTypeId){
                                 case 1: $TypeName="社保";break;
                                 case 2: $TypeName="公积金";break;
                                 case 3: $TypeName="意外险";break;
                              }
                     $TypeId=$TypeId==""?$thisTypeId:$TypeId;
                    if($TypeId==$thisTypeId){
                                     echo"<option value='$thisTypeId' selected>$TypeName</option>";
                                     $SearchRows.=" AND S.TypeId='$thisTypeId'";
                                }
                       else{
                                   echo"<option value='$thisTypeId' >$TypeName</option>";
                              }
                   }while($TypeRow=mysql_fetch_array($TypeResult));
               }
      }
	//echo "SELECT M.CompanyId,P.Forshort,P.Letter FROM $mainData M LEFT JOIN providerdata P ON P.CompanyId=M.CompanyId WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter";
	//供应商
	if($funFrom=="cw_fkout"){
		//货币
		
		$cResult=mysql_query("SELECT C.Id,C.Symbol FROM $mainData M 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 $SearchRows GROUP BY C.Id",$link_id);
		if($cRow = mysql_fetch_array($cResult)){
			echo"<select name='Currency' id='Currency' onchange='document.form1.submit()'>";
			do{
				$cId=$cRow["Id"];
				$Symbol=$cRow["Symbol"];
				$Currency=$Currency==""?$Id:$Currency;
				if($Currency==$cId){
					echo"<option value='$cId' selected>$Symbol</option>";
					$SearchRows.=" and P.Currency='$cId'";
					}
				else{
					echo"<option value='$cId'>$Symbol</option>";
					}					
				}while($cRow = mysql_fetch_array($cResult));
			echo"</select>&nbsp;";
			}
			
			
		$pResult = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
		FROM $mainData M 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部供应商</option>";
			do{
				$Letter=$pRow["Letter"];
				$Forshort=$pRow["Forshort"];
				$Forshort=$Letter.'-'.$Forshort;
				$thisCompanyId=$pRow["CompanyId"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$Forshort </option>";
					$SearchRows.=" and M.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
			
		 $typeSql= mysql_query("SELECT T.TypeId,T.Letter,T.TypeName 
		        FROM $mainData M 
		        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN  $DataIn.cw1_fkoutsheet S ON S.Mid=M.Id 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		       WHERE 1 $SearchRows GROUP BY T.TypeId ORDER BY T.Letter",$link_id);
		if($typeRow = mysql_fetch_array($typeSql)){
			echo "<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
			echo "<option value='' >全部类型</option>";
			do{
				$TypeName=$typeRow["Letter"] .'-'. $typeRow["TypeName"];
				$thisTypeId=$typeRow["TypeId"];			
				if($TypeId==$thisTypeId){
					echo"<option value='$thisTypeId' selected>$TypeName </option>";
					$SearchRows.=" and D.TypeId='$thisTypeId'";
					}
				else{
					echo"<option value='$thisTypeId'>$TypeName</option>";
					}
				}while ($typeRow = mysql_fetch_array($typeSql));
			echo"</select>&nbsp;";
			}

			
		$TempEstateSTR="InvoiceSTR".strval($InvoiceSign); 
        $$TempEstateSTR="selected";

		echo"<select name='InvoiceSign' id='InvoiceSign' onchange='document.form1.submit()'>";
		echo"<option value=''  $InvoiceSTR>全  部</option>";
		echo"<option value='1' $InvoiceSTR1>有发票</option>";
		echo"<option value='2' $InvoiceSTR2>无发票</option>";
		echo"</select>&nbsp;";
		
		switch($InvoiceSign){
		   case 1: $SearchRows.=" and S.InvoiceId>0 ";break;
		   case 2: $SearchRows.=" and S.InvoiceId=0 ";break;
		   default:break;
		}

		}
	
	 if ($funFrom=="cw_gyssk"){
		 // BOM、非BOM分类
		 $TempEstateSTR="GysTypeSTR".strval($GysType); 
         $$TempEstateSTR="selected";

		echo"<select name='GysType' id='GysType' onchange='document.form1.submit()'>";
		echo"<option value='' $GysTypeSTR>全  部</option>";
		echo"<option value='1' $GysTypeSTR1>BOM</option>";
		echo"<option value='2' $GysTypeSTR2>非BOM</option>";
		echo"</select>&nbsp;";
		
		switch($GysType){
		   case 1: $SearchRows.=" and S.Remark NOT LIKE '%非BOM%'";break;
		   case 2: $SearchRows.=" and S.Remark  LIKE '%非BOM%'";break;
		   default:break;
		}

	 }

	$SearchRows.=" and S.Estate=0";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
?>