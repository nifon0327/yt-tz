<?php 
/*
$DataIn.zw3_purchases
$DataIn.zw3_purchaset
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=600;
$sumCols="5";		//求和列
ChangeWtitle("$SubCompany 总务申购清单");
$funFrom="zw_purchases";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|申购物品名称|250|物品类别|80|图片|40|数量|50|单位|50|使用地点|50|采购说明|200|采购状态|60|请款状态|60|供应商|100|申购日期|75|申购人|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8,52";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

 echo"<select name='chooseType' id='chooseType' onchange='gotoPage(this.value)'>";
     if ($funFrom=="zw_purchases"){
         echo"<option value='1' selected>申购</option>";
         echo"<option value='2'>请款</option>";
         }
     else{
         echo"<option value='1'>申购</option>";
         echo"<option value='2' selected>请款</option>";
     }
   echo"</select>&nbsp;&nbsp;";

//必选,过滤条件
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Month FROM $DataIn.zw3_purchases WHERE 1 group by DATE_FORMAT(Date,'%Y-%m') order by Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
      
 $checkType =mysql_query("SELECT Id,Name FROM $DataPublic.zw_goodstype  WHERE Estate=1",$link_id);
 if($checkTypeRow = mysql_fetch_array($checkType)){
   $typestr="<select name='GoodsType' id='GoodsType'  onchange='ResetPage(this.name)'>";
   $typestr.="<option value='' selected>物品类别</option>";
   do{
        $TypeId=$checkTypeRow["Id"];
	$Name=$checkTypeRow["Name"];
        if ($TypeId==$GoodsType){
	   $typestr.="<option value='$TypeId' selected>$Name</option>";
           $SearchRows.=" and T.TypeId='$TypeId'";
        }else{
           $typestr.="<option value='$TypeId'>$Name</option>"; 
        }
     }while ( $checkTypeRow = mysql_fetch_array($checkType));
     $typestr.="</select>";
     echo $typestr;
    } 
    
   $selState="selState" . $cgSign;
   $$selState="selected"; 
   echo "<select name='cgSign' id='cgSign'  onchange='ResetPage(this.name)'>";
   echo "<option value='' $selState>采购状态</option>";
   echo "<option value='1' $selState1>未申购</option>";
   echo "<option value='2' $selState2>申购审核</option>";
   echo "<option value='3' $selState3>待购</option>";
   echo "<option value='0' $selState0>已购回</option></select>";
   if ($cgSign!="") $SearchRows.=" and S.cgSign='$cgSign'";
 }

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT S.Id,S.Unit,S.Price,S.Qty,T.TypeName,T.Estate AS TypeEstate,S.WorkAdd,S.cgSign,S.Remark,S.Estate,S.Locks,S.Date,S.Operator,T.Id AS TId,T.Attached,C.cName,C.Linkman,C.Tel,G.Name AS GoodsType  
FROM $DataIn.zw3_purchases S 
LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId 
LEFT JOIN $DataPublic.zw_goodstype  G ON G.Id=T.TypeId 
LEFT JOIN $DataIn.retailerdata C ON C.Id=S.Cid 
WHERE 1 $SearchRows ORDER BY S.Date DESC";//AND S.Operator='$Login_P_Number' 
//echo $mySql;	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"];
		$TypeName=$myRow["TypeName"];
		//$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Remark=$myRow["Remark"];
		$LockRemark="";
		$TId=$myRow["TId"];
		$Attached=$myRow["Attached"];
        $GoodsType=$myRow["GoodsType"]==""?"&nbsp;":$myRow["GoodsType"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$TId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
			
	    $WorkAddFrom=$myRow["WorkAdd"];
	    if ($WorkAddFrom>0){
	       include "../model/subselect/WorkAdd.php"; 
	    }
	    else{
		    $WorkAdd="<div class='redB'>未设置</div>";
	    }
		
		
		$cgSign=$myRow["cgSign"];
		
		switch($cgSign){
			case 0:$cgSign="<div class='greenB'>已购回</div>";
				$LockRemark="记录已购回";
				break;
			case 1:$cgSign="<div class='redB'>未申购</div>";
				
				break;
			case 2:$cgSign="<div class='yellowB'>申购审核</div>";
				$LockRemark="记录审核中";
				break;
			case 3:$cgSign="<div class='yellowB'>待购</div>";
				break;
			}
		
		if ($myRow["TypeEstate"]==2) {
		   $TypeName="<div class='redB'>$TypeName</div>";
			$LockRemark="物品名称未审核";
		}

		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:$Estate="<div class='greenB'>已结付</div>";
				break;
			case 1:$Estate="<div class='redB'>未请款</div>";
				break;
			case 2:$Estate="<div class='yellowB'>请款中</div>";
				break;
			case 3:$Estate="<div class='yellowB'>未结付</div>";
				break;
			}
                $cName=$myRow["cName"];
                if ($cName!=""){
                    $cName="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$cName</span>";
                }
                else{
                    $cName="&nbsp;";
                }
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$TmpOperator=$Operator;
		
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		
		if(floor($Qty)==$Qty) { $Qty=floor($Qty); }
		$ValueArray=array(
			array(0=>$TypeName),
            array(0=>$GoodsType,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$WorkAdd,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$cgSign, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
            array(0=>$cName, 1=>"align='left'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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
<script type="text/javascript">
   function gotoPage(Flag){
       if (Flag==1){
         document.form1.action="zw_purchases_read.php";
	 document.form1.submit();
       }
       else{
         document.form1.action="zw_purchasesqk_read.php";
	 document.form1.submit();   
       } 
   }
</script>