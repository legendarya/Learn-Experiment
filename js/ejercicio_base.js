var tabla_tiempos=[0,1,5,10,20]
var repaso=4 // Nivel a partir del cual consideramos preguntas de repaso
var max_repaso=2 // Máximo número de repasos seguidos

var tipo_leccion=0;
var max_nivel=tabla_tiempos.length
var fallos=0;

var ignorar_signos=0
var siguiente_ejercicio;

var pronunciar // Indica si se debe pronunciar la pregunta o respuesta, y cuando

var aciertos_anterior=null;
var listado_preguntas=[];
var fecha_inicio=0; // Fecha relativa al servidor
var tiempo_leccion=0;

var dir=(""+window.location).split("/");
if(dir[2]=="127.0.0.1" || dir[2]=="localhost" || dir[2]=="legendarya.com" || dir[2]=="www.legendarya.com")
	var url=(""+window.location).split('/').slice(5).join('/')
else var url=(""+window.location).split('/').slice(4).join('/')

//gestiona la respuesta del usuario
function preguntaRespondida(){
	if(!listado_preguntas.length) return false;
	if(compara_equivalencia(listado_preguntas[pregunta_actual].respuesta,
	$("#response").attr('value'))) return respuesta_correcta();
	else return respuesta_erronea();
}

// Comprobamos que las respuestas a y b sean equivalentes
function compara_equivalencia(a,b){
	var correctas=splitAndTrim(a)
	var respuestas=splitAndTrim(b)
	
	var bien=correctas.length==respuestas.length;
	for(var i=0;i<correctas.length;i++)
		if(respuestas[i]!=correctas[i]) return false;
	return bien
}

function respuesta_correcta(){
	if(pronunciar==2 || pronunciar==4) pronuncia_palabra((pronunciar==2)?listado_preguntas[pregunta_actual].pregunta:listado_preguntas[pregunta_actual].respuesta)
	cierraDialogoDesc();
	if(listado_preguntas[pregunta_actual].nivel<max_nivel){
		// Si acertamos a la primera solo lo mostraremos una vez mas
		if(listado_preguntas[pregunta_actual].nivel==-1){
			listado_preguntas[pregunta_actual].nivel=max_nivel-1
		}
		else listado_preguntas[pregunta_actual].nivel++
	}
	
	 // Próxima aparicion
	listado_preguntas[pregunta_actual].siguiente=tabla_tiempos[listado_preguntas[pregunta_actual].nivel]
	preguntar();
	estadisticas();
	return false;
}

// Acciones si la respuesta es correcta
function respuesta_erronea(evt){
	var error=document.getElementById("response").value;
	// Si es un ejercicio de opciones, guardamos el error cometido
	if(tipo_leccion==1){
		error=evt.target.innerHTML
		if(listado_preguntas[pregunta_actual].comparaciones[error])
			listado_preguntas[pregunta_actual].comparaciones[error].error++
	}
	
	if(pronunciar==2 || pronunciar==4) pronuncia_palabra((pronunciar==2)?listado_preguntas[pregunta_actual].pregunta:listado_preguntas[pregunta_actual].respuesta)
	cierraDialogoDesc();
	fallos++;
	if(error!=""  || tipo_leccion==3){
		document.getElementById("mensaje_respuesta_fallada").style.display="block"
			
		if(tipo_leccion!=1)
			setText("respuesta_fallada",error);
		
		// Comprobamos si noshemos confundido con otra respuesta
		var respuesta_confundida=false;
		for(var i=0;i<listado_preguntas.length;i++)
			if(compara_equivalencia(error,listado_preguntas[i].respuesta))
				if(respuesta_confundida!==false){ // Si hay mas de una respuesta similar, abandonamos
					respuesta_confundida=false
					break;
				}
				else respuesta_confundida=i
			
		// Si solo hay una respuesta confundida,  reiniciamos también la respuesta confundida
		if(respuesta_confundida!==false)
		{
			listado_preguntas[respuesta_confundida].nivel=0;
			listado_preguntas[respuesta_confundida].siguiente=tabla_tiempos[listado_preguntas[respuesta_confundida].nivel];
		}
	}
	else document.getElementById("mensaje_respuesta_fallada").style.display="none"
	
	document.getElementById("question").style.display="none";
	document.getElementById("mensaje_respuesta").style.display="block";
		
	listado_preguntas[pregunta_actual].nivel=0 // Nivel
	listado_preguntas[pregunta_actual].siguiente= tabla_tiempos[listado_preguntas[pregunta_actual].nivel]	// Próxima aparicion
	
	document.getElementById("mensaje_respuesta_correcta").style.display="block";
	vaciar(document.getElementById("respuesta_correcta"));
	document.getElementById("respuesta_correcta").appendChild(document.createTextNode(listado_preguntas[pregunta_actual].respuesta));
	document.getElementById("seguir").focus();
	estadisticas();
	return false;
}

