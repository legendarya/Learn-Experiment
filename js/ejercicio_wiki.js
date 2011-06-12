var tipo_leccion=0;
id=0;
var respondido=false; // indica si estamos respondiendo o mostrando el resultado de la respuesta
var siguiente_ejercicio;

function reiniciar_ejercicio(){
	if(confirm("¿Seguro que quieres reiniciar el ejercicio? Perderás todo lo avanzado en él."))
		reiniciar_leccion();
	return false;
}

function reiniciar_leccion(){
	for(var i=0;i<listado_preguntas.length;i++){
			listado_preguntas[i][3]=0;
			listado_preguntas[i][4]=0;
	}
	fallos=0;
	fecha_inicio=(new Date()).getTime();
	estadisticas();
	preguntar();
}

function siguiente_leccion(){
	window.location="?leccion="+siguiente_ejercicio;
}

function comprueba_equivalencia(respuesta){
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i][4]>1 && compara_equivalencia(respuesta,listado_preguntas[i][2])){
			listado_preguntas[i][4]=1;
			listado_preguntas[i][3]=fecha_inicio+Math.floor((new Date()).getTime()/1000);
		}
}

function comprueba_pregunta(){
	var response=document.getElementById("response");
	return compara_equivalencia(listado_preguntas[pregunta_actual][2],response.value)
}

function compara_equivalencia(a,b){
	var correctas=splitAndTrim(a)
	var respuestas=splitAndTrim(b)
	
	var bien=correctas.length==respuestas.length;
	for(var i=0;i<correctas.length;i++)
		if(respuestas[i]!=correctas[i]) bien=false;
	return bien
}

function pulsaTecla(e){
	if(comprueba_pregunta()) preguntaRespondida();
}

var max_nivel=tabla_tiempos.length-1

//gestiona la respuesta del usuario
function preguntaRespondida(){
	if(comprueba_pregunta())
		return respuesta_correcta();
	else return respuesta_erronea();
}

var fallos=0;
var errores=0;
function estadisticas(){
	var valor=0;
	var temp="";
	for(var i=0;i<listado_preguntas.length;i++){
		temp+="["+listado_preguntas[i][1]+","+listado_preguntas[i][2]+","+listado_preguntas[i][3]+","+listado_preguntas[i][4]+"]"
		valor+=listado_preguntas[i][4];
	}
	
	var valor1=valor
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	// Si el valor es 100, hemos terminado
	if(valor==100){
		document.getElementById("objeto_pregunta").style.display="none";
		document.getElementById("question").style.display="none";
		document.getElementById("mensaje_respuesta").style.display="none";
		document.getElementById("enhorabuena_final").style.display="block";
		salvaEstado()
	}
	
	//setText("debug",temp);
	if(valor==0)
		setText("porcentaje_fallos","0");
	else setText("porcentaje_fallos",fallos);
	document.getElementById("barra_progreso").style.backgroundPosition="-"+Math.floor((100-valor)*2)+"px"
	setText("porcentaje",""+valor.toFixed(2));
}

function guardar_ejercicio(){
	salvaEstado()
	if(no_identificado)
	{
		if(confirm("Los resultados se han guardado temporalmente.\n\n"+
			"Para guardar definitivamente y poder recuperarlos mas adelante debe registrarse o identificarse.\n\n"+
			"¿Desea registrarse ahora?"))
			window.location="pag420";
	}
	return false;
}

function salvaEstado(){
	var valor=0;
	var preguntas="";
	for(var i=0;i<listado_preguntas.length;i++){
		var tiempo_proxima="0"
		if(listado_preguntas[i][3]>0)
			var tiempo_proxima=(listado_preguntas[i][3]-(fecha_inicio+Math.floor((new Date()).getTime()/1000)));
		preguntas+=(i?"/":"")+listado_preguntas[i][1]+","+
			tiempo_proxima+","+listado_preguntas[i][4]
		valor+=listado_preguntas[i][4];
	}
	
	if(!valor) return;
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	ajax(go_up()+"componente/ajax_guardaPregunta","articulo="+articulo+"&id="+id_componente+
		"&tiempo="+(((new Date()).getTime()-fecha_inicio)/1000)+"&fallos="+fallos+"&porcentaje="+valor+"&preguntas="+preguntas,borrarTemp);
}

