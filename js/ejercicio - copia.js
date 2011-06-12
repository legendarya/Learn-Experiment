HTML5.Audio.Proxy.getProxy({swfPath:base_url()+'swf/html5-audio.swf'});

var debug=0;
var tabla_tiempos=[0,3,10,20,30,40]
var max_nivel=tabla_tiempos.length-1
var repaso=4 // Nivel a partir del cual consideramos preguntas de repaso
var max_repaso=2
var ignorar_signos=0
var no_identificado=true

var tipo_leccion=0;
id=0;
var respondido=false; // indica si estamos respondiendo o mostrando el resultado de la respuesta
var siguiente_ejercicio;
var max_nivel=tabla_tiempos.length-1
var fallos=0;

//gestiona la respuesta del usuario
function preguntaRespondida(){
	if(comprueba_pregunta()) return respuesta_correcta();
	return respuesta_erronea();
}

function comprueba_pregunta(){
	return compara_equivalencia(listado_preguntas[pregunta_actual][2],document.getElementById("response").value)
}

function compara_equivalencia(a,b){
	var correctas=splitAndTrim(a)
	var respuestas=splitAndTrim(b)
	
	var bien=correctas.length==respuestas.length;
	for(var i=0;i<correctas.length;i++)
		if(respuestas[i]!=correctas[i]) return false;
	return bien
}

// Actualiza las estadísticas
function estadisticas(){
	var valor=0;
	for(var i=0;i<listado_preguntas.length;i++)
		valor+=listado_preguntas[i][4];
	
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	if(valor==100){ // Si el valor es 100, hemos terminado
		document.getElementById("name").style.display="none";
		document.getElementById("question").style.display="none";
		document.getElementById("mensaje_respuesta").style.display="none";
		document.getElementById("enhorabuena_final").style.display="block";
		salvaEstado()
	}
	
	if(valor==0) setText("porcentaje_fallos","0");
	else setText("porcentaje_fallos",fallos);
	document.getElementById("barra_progreso").style.backgroundPosition="-"+Math.floor((100-valor)*2)+"px"
	setText("porcentaje",""+valor.toFixed(2));
}

// Salva el estado del ejercicio
function salvaEstado(){
	var valor=0;
	var preguntas=new Array();
	for(var i=0;i<listado_preguntas.length;i++){
		preguntas.push(listado_preguntas[i][0]+","+listado_preguntas[i][3]+","+listado_preguntas[i][4])
		valor+=listado_preguntas[i][4];
	}
	
	if(!valor) return;
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	
	var url=(""+window.location).split('/').slice(5).join('/')
	Ajax(base_url()+"ejercicio/guardar/"+url+"?tiempo="+tiempo_leccion+
		"&fallos="+fallos+"&porcentaje="+valor+"&preguntas="+preguntas.join('//'))
}

var pregunta_actual=null;

// Pasa a la siguiente pregunta
function siguiente(){
	document.getElementById("question").style.display="block";
	document.getElementById("mensaje_respuesta").style.display="none";
	preguntar();
	return false;
}

// Pone los elementos de  progutas o respuestas
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
		if(parts.length>1) document.getElementById(id).appendChild(document.createTextNode(parts[1]));
	}
	else setText(id,text);
}

var lista_sonidos=new Array();
function pronuncia_palabra(palabra){
	for(var i=0;i<lista_sonidos.length;i++)
		if(lista_sonidos[i][0]==palabra){
			lista_sonidos[i][1].play();
			return;
		}
		
	Ajax(base_url()+'ejercicio/pronunciacion_general/'+palabra,'','recibe_pronunciacion')
}

function recibe_pronunciacion(xmlhttp){
	lista_sonidos.push([xmlhttp.responseXML.firstChild.getAttribute('palabra'),new HTML5.Audio(xmlhttp.responseXML.firstChild.getAttribute('mp3'))]);
	pronuncia_palabra(xmlhttp.responseXML.firstChild.getAttribute('palabra'))
}

// Pone la pregunta
function preguntar(){
	document.getElementById("response").value="";
	pregunta_actual=escogerPregunta()
	setItem("name",listado_preguntas[pregunta_actual][1])
	pronuncia_palabra(listado_preguntas[pregunta_actual][1])
	document.getElementById("enhorabuena_final").style.display="none";
	document.getElementById("question").style.display="block";
	document.getElementById("name").style.display="block";
	
	if(tipo_leccion==0) mostraPreguntaDeTexto(pregunta_actual)
	else if(tipo_leccion==3) mostramosPreguntaDeOrdenar(pregunta_actual)
	else mostraPreguntaDeOpciones(pregunta_actual)
}

function respuesta_correcta(){
	cierraDialogoDesc();
	if(tabla_tiempos.length>listado_preguntas[pregunta_actual][4] && listado_preguntas[pregunta_actual][4]<max_nivel){
		if(listado_preguntas[pregunta_actual][4]!=0)
			listado_preguntas[pregunta_actual][4]++ // Nivel
		else listado_preguntas[pregunta_actual][4]=2
	}
	
	 // Próxima aparicion
	listado_preguntas[pregunta_actual][3]=Math.floor((new Date()).getTime()/1000)+tabla_tiempos[listado_preguntas[pregunta_actual][4]-1]
	preguntar();
	estadisticas();
	return false;
}

