<?php 
/*
$DataIn.cwxzsheet$DataIn.电信---yang 20120801
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工薪资资料");//需处理
$fromWebPage=$funFrom."_m";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("
SELECT 
S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Remark,M.Name,S.Gjj,S.Ct
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number WHERE 1 and S.Id='$Id' LIMIT 1",$link_id));
$Number=$upData["Number"];
$Name=$upData["Name"];
$Dx=$upData["Dx"];
$Jbf=$upData["Jbf"];	
$Gljt=$upData["Gljt"];
$Gwjt=$upData["Gwjt"];
$Jj=$upData["Jj"];
$Shbz=$upData["Shbz"];
$Zsbz=$upData["Zsbz"];
$Jtbz=$upData["Jtbz"];
$Jbf=$upData["Jbf"];
$Yxbz=$upData["Yxbz"];
$taxbz=$upData["taxbz"];
$Jz=$upData["Jz"];
$Sb=$upData["Sb"];
$Gjj=$upData["Gjj"];
$Ct=$upData["Ct"];
$Kqkk=$upData["Kqkk"];
$dkfl=$upData["dkfl"];   ////有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529,一天74块钱扣,但应到工时，要加上它。 add by zx 20130529

$RandP=$upData["RandP"];
$Otherkk=$upData["Otherkk"];
//$Amount=$upData["Amount"];
$Remark=$upData["Remark"];

$Amount=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk-dkfl-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk;
//$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk;
//$AmountSys=$Total-$Jz-$Sb-$RandP-$Otherkk;	
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,chooseMonth,$chooseMonth";
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";

//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center">
          <tr>
            <td width="169" align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td width="521" scope="col"><?php  echo $Name;?></td>
          </tr>
          <tr>
            <td align="right">底&nbsp;&nbsp;&nbsp;&nbsp;薪</td>
            <td> &nbsp;+ 
              <input name="Dx" type="text" class="inR0000" id="Dx" value="<?php  echo $Dx?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">加 班 费</td>
            <td> &nbsp;+
              <input name="Jbf" type="text" class="inR0000" id="Jbf" value="<?php  echo $Jbf?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">工龄津贴</td>
            <td> &nbsp;+
              <input name="Gljt" type="text" class="inR0000" id="Gljt" value="<?php  echo $Gljt?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">岗位津贴</td>
            <td> &nbsp;+
              <input name="Gwjt" type="text" class="inR0000" id="Gwjt" value="<?php  echo $Gwjt?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">奖&nbsp;&nbsp;&nbsp;&nbsp;金</td>
            <td> &nbsp;+
            <input name="Jj" type="text" class="inR0100" id="Jj" value="<?php  echo $Jj?>" size="5" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
          </tr>
          <tr>
            <td align="right" valign="top">生活补助</td>
            <td> &nbsp;+
            <input name="Shbz" type="text" class="inR0000" id="Shbz" value="<?php  echo $Shbz?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">住宿补助</td>
            <td> &nbsp;+
            <input name="Zsbz" type="text" class="inR0000" id="Zsbz" value="<?php  echo $Zsbz?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">交通补助</td>
            <td> &nbsp;+
            <input name="Jtbz" type="text" class="inR0000" id="Jtbz" value="<?php  echo $Jtbz?>" size="5" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
          </tr>
          <tr>
            <td align="right" valign="top">夜宵补助</td>
            <td> &nbsp;+
            <input name="Yxbz" type="text" class="inR0000" id="Yxbz" value="<?php  echo $Yxbz?>" size="5" readonly></td>
          </tr>
           <tr>
            <td align="right" valign="top">个税补助</td>
            <td> &nbsp;+
            <input name="taxbz" type="text" class="inR0000" id="taxbz" value="<?php  echo $taxbz?>" size="5" readonly></td>
          </tr>         
          <tr>
            <td align="right" valign="top">考勤扣款</td>
            <td> &nbsp;-
            <input name="Kqkk" type="text" class="inR0000" id="Kqkk" value="<?php  echo $Kqkk?>" size="5" readonly></td>
          </tr>
           <tr>
            <td align="right" valign="top">福利扣款</td>
            <td> &nbsp;-
            <input name="dkfl" type="text" class="inR0000" id="dkfl" value="<?php  echo $dkfl?>" size="5"  onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
          </tr>         
          <tr>
            <td align="right" valign="top">借&nbsp;&nbsp;&nbsp;&nbsp;支</td>
            <td> &nbsp;-
              <input name="Jz" type="text" class="inR0000" id="Jz" value="<?php  echo $Jz?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">社&nbsp;&nbsp;&nbsp;&nbsp;保</td>
            <td> &nbsp;-
            <input name="Sb" type="text" class="inR0000" id="Sb" value="<?php  echo $Sb?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">住房公积金</td>
            <td> &nbsp;-
            <input name="Gjj" type="text" class="inR0000" id="Gjj" value="<?php  echo $Gjj?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">餐费扣款</td>
            <td> &nbsp;-
            <input name="Ct" type="text" class="inR0000" id="Ct" value="<?php  echo $Ct?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">个&nbsp;&nbsp;&nbsp;&nbsp;税</td>
            <td> &nbsp;+
            <input name="RandP" type="text" class="inR0000" id="RandP" value="<?php  echo $RandP?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">其&nbsp;&nbsp;&nbsp;&nbsp;它</td>
            <td> &nbsp;-
            <input name="Otherkk" type="text" class="inR0100" id="Otherkk" value="<?php  echo $Otherkk?>" size="5" onchange="javascript:CheckNum(this,0)" onfocus="toTempValue(this.value)"></td>
          </tr>
          <tr>
            <td align="right" valign="top">合&nbsp;&nbsp;&nbsp;&nbsp;计</td>
            <td> &nbsp;=
            <input name="Amount" type="text" class="totalINPUT" id="Amount" value="<?php  echo $Amount?>" size="5" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="60" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
   </table>
</td></tr></table>
<input name="TempValue" type="hidden" id="TempValue">
<input name="Number" type="hidden" id="Number" value="<?php  echo $Number?>">
<input name="DataIn" type="hidden" id="DataIn" value="<?php  echo $DataIn?>">

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function CheckNum(obj,Sign){
	var oldValue=document.form1.TempValue.value;
	var theNumber=obj.value;
	var checkNum=fucCheckNUM(theNumber);
	if(checkNum==0){
		alert("格式不对");
		obj.value=oldValue;
		obj.select();
		//回值，并选取
		}
	else{
		//重新求和
		//差值
		var xvalue=oldValue*1-theNumber*1;
		if(Sign==1){
			GetAmount();
			//document.form1.Amount.value=document.form1.Amount.value*1-xvalue*1;
			}
		else{
			GetAmount();
			//document.form1.Amount.value=document.form1.Amount.value*1+xvalue*1;
			}
		}
	}

function GetAmount()
{

	//$TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz-$Kqkk-$Sb-$Otherkk;//+$Holidayjb+假日加班费
			//底新+工龄津贴+岗位津贴+奖金+生活补助+住宿补助+加班费+夜宵补助+交通补助-考勤扣款-个人社保
	
	var Dx=document.getElementById("Dx").value*1; 
	
	var Gljt=document.getElementById("Gljt").value*1; 
	var Gwjt=document.getElementById("Gwjt").value*1; 
	var Jj=document.getElementById("Jj").value*1; 
	var Shbz=document.getElementById("Shbz").value*1; 
	var Zsbz=document.getElementById("Zsbz").value*1; 
	var Jbf=document.getElementById("Jbf").value*1; 
	var Yxbz=document.getElementById("Yxbz").value*1; 
	var Jtbz=document.getElementById("Jtbz").value*1; 
	var Kqkk=document.getElementById("Kqkk").value*1;
	
	var dkfl=document.getElementById("dkfl").value*1;
	var Jz=document.getElementById("Jz").value*1;
	var Sb=document.getElementById("Sb").value*1; 
	var Gjj=document.getElementById("Gjj").value*1; 
	var Ct=document.getElementById("Ct").value*1; 
	var Otherkk=document.getElementById("Otherkk").value*1; 
	var SNumber=document.getElementById("Number").value; 
	var DataIn=document.getElementById("DataIn").value;
	var TaxAmount=(Dx+Gljt+Gwjt+Jj+Shbz+Zsbz+Jbf+Yxbz+Jtbz-Kqkk-dkfl-Sb-Gjj-Ct-Otherkk)*1;
	
	var taxbz=0;
	RandP=0;
	/*
	if(TaxAmount>=2000){
		if(TaxAmount>2500){
			if(TaxAmount>4000){
				RandP=175;
				}
			else{
				RandP=(TaxAmount-2000)*0.1-25;
				}
			}
		else{//2000-2500
			RandP=(TaxAmount-2000)*0.05;
			}
		}
	*/
		if(TaxAmount>3500){
			if(TaxAmount>4000){
				RandP=15;
				}
			else{//2000-2500
				RandP=(TaxAmount-3500)*0.03;
				//echo "$RandP=($TaxAmount-2000)*0.05 <br>";
				}
			}	
		
	//alert(DataIn.toUpperCase());
	 //龙宝不用扣
		
	if((SNumber==10383) || (SNumber==10138)  || (SNumber==10943) || (SNumber==10855) || (SNumber==11136) ){
			if(DataIn.toUpperCase()!="D5"){
			RandP=15; //175;
			}
			else{RandP=0;}

		}
	
	//if((SNumber==10001) || (SNumber==10822)){RandP=0;}
	if((SNumber==10001) || (SNumber==10822) || (SNumber==10943) || (SNumber==10855) || (SNumber==11136) ){$RandP=0;} 
	RandP=Math.round(RandP);
	/*
	if(RandP>=175){
		taxbz=100;  //个税补
	}
	*/
	//alert("123");
	taxbz=0;
	var Amount=Dx+Gljt+Gwjt+Jj+Shbz+Zsbz+Jbf+Yxbz+Jtbz+taxbz-Jz-Sb-Gjj-Ct-Kqkk-dkfl-Otherkk-RandP;
	document.getElementById("taxbz").value=taxbz;
	document.getElementById("RandP").value=RandP;
	document.getElementById("Amount").value=Amount;
	
	


}
	
</script>