var pregunta_actual=null;

function siguiente(){
	document.getElementById("question").style.display="block";
	document.getElementById("mensaje_respuesta").style.display="none";
	preguntar();
	return false;
}

function setItem(id,text,option){
	if(text.substr(0,7)=="http://"){
		while(document.getElementById(id).firstChild) 
			document.getElementById(id).removeChild(document.getElementById(id).firstChild)
			
		var parts=text.split(" ")
		var img=document.createElement("img");
		img.src=parts[0]
		
		if(option){
			img.style.height="40px"
		}
		else{
			img.style.width="100px"
			img.style.margin="auto"
			img.style.display="block"
		}
		document.getElementById(id).appendChild(img);
		if(parts.length>1)
			document.getElementById(id).appendChild(document.createTextNode(parts[1]));
	}
	else setText(id,text);
}

function randOrder(ar){
	var result=new Array();
	for(var i=0;i<ar.length;i++)
		result.splice(Math.round(Math.random()*result.length),0,ar[i])
	return result;
}

function preguntar(){
	var response=document.getElementById("response");
	response.value="";
	
	pregunta_actual=escogerPregunta()
	
	
	setItem("objeto_pregunta",listado_preguntas[pregunta_actual][1])
	
	
	document.getElementById("enhorabuena_final").style.display="none";
	document.getElementById("question").style.display="block";
	document.getElementById("objeto_pregunta").style.display="block";
	
	if(tipo_leccion==0){
	response.focus();
		document.getElementById("mensaje_respuesta").style.display="none";
		
		document.getElementById("campo_respuesta").style.display=""
	}
	else if(tipo_leccion==3){
		vaciar(document.getElementById('origen_palabras'))
		vaciar(document.getElementById('destino_palabras'))
		
		var palabras=randOrder(listado_preguntas[pregunta_actual][2].split(' '));
		for(var i=0;i<palabras.length;i++){
			var palabra=document.createElement('a');
			document.getElementById('origen_palabras').appendChild(palabra);
			palabra.appendChild(document.createTextNode(palabras[i]))
			palabra.id='palabra'+(i+1)
			//palabra.href=''
			palabra.onclick=ponerPalabra
			
			vaciar(document.getElementById('destino_palabras'))
		}
	}
	else{
		var opciones=new Array();
		opciones[0]=listado_preguntas[pregunta_actual][2];
		
		var i=3;
		while(i>0){
			var encontrado=false;
			var res=Math.floor(Math.random()*listado_preguntas.length);
			for(var j=0;j<opciones.length;j++)
				if(opciones[j]==listado_preguntas[res][2]){
					encontrado=true
					break;
				}
				
			var r=Math.random();
			
			if(!encontrado){
			
				i--;
				if(Math.random()>0.5) 
				opciones.push(listado_preguntas[res][2])
				else opciones.unshift(listado_preguntas[res][2])
			}
		}
		
		for(var i=0;i<opciones.length;i++){
			if(opciones[i]==listado_preguntas[pregunta_actual][2]) 
				document.getElementById("respuesta"+i).onclick=respuesta_correcta
			else document.getElementById("respuesta"+i).onclick=respuesta_erronea
			
	setItem("respuesta"+i,opciones[i],1)
		}
	}
}

function ponerPalabra(evt){
	var event = evt || window.event;
	var element = event.target || event.srcElement;

	// Cambiamos de posición la palabra
	if(element.parentNode.id=='destino_palabras')
		document.getElementById('origen_palabras').appendChild(element);
	else document.getElementById('destino_palabras').appendChild(element);
	
	// Si hemos escogido todas la palabras comprobamos si es correcto o no
	if(!document.getElementById('origen_palabras').childNodes.length){
		var palabras=listado_preguntas[pregunta_actual][2].split(' ')
		for(var i=0;i<document.getElementById('destino_palabras').childNodes.length;i++)
			if(document.getElementById('destino_palabras').childNodes[i].firstChild.nodeValue!=palabras[i]){
				respuesta_erronea()
				return
			}
		respuesta_correcta()
	}
	
	return false;
}

