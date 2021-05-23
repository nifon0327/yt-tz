<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<script>
function zhtj(obj){
	switch(obj){
		case "chooseDate"://改变采购
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="ck_th_read.php";
	document.form1.submit();
}
</script>
<?php 
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 物料退换列表");
$funFrom="ck_th";
$From=$From==""?"read":$From;
$sumCols="5";			//求和列,需处理
$MergeRows=4;
$Th_Col="退换日期|70|退换单号|75|图片说明|60|序号|40|配件Id|50|配件名称|300|单位|40|退换数量|60|原因|50|操作|100";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;	
//每页默认记录数量
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
$Keys=31;
$ActioToS="1";
$SearchRows.=" AND M.CompanyId=$myCompanyId";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	//$SearchRows="";
	$SearchRows=" AND M.CompanyId=$myCompanyId";
	$date_Result = mysql_query("SELECT A.Date FROM (
	     SELECT M.Date FROM $DataIn.ck2_thmain M WHERE 1 $SearchRows  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') 
	UNION ALL 
	     SELECT M.Date FROM $DataIn.ck12_thmain M WHERE 1 $SearchRows  GROUP BY DATE_FORMAT(M.Date,'%Y-%m')  
	)A GROUP BY A.Date ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}

	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.* FROM (
		SELECT '1' AS Sign,M.BillNumber,M.Date,M.Attached,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Locks,D.StuffCname,D.Picture,U.Name AS UnitName   
		FROM $DataIn.ck12_thsheet S
		LEFT JOIN $DataIn.ck12_thmain M ON S.Mid=M.Id 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
		WHERE 1 $SearchRows 
UNION ALL 
		SELECT '2' AS Sign,M.BillNumber,M.Date,M.Attached,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Locks,D.StuffCname,D.Picture,U.Name AS UnitName   
		FROM $DataIn.ck2_thsheet S
		LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
		WHERE 1 $SearchRows  AND S.Estate =0
)A ORDER BY A.Date DESC,A.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$ImgDir="../download/thimg/";
	do{
		$LockRemark="锁定操作!";
		$m=1;
		$Id=$mainRows["Id"];
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$Sign=$mainRows["Sign"];
		$BillNumber=$mainRows["BillNumber"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$BillNumberStr="<a href='../public/ck_th_view.php?f=$MidSTR&Sign=$Sign' target='_blank'>$BillNumber</a>";
		
		$upMian="<img location.href=\"#\"' style='CURSOR: hand'  src='../images/edit.gif' title='主单资料' width='13' height='13'>";	
		$Attached=$mainRows["Attached"];
		$Dir=anmaIn($ImgDir,$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached=$BillNumber.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: hand;color:#FF6633'>查看</span>";
			}
		else{
			$Attached="&nbsp;";
			}
		//明细资料
		$StuffId=$mainRows["StuffId"];		
		if($StuffId>0){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
		    //....获得旧系统的配件ID，方面供应商和仓库比对资料
		    include "../model/subprogram/get_oldStuffCname_StuffId.php"; 
		
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$Remark=trim($mainRows["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
			$Locks=$mainRows["Locks"];
			//检查是否有图片
			$Picture=$mainRows["Picture"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			include "../model/subprogram/stuffimg_model.php";
			
			$UpdateIMG="&nbsp;";
			
			$thTableReview=$Sign==1?"ck12_threview":"ck2_threview";
			$checkThSql=mysql_query("SELECT R.Estate,R.Remark  FROM $DataIn.$thTableReview R WHERE R.Mid='$Id' LIMIT 1",$link_id);
			if($checkThRows = mysql_fetch_array($checkThSql)){
			      $thEstate=$checkThRows["Estate"];
			      $thRemark=$checkThRows["Remark"];
			     if ($thEstate==2){
				    $UpdateIMG="<span class='redB'>有异议</span> &nbsp;&nbsp;<img src='../images/remark.gif' title='$thRemark' width='18' height='18'>"; 
			     }
			     else{
			         $UpdateIMG="<div class='greenB'>已审核通过</div>";
			     }
				 //$upMian="<img src='../images/lock.gif' title='锁定操作!' width='9' height='15'>";
				 
			}
			else{
				//$upMian="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Mid' >";
				$UpdateIMG="<span id='span_$Id'><img src='../images/Pass.png' width='25' height='25' onclick='onPassClick($Id,this,1,$Sign)'> &nbsp;&nbsp;&nbsp;&nbsp;
				<img src='../images/unPass.png' width='25' height='25' onclick='onPassClick($Id,this,2,$Sign)'></span>";
			}


			$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.gif' title='锁定操作!' width='9' height='15'>";
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
				//echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				//$unitWidth=$tableWidth-$Field[$m];
				//$m=$m+2;
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' >$Date</td>";//退换日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";//退换单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";//图片说明
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
					
				$unitFirst=$Field[$m]-1;
							
				//$m=$m+2;
				echo"<td class='A0001' width='$unitFirst' height='20' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";				//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//退换数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]'  align='center'>$Remark</td>";		//退换原因
				$m=$m+2;
				echo"<td  width='' align='center'>$UpdateIMG</td>";		//退换操作
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
				
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Date</td>";//退换日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";//退换单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";//图片说明
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
					
				$unitFirst=$Field[$m]-1;
				
				echo"<td class='A0001' width='$unitFirst' height='20' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";				//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//退换数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Remark</td>";		//退换原因
				$m=$m+2;
				echo"<td  width='' align='center'>$UpdateIMG</td>";		//退换操作
				echo"</tr></table>";
				$i++;
				$j++;
				}
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
List_Title($Th_Col,"0",1);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
   document.body.style.backgroundColor="#fff";
   
 function onPassClick(Id,ee,Action,Sign){
  
	 if (Action==2){
		   var remark=prompt("请填写原因:",""); 
		   remark=remark.replace(/^\s+|\s+$/g,"");//去除两边空格
		   if (remark=="") return false;
	}
	else{
		var remark="";
	}
	var url="ck_th_ajax.php?Id="+Id+"&Action="+Action+"&Sign="+Sign+"&Remark="+encodeURIComponent(remark)+"&sid="+Math.random(); 
	//alert(url);
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4){
	           //alert(ajax.responseText);
	          if (ajax.responseText=="Y")   document.getElementById("span_"+Id).innerHTML="&nbsp;";
			}
		}
　	ajax.send(null);   
}
</script>
