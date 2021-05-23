<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新SGS资料");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

$result = mysql_query("SELECT * FROM $DataIn.sgsdata WHERE Id=$Id",$link_id);
if($myrow = mysql_fetch_array($result)){
	$SgsId=$myrow["SgsId"];
	$SgsNo=$myrow["SgsNo"];
	$ItemC=$myrow["ItemC"];
	$ItemE=$myrow["ItemE"];
	$Type=$myrow["Type"];
	$CompanyId=$myrow["CompanyId"];
	}
	
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,SgsId,$SgsId,SgsNo,$SgsNo";
//步骤5：//需处理

?>
<script>
//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
function deleteImg(ImgId,rowIndex){
	var message=confirm("确定要删除原图片"+ImgId+"吗?");
	if (message==true){
		myurl="sgs_delimg.php?ImgId="+ImgId;
		document.form1.action=myurl;
		document.form1.submit();
		/*
		retCode=openUrl(myurl);
		if (retCode!=-2){
			NoteTable.deleteRow(rowIndex);
			ShowSequence(NoteTable);
			}
		else{
			alert("删除失败！");return false;
			}
		*/
		}
	}
//序号重置
//function ShowSequence(TableTemp){
	//for(i=1;i<TableTemp.rows.length;i++){ 
		//TableTemp.rows[i].cells[1].innerText=i;//
		//var j=i-1;
		//document.getElementsByName("Picture[]")[j].Row=i;
		//}
	//}   
function ShowSequence(TableTemp){
	//原档个数
	var oldNum=document.getElementsByName("OldImg[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldImg[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/sgsreport/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
			}
		else{
			TableTemp.rows[i].cells[1].innerHTML=i;//如果原序号带连接、带CSS的处理是？
			}
		document.getElementsByName("Picture[]")[j].Row=i;
		}
	}   

function AddRow(){
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0111";
	oTD.align="center";
	oTD.height="30";
				
	//第二列:序号
	oTD=oTR.insertCell(1);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0101";
	oTD.align="center";
				
	//三、说明
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='pdf,jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='3'>";
	oTD.className ="A0101";
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
      <table width="800" border="0" align="center" cellspacing="0">
		<tr>
            <td width="90" height="30" align="right" class="A1111" scope="col"> 客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td scope="col" class="A1101">
              <select name="CompanyId" id="CompanyId" style="width:490px">
			  <?php 
				$checkCompanySql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE  Estate=1  AND  ObjectSign IN (1,2) ORDER BY OrderBY DESC",$link_id);
				if($checkCompanyRow=mysql_fetch_array($checkCompanySql)){
					do{
						$theCompanyId=$checkCompanyRow["CompanyId"];
						$Forshort=$checkCompanyRow["Forshort"];
						if($CompanyId==$theCompanyId){
							echo"<option value='$theCompanyId' selected>$Forshort</option>";
							}
						else{
							echo"<option value='$theCompanyId'>$Forshort</option>";
							}
						}while($checkCompanyRow=mysql_fetch_array($checkCompanySql));
					}
				?>
              </select>
			  </td></tr>
          <tr>
            <td height="30" align="right" class="A0111">SGS编号*</td>
            <td class="A0101"><input name="SgsNo" type="text" id="SgsNo" value="<?php  echo $SgsNo?>" size="43" maxlength="14">
            /<input name="Type" type="text" id="Type" value="<?php  echo $Type?>" size="40" maxlength="2"></td>
          </tr>
          <tr>
            <td height="30" align="right" class="A0111">中文描述</td>
            <td class="A0101"><input name="ItemC" type="text" id="ItemC" value="<?php  echo $ItemC?>" size="92"></td>
          </tr>
          <tr>
            <td valign="top" align="right"  class="A0011">英文描述<br>*</td>
            <td class="A0001"><textarea name="ItemE" cols="59" rows="4" id="ItemE"><?php  echo $ItemE?></textarea></td>
	</tr>
</table>
<table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  <td width="50" align="center" class="A1101">序号</td>
			<td width="740" align="center" class="A1101">附件图片</td>
		</tr>
	<?php 
	//检查是否有旧文件,如果有则列出
	//如果没有
	$checkImgSql=mysql_query("SELECT Id,FileName FROM $DataIn.sgsfile WHERE SgsId=$SgsId ORDER BY Id",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$Id=$checkImgRow["Id"];
			$ImgName=$checkImgRow["FileName"];
			$Item="<a href='../download/sgsreport/$ImgName' target='_black'><div class='redB'>$i</div></a>";
			echo"
			<tr>
				<td class='A0111' align='center' height='30'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$Id\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a></td>
				<td class='A0101' align='center'>$Item</td>
				<td class='A0101'><input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='$i' Cel='3'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{
	?>
		<tr>
            <td class="A0111" align="center" height="30"><input name="OldImg[]" type="hidden" id="OldImg[]">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
            <td class="A0101"><input name="Picture[]" type="file" id="Picture[]" size="80" DataType="Filter" Accept="pdf,jpg" Msg="格式不对,请重选" Row="1" Cel="3"></td>
    	</tr>
	<?php 
	}
	?>
	</table>   </td></tr></table>
   
<?php 
include "../model/subprogram/add_model_b.php";
?>