// Acciones si la respuesta es correcta
function respuesta_erronea(evt){
	cierraDialogoDesc();
	fallos++;
	if(document.getElementById("response").value!="" || tipo_leccion==1 || tipo_leccion==3){
		document.getElementById("mensaje_respuesta_fallada").style.display="block"
			
		if(tipo_leccion!=1)
			setText("respuesta_fallada",document.getElementById("response").value);
		
		// Comprobamos si noshemos confundido con otra respuesta
		var respuesta_confundida=false;
		for(var i=0;i<listado_preguntas.length;i++)
			if(compara_equivalencia(document.getElementById("response").value,listado_preguntas[i][2]))
				if(respuesta_confundida!==false){ // Si hay mas de una respuesta similar, abandonamos
					respuesta_confundida=false
					break;
				}
				else respuesta_confundida=i
			
		// Si solo hay una respuesta confundida,  reiniciamos también la respuesta confundida
		if(respuesta_confundida!==false)
		{
			listado_preguntas[respuesta_confundida][4]=1;
			listado_preguntas[respuesta_confundida][3]=Math.floor((new Date()).getTime()/1000);
		}
	}
	else document.getElementById("mensaje_respuesta_fallada").style.display="none"
	
	document.getElementById("question").style.display="none";
	document.getElementById("mensaje_respuesta").style.display="block";
		
	listado_preguntas[pregunta_actual][4]=1 // Nivel
	listado_preguntas[pregunta_actual][3]= Math.floor((new Date()).getTime()/1000)	// Próxima aparicion
	
	respondido=true;
	document.getElementById("mensaje_respuesta_correcta").style.display="block";
	vaciar(document.getElementById("respuesta_correcta"));
	document.getElementById("respuesta_correcta").appendChild(document.createTextNode(listado_preguntas[pregunta_actual][2]));
	document.getElementById("seguir").focus();
	estadisticas();
	return false;
}

var num_repasos=0
var preguntas_restantes=0; // Preguntas que se podr�an preguntar actualmente
var nuevas_preguntas_restantes=0;
var preguntas_recibidas=0;
var nuevas_recibidas=0;
var avisado_sin_preguntas=false;

// Función que escoge la pregunta siguiente
function escogerPregunta(){
	// Obtenemos el momento actual
	var momentoActual=Math.floor((new Date()).getTime()/1000)
	
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
	
	// si no hay, o hemos superado el número de repasos, buscamos una nueva
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

var aciertos_anterior=null;
var celda=null;
var listado_preguntas=[];
var fecha_inicio=0; // Fecha relativa al servidor
var tiempo_leccion=0;

//manejador de la obtención de preguntas
function recibe_pregunta(xmlhttp){
	document.getElementById("enhorabuena_final").style.display="none";
		if(xmlhttp.responseXML==null) alert(xmlhttp.responseText);
		var preguntas=xmlhttp.responseXML.documentElement;
		
		cargaContenidoExterno(preguntas.getAttribute("contenido_externo"));
		cargaVideo(preguntas.getAttribute("video"));
		
		tipo_leccion=preguntas.getAttribute("tipo")
		siguiente_ejercicio=preguntas.getAttribute("siguiente")
		
		// Según el tipo
		if(tipo_leccion==1) inicializamosLasPreguntaDeOpciones()
		else if(tipo_leccion==3) inicializamosLasPreguntaDeOrdenar()
		
		tiempo_leccion=preguntas.getAttribute("tiempo")
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		setText("tiempo_leccion",Math.floor(tiempo_leccion/60)+":"+(tiempo_leccion%60<10?"0":"")+(tiempo_leccion%60).toFixed(0));
		fallos=preguntas.getAttribute("fallos")
		
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

// Cierra el diálogo de la descripción al responder a una pregunta o al continuar
function cierraDialogoDesc(){
	if(timer==null){
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		pasa_tiempo();
	}
}

var timer=null;

// Función que actualiza el cronómetro
function pasa_tiempo(){
	var tiempo = ((new Date()).getTime()-fecha_inicio)/1000;
	setText("tiempo_leccion",Math.floor(tiempo/60)+":"+(tiempo%60<10?"0":"")+(tiempo%60).toFixed(0));
	timer=setTimeout(pasa_tiempo,1000);
	// tiempo_leccion
}

//función inicializadora del script
function initialize(){
	var url=(""+window.location).split('/').slice(5).join('/')
	
	document.getElementById("form_aprender").setAttribute("autocomplete","off");
	Ajax(base_url()+"ejercicio/info/"+url,'','recibe_pregunta')
	window.onunload = salvaEstado;
	window.onbeforeunload  = salvaEstado;
}
//llama a la función a ejecutar al cargar la página
Behaviour.addLoadEvent(initialize)