var ultimo_salvado=0;

// Actualiza las estadísticas
function estadisticas(){
	var valor=0;
	var tabla=''
	for(var i=0;i<listado_preguntas.length;i++){
		valor+=listado_preguntas[i].nivel>0?listado_preguntas[i].nivel:0;
		
		var comparaciones='';
		for(comparacion in listado_preguntas[i].comparaciones)
			comparaciones+=comparacion+'('+listado_preguntas[i].comparaciones[comparacion].error+
				'/'+listado_preguntas[i].comparaciones[comparacion].apariciones+')'+' '
		
		tabla+='<tr><td>'+listado_preguntas[i].pregunta+'</td><td>'+listado_preguntas[i].respuesta+'</td><td>'+listado_preguntas[i].siguiente+
		'</td><td>'+listado_preguntas[i].nivel+'</td><td>'+comparaciones+'</td></tr>';
	}
	
	//$('#contentTab0').html('<table><tr><th>Preg</th><th>Resp</th><th>Siguiente</th><th>Nivel</th><th>Comparaciones</th></tr>'+tabla+'</tabla>');
	
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	if(valor==100){ // Si el valor es 100, hemos terminado
		document.getElementById("name").style.display="none";
		document.getElementById("question").style.display="none";
		document.getElementById("mensaje_respuesta").style.display="none";
		document.getElementById("enhorabuena_final").style.display="block";
		salvaEstado()
	}
	
	//if(ultimo_salvado.getTime()+20000<(new Date()).getTime()) salvaEstado()
	salvaEstado()
	
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
		preguntas.push(listado_preguntas[i][0]+","+listado_preguntas[i].siguiente+","+listado_preguntas[i].nivel)
		valor+=listado_preguntas[i].nivel;
	}
	
	if(!valor) return;
	valor=valor*100/(listado_preguntas.length*max_nivel)
	
	
	Ajax(base_url()+"ejercicio/guardar/"+url+"?tiempo="+tiempo_leccion+
		"&fallos="+fallos+"&porcentaje="+valor+"&preguntas="+preguntas.join('//'))
	ultimo_salvado=new Date();
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

// Pone la pregunta
function preguntar(){
	document.getElementById("response").value="";
	pregunta_actual=escogerPregunta()
	
	// reducimos el contador de "siguiente"
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel>=0) listado_preguntas[i].siguiente--
		
	setItem("name",listado_preguntas[pregunta_actual].pregunta)
	if(pronunciar==1 || pronunciar==3) pronuncia_palabra((pronunciar==1)?listado_preguntas[pregunta_actual].pregunta:listado_preguntas[pregunta_actual].respuesta)
	document.getElementById("enhorabuena_final").style.display="none";
	document.getElementById("question").style.display="block";
	document.getElementById("name").style.display="block";
	
	if(tipo_leccion==0) mostraPreguntaDeTexto(pregunta_actual)
	else if(tipo_leccion==3) mostramosPreguntaDeOrdenar(pregunta_actual)
	else mostraPreguntaDeOpciones(pregunta_actual)
}


var num_repasos=0
var preguntas_restantes=0; // Preguntas que se podrían preguntar actualmente
var nuevas_preguntas_restantes=0;
var preguntas_recibidas=0;
var nuevas_recibidas=0;
var avisado_sin_preguntas=false;

