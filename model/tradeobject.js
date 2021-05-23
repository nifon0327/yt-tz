function ChangeInfo(){
    var ObjectSignValue=document.getElementById("ObjectSign").value*1;
    switch(ObjectSignValue){
        case 2://客户
            setElementHidden("ClientTable",false);
            setClientTableElement(false);
            setElementHidden("SupplierTable",true);
            setSupplierTableElement(true);
            setElementHidden("TradeTable",false);
            setTradeTableElement(false);
            break;
        case 3://供应商
            setElementHidden("ClientTable",true);
            setClientTableElement(true);
            setElementHidden("SupplierTable",false);
            setSupplierTableElement(false);
            setElementHidden("TradeTable",true);
            setTradeTableElement(true);
            break;
        case 1://客户/供应商
            setElementHidden("ClientTable",false);
            setElementHidden("SupplierTable",false);
            setClientTableElement(false);
            setSupplierTableElement(false);
            setElementHidden("TradeTable",false);
            setTradeTableElement(false);
            break;
        default:
            setElementHidden("ClientTable",true);
            setElementHidden("SupplierTable",true);
            setElementHidden("TradeTable",true);
            setTradeTableElement(true);
            setClientTableElement(true);
            setSupplierTableElement(true);
            break;
    }

    ChangeStatus();

}

function setClientTableElement(hidden){
    setElementHidden("PayType",hidden);
    setElementHidden("PayMode",hidden);
    setElementHidden("SaleMode",hidden);
    setElementHidden("BankId",hidden);
    setElementHidden("ChinaSafeSign",hidden);
    setElementHidden("PriceTerm",hidden);
    if (hidden){
        document.getElementById("PayType").value='';
        document.getElementById("PayMode").value='';
        document.getElementById("SaleMode").value='';
        document.getElementById("BankId").value='';
        document.getElementById("ChinaSafeSign").value='';
        document.getElementById("PriceTerm").value="";
    }
}
//add by ckt 2017-12-25
function setTradeTableElement(hidden){
    setElementHidden("TradeNo",hidden);
    setElementHidden("ProofreaderName",hidden);
    setElementHidden("Proofreader1Name",hidden);
    setElementHidden("CheckerName",hidden);
    setElementHidden("Members",hidden);
    setElementHidden("ProducerName",hidden);
    setElementHidden("CmptTotal",hidden);
    if (hidden){
        document.getElementById("TradeNo").value='';
        document.getElementById("ProofreaderName").value='';
        document.getElementById("Proofreader1Name").value='';
        document.getElementById("CheckerName").value='';
        document.getElementById("Members").value='';
        document.getElementById("ProducerName").value="";
        document.getElementById("CmptTotal").value="";
    }
}

function setSupplierTableElement(hidden){
    setElementHidden("ProviderType",hidden);
    setElementHidden("GysPayMode",hidden);
    setElementHidden("Bank",hidden);
    setElementHidden("BankUID",hidden);
    setElementHidden("BankAccounts",hidden);
    setElementHidden("InvoiceTax",hidden);
    setElementHidden("AddValueTax",hidden);
    setElementHidden("LegalPerson",hidden);
    setElementHidden("BulidTime",hidden);
    setElementHidden("ValidTime",hidden);
    setElementHidden("Capital",hidden);
    setElementHidden("CompanySize",hidden);
    setElementHidden("StaffNum",hidden);
    setElementHidden("CompanyNature",hidden);
    setElementHidden("CompanyCategory",hidden);
    setElementHidden("MainBusiness",hidden);
    setElementHidden("CompanyPicture",hidden);
    setElementHidden("DealRange",hidden);
    setElementHidden("BusinessLicence",hidden);
    setElementHidden("TaxCertificate",hidden);
    setElementHidden("BankPermit",hidden);
    setElementHidden("TaxpayerIdentifi",hidden);
    setElementHidden("SalesAgreement",hidden);
    setElementHidden("PaymentOrder",hidden);
    setElementHidden("ProductionCertificate",hidden);
    if (hidden){
        document.getElementById("ProviderType").value='';
        document.getElementById("GysPayMode").value=0;
        document.getElementById("Bank").value='';
        document.getElementById("BankUID").value='';
        document.getElementById("BankAccounts").value='';
        document.getElementById("InvoiceTax").value=0;
        document.getElementById("AddValueTax").value='';
        document.getElementById("LegalPerson").value="";
        document.getElementById("BulidTime").value="";
        document.getElementById("ValidTime").value="";
        document.getElementById("Capital").value="";
        document.getElementById("CompanySize").value="";
        document.getElementById("StaffNum").value="";
        document.getElementById("CompanyNature").value="";
        document.getElementById("CompanyCategory").value="";
        document.getElementById("CompanyPicture").value="";
        document.getElementById("MainBusiness").value="";
        document.getElementById("DealRange").value="";
        document.getElementById("BusinessLicence").value="";
        document.getElementById("TaxCertificate").value="";
        document.getElementById("BankPermit").value="";
        document.getElementById("TaxpayerIdentifi").value="";
        document.getElementById("SalesAgreement").value="";
        document.getElementById("PaymentOrder").value="";
        document.getElementById("ProductionCertificate").value="";
    }
}


