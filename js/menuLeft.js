// Variable que guarda la ultima opcion de la barra de herramientas de funcionalidades seleccionada
function MM_swapImgRestore() { //v3.0
    var i,x,a=document.MM_sr;
    for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments;
    for(i=0; i<a.length; i++) {
        if (a[i].indexOf("#")!=0){
            d.MM_p[j]=new Image;
            d.MM_p[j++].src=a[i];
        }   
    }
}

function MM_findObj(n, d) { //v4.01
    var p,i,x;
    if(!d) d=document;
    
    if((p=n.indexOf("?"))>0&&parent.frames.length) {
        d=parent.frames[n.substring(p+1)].document;
        n=n.substring(0,p);
    }

    if(!(x=d[n])&&d.all) x=d.all[n];
    for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && d.getElementById) 
        x=d.getElementById(n);
    return x;
}

function MM_swapImage() { //v3.0
    var i,j=0,x,a=MM_swapImage.arguments;
    document.MM_sr=new Array;
    for(i=0;i<(a.length-2);i+=3) {
        if ((x=MM_findObj(a[i]))!=null){
            document.MM_sr[j++]=x; 
            if(!x.oSrc) x.oSrc=x.src; 
                x.src=a[i+2];
        }
    }
}

function reload_window($carpetano,$carp_nomb,$tipo_carp) {
    document.write("<form action=cuerpo.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>&ascdesc=desFc method=post name=form4 target=mainFrame>");
    document.write("<input type=hidden name=carpetano value=" + $carpetano + ">");
    document.write("<input type=hidden name=carp_nomb value=" + $carp_nomb + ">");
    document.write("<input type=hidden name=tipo_carpp value=" + $tipo_carp + ">");
    document.write("<input type=hidden name=tipo_carpt value=" + $tipo_carpt + ">");
    document.write("</form>");
    document.form4.submit();
}

selecMenuAnt=-1;
swVePerso = 0;
numPerso = 0;
function cambioMenu(img){
    MM_swapImage('plus' + img,'','imagenes/menuraya.gif',1);

	if (selecMenuAnt!=-1 && img!=selecMenuAnt)
		MM_swapImage('plus' + selecMenuAnt,'','imagenes/menu.gif',1);
	
    selecMenuAnt = img;

	if (swVePerso==1 && numPerso!=img){
		document.getElementById('carpersolanes').style.display="none";
		MM_swapImage('plus' + numPerso,'','imagenes/menu.gif',1);
		swVePerso=0;
	}
}

function verPersonales(img){
    if (swVePerso!=1){
		document.getElementById('carpersolanes').style.display="";
		swVePerso=1;
	}else{
		document.getElementById('carpersolanes').style.display="none";
		MM_swapImage('plus' + selecMenuAnt,'','imagenes/menu.gif',1);
		selecMenuAnt = img;
		swVePerso=0;
	}
	numPerso = img;
}
