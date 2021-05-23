<?php 
/*
加入加班奖金、餐费 ewen 2013-08-04
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工薪资资料");//需处理
//$fromWebPage=$funFrom."_m";		
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("
SELECT 
S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Jbjj,S.Ywjj,S.Yxbz,S.taxbz,S.Studybz,S.Housebz,S.Jz,S.Sb,S.Kqkk,S.Gjj,S.Ct,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Remark,S.Currency,S.Month,M.Name,M.KqSign  
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Number WHERE 1 and S.Id='$Id' LIMIT 1",$link_id));
$Number=$upData["Number"];
$Name=$upData["Name"];
$KqSign=$upData["KqSign"];
$Dx=$upData["Dx"];
$Jbf=$upData["Jbf"];	
$Gljt=$upData["Gljt"];
$Gwjt=$upData["Gwjt"];
$Jj=$upData["Jj"];
$Shbz=$upData["Shbz"];
$Zsbz=$upData["Zsbz"];
$Jtbz=$upData["Jtbz"];
$Studybz=$upData["Studybz"];
$Housebz=$upData["Housebz"];
$Jbf=$upData["Jbf"];
$Jbjj=$upData["Jbjj"];
$Ywjj=$upData["Ywjj"];
$Yxbz=$upData["Yxbz"];
$taxbz=$upData["taxbz"];
$Jz=$upData["Jz"];
$Sb=$upData["Sb"];
$Gjj=$upData["Gjj"];
$Currency=$upData["Currency"];
$Kqkk=$upData["Kqkk"];
$dkfl=$upData["dkfl"];   

$RandP=$upData["RandP"];
$Otherkk=$upData["Otherkk"];
$Ct=$upData["Ct"];
$Remark=$upData["Remark"];

$Month=$upData["Month"];
$hdjbf=0;
$checkResult = mysql_query("SELECT Amount FROM $DataIn.hdjbsheet WHERE Number='$Number' and Month='$Month'",$link_id); 
if($checkRow = mysql_fetch_array($checkResult)){
    $hdjbf=sprintf("%.0f",$checkRow["Amount"]);
	$Jbjj+=$hdjbf;
}

$Amount=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Ywjj+$Shbz+$Zsbz+$Jtbz+$Studybz+$Housebz+$Yxbz+$taxbz-$Kqkk-$dkfl-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk;//+$Jbjj

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,chooseMonth,$chooseMonth";
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";

//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" id="NoteTable">
          <tr>
            <td width="169" align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td width="201" scope="col">：&nbsp;<?php  echo $Name;?></td>
            <input name="Currency" type="hidden"  id="Currency" value="<?php  echo $Currency?>" >
            <input name="KqSign" type="hidden"  id="KqSign" value="<?php  echo $KqSign?>" >
            <input name="Month" type="hidden"  id="Month" value="<?php  echo date("Ym",strtotime($Month))?>" >
            <input name="Hdjbf" type="hidden"  id="Hdjbf" value="<?php  echo $hdjbf?>" >
            <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right">底&nbsp;&nbsp;&nbsp;&nbsp;薪</td>
            <td> &nbsp;+ 
              <input name="Dx" type="text" class="inR0000" id="Dx" value="<?php  echo $Dx?>" size="8" readonly></td>
              <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">加 班 费</td>
            <td> &nbsp;+
              <input name="Jbf" type="text" class="inR0000" id="Jbf" value="<?php  echo $Jbf?>" size="8" readonly></td>
              <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">工龄津贴</td>
            <td> &nbsp;+
              <input name="Gljt" type="text" class="inR0000" id="Gljt" value="<?php  echo $Gljt?>" size="8" readonly></td>
               <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">岗位津贴</td>
            <td> &nbsp;+
              <input name="Gwjt" type="text" class="inR0000" id="Gwjt" value="<?php  echo $Gwjt?>" size="8" readonly></td>
               <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">绩效奖金</td>
            <td> &nbsp;+
            <input name="Jj" type="text" class="inR0100" id="Jj" value="<?php  echo $Jj?>" size="8" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
         
        <tr style='display:none;'>
            <td align="right" valign="top">超出加班费</td>
            <td> &nbsp;+
            <input name="Jbjj" type="text" class="inR0100" id="Jbjj" value="<?php  echo $Jbjj?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>   
          <tr>
            <td align="right" valign="top">其他奖金</td>
            <td> &nbsp;+
            <input name="Ywjj" type="text" class="inR0100" id="Ywjj" value="<?php  echo $Ywjj?>" size="8" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)">
            </td>
             <td width="320" scope="col"><input name='YwjjPicture' type='file' id='YwjjPicture' size='30' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='7' Cel="2"></td>
          </tr>   
                 <tr>
            <td align="right" valign="top">生活补助</td>
            <td> &nbsp;+
            <input name="Shbz" type="text" class="inR0000" id="Shbz" value="<?php  echo $Shbz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">住宿补助</td>
            <td> &nbsp;+
            <input name="Zsbz" type="text" class="inR0000" id="Zsbz" value="<?php  echo $Zsbz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">交通补助</td>
            <td> &nbsp;+
            <input name="Jtbz" type="text" class="inR0000" id="Jtbz" value="<?php  echo $Jtbz?>" size="8" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr> 
          <tr>
            <td align="right" valign="top">就学补助</td>
            <td> &nbsp;+
            <input name="Studybz" type="text" class="inR0000" id="Studybz" value="<?php  echo $Studybz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr> 
          
             <tr>
            <td align="right" valign="top">购房补助</td>
            <td> &nbsp;+
            <input name="Housebz" type="text" class="inR0000" id="Housebz" value="<?php  echo $Housebz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr> 
          
          <tr style='display:none;'>
            <td align="right" valign="top">夜宵补助</td>
            <td> &nbsp;+
            <input name="Yxbz" type="text" class="inR0000" id="Yxbz" value="<?php  echo $Yxbz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
           <tr style='display:none;'>
            <td align="right" valign="top">个税补助</td>
            <td> &nbsp;+
            <input name="taxbz" type="text" class="inR0000" id="taxbz" value="<?php  echo $taxbz?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>  
               
          <tr>
            <td align="right" valign="top">考勤扣款</td>
            <td> &nbsp;-
            <input name="Kqkk" type="text" class="inR0000" id="Kqkk" value="<?php  echo $Kqkk?>" size="8" onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
           <tr>
            <td align="right" valign="top">取消津贴</td>
            <td> &nbsp;-
            <input name="dkfl" type="text" class="inR0000" id="dkfl" value="<?php  echo $dkfl?>" size="8"  onchange="javascript:CheckNum(this,1)" onfocus="toTempValue(this.value)"></td>
              <td width="320" scope="col"><input name='dkflPicture' type='file' id='dkflPicture' size='30' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='15' Cel="2"></td>
          </tr>         
          <tr>
            <td align="right" valign="top">借&nbsp;&nbsp;&nbsp;&nbsp;支</td>
            <td> &nbsp;-
              <input name="Jz" type="text" class="inR0000" id="Jz" value="<?php  echo $Jz?>" size="8" onchange="javascript:CheckNum(this,1)"></td>
               <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">社&nbsp;&nbsp;&nbsp;&nbsp;保</td>
            <td> &nbsp;-
            <input name="Sb" type="text" class="inR0000" id="Sb" value="<?php  echo $Sb?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">公积金</td>
            <td> &nbsp;-
            <input name="Gjj" type="text" class="inR0000" id="Gjj" value="<?php  echo $Gjj?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">个&nbsp;&nbsp;&nbsp;&nbsp;税</td>
            <td> &nbsp;-
            <input name="RandP" type="text" class="inR0000" id="RandP" value="<?php  echo $RandP?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">其&nbsp;&nbsp;&nbsp;&nbsp;它</td>
            <td> &nbsp;-
            <input name="Otherkk" type="text" class="inR0100" id="Otherkk" value="<?php  echo $Otherkk?>" size="8" onchange="javascript:CheckNum(this,0)" onfocus="toTempValue(this.value)"></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr style='display:none;'>
            <td align="right" valign="top">餐&nbsp;&nbsp;&nbsp;&nbsp;费</td>
            <td> &nbsp;-
            <input name="Ct" type="text" class="inR0100" id="Ct" value="<?php  echo $Ct?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr> 
           <tr>
            <td align="right" valign="top">合&nbsp;&nbsp;&nbsp;&nbsp;计</td>
            <td>=
            <input name="Amount" type="text" class="totalINPUT" id="Amount" value="<?php  echo $Amount?>" size="8" readonly></td>
             <td width="320" scope="col">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan='2'><textarea name="Remark" cols="60" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
   </table>
</td></tr></table>
<input name="TempValue" type="hidden" id="TempValue">
<input name="Number" type="hidden" id="Number" value="<?php  echo $Number?>">
<input name="DataIn" type="hidden" id="DataIn" value="<?php  echo $DataIn?>">
<input name="fromWebPage" type="hidden" id="fromWebPage" value="<?php  echo $fromWebPage?>">

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
	var checkNum=fucCheckNUM(theNumber,"Price");
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
	var KqSign=document.getElementById("KqSign").value*1;
	var Month=document.getElementById("Month").value;
	var Hdjbf=document.getElementById("Hdjbf").value;
	
	var Dx=document.getElementById("Dx").value*1; 
	
	var Gljt=document.getElementById("Gljt").value*1; 
	var Gwjt=document.getElementById("Gwjt").value*1; 
	var Jj=document.getElementById("Jj").value*1; 
	var Jbjj=document.getElementById("Jbjj").value*1; 
	var Ywjj=document.getElementById("Ywjj").value*1; 
	var Shbz=document.getElementById("Shbz").value*1; 
	var Zsbz=document.getElementById("Zsbz").value*1; 
	var Jbf=document.getElementById("Jbf").value*1; 
	var Yxbz=document.getElementById("Yxbz").value*1; 
	var Jtbz=document.getElementById("Jtbz").value*1; 
	var Studybz=document.getElementById("Studybz").value*1;
	var Housebz = document.getElementById("Housebz").value*1;
	var Kqkk=document.getElementById("Kqkk").value*1;
	
	var dkfl=document.getElementById("dkfl").value*1;
	var Jz=document.getElementById("Jz").value*1;
	var Sb=document.getElementById("Sb").value*1; 
	var Gjj=document.getElementById("Gjj").value*1; 
	var Ct=document.getElementById("Ct").value*1; 
	var Otherkk=document.getElementById("Otherkk").value*1; 
	var SNumber=document.getElementById("Number").value; 
	var DataIn=document.getElementById("DataIn").value;
	var TaxAmount=(Dx+Gljt+Gwjt+Jj+Ywjj+Shbz+Zsbz+Jbf+Jbjj+Yxbz+Jtbz+Studybz+Housebz-Kqkk-dkfl-Sb-Gjj-Ct-Otherkk)*1;
	if (KqSign==1 || Month>=201511){
		TaxAmount=TaxAmount-Hdjbf;
	}
	
	var taxbz=0;
	var Currency=document.getElementById("Currency").value*1;
	if (Currency==1){
	RandP=0;
	
	if (SNumber==10001 || SNumber==11880){
		var baseTax = 4800;
		
		TaxAmount-=baseTax;
		//暂时更改为工资超过10000按10000元计算个人所得税
       // TaxAmount=TaxAmount>5200?5200:TaxAmount;

		if(TaxAmount >1500 && TaxAmount<= 4500){
		      RandP =  TaxAmount*0.1-105;
		}else if(TaxAmount > 4500 && TaxAmount <= 9000){
		      RandP =  TaxAmount*0.2-555;
		}else if(TaxAmount > 8000 && TaxAmount <= 12500){
		      RandP =  TaxAmount*0.2-555;
		}else if(TaxAmount > 9000 && TaxAmount <= 35000){
		      RandP =  TaxAmount*0.3-2755;
		}else if(TaxAmount >55500 && TaxAmount <= 80000){
		      RandP =  TaxAmount*0.35-5505;
		}else if(TaxAmount >80000 ){
		      RandP =  TaxAmount*0.45-13505;
		}
		//alert(RandP);
	}
	else{
		var baseTax = 3500;
		
		
	    if(TaxAmount >3500 && TaxAmount<= 5000){
	          RandP =  (TaxAmount - baseTax )*0.03;
		}else if(TaxAmount > 5000 && TaxAmount <= 8000){
		      RandP =  (TaxAmount - baseTax )*0.1-105;
		}else if(TaxAmount > 8000 && TaxAmount <= 12500){
		      RandP =  (TaxAmount-baseTax )*0.2-555;
		}else if(TaxAmount > 12500 && TaxAmount <= 38500){
		      RandP =  (TaxAmount - baseTax )*0.25-1005;
		}else if(TaxAmount >38500 && TaxAmount <= 58500){
		      RandP =  (TaxAmount - baseTax )*0.3-2755;
		}else if(TaxAmount >58500 && TaxAmount <= 83500){
		      RandP =  (TaxAmount - baseTax )*0.35-5505;
		}else if(TaxAmount >83500 ){
		      RandP =  (TaxAmount - baseTax )*0.45-13505;
		}
		/*
		//暂时更改为工资超过10000按10000元计算个人所得税
		if(TaxAmount >3500 && TaxAmount<= 5000){
	          RandP =  (TaxAmount - baseTax )*0.03;
		}else if(TaxAmount > 5000 && TaxAmount <= 8000){
		      RandP =  (TaxAmount - baseTax )*0.1-105;
		}else if(TaxAmount > 8000){
		      TaxAmount=TaxAmount>12000?12000:TaxAmount;
		      RandP =  (TaxAmount-baseTax )*0.2-555;
		}
		*/
	}
    RandP=Math.round(RandP);
    
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
	
	//if((SNumber==10001) || (SNumber==10822)){RandP=0;} //老板不扣,大卫不扣,//陈信荣
	
	if((SNumber==10001) || (SNumber==10822) || (SNumber==10943) || (SNumber==10855) || (SNumber==11136)  || (SNumber==11880)){RandP=0;} 
	RandP=Math.round(RandP);
	
	if(RandP>=175){
		taxbz=100;  //个税补
	}
	*/
	//alert("123");
	}
	else{
		RandP=document.getElementById("RandP").value;
	}
	
	taxbz=0;
	var Amount=Dx+Gljt+Gwjt+Jj+Ywjj+Shbz+Zsbz+Jbf+Yxbz+Jtbz+taxbz+Studybz+Housebz-Jz-Sb-Gjj-Ct-Kqkk-dkfl-Otherkk-RandP;//+Jbjj
	document.getElementById("taxbz").value=taxbz;
	document.getElementById("RandP").value=RandP;
	document.getElementById("Amount").value=Amount;
	
	


}
	
</script>