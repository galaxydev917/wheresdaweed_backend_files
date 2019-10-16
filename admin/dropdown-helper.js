function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function addItemToDropDown(dropdown,text,value){
    var option = document.createElement("option");
    option.text = text;
    option.value=value;
    dropdown.add(option);
}

function clearDropDown(dropdown){
    var length = dropdown.options.length;
    while (dropdown.options.length > 0) {
        dropdown.options[0] = null;
    }
}

function selectDropDownText(dropdown,txt){
	var length = dropdown.options.length;
	for (var a=0;a<length;a++){
		//console.log("Option: "+dropdown.options[a].text+","+txt);
		if (dropdown.options[a].text.toLowerCase()==txt.toLowerCase()){
			dropdown.selectedIndex=a;
			break;
		}
	}
}

function isValidIdNumber(number){
	if(number.length!=13){
		return false;
	}
	for (var a=0;a<number.length;a++){
		if (number[a]<'0' || number[a]>'9'){
			return false;
		}
	}
	year=parseInt(number.substring(0,2));
	if (year<10){
		return false;
	}
	month=parseInt(number.substring(2,4));
	if (month<1 || month > 12){
		return false;
	}
	day=parseInt(number.substring(4,6));
	if (day<1 || day > 31){
		return false;
	}
	ctz=parseInt(number.substring(10,11));
	if (ctz!=0 && ctz!=1){
		return false;
	}
	ctz=parseInt(number.substring(11,12));
	if (ctz!=8 && ctz!=9){
		return false;
	}
	return true;
}

function isValidContactNumber(number){
	if (number.length!=10){
		return false;
	}
	for (var a=0;a<number.length;a++){
		if (number[a]<'0' || number[a]>'9'){
			return false;
		}
	}
	cellPrefixes=["071","060","074","081","084","061","072", "076", "073", "079", "078", "082", "083", "089","0835", "051", "021", "031", "0431", "0712", "011", "041", "012", "057"];
	//landPrefixes=["0835", "051", "021", "031", "0431", "0712", "011", "041", "012", "057"];
	prefixFound=false;
	for (var a=0;a<cellPrefixes.length;a++){
		pLen=cellPrefixes[a].length;
		prf=number.substring(0,pLen);
		if (prf==cellPrefixes[a]){
			prefixFound=true;
			break;
		}
	}
	if (!prefixFound){
		return false;
	}
	return true;
}

function isValidEmail(email){
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function isValidCostPrice(cp){
	var dotCount=0;
	for(var a=0;a<cp.length;a++){
		if ((cp[a]<'0' || cp[a]>'9') && cp[a]!=','){
			if (cp[a]=='.' && dotCount==0){
				dotCount++;
				continue;
			}else{
				return false;
			}
		}
	}
	return true;
}

function hasMaxTwoDecimals(cp){
	var cl=cp.split(".");
	if (cl.length<=1){
		return true;
	}
	if (cl[1].length>2){
		return false;
	}
	return true;
}

function isInt(n){
    return Number(n) === n && n % 1 === 0;
}

function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}