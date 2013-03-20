function number_format(n,d,b,t){var n=!isFinite(+n)?0:+n,prec=!isFinite(+d)?0:Math.abs(d),sep=(typeof t==='undefined')?',':t,dec=(typeof b==='undefined')?'.':b,s='',toFixedFix=function(n,prec){var k=Math.pow(10,prec);return ''+Math.round(n*k)/k;};
s=(prec?toFixedFix(n,prec):''+Math.round(n)).split('.');if(s[0].length>3){s[0]=s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,sep);}
if((s[1]||'').length<prec){s[1]=s[1]||'';s[1]+=new Array(prec-s[1].length+1).join('0');}
return s.join(dec);}
function empty(a){if(a==""||a==0){return true;}else{return false;}};