function respuesta_correcta(){
	if(tabla_tiempos.length>listado_preguntas[pregunta_actual][4] && listado_preguntas[pregunta_actual][4]<max_nivel){
		if(listado_preguntas[pregunta_actual][4]!=0)
			listado_preguntas[pregunta_actual][4]++ // Nivel
		else listado_preguntas[pregunta_actual][4]=2
	}
	 // Próxima aparicion
	listado_preguntas[pregunta_actual][3]=
		fecha_inicio+Math.floor((new Date()).getTime()/1000)+tabla_tiempos[listado_preguntas[pregunta_actual][4]-1]
	preguntar();
	estadisticas();
	return false;
}


function respuesta_erronea(evt){
	fallos++;
	if(document.getElementById("response").value!="" || tipo_leccion==1 || tipo_leccion==3){
		document.getElementById("mensaje_respuesta_fallada").style.display="block"
			
		if(tipo_leccion!=1)
			setText("respuesta_fallada",document.getElementById("response").value);
		comprueba_equivalencia(document.getElementById("response").value)
	}
	else document.getElementById("mensaje_respuesta_fallada").style.display="none"
	
	document.getElementById("question").style.display="none";
	document.getElementById("mensaje_respuesta").style.display="block";
		
	listado_preguntas[pregunta_actual][4]=1 // Nivel
	// Próxima aparicion
	listado_preguntas[pregunta_actual][3]= 
			fecha_inicio+Math.floor((new Date()).getTime()/1000)+tabla_tiempos[listado_preguntas[pregunta_actual][4]-1]		
	
	respondido=true;
	document.getElementById("mensaje_respuesta_correcta").style.display="block";
	vaciar(document.getElementById("respuesta_correcta"));
	document.getElementById("respuesta_correcta").appendChild(document.createTextNode(listado_preguntas[pregunta_actual][2]));
	document.getElementById("seguir").focus();
	estadisticas();
	return false;
}

var peticion_anterior=0
var num_repasos=0
var preguntas_restantes=0; // Preguntas que se podr�an preguntar actualmente
var nuevas_preguntas_restantes=0;
var preguntas_recibidas=0;
var nuevas_recibidas=0;
var avisado_sin_preguntas=false;

function escogerPregunta(){
	// Obtenemos el momento actual
	var momentoActual=fecha_inicio+Math.floor((new Date()).getTime()/1000)
	
	var minNivelEncontrado=100
	var maxTiempoEncontrado=-1
	var encontrada=null; // Pregunta encontrada
	
	// Buscamos la pregunta de nivel mas bajo, no nulo, que se acerca mas al momento actual sin pasarlo
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i][3]<=momentoActual){
			if((listado_preguntas[i][4] < minNivelEncontrado && listado_preguntas[i][4]!=0) || 
				((listado_preguntas[i][4]==minNivelEncontrado) && (maxTiempoEncontrado<listado_preguntas[i][3]))){
				maxTiempoEncontrado=listado_preguntas[i][3]
				minNivelEncontrado=listado_preguntas[i][4]
				encontrada=i
			}
		}
		
	if(encontrada && listado_preguntas[encontrada][4]>=repaso) num_repasos++;
	
	// si no hay, buscamos una nueva
	if (encontrada===null || num_repasos>max_repaso)
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i][4]==0) {
			num_repasos=0
			return i
		}
		
	if(encontrada!==null) return encontrada
		
	var num_max=0;
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i][4] == max_nivel) num_max++;
		
	var j=Math.random()*num_max
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i][4] == max_nivel)
			if(--j<1) return i
	return 0
}

//realiza la llamada a AJAX
function getPregunta(respuesta,acertado){
	if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest()
	// code for IE
	else if (window.ActiveXObject) xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
	
	if (xmlhttp!=null)	//comprueba la existencia del objeto XMLHttpRequest
	  {
		var url = go_up()+"componente/ajax_getPreguntas";
		var params = "articulo="+articulo+"&id="+id_componente+"&data=";

		var primero=true;
		for(var i=0;i<listado_preguntas.length;i++){
			if (listado_preguntas[i][5]) // Si se ha modificado lo enviamos a guardar
			{
				if(primero) primero=false;
				else params+=";";
				
				params+=listado_preguntas[i][0]+","+listado_preguntas[i][3]+","+listado_preguntas[i][4]
				listado_preguntas[i][5]=0;
			}
		}
		
		xmlhttp.open("POST", url, true);
		xmlhttp.onreadystatechange =recibe_pregunta;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
	  }
}

