//日历和日记 二合一已更新
var days=new Array("日","一","二","三","四","五","六");
var mtend=new Array("",31,28,31,30,31,30,31,31,30,31,30,31);
var Tday=new Date();
var y=Tday.getYear();
var m=Tday.getMonth()+1;
var Tday=Tday.getDate();
var allday=mtend[m];
var nStr1 = new Array('日','一','二','三','四','五','六','七','八','九','十');
var nStr2 = new Array('初','十','廿','卅','□');
var nStr3 = new Array('','十','廿','卅','□');

if(m==2 & y%4==0) allday=29;
var link=new Array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,'');

function show(d){
    var mc=m;
    var canjump=0;
    for(no=0;no<link.length;no++){
        if(d==link[no]){
            canjump=1;
            continue;
        }
    }
    if(canjump==1){
        if(m<10) mc="0"+m;
		if (d<10) {
			dc="0"+d;}
		else
			{dc=d;}
	   document.write(d);       //whatday='?'+'S_choose=Date&S_date='+y+'-'+mc+'-'+dc; document.write('<a href="product_search.php'+whatday+'"  class="ca">'+d+"</a>");
    	}
	else{
        document.write(d);}
	}

function list(){	
	//求当月第一天是星期几
   	var firstday=new Date(y,(m-1),1);
	firstday=firstday.getDay();
    var theday=0;
    //表头：日 一 二 三 四 五 六
	for(i=0;i<days.length;i++){
        document.write(days[i]+"&nbsp;");}
	
	r=0;
    for(i=1;i<=(allday+firstday);i++){
	//换行 移进位
        if((i-1)%7==0) {document.write('<br>');}
        if(i<=firstday){
           //空白星期以三个空格代替，星期占两个空格，一个空格用来分隔
            document.write("&nbsp;&nbsp;&nbsp;");
            continue;
        	}
		else{
		//输出日期
            theday++;
			if (theday<10){
			document.write("&nbsp;");}
			
       		if (theday==Tday){
				document.write("<span style='color:#CC0000;'>");
				show(theday);
				document.write("</snap>");
				document.write("&nbsp;");
				}
			else{
				document.write("<span style='color:979596'>");
				show(theday);
				document.write("</snap>");
				document.write("&nbsp;");
				}
        	}
    	}
 	}
var lunarInfo=new Array(
0x4bd8,0x4ae0,0xa570,0x54d5,0xd260,0xd950,0x5554,0x56af,0x9ad0,0x55d2,
0x4ae0,0xa5b6,0xa4d0,0xd250,0xd295,0xb54f,0xd6a0,0xada2,0x95b0,0x4977,
0x497f,0xa4b0,0xb4b5,0x6a50,0x6d40,0xab54,0x2b6f,0x9570,0x52f2,0x4970,
0x6566,0xd4a0,0xea50,0x6a95,0x5adf,0x2b60,0x86e3,0x92ef,0xc8d7,0xc95f,
0xd4a0,0xd8a6,0xb55f,0x56a0,0xa5b4,0x25df,0x92d0,0xd2b2,0xa950,0xb557,
0x6ca0,0xb550,0x5355,0x4daf,0xa5b0,0x4573,0x52bf,0xa9a8,0xe950,0x6aa0,
0xaea6,0xab50,0x4b60,0xaae4,0xa570,0x5260,0xf263,0xd950,0x5b57,0x56a0,
0x96d0,0x4dd5,0x4ad0,0xa4d0,0xd4d4,0xd250,0xd558,0xb540,0xb6a0,0x95a6,
0x95bf,0x49b0,0xa974,0xa4b0,0xb27a,0x6a50,0x6d40,0xaf46,0xab60,0x9570,
0x4af5,0x4970,0x64b0,0x74a3,0xea50,0x6b58,0x5ac0,0xab60,0x96d5,0x92e0,
0xc960,0xd954,0xd4a0,0xda50,0x7552,0x56a0,0xabb7,0x25d0,0x92d0,0xcab5,
0xa950,0xb4a0,0xbaa4,0xad50,0x55d9,0x4ba0,0xa5b0,0x5176,0x52bf,0xa930,
0x7954,0x6aa0,0xad50,0x5b52,0x4b60,0xa6e6,0xa4e0,0xd260,0xea65,0xd530,
0x5aa0,0x76a3,0x96d0,0x4afb,0x4ad0,0xa4d0,0xd0b6,0xd25f,0xd520,0xdd45,
0xb5a0,0x56d0,0x55b2,0x49b0,0xa577,0xa4b0,0xaa50,0xb255,0x6d2f,0xada0,
0x4b63,0x937f,0x49f8,0x4970,0x64b0,0x68a6,0xea5f,0x6b20,0xa6c4,0xaaef,
0x92e0,0xd2e3,0xc960,0xd557,0xd4a0,0xda50,0x5d55,0x56a0,0xa6d0,0x55d4,
0x52d0,0xa9b8,0xa950,0xb4a0,0xb6a6,0xad50,0x55a0,0xaba4,0xa5b0,0x52b0,
0xb273,0x6930,0x7337,0x6aa0,0xad50,0x4b55,0x4b6f,0xa570,0x54e4,0xd260,
0xe968,0xd520,0xdaa0,0x6aa6,0x56df,0x4ae0,0xa9d4,0xa4d0,0xd150,0xf252,
0xd520);

