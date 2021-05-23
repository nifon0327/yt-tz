<?php  //二合一已更新?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td class="timeBottom" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class=''>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink">
					<nobr>
					<?php 
					echo $CustomFun;//自定义功能
					if($CheckFormURL=="thisPage"){
							if($SaveSTR!="NO"){
							echo"<span onClick='checkInput()' $onClickCSS>保存</span>&nbsp;";							
							}
							echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
						}
					else{
						if($SaveSTR!="NO"){
							$ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
							echo"<span onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' $onClickCSS>保存</span>&nbsp;";
							echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
							}
						}
					if($isBack!="N"){
						echo"&nbsp;<span onClick='javascript:ReOpen(\"$fromWebPage\");' $onClickCSS>返回</span>";
						}
					?>
					</nobr>					
				</td>
			</tr>
	 </table>
   </td>
   </tr>
   	<?php 
	if($Parameter!=""){
		PassParameter($Parameter);
		}
	?>
  </form>
</table>
</body>
</html>
<script language='javascript'>
  function checkInput(){
	//检查对应数量是否正确
	var Message="";
	if(document.form1.ProductId.value==""){
		alert("没有指定产品！");
		return false;
		}
		
	 var DataSTR="";
	 var Qty=document.getElementsByName('Qty[]');//对应关系
	 var bpRate=document.getElementsByName('bpRate[]');//备品率
	 var Unite=document.getElementsByName('Unite[]');//关联配件
	// var UniteRelation=document.getElementsByName('UniteRelation[]');//关联配件关系
	for(var i = 0; i<Qty.length; i++) {
		var thisData=getinnerText(ListTable.rows[i].cells[3]);
		thisData=thisData+"^"+Qty[i].value+"^"+bpRate[i].value+"^"+Unite[i].value;//+"^"+UniteRelation[i].value
		//alert(thisData);
		if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
		  }
	 }

	 if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="pands_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何配件！请先加入配件！");
			return false;
			}
  }
  
</script>