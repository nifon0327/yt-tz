<?php
include "../model/modelhead.php";

//步骤2：需处理
$Th_Col="选项|40|序号|30|客户|80|产品ID|50|中文名|180|Product Code|180|替换单号|150";
$ColsNumber=17;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页

//步骤3：
$CompanyId = $_GET['companyId'];
$SearchRows = " AND P.CompanyId = $companyId ";

$isPage=$isPage==""?0:$isPage;
$Pagination=$Pagination==""?$isPage:$Pagination;	//默认分页方式:1分页，0不分页
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
if(isFireFox()==1){	 //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
	//echo "FireFox";
	$tableWidth=$tableWidth+$Count*2;
}

if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}

$Page=$Page==""?1:$Page;
if($Pagination==1){
	$Pagination1="selected";
	$PageSTR=($Page-1)*$Page_Size.",".$Page_Size;
	$PageSTR="LIMIT ".$PageSTR;
	}
else{
	$Pagination0="selected";
	$Page=1;
	$PageSTR="";
	}
echo"	
<script type=text/javascript>window.name='win_test'</script><BASE target=_self>
<body onload='closeLoading()' onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' enctype='multipart/form-data' method='post' action='' target='win_test'>";
//echo "$Parameter  --- <br>";
//想把变量带过来，必须在_s1中加入,可见cg_cgdmain_s1.php
//$Parameter.=",CompanyId,$CompanyId,BuyerId,$BuyerId";  //这几个要带过去，也就是要带到 ,可见cg_cgdmain_s2.php,加入,可见cg_cgdmain_s1.php
//PassParameter($Parameter);
echo"<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr style='background-color: #f2f3f5;'>
    <td class='timeTop' id='menuT1' width='$tableMenuS'>";
//求和sumAmount
$MergeRows=$MergeRows==""?0:$MergeRows;
$sumCols=$sumCols==""?"":$sumCols;
echo"<input name='sumCols' type='hidden' id='sumCols' value='$sumCols'><input name='MergeRows' type='hidden' id='MergeRows' value='$MergeRows'>";