function ChangeStatus(){
    var ObjectSignValue=document.getElementById("ObjectSign").value;
    var CurrencyValue=  document.getElementById("Currency").value;

    if(CurrencyValue==5 && (ObjectSignValue==1 ||ObjectSignValue==3)){
        setElementHidden("Tr_IBAN",false);
    }
    else{
        setElementHidden("Tr_IBAN",true);
    }
}

function setElementHidden(elementName,hidden){
    if (hidden){
        document.getElementById(elementName).disabled="disabled";
        document.getElementById(elementName).hidden="hidden";
    }
    else{
        document.getElementById(elementName).disabled="";
        document.getElementById(elementName).hidden="";
    }
}

function ChinaSafeSignChange(){
    var ChinaSafeSign=document.getElementById("ChinaSafeSign");
    var ChinaSafe=document.getElementById("ChinaSafe");
    if (ChinaSafeSign.value==1){
        ChinaSafe.disabled="";
    }
    else{
        ChinaSafe.disabled="disabled";
    }
}

function ProviderTypeChange(){
    var ProviderType=document.getElementById("ProviderType").value;
    var hidden=false;

    if (ProviderType==2){
        hidden=true;
    }
    setElementHidden("GysPayMode",hidden);
    setElementHidden("Bank",hidden);
    setElementHidden("BankUID",hidden);
    setElementHidden("BankAccounts",hidden);
    setElementHidden("InvoiceTax",hidden);
    setElementHidden("AddValueTax",hidden);
    setElementHidden("FscNo",hidden);
    setElementHidden("LegalPerson",hidden);
    setElementHidden("BulidTime",hidden);
    setElementHidden("ValidTime",hidden);
    setElementHidden("Capital",hidden);
    setElementHidden("CompanySize",hidden);
    setElementHidden("StaffNum",hidden);
    setElementHidden("CompanyNature",hidden);
    setElementHidden("CompanyCategory",hidden);
    setElementHidden("MainBusiness",hidden);
    setElementHidden("CompanyPicture",hidden);
    setElementHidden("DealRange",hidden);
    setElementHidden("BusinessLicence",hidden);
    setElementHidden("TaxCertificate",hidden);
    setElementHidden("BankPermit",hidden);
    setElementHidden("TaxpayerIdentifi",hidden);
    setElementHidden("SalesAgreement",hidden);
    setElementHidden("PaymentOrder",hidden);
    setElementHidden("ProductionCertificate",hidden);
}
//内部员工选择函数 by ckt 2017-12-26
function searchStaffId(Action, SearchNum,BackName,BackId) {
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[4]) {
        var num = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'searchStaffId("","'+SearchNum+'","'+BackName+'","'+BackId+'",true)';
        var url = "/public/staff_s1.php?r=" + num +"&uType=1&tSearchPage=staff&fSearchPage=tradeobject&Action=" + Action+"&SearchNum="+SearchNum;
        openFrame(url, 696, 650);//url需为绝对路径afagagadgasdgasdfasdfasdfsd
        return false;
    }
    if (SafariReturnValue.value) {
        if(SearchNum==1){//单选
            var FieldArray = SafariReturnValue.value.split("^^");
            document.getElementById(BackId).value = FieldArray[0];
            document.getElementById(BackName).value = FieldArray[1];
        }else{//多选
            var FieldArray = SafariReturnValue.value.split("``");
            var ReturnValue = '';
            var iVal;
            FieldArray.forEach(function(Val){
                iVal = Val.split("^^");
                ReturnValue += iVal[1]+',';
            });
            document.getElementById(BackName).value = ReturnValue.substr(0, ReturnValue.length-1);
        }
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
    }
}