function go_up(){
	var dir=(""+window.location).split("/");
	pos=dir.length-4
	if(dir[2]=="127.0.0.1" || dir[2]=="localhost") pos-=2
	else if(dir[2]=="openwebdeveloper.com" || dir[2]=="www.openwebdeveloper.com") pos--
	
	result='';
	for(var i=0;i<pos;i++)
		result+="../";
	
	return result;
}

function ajax(query,params,func){
	if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest()
	else if (window.ActiveXObject) xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
	if (xmlhttp!=null){
		xmlhttp.open("POST", query, true);
		xmlhttp.onreadystatechange =func;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
	  }
}

var aciertos_anterior=null;
var celda=null;
var listado_preguntas=[];
var fecha_inicio=0; // Fecha relativa al servidor
var tiempo_leccion=0;
var primera_pregunta=true;

function borrarTemp(){
  if(xmlhttp.readyState==4){
  }
}

//manejador de la respuesta AJAX
function recibe_pregunta(){
  if(xmlhttp.readyState==4){
	document.getElementById("enhorabuena_final").style.display="none";
		//alert(xmlhttp.responseText)
		if(xmlhttp.responseXML==null) alert(xmlhttp.responseText);
		var preguntas=xmlhttp.responseXML.documentElement;
		tipo_leccion=preguntas.getAttribute("tipo")
		siguiente_ejercicio=preguntas.getAttribute("siguiente")
		
		// Según el tipo
		if(tipo_leccion==1){
			document.getElementById("form_aprender").style.display="none";
			
			document.getElementById("options").appendChild(ops=document.createElement("div"));
			for(var i=0;i<2;i++){
				var a=document.createElement("a");
				a.id="respuesta"+i
				a.href=""
				ops.appendChild(a);
				ops.appendChild(document.createTextNode(" "));
			}
			
			document.getElementById("options").appendChild(ops=document.createElement("div"));
			for(var i=2;i<4;i++){
				var a=document.createElement("a");
				a.id="respuesta"+i
				a.href=""
				ops.appendChild(a);
				ops.appendChild(document.createTextNode(" "));
			}
		}
		else if(tipo_leccion==3){
			document.getElementById("form_aprender").style.display="none";
			
			var parrafo=document.createElement("p")
			parrafo.appendChild(document.createTextNode("Frase desordenada (pulsa en ellas para ordenarlas debajo):"))
			parrafo.className='explicacion_ordenar';
			document.getElementById("options").appendChild(parrafo)
			
			var origen=document.createElement("div");
			document.getElementById("options").appendChild(origen)
			origen.id='origen_palabras'
			
			parrafo=document.createElement("p")
			parrafo.appendChild(document.createTextNode("Frase ordenada:"))
			parrafo.className='explicacion_ordenar';
			document.getElementById("options").appendChild(parrafo)
			
			var destino=document.createElement("div")
			document.getElementById("options").appendChild(destino)
			destino.id='destino_palabras'
		}
		
		//fecha_inicio=(new Date()).getTime();
		tiempo_leccion=preguntas.getAttribute("tiempo")
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		setText("tiempo_leccion",Math.floor(tiempo_leccion/60)+":"+(tiempo_leccion%60<10?"0":"")+(tiempo_leccion%60).toFixed(0));
		fallos=preguntas.getAttribute("fallos")
		pasa_tiempo();
		
		
		lista_preguntas=preguntas.getElementsByTagName("pregunta");
		
		// Cargamos las preguntas recibidas
		for(var i=0;i<lista_preguntas.length;i++){
			listado_preguntas[listado_preguntas.length]=[
				lista_preguntas[i].getAttribute("id"),
				lista_preguntas[i].getAttribute("pregunta"),
				lista_preguntas[i].getAttribute("respuesta"),
				parseInt(lista_preguntas[i].getAttribute("tiempo")),
				parseInt(lista_preguntas[i].getAttribute("nivel")),0
			];
		}
		
		preguntar();
		estadisticas();
		
		var lista_teclados=preguntas.getElementsByTagName("teclado");
		if(lista_teclados.length) mostrarTeclado(lista_teclados[0].firstChild.nodeValue)
	}
}