//楼栋
$result = mysql_query("SELECT P.BuildingNo 
        FROM $DataIn.pands A
        LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
        LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId  
        WHERE M.Estate>0 $SearchRows GROUP BY P.BuildingNo ORDER BY P.BuildingNo+0 ",$link_id);

if($myrow = mysql_fetch_array($result)){
    echo "<select name='Build' id='Build' onchange='ResetPages(\"Build\")'>";
//        echo "<option value='all' selected>全部楼栋</option>";
    do{
        $theBuild=$myrow["BuildingNo"];
        $Build=$Build==""?$theBuild:$Build;
        if($Build==$theBuild){
            echo"<option value='$theBuild' selected>$theBuild 栋 </option>";
            $SearchRows .=" AND P.BuildingNo = '$theBuild'";
        }
        else{
            echo"<option value='$theBuild'>$theBuild 栋 </option>";
        }
    }while($myrow = mysql_fetch_array($result));
}
echo"</select>&nbsp; ";

//楼层
$result = mysql_query("SELECT P.FloorNo  
        FROM $DataIn.pands A
        LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
        LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId  
        WHERE M.Estate>0 $SearchRows GROUP BY P.FloorNo ORDER BY P.FloorNo+0 ",$link_id);

if($myrow = mysql_fetch_array($result)){
    echo "<select name='Floor' id='Floor' onchange='ResetPages(\"Floor\")'>";
//        echo "<option value='all' selected>全部楼层</option>";
    do{
        $theFloor=$myrow["FloorNo"];
        $Floor=$Floor==""?$theFloor:$Floor;
        if($Floor==$theFloor){
            echo"<option value='$theFloor' selected>$theFloor 层</option>";
            $SearchRows .=" AND P.FloorNo = '$theFloor'";
        }
        else{
            echo"<option value='$theFloor'>$theFloor 层</option>";
        }
    }while($myrow = mysql_fetch_array($result));
}
echo"</select>&nbsp; ";


$result = mysql_query("SELECT P.TypeId,T.TypeName
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter",$link_id);
echo "<select name='GType' id='GType' onchange='ResetPages(\"ProductType\")'>";
echo "<option value='all' selected>全部类型</option>";
while ($myrow = mysql_fetch_array($result)){
    $TypeId=$myrow["TypeId"];
    $TypeName=$myrow["TypeName"];
    $GType = $GType ==""?$TypeId:$GType;
    if ($GType==$TypeId){
        echo "<option value='$TypeId'  selected> $TypeName</option>";
        $SearchRows .= " AND P.TypeId=".$TypeId;
    }
    else{
        echo "<option value='$TypeId' > $TypeName</option>";
    }
}
echo"</select>&nbsp;";

//步骤4：可选，其它预设选项
echo"  <select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";


//步骤5：
//include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Remark,P.pRemark,P.Description,P.Price,P.TestStandard,P.Code,P.Estate,P.PackingUnit,
	P.Unit,P.Date,P.Locks,P.Operator,C.CompanyId,C.Forshort,C.Currency,T.TypeName,D.Attached,P.buySign,CS.ReplaceNO
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId AND  C.ObjectSign IN (1,2)
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN  $DataIn.yw7_clientproduct A ON A.ProductId=P.ProductId
	LEFT JOIN  $DataIn.yw7_clientproxy D  ON D.Id=A.cId
	LEFT JOIN $DataIn.ck_substitute CS ON CS.YProductId=P.ProductId
	where 1 and C.Estate=1 $SearchRows order by Id DESC";//客户在使用中，记录可用中
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":"<span class='redB'>".$myRow["pRemark"]."</span>";
		$Price=$myRow["Price"];
		$buySign=$myRow["buySign"];
		$ReplaceNO = $myRow['ReplaceNO'];




		//操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$thisCId=$myRow["CompanyId"];
		$Client=$myRow["Forshort"];
		$TypeName=$myRow["TypeName"];
			$ValueArray=array(
				array(0=>$Client ,1=>"align='center'"),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$cName,		3=>"..."),
				array(0=>$eCode,				3=>"..."),
				array(0=>$ReplaceNO,				3=>"..."),
//				array(0=>$buySign,			1=>"align='center'"),
//				array(0=>$QCImage,		1=>"align='center'"),
//				array(0=>$Attached,		1=>"align='center'"),
//				array(0=>$pRemark),
//				array(0=>$Description,		1=>"align='center'"),
//				array(0=>$Price."&nbsp;", 	1=>"align='right'"),
//				array(0=>$PackingUnit,		1=>"align='center'"),
//				array(0=>$Code),
//				array(0=>$TypeName,			1=>"align='center'")
				);
		$checkidValue=$ProductId.'|'.$cName;

                $Choose = "<input name='checkId[]' type='checkbox' id='checkid$i' value=\"$checkidValue\" disabled><img src='../images/lock.png' width='15' height='15'>";

        echo"<table width='$tableWidth' border='0' cellspacing='0' class='ListTable' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
        echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
 onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
 onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";

        echo"<td class='A0111' width='$Field[$m]' align='center'>$Choose&nbsp;</td>";
        $m=$m+2;
        echo"<td class='A0101' width='$Field[$m]' align='center'>$j</td>";
        for ($k = 0; $k < count($ValueArray); $k++) {
            $m = $m + 2;
            echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $ValueArray[$k][0] . "</td>";
        }
        echo "</tr></table>";
        $i++;
        $j++;

		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
    // 刷新
    function ResetPages(e) {
        switch (e){
            case 'Build':
                document.forms["form1"].elements["Floor"].value="";
                document.forms["form1"].elements["GType"].value="";
                document.form1.submit();
                break;
            case 'Floor':
                document.forms["form1"].elements["GType"].value="";
                document.form1.submit();
                break;
            case 'ProductType':
                document.form1.submit();
                break;
        }
    }
</script>