function lYearDays(y) {
 var i, sum = 348;
 for(i=0x8000; i>0x8; i>>=1) sum += (lunarInfo[y-1900] & i)? 1: 0;
 return(sum+leapDays(y));
}

//====================================== 返回农历 y年闰月的天数
function leapDays(y) {
 if(leapMonth(y)) return( (lunarInfo[y-1899]&0xf)==0xf? 30: 29);
 else return(0);
}

//====================================== 返回农历 y年闰哪个月 1-12 , 没闰返回 0
function leapMonth(y) {
 var lm = lunarInfo[y-1900] & 0xf;
 return(lm==0xf?0:lm);
}

//====================================== 返回农历 y年m月的总天数
function monthDays(y,m) {
 return( (lunarInfo[y-1900] & (0x10000>>m))? 30: 29 );
}
function Lunar(objDate) {

   var i, leap=0, temp=0;
   var offset   = (Date.UTC(objDate.getFullYear(),objDate.getMonth(),objDate.getDate()) - Date.UTC(1900,0,31))/86400000;

   for(i=1900; i<2100 && offset>0; i++) { temp=lYearDays(i); offset-=temp; }

   if(offset<0) { offset+=temp; i--; }

   this.year = i;

   leap = leapMonth(i); //闰哪个月
   this.isLeap = false;

   for(i=1; i<13 && offset>0; i++) {
      //闰月
      if(leap>0 && i==(leap+1) && this.isLeap==false)
         { --i; this.isLeap = true; temp = leapDays(this.year); }
      else
         { temp = monthDays(this.year, i); }

      //解除闰月
      if(this.isLeap==true && i==(leap+1)) this.isLeap = false;

      offset -= temp;
   }

   if(offset==0 && leap>0 && i==leap+1)
      if(this.isLeap)
         { this.isLeap = false; }
      else
         { this.isLeap = true; --i; }

   if(offset<0){ offset += temp; --i; }

   this.month = i;
   this.day = offset + 1;
}
//====================== 输出中文日期
function cDay(d,b){
   var s;

   switch (d) {
      case 10:
         s = '初十'; break;
      case 20:
         s = '二十'; break;
         break;
      case 30:
         s = '三十'; break;
         break;
      default :
	  {if (b=='m')
         s =nStr3[Math.floor(d/10)];
		else
		 s =nStr2[Math.floor(d/10)];
	  
         s += nStr1[d%10];}
   }
   return(s);
}

function ok(){
	var Today = new Date();
	var tY = Today.getFullYear();
	var tM = Today.getMonth();
	var tD = Today.getDate();
    sDObj = new Date(tY,tM,tD);    //当月一日日期
    lDObj = new Lunar(sDObj);     //农历
    lY    = lDObj.year;           //农历年
    lM    = lDObj.month;          //农历月
    lD    = lDObj.day;            //农历日
    lL    = lDObj.isLeap;         //农历是否闰月
	lL=cDay(lM,'m')+"月"+cDay(lD,'d');
	document.write('农历：'+lL+'<br>');
}