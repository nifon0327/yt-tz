<?php 
/*
$DataIn.hzqksheet
$DataPublic.adminitype
$DataPublic.currencydata
二合一已更新
电信-joseph
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	echo $MonthSelect;
	$SearchRows.="and S.Estate=3";
	
	$checkType =mysql_query("SELECT G.Id,G.Name FROM $DataPublic.zw_goodstype  G
	LEFT JOIN $DataIn.zw3_purchaset T ON T.TypeId=G.Id
	LEFT JOIN $DataIn.zw3_purchases S ON S.TypeId=T.Id
	WHERE G.Estate=1 $SearchRows GROUP BY G.Id",$link_id);
    if($checkTypeRow = mysql_fetch_array($checkType)){
    echo"<select name='GoodsType' id='GoodsType'  onchange='ResetPage(this.name)'>";
    echo"<option value='' selected>物品类别</option>";
        do{
           $TypeId=$checkTypeRow["Id"];
	       $Name=$checkTypeRow["Name"];
        if ($TypeId==$GoodsType){
	           echo"<option value='$TypeId' selected>$Name</option>";
               $SearchRows.=" and T.TypeId='$TypeId'";
             }
	    else{
              echo "<option value='$TypeId'>$Name</option>"; 
            }
         }while ( $checkTypeRow = mysql_fetch_array($checkType));
       echo"</select>";
       }	
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>&nbsp;";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Unit,S.Price,S.Qty,T.TypeName,S.cgSign,S.Remark,S.Estate,M.Name AS Buyer,S.Bill,S.Locks,S.qkDate,S.Operator,T.Id AS TId,T.Attached,C.cName,C.Linkman,C.Tel,S.Date
FROM $DataIn.zw3_purchases S 
LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
LEFT JOIN $DataIn.retailerdata C ON C.Id=S.Cid
WHERE 1 $SearchRows ORDER BY S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$TypeName=$myRow["TypeName"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":trim($myRow["Remark"]);
		$qkDate=$myRow["qkDate"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Buyer=$myRow["Buyer"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="Z".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$Locks=1;
		$TId=$myRow["TId"];
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$TId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$cName=$myRow["cName"];
                if ($cName!=""){
                    $cName="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$cName</span>";
                }
                else{
                    $cName="&nbsp;";
                }
          if(floor($Qty)==$Qty) { $Qty=floor($Qty); }
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$cName, 1=>"align='left'"),
			array(0=>$Remark, 1=>"align='center'"),
			array(0=>$Buyer, 1=>"align='center'"),
			array(0=>$qkDate, 1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
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