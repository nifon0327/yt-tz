//************************************************
//规则检查排序
function RegCheck(objs)
 {
  var str = objs.checktype;
  switch (str)
   {
       case "cn" :    //要检查的表单控件的输入类型必须为中文
          return CnWordRegCheck(objs);
          break;
       case "idnum" : 
          return IdCardRegCheck(objs); //要检查的表单控件的输入类型必须为身份证号
          break;
    case "num" :    //要检查的表单控件的输入类型必须为数字
           return NumRegCheck(objs);
     break;
    case "mail" :    //要检查的表单控件的输入类型必须为EMAIL
          return EmailRegCheck(objs);
          break;
    case "txt" :   //要检查的表单控件的输入类型必须为字符串
     return SpecialWordRegCheck(objs);
     break;
    case "notes" : 
     return true; //要检查的表单控件的输入类型必须为什么都可以
     break;
   }
 }
//************************************************
//检查电话号码
function NumRegCheck(obj)
 {
  var uplimit = obj.checkrule.split(",")[0];
  var downlimit = obj.checkrule.split(",")[1];
  var reg = ""; 
  if (downlimit == null) 
   { 
     reg = eval("/^[0-9]{"+uplimit+"}$/");
   }
  else
   {
     reg = eval("/^[0-9]{"+uplimit+","+downlimit+"}$/");
   }
  var str = obj.value;
  var flag = reg.test(str);
  return flag;
 }
//************************************************
//检查身份证号 
function IdCardRegCheck(obj)
 {
  var str = obj.value;
  var reg = /^([0-9]{15}|[0-9]{18})$/;
  var flag = reg.test(str);
  return flag; 
 }
//************************************************
//检查EMAIL
function EmailRegCheck(obj)
 {
  var str = obj.value;
  var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
  var flag = reg.test(str);
  return flag;
 }
//***************************************
//检查中文输入
function CnWordRegCheck(obj)
 {
  var str = obj.value;
  var reg=/^[\u4e00-\u9fa5](\s*[\u4e00-\u9fa5])*$/;
  var flag = reg.test(str);
  //alert(flag);
  return flag;
 }
//***************************************
//检查特殊字符
function SpecialWordRegCheck(obj)
 {
  var reg= /[(\/)(\\)(')(")(<)(>)]/g;
  var str = obj.value;
  var flag= reg.test(str);
  flag = !flag;
  return flag;
 }
//************************************************
//检查主引导函数
function CheckForm(obj)
 {
  var myform = eval("document."+obj.name);
 
  for (i=0;i<myform.elements.length;i++)
   {
    var formvalue = myform.elements[i].value;
    //内容非空检查,长度检查
    if ((myform.elements[i].value == "")||(myform.elements[i].value.length>myform.elements[i].maxlength))
     {
      alert("您忘了填写"+myform.elements[i].cnname+"!"+"或者您填写的信息不符合规范!"); 
      myform.elements[i].focus();
      return false;
      break;
     }
  
     if (myform.elements[i].value == 0)
     {
      alert("您忘了选择"+myform.elements[i].cnname+"!");
      myform.elements[i].focus();
      return false;
      break;
     }
    //数据规范化检查
    var myobj = myform.elements[i];
    //alert(myobj.checktype);
    //break;
    if (!RegCheck(myobj))
     {
      alert(myobj.cnname+"输入有误,请按填写要求填写!");
        myobj.focus();
      return false;
      break;
     }
   }
 }
