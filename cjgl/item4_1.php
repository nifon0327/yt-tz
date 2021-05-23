<?php
//电信-zxq 2012-08-01
$ClientList="";
$nowInfo="当前:订单设置";
if($SearchOrder!=""){
	$SearchRows="S.scFrom>0 AND S.Estate=1 AND (P.cName LIKE '%$SearchOrder%' OR P.eCode LIKE '%$SearchOrder%')";
	$ClientList="来自于查询页面&nbsp;&nbsp;<input class='ButtonH_25' type='button'  onclick='document.form1.submit()' value='返  回'/>";
	$scFrom=$NewscFrom;

}
else{
$SearchRows="S.scFrom>0 AND S.Estate=1";//生产状态：1、未生产，或2、生产中；出货状态：1、未出货
if($CompanyId==1)$CompanyStr='selected';else $CompanyStr="";
$ClientResult= mysql_query("
SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
	WHERE $SearchRows GROUP BY M.CompanyId order by M.CompanyId ,M.OrderDate desc",$link_id);
if ($ClientRow = mysql_fetch_array($ClientResult)){
	$ClientList="<select name='CompanyId' id='CompanyId'  onChange='ResetPage(1,4)'>";
	$i=1;
    //$ClientList.="<option value='' >全部</option>";
	do{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
		if($CompanyId==$theCompanyId){
			$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theCompanyId'";
			$nowInfo.=" - ".$theForshort;
			}
		else{
			$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($ClientRow = mysql_fetch_array($ClientResult));
         $ClientList.="<option value='1'  $CompanyStr>全部</option>";
		$ClientList.="</select>";
	}

      // $scFrom=$scFrom==""?1:$scFrom;
        $TempEstateSTR="SignTypeStr".strval($scFrom);
	    $$TempEstateSTR="selected";
	    $SignList.="<select name='scFrom' id='scFrom' style='width:150px' onChange='ResetPage(1,4)'>";
		$SignList.="<option value='0' $SignTypeStr1>全部</option>";
		$SignList.="<option value='13' $SignTypeStr13>已备料订单</option></select>";
         switch($scFrom){
            case 0:
                   $SearchRows.="";
             break;
           case 13:
                  $blSignType=13;
             break;

                    }

	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName,C.Color 
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE  $SearchRows GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);

	if ($TypeRow = mysql_fetch_array($TypeResult)){
		$thisType="<select name='TypeId' id='TypeId' onchange='ResetPage(1,4)'>";
		$thisType.="<option value='' selected>全部</option>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			$Color=$TypeRow["Color"]==""?"#FFFFFF":$TypeRow["Color"];
			if($TypeId==$theTypeId){
				$thisType.="<option value='$theTypeId' style= 'color: $Color;font-weight: bold' selected>$TypeName</option>";
				$SearchRows.=" AND P.TypeId='$theTypeId'";
				$nowInfo.=" - ".$TypeName;
				}
			else{
				$thisType.="<option value='$theTypeId' style= 'color: $Color;font-weight: bold'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($TypeResult));
		$thisType.="</select>&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		// OrderPO
    $POResult= mysql_query("SELECT S.OrderPO
              FROM $DataIn.yw1_ordersheet S
              LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
              LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
              LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
              LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
              WHERE $SearchRows GROUP BY S.OrderPO",$link_id);
    if ($PORow = mysql_fetch_array($POResult)){
        $thisPO="<select name='OrderPO' id='OrderPO' onchange='ResetPage(1,4)'>";
        $thisPO.="<option value='all' selected>全部</option>";
        do{
            $theOrderPO=$PORow["OrderPO"];
            $OrderPO=$OrderPO==""?$theOrderPO:$OrderPO;
            if($OrderPO==$theOrderPO){
                $thisPO.="<option value='$theOrderPO' selected>$theOrderPO</option>";
                $SearchRows.=" AND S.OrderPO='$theOrderPO'";
            }
            else{
                $thisPO.="<option value='$theOrderPO'>$theOrderPO</option>";
            }
        }while($PORow = mysql_fetch_array($POResult));
        $thisPO.="</select>&nbsp;&nbsp;&nbsp;&nbsp;";
    }

		//****************拆单权限
	/*
	if($Login_P_Number==10360||$Login_P_Number==10801||$Login_P_Number==10037||$Login_P_Number==10691 ||$Login_P_Number==10871 ||$Login_P_Number==10325||$Login_P_Number==11203 ||$Login_P_Number==10002 || $Login_P_Number==10214 || $Login_P_Number==10868 || $Login_P_Number==11008){
		 $SplitStr="<input class='ButtonH_25' type='button'  id='splitBut' name='splitBut' onclick='splitOrder()' value='拆 单'>";
		    }
	    else{
		   $SplitStr="";
		   }
*/
}

$Th_Col="选项|35|配件|35|ID|30|客户|80|PO|70|中文名|225|Product Code|90|Qty|50|出货<br>方式|40|物料|45|备料|45|组装|45|期限|45|生管备注|100|订单备注|100|<span class='redB'>可否拆单</span>|50";
$Cols=15;
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;;
//步骤5：

if($ClientList!=""){
	echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
	<tr>
	<td colspan='".($Cols-4)."' height='40px' class=''>$ClientList &nbsp; $thisType &nbsp;$thisPO $SignList &nbsp; &nbsp;&nbsp;$SplitStr</td><td colspan='4' align='center' class=''><input name='SearchOrder' id='SearchOrder' type='text' size='20'><input class='ButtonH_25' type='button'  id='Querybtn' name='Querybtn' onclick='SearchScOrder()' value='查 询'></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$j=2;
$i=1;$today=date("Y-m-d");
$mySql="SELECT 
S.Id,O.Forshort,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.ShipType,S.scFrom,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.CompanyId,U.Name AS Unit,M.OrderDate,S.dcRemark,S.sgRemark,M.OrderDate,S.PackRemark
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
WHERE $SearchRows ORDER BY M.OrderDate";
//echo "$mySql";
$SumQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$AskDay="";
		$czSign=1;//操作标记
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		$Id=$myRow["Id"];
		$Forshort=$myRow['Forshort'];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
       $PackRemark=$myRow["PackRemark"]==""?"&nbsp;":$myRow["PackRemark"];
	    $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":"<span class='redB'>(".$myRow["dcRemark"].")</span>";
        $Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
        $OrderDate=$myRow["OrderDate"];
        include "../admin/order_date.php";
         if ($blSignType==13 && $sc_cycle=="&nbsp;")
                {
                    continue;
                }
       $SumQty=$SumQty+$Qty;
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$ShipType=$myRow["ShipType"]==""?"&nbsp;":$myRow["ShipType"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		$OrderDate=CountDays($OrderDate,0);
		$sgRemark=$myRow["sgRemark"]==""?$dcRemark:$myRow["sgRemark"]."<br>".$dcRemark;
		$unCg=0;
		//订单状态色
		$checkColor=mysql_query("SELECT S.Id FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE S.Mid='0' and (S.FactualQty>'0' OR S.AddQty>'0' ) AND S.PorderId='$POrderId' AND T.mainType<2 LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor) && mysql_affected_rows()>0){
			$OrderSignColor="bgColor='#69B7FF'";//有未下需求单
			//$czSign=0;//操作标记:不可操作
			}
		else{//已全部下单，看领料数量
			$OrderSignColor="bgColor='#FFCC00'";	//设默认绿色
			//生产数量不等时，黄色	不能审核
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
			$scQty=$CheckscQty["scQty"];
			if($gxQty==$scQty){
				$OrderSignColor="bgColor='#339900'";	//黄色	不能审核
				//$czSign=0;//不能审核,状态出错
				}
			}
		$ColbgColor="";$NoConfirm=0;
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
					case 2:$ColbgColor="bgcolor='#FF0000'";$czSign=0;$NoConfirm=1;break;		//未确定产品
					case 7:$theDefaultColor="#FFA6D2";break;		//加急
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}

          if ($blSignType==13 && $NoConfirm==1) continue;
        //限制登记分批出
	//	echo $i."-".2."<br>";
          $CheckDisable="";$splitEstate="";
          if($blSign==1){
               $CheckDisable="disabled";
               $splitEstate="";
             }
         else {
                      if($llSign==0){//库存足够显示
                            $CheckDisable="";
                            $splitEstate="<img src='../images/ok.gif' width='30' height='30'";
                              }
                      else{
                            $CheckDisable="disabled";
                               $splitEstate="";
                               }
              }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

		$chooseStr="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id' $CheckDisable>";
		echo"<tr bgcolor='$theDefaultColor'><td class='A0111' align='center'  height='25' valign='middle' >$chooseStr</td>
		<td class='A0101' align='center' id='theCel$i' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
		echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
        echo"<td class='A0101' align='center'>$Forshort</td>";
		echo"<td class='A0101' align='center'>$OrderPO</td>";
		echo"<td class='A0101'>$TestStandard</td>";
		echo"<td class='A0101'>$eCode</td>";
		//echo"<td class='A0101' align='center'>$CaseReport</td>";
		echo"<td class='A0101' align='right'>$Qty</td>";
		echo"<td class='A0101' align='center'>$ShipType</td>";
        echo"<td class='A0101' align='center'>$wl_cycle</td>";
        echo"<td class='A0101' align='center'>$bl_cycle</td>";
        echo"<td class='A0101' align='center'>$sc_cycle</td>";
		echo"<td class='A0101' align='center' $BackImg>$OrderDate</td>";
		echo "<td class='A0101' onclick='InputRemark($j,$Id)'>$sgRemark</td>";
        echo"<td class='A0101' >$PackRemark</td>";
        echo"<td class='A0101' align='center'>$splitEstate</td>";
		echo"</tr>";
		echo $ListRow;
		$j=$j+2;$i++;
		}while ($myRow = mysql_fetch_array($myResult));
        echo"<tr><td class='A0111' align='center' valign='middle' height='25'  colspan='7'>总    计</td>";
		echo"<td class='A0101' align='right'>$SumQty</td>";
		echo"<td class='A0101' align='center' colspan='8'>&nbsp;</td></tr>";
		echo "</table>";
		echo "<input type='hidden' id='AllId' name='AllId' value='$i'";
	}
	}
else{
	echo"<div class='redB' style='background-color: #ffffff'>没有记录!</div>";
	}
	?>
	<input id="NewscFrom" type="hidden" name="NewscFrom" value="<?php    echo $scFrom?>" />
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
</html>
<script src='dateboard.js' type=text/javascript></script>
<script>
var Img2="<img src='../images/register.png' width='30' height='30'>";
var dateboard=new DateBoard();

function RegisterEstate(POrderId,e){
	var url="item4_ajax.php?POrderId="+POrderId+"&ActionId=4";
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	alert("设定成功");
			//更新该单元格底色和内容
			e.innerHTML="&nbsp;";
			e.style.backgroundColor="#339900";
			e.onclick="";
			}
		}
　	ajax.send(null);
	}

function setNewDate(POrderId,e,Flag){
	var eStr=e.innerHTML;
	var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
	var saveFun=function(){saveDate(POrderId,e);};
	var delFun=function(){UpdateAjax(POrderId,e,'');};
    dateboard.show(e,2,saveFun,delFun);
}

function saveDate(POrderId,e){
    var eStr=e.innerHTML;
	var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
	if (!reg.test(eStr)){
	     alert("请输入正确的日期！");
		 return false;
	}
     UpdateAjax(POrderId,e,eStr);
}

function UpdateAjax(POrderId,e,sDate){
	var eStr,ActionId,url;

	if (sDate==""){
	    ActionId="23";
	    url="item4_ajax.php?POrderId="+POrderId+"&ActionId="+ActionId;
		eStr=Img2;
	}
	else{
	   ActionId="21";
	   var url="item4_ajax.php?POrderId="+POrderId+"&sDate="+sDate+"&ActionId="+ActionId;
	   eStr=sDate;
	}
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			//更新该单元格内容
			if (ajax.responseText=="Y"){
			  //alert ("数据更新成功！");
		       e.innerHTML=eStr;
			}else{
				alert ("数据更新失败！");
				e.innerHTML=Img2;
			}
		}
	}