function mostrarTeclado(teclas){
	var filas=teclas.split("\n")
	var teclado=document.createElement('div')
	teclado.className='tecladoVirtual'
	for(var i=0;i<filas.length;i++){
		var fila_teclas=document.createElement('div')
		var teclas=filas[i].split(' ')
		for(var j=0;j<teclas.length;j++){
			var tecla=document.createElement('a')
			tecla.appendChild(document.createTextNode(teclas[j].charAt(0)=='/'?teclas[j].charAt(1):teclas[j].charAt(0)))
			if(teclas[j].charAt(0)=='/') tecla.className='modificador';
			tecla.onclick=escribirTecla
			tecla.href=''
			tecla.data=teclas[j]
			fila_teclas.appendChild(tecla)
		}
		teclado.appendChild(fila_teclas)
	}
	document.getElementById('expandWindow').parentNode.appendChild(teclado)
}

var teclaModificadorActual=null
function escribirTecla(evt){
	var event = evt || window.event;
	var element = event.target || event.srcElement;
	
	if(element.data.charAt(0)=='/'){
		element.style.background='#fc0';
		teclaModificadorActual=element
		var teclado=element.parentNode.parentNode
		for(var i=0;i<teclado.childNodes.length;i++){
			for(var j=0;j<teclado.childNodes[i].childNodes.length;j++){
				var tecla=teclado.childNodes[i].childNodes[j];
				for(var k=1;k<tecla.data.length;k+=2){
					if(tecla!=element && tecla.data.charAt(k)==element.data.charAt(1)){
						tecla.firstChild.nodeValue=tecla.data.charAt(k+1)
						tecla.style.background='#aaf';
					}
				}
			}
		}
	}
	else{
		document.getElementById('response').value+=element.firstChild.nodeValue;
		if(teclaModificadorActual){
			teclaModificadorActual.style.background='';
			
			var teclado=element.parentNode.parentNode
			for(var i=0;i<teclado.childNodes.length;i++){
				for(var j=0;j<teclado.childNodes[i].childNodes.length;j++){
					var tecla=teclado.childNodes[i].childNodes[j];
					if(tecla.data.charAt(0)!='/')
						tecla.firstChild.nodeValue=tecla.data.charAt(0)
					tecla.style.background='';
				}
			}
		}
		teclaModificadorActual=null
	}
	return false
}

function pausar_ejercicio(){
	tiempo_leccion = ((new Date()).getTime()-fecha_inicio)/1000;
	clearTimeout(timer);
	return false
}

var timer;

function pasa_tiempo(){
	var tiempo = ((new Date()).getTime()-fecha_inicio)/1000;
	setText("tiempo_leccion",Math.floor(tiempo/60)+":"+(tiempo%60<10?"0":"")+(tiempo%60).toFixed(0));
	timer=setTimeout(pasa_tiempo,1000);
	// tiempo_leccion
}

var invertir=false;


//bloquea en el navegador la funcinalidad de autocompletar
function blockAutoComplete() {
    document.getElementById("form_aprender").setAttribute("autocomplete","off");
}

//función inicializadora del script
function initialize(){
	blockAutoComplete(); 
	getPregunta("");
	window.onunload = salvaEstado;
	window.onbeforeunload  = salvaEstado;
}
		//llama a la función a ejecutar al cargar la página
		Behaviour.addLoadEvent(initialize)

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
	while ( name.firstChild) name.removeChild( name.firstChild );
	name.appendChild(document.createTextNode(text));
}

//parsea las respuestas para compararlas
function splitAndTrim(cadena){
	var data=cadena;
	
	if(ignorar_signos){
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
	}
	
	data=data.toUpperCase().split(",")
	for(var i=0;i<data.length;i++){
		data[i]=data[i].replace(/^\s+|\s+$/g,"")
	}
	return data.sort()
}

//elimina todos los hijos de un elemento
function vaciar(element){
	while(element.firstChild) element.removeChild(element.firstChild);
}