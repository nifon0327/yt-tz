<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php 
//步骤1 $DataIn.cwdyfsheet  二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
$sumCols="8";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 开发费用列表");
$funFrom="prayforcost";
//$Th_Col="选项|40|序号|40|项目ID|60|费用说明|450|费用|60|备注|40|单据|40|供应商|140|状态|40|申请人|50|请款日期|75";
$Th_Col="选项|40|序号|40|所属公司|60|项目ID|60|费用分类|70|请款说明|250|供应商|100|请款日期|65|请款金额|60|退回金额|60|货币类型|60|凭证|40|请款人|50|状态|40|备注|40|退回金额操作|90";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录

	if(!($Keys & mLOCK)){
		 $SearchRows=" and S.Operator=$Login_P_Number";
		}
	//$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cwdyfsheet S WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($dateValue==$chooseMonth){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";	
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}			
		}
		$SearchRows.=$PEADate;	
		
		$kftypedata_Result = mysql_query("SELECT Id,Name FROM $DataPublic.kftypedata WHERE Estate=1 order by Id",$link_id);
		if($kftypedata_Row = mysql_fetch_array($kftypedata_Result)){
			 echo "<select name='TypeID' id='TypeID' onchange='zhtj(this.name)' >";
			 echo "<option value='' >全  部</option>";
			do{
				$KName=$kftypedata_Row["Name"];
				$KId=$kftypedata_Row["Id"];
				if($KId==$TypeID){
					echo"<option value='$KId' selected>$KName</option>";
					$SearchRows.=" and K.id='$KId'";
					}
				else{	
					echo"<option value='$KId'>$KName</option>";
					}
				}while ($kftypedata_Row = mysql_fetch_array($kftypedata_Result));
			echo"</select>&nbsp;";	
			}
	
	switch($Estate){
		case "0":			$EstateSTR0="selected";			break;
		case "1":			$EstateSTR1="selected";			break;
		case "2":			$EstateSTR2="selected";			break;
		case "3":			$EstateSTR3="selected";			break;
		default:			$EstateSTR4="selected";			break;
		}	
	echo"<select name='Estate' id='Estate' onchange='zhtj(this.name)'>
	<option value='' $EstateSTR4>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	if ($Estate!=""){
      $SearchRows.=" and S.Estate=$Estate";
	}

		
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator,S.OutAmount,M.Mid,S.cSign
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	LEFT JOIN $DataIn.cw16_modelfee  M  ON M.Mid=S.Id
	WHERE 1 AND S.TypeID!='15' $SearchRows order by S.Date DESC";
	//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$KName=$myRow["KName"];
		$Description=$myRow["Description"]==""?"&nbsp":$myRow["Description"];
		$Amount=$myRow["Amount"];
		$OutAmount=$myRow["OutAmount"];
		$CName=$myRow["CName"];
		$ModelDetail=$myRow["ModelDetail"]==""?"&nbsp":$myRow["ModelDetail"];
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];	
		$sMid=$myRow["Mid"];			
			switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB' title='未处理'>×</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "3":
					$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "0":
					$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		//财务强制锁:非未处理皆锁定
		//array(0=>$ModelDetail,3=>"..."),
		echo "<input type='hidden' id='Amount$i' name='Amount$i' value='$Amount'>";
		$onclickstr="";
		if($OutAmount>0){
		    if($sMid==""){
		               $onclickstr="<input  type='button' id='addOutAmount' name='addOutAmount' onclick='addOut(\"$Id\",this,\"$i\")' value='模费退回'><input type='hidden' id='OutAmount$i' name='OutAmount$i' value='$OutAmount'><input type='hidden' id='Content$i' name='Content$i' value='$Description'>";
		                 }
		        else{
			             $onclickstr="<span class='greenB'>已加入</span>";
		                 }
		      }
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$ItemId,1=>"align='center'"),
			array(0=>$KName,1=>"align='center'"),
			array(0=>$Description,3=>"..."),
			array(0=>$Provider,3=>"..."),	
			array(0=>$Date,1=>"align='center'"),			
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$OutAmount,1=>"align='right'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,$ItemId,$Id)' style='CURSOR: pointer;'"),
			array(0=>$CName,1=>"align='center'"),
			array(0=>$Bill,1=>"align='center'"),			
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),		
			array(0=>$Remark,1=>"align='center'")	,	
			array(0=>$onclickstr,1=>"align='center'")				
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

<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function updateJq(TableId,ItemId,Id){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+10+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft+10+'px';	
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		InfoSTR="<input name='ItemId' type='text' id='ItemId' value='"+ItemId+"' size='8' class='TM0000' readonly>的退回金额:<input name='OutAmount' type='text' id='OutAmount' size='10' maxlength='10' class='INPUT0100'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+Id+","+TableId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}

function aiaxUpdate(TempId,index){
   var tempTableId=document.form1.ActionTableId.value;
	var tempOutAmount=parseFloat(document.form1.OutAmount.value);
	var Amount=parseFloat(document.getElementById("Amount"+index).value);
	//alert(tempOutAmount)
	if(tempOutAmount>Amount){alert("超出范围,请重新填写");return false;}
	if(tempOutAmount=="")CloseDiv();	
	   myurl="prayforcost_read_ajax.php?Id="+TempId+"&OutAmount="+tempOutAmount+"&ActionId=1";
       var ajax=InitAjax(); 
	   ajax.open("GET",myurl,true);
	   ajax.onreadystatechange =function(){
		   if(ajax.readyState==4 && ajax.status ==200){
               if (ajax.responseText=="Y"){
                     //alert(ajax.responseText);
                         //ListTable.
	                  }
		      	}
	      	}
	ajax.send(null); 
        CloseDiv();	
	}
	function addOut(TempId,e,index){
	      var OutAmount=document.getElementById("OutAmount"+index).value;
	      var Content=document.getElementById("Content"+index).value;
	      var msg="请确定模具费退回金额为:"+OutAmount;
	      if(confirm(msg)){
		 var myurl="prayforcost_read_ajax.php?Id="+TempId+"&OutAmount="+OutAmount+"&Content="+Content+"&ActionId=2";
		 //alert(myurl);
         var ajax=InitAjax(); 
	      ajax.open("GET",myurl,true);
	      ajax.onreadystatechange =function(){
		   if(ajax.readyState==4 && ajax.status ==200){
		    // alert( ajax.responseText);
               if (ajax.responseText=="Y"){
                       e.innerHTML="&nbsp;";
			           e.style.backgroundColor="#339900";
			          e.onclick="";
	                  }
		      	}
	      	}
	     ajax.send(null); 
	    }
	}
	
	function zhtj(obj){
	switch(obj){
		case "chooseMonth"://改变采购
			if(document.all("TypeID")!=null){
				document.forms["form1"].elements["TypeID"].value="";
				}
			if(document.all("Estate")!=null){
				document.forms["form1"].elements["Estate"].value="";
				}
		break;
		case "TypeID":
			if(document.all("Estate")!=null){
				document.forms["form1"].elements["Estate"].value="";
				}
		break;
		}
	document.form1.submit();
}

</script>