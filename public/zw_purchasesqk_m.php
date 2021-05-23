<?php 
/*
$DataIn.zw3_purchases
$DataIn.zw3_purchaset
二合一已更新
电信-joseph
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=600;
$sumCols=8;		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 总务申购请款审核");
$funFrom="zw_purchasesqk";
$Th_Col="选项|40|序号|40|采购物品名称|100|图片|40|数量|50|单位|50|历史单价|70|单价|50|小计|60|使用地点|50|采购说明|260|申购状态|60|申购日期|75|申购人|50|采购|50|凭证|40|上次<br>购买日期|60|上次<br>购买单价|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
        //$sBranchId=$sBranchId==""?1:$sBranchId;
        $selSignStr="selSign" . $sBranchId;
        $$selSignStr="selected";
	echo"<select name='sBranchId' id='sBranchId' onchange='ResetPage(this.name)'>";
        echo"<option value='' $selSign>全部</option>";
        echo"<option value='1' $selSign1>总务</option>";
        echo"<option value='4' $selSign4>开发</option>";
        echo"<option value='3' $selSign3>采购</option>";
        echo"<option value='99' $selSign99>其它</option>";
        echo"</select>&nbsp;";
        
        switch($sBranchId){
            case 1:
                $SearchRows=" AND (M.BranchId=1 OR M.BranchId=8)";   
                break;
            case 3:
                $SearchRows=" AND M.BranchId=3";   
                break;
            case 4:
                $SearchRows=" AND M.BranchId=4";   
                break;
            case 99:
                $SearchRows="  AND M.BranchId NOT IN (1,3,4,8)";   
                break;
            default: 
                break;
        }
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Unit,S.Price,S.Qty,S.TypeId,T.TypeName,S.WorkAdd,S.cgSign,S.Remark,S.Estate,M.Name AS Buyer,S.Bill,S.Locks,S.Date,S.Operator,T.Id AS TId,T.Attached
FROM $DataIn.zw3_purchases S 
LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
WHERE 1 $SearchRows AND S.Estate=2 AND cgSign='0' ORDER BY S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"];
		$Amount=sprintf("%.2f",$Price*$Qty);
		$TypeId=$myRow["TypeId"];
		//历史单价,最大值和最小值
		$checkPrice=mysql_query("SELECT MAX(Price) AS maxPrice,MIN(Price) AS minPrice FROM $DataIn.zw3_purchases WHERE 1 AND TypeId='$TypeId' ORDER BY TypeId",$link_id);
		$maxPrice=mysql_result($checkPrice,0,"maxPrice");
		$minPrice=mysql_result($checkPrice,0,"minPrice");
		if($maxPrice==""){
				$PriceInfo="&nbsp;";
				}
			else{
				$PriceInfo="<a href='zw_historyprice.php?TypeId=$TypeId' target='_blank' title='最低历史单价: $minPrice 最高历史单价: $maxPrice'>查看</a>";
				}

		$TypeName=$myRow["TypeName"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":trim($myRow["Remark"]);
		$cwSgin="<div class='greenB'>已购回</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
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
			
		$WorkAddFrom=$myRow["WorkAdd"];
	    if ($WorkAddFrom>0){
	       include "../model/subselect/WorkAdd.php"; 
	    }
	    else{
		    $WorkAdd="<div class='redB'>未设置</div>";
	    }

		//上次购买记录       
                 $PriceResult = mysql_query("SELECT S.Price,S.Date
                        FROM $DataIn.zw3_purchases S 
                        WHERE S.TypeId=$TypeId AND S.Price>0 AND S.Id<'$Id' ORDER BY S.Date DESC LIMIT 1",$link_id);
                if ($PriceResult ){
                  if($PriceRows = mysql_fetch_array($PriceResult)){
                        $lastDate=$PriceRows["Date"];
                        $lastPrice=$PriceRows["Price"];
                        if ($lastPrice<$Price){
                               $Price="<div class='redB'>$Price</div>";
                              }
                        else{
                           if ($lastPrice>$Price){
                                $Price="<div class='greenB'>$Price</div>";
                              } 
                        }
                    }
                    else{
                         $lastDate="&nbsp;";
                         $lastPrice="&nbsp;"; 
                        }
                  }
                 else{
                       $lastDate="&nbsp;";
                       $lastPrice="&nbsp;"; 
                    }
         if(floor($Qty)==$Qty) { $Qty=floor($Qty); }
		$ValueArray=array(
			array(0=>$TypeName),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$PriceInfo,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$WorkAdd, 1=>"align='center'"),
			array(0=>$Remark, 1=>"align='center'"),
			array(0=>$cwSgin, 1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$Buyer, 1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
		   array(0=>$lastDate, 1=>"align='center'"),
			array(0=>$lastPrice, 1=>"align='center'")
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