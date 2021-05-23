//PConline IP地址查询接口
var whois = {
	root: 'http://whois.pconline.com.cn',
    version:2.0,
    alertIp:function(ip){
        var s=document.getElementsByTagName('head')[0].appendChild(document.createElement("script"));
        s.src=this.root+"/jsAlert.jsp?ip="+ip;
    },
    lableIp:function(id,ip){
        var s=document.getElementsByTagName('head')[0].appendChild(document.createElement("script"));
        s.src=this.root+"/jsLabel.jsp?ip="+ip+"&id="+id;
    }
}
function alertIp(ip){
    whois.alertIp(ip);
  
}
function labelIp(id,ip){
    whois.lableIp(id,ip);
}