// Función que escoge la pregunta siguiente
function escogerPregunta(){
	
	var minNivelEncontrado=100
	var maxTiempoEncontrado=-1
	var encontrada=null; // Pregunta encontrada
	
	// Buscamos la pregunta de nivel mas bajo, no nulo, que se acerca mas a 0 sin superarlo
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].siguiente<=0){
			if((listado_preguntas[i].nivel < minNivelEncontrado && listado_preguntas[i].nivel!=-1) || 
				((listado_preguntas[i].nivel==minNivelEncontrado) && (maxTiempoEncontrado<listado_preguntas[i].siguiente))){
				maxTiempoEncontrado=listado_preguntas[i].siguiente
				minNivelEncontrado=listado_preguntas[i].nivel
				encontrada=i
			}
		}
		
	if(encontrada && listado_preguntas[encontrada].nivel>=repaso) num_repasos++;
	
	// si no hay, o hemos superado el número de repasos, buscamos una nueva
	if (encontrada===null || num_repasos>max_repaso)
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel==-1) {
			num_repasos=0
			return i
		}
		
	if(encontrada!==null) return encontrada
		
	var num_max=0;
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel == max_nivel) num_max++;
		
	var j=Math.random()*num_max
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel == max_nivel)
			if(--j<1) return i
	return 0
}

//manejador de la obtención de preguntas
function recibe_pregunta(xmlhttp){
	ultimo_salvado=new Date();
	document.getElementById("enhorabuena_final").style.display="none";
		if(xmlhttp.responseXML==null) alert(xmlhttp.responseText);
		var preguntas=xmlhttp.responseXML.documentElement;
		
		cargaContenidoExterno(preguntas.getAttribute("contenido_externo"));
		cargaVideo(preguntas.getAttribute("video"));
		pronunciar=preguntas.getAttribute("pronunciar");
		
		tipo_leccion=preguntas.getAttribute("tipo")
		siguiente_ejercicio=preguntas.getAttribute("siguiente")
		
		tiempo_leccion=preguntas.getAttribute("tiempo")
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		setText("tiempo_leccion",Math.floor(tiempo_leccion/60)+":"+(tiempo_leccion%60<10?"0":"")+(tiempo_leccion%60).toFixed(0));
		fallos=preguntas.getAttribute("fallos")
		
		lista_preguntas=preguntas.getElementsByTagName("pregunta");
		
		var cero=0;
		
		// Cargamos las preguntas recibidas
		for(var i=0;i<lista_preguntas.length;i++){
			listado_preguntas[listado_preguntas.length]=new Object();
			listado_preguntas[listado_preguntas.length-1].pregunta=lista_preguntas[i].getAttribute("pregunta");
			listado_preguntas[listado_preguntas.length-1].respuesta=lista_preguntas[i].getAttribute("respuesta");
			listado_preguntas[listado_preguntas.length-1].siguiente=parseInt(lista_preguntas[i].getAttribute("tiempo"));
			listado_preguntas[listado_preguntas.length-1].nivel=parseInt(lista_preguntas[i].getAttribute("nivel"));
			
			if(listado_preguntas[listado_preguntas.length-1].nivel==0) cero++  // Contamos los ceros en la lista
		}
		
		// si hay más de dos ceros, estamos con la versión vieja, hay que pasar los 0 a -1
		//if(cero)
		
		// Ponemos las preguntas con nivel==-1 al final
		listado_preguntas.sort(function(a,b) {return b.nivel==-1})
		
		
		// Según el tipo
		if(tipo_leccion==1) inicializamosLasPreguntaDeOpciones()
		else if(tipo_leccion==3) inicializamosLasPreguntaDeOrdenar()
		
		preguntar();
		estadisticas();
		
		var lista_teclados=preguntas.getElementsByTagName("teclado");
		if(lista_teclados.length) mostrarTeclado(lista_teclados[0].firstChild.nodeValue)
}

var timer=null;
// Cierra el diálogo de la descripción al responder a una pregunta o al continuar
function cierraDialogoDesc(){
	if(timer==null){
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		pasa_tiempo();
	}
}

// Función que actualiza el cronómetro
function pasa_tiempo(){
	var tiempo = ((new Date()).getTime()-fecha_inicio)/1000;
	setText("tiempo_leccion",Math.floor(tiempo/60)+":"+(tiempo%60<10?"0":"")+(tiempo%60).toFixed(0));
	timer=setTimeout(pasa_tiempo,1000);
}