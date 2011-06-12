
function getURLParam(url,parametro){
	url=new String(url)
	var inicio=url.indexOf(parametro+"=")
	if(inicio==-1) return null;
	else{
		fin=url.indexOf("&",inicio+parametro.length+1)
		if(fin==-1) fin=url.length;
		inicio=inicio+parametro.length+1
		return url.substring(inicio,fin)
	}
}

//establece el texto de un elemento
function setText(element,text){
	var name=document.getElementById(element);
	if(name){
		while ( name.firstChild) name.removeChild( name.firstChild );
		name.appendChild(document.createTextNode(text));
	}
}

//parsea las respuestas para compararlas
function splitAndTrim(cadena){
	var data=cadena;
	
	/*if(getOpcionInterfaz('ignorar_acentos')){
		data=data.replace(/[á|à|â|ä]/g, "a");
		data=data.replace(/[é|è|ê|ë]/g, "e");
		data=data.replace(/[í|ì|î|ï]/g, "i");
		data=data.replace(/[ó|ò|ô|ö]/g, "o");
		data=data.replace(/[ú|ù|û|ü]/g, "u");
		data=data.replace(/[Á|À|Â|Ä]/g, "A");
		data=data.replace(/[É|È|Ê|Ë]/g, "E");
		data=data.replace(/[Í|Ì|Î|Ï]/g, "I");
		data=data.replace(/[Ó|Ò|Ô|Ö]/g, "O");
		data=data.replace(/[Ú|Ù|Û|Ü]/g, "U");
	}*/
	
	data=data.toUpperCase().split(",")
	for(var i=0;i<data.length;i++){
		data[i]=data[i].replace(/^\s+|\s+$/g,"")
	}
	return data.sort()
}

//elimina todos los hijos de un elemento
function vaciar(element){
	while(element.firstChild){
		element.removeChild(element.firstChild);
	}
}

// Desordena un array
function randOrder(ar){
	var result=new Array();
	for(var i=0;i<ar.length;i++)
		result.splice(Math.round(Math.random()*result.length),0,ar[i])
	return result;
}

// Devuelve la url base de la página
function base_url(){
	var dir=(""+window.location).split("/");
	pos=dir.length-4
	if(dir[2]=="127.0.0.1" || dir[2]=="localhost") pos--
	else if(dir[2]=="legendarya.com" || dir[2]=="www.legendarya.com") pos--
	
	result='';
	for(var i=0;i<pos;i++)
		result+="../";
	
	return result;
}

// Petición Ajax
function Ajax(query,params,func){
	var request
	if (window.XMLHttpRequest) request=new XMLHttpRequest()
	else if (window.ActiveXObject) request=new ActiveXObject("Microsoft.XMLHTTP")
	
	if (request!=null)
	{
		if(func)
			request.onreadystatechange=function(){
			  if(request.readyState==4){
				eval(func+'(request)')
			  }
			}
			
		if(params){
			request.open("POST",query,true)
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.setRequestHeader("Content-length", params.length);
			request.setRequestHeader("Connection", "close");
			request.send(params)
		}
		else{
			request.open("GET",query,true)
			request.send(null)
		}
	}
}