　	ajax.send(null);
}
function SearchScOrder(){
    // var SearchOrder=document.getElementById("SearchOrder").value;
     document.form1.submit();

}

function splitOrder(){
   var j=0;
   var checkdata;
   var Temp;
   var index=document.getElementById("AllId").value;
    for(var i=1;i<=index-1;i++)
	 {
	  var checkid="checkid"+i;
	  var checkStr=document.getElementById(checkid);
	  if(checkStr.checked){j++;Temp=i;}
	  if(j>1)document.getElementById("checkid"+Temp).checked=false;
	 }
	if(j==0){alert("请选择相关记录");return false;}
	if(j>1){alert("只能选一条记录");return false;}
	checkdata=document.getElementById("checkid"+Temp).value;
	divShadow.innerHTML="";
	var url="item4_1_split.php?Id="+checkdata;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}
function ChangeQty(){
    var Relation=document.getElementById("Relation").value;
	var QtyTemp=document.getElementById("Qty").value;
	var Qty1Temp=document.getElementById("Qty1").value;
	var Qty2Temp=QtyTemp-Qty1Temp;
	var BoxNum=Qty1Temp%Relation;
	if(BoxNum>0){
	Qty1Temp.value="";
	alert("请确定输入的数量是整箱数!");
	document.getElementById("Qty1").value="";
	document.getElementById("Qty2").value="";
	return false;
	};
	var Result=fucCheckNUM(Qty1Temp,'');
	if(Result==0){
		alert("输入了不正确的数量:"+Qty1Temp+",重新输入!");
		document.getElementById("Qty2").value="";
		return false;
		}
	else{
		if((Qty1Temp*1>=QtyTemp*1) || (Qty1Temp*1==0)){
			alert("拆分的数量不对!拆分的子单数量不能为0或>=原单的数量");
			document.getElementById("Qty1").value="";
			document.getElementById("Qty2").value="";
			return false;
			}
		else{
			document.getElementById("Qty2").value=Qty2Temp;
			}
		}
	if(document.getElementById("Qty2").value!="")document.getElementById("spiltBtn2").disabled=false;
	}

function ToSplietOrder(llSign){
	//后台保存
	var RemarkStr;
	var Id=document.getElementById("Id").value;
	var POrderId=document.getElementById("POrderId").value;
	var Qty=document.getElementById("Qty").value;
	var Qty1=document.getElementById("Qty1").value;
	var Qty2=document.getElementById("Qty2").value;
	if(Qty1==""){alert("请输入拆单数量");return false;}
	if(llSign==1){
	    var SpiltRemark=document.getElementById("SpiltRemark").value;
		if(SpiltRemark==""){alert("请输入拆单原因");return false;}
		RemarkStr="&SpiltRemark="+SpiltRemark;
	    }
	var msg="确定拆单";
	if(confirm(msg)){
	var url="item4_1_split_ajax.php?Id="+Id+"&llSign="+llSign+"&POrderId="+POrderId+"&Qty1="+Qty1+"&Qty2="+Qty2+"&Qty="+Qty+RemarkStr;
    document.getElementById("Qty1").value="";
    document.getElementById("Qty2").value="";
	var ajax=InitAjax();
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
		  //alert(ajax.responseText);
			if(ajax.responseText=="Y"){//更新成功
				document.form1.submit();
				}
			}
		}
	ajax.send(null);
	}
}
</script>