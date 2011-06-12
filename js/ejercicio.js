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
	if(compara_equivalencia(listado_preguntas[pregunta_actual].respuesta,document.getElementById("response").value)) return respuesta_correcta();
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
	if(pronunciar.pronuncia_respuesta) 
		pronuncia_palabra(pronunciar.pronuncia_respuesta==1?listado_preguntas[pregunta_actual].pregunta:
		listado_preguntas[pregunta_actual].respuesta,pronunciar.idioma_respuesta)
	
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
		listado_preguntas[pregunta_actual].comparaciones[error].error++
	}
	
	
	if(pronunciar.pronuncia_respuesta) 
		pronuncia_palabra(pronunciar.pronuncia_respuesta==1?listado_preguntas[pregunta_actual].pregunta:
		listado_preguntas[pregunta_actual].respuesta,pronunciar.idioma_respuesta)
	
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
	//salvaEstado()
	
	if(valor==0) setText("porcentaje_fallos","0");
	else setText("porcentaje_fallos",fallos);
	document.getElementById("barra_progreso").style.backgroundPosition="-"+Math.floor((100-valor)*2)+"px"
	setText("porcentaje",""+valor.toFixed(2));
}

// Salva el estado del ejercicio
function salvaEstado(){
	$.post(base_url()+"ejercicio/guarda_resultado/"+url+"?tiempos="+tiempo_leccion+"&fallos="+fallos);
	return
	
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
	
	// Mostramos el logo de forvo con transparencia si no hay pronunciacion para esta palabra
	if(pronunciar.pronuncia_pregunta || pronunciar.pronuncia_respuesta){
		document.getElementById('logoForvo').className=''
		if(pronunciar.pronuncia_pregunta==1 || pronunciar.pronuncia_respuesta==1) var palabra=listado_preguntas[pregunta_actual].pregunta
		else var palabra=listado_preguntas[pregunta_actual].respuesta
				//alert(lista_sonidos)
		for(var i=0;i<lista_sonidos.length;i++)
			if(lista_sonidos[i][0]==palabra){
				if(!lista_sonidos[i][1]) document.getElementById('logoForvo').className='semiOculto'
				break;
			}
	}
	
	// reducimos el contador de "siguiente"
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel>=0) listado_preguntas[i].siguiente--
		
	setItem("name",listado_preguntas[pregunta_actual].pregunta)
	
	
	if(pronunciar.pronuncia_pregunta) 
		pronuncia_palabra(pronunciar.pronuncia_pregunta==1?listado_preguntas[pregunta_actual].pregunta:
		listado_preguntas[pregunta_actual].respuesta,pronunciar.idioma_pregunta)
		
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
var anteriorSabida=false // Indica si en la pregunta anterior ya se preguntó una pregunta que habíamos dado por sabida, por rellenar

// Indica la última pregunta precargada
var pregunta_precargada=9
// Función que escoge la pregunta siguiente
function escogerPregunta(){

	// Precargamos una nueva pregunta, si había alguna sin pregargar
	if((pronunciar.pronuncia_pregunta || pronunciar.pronuncia_respuesta) && (listado_preguntas.length>(pregunta_precargada+1))){
		pregunta_precargada++
		
		if(pronunciar.pronuncia_pregunta)
			carga_palabra(lista_preguntas[pregunta_precargada].getAttribute(pronunciar.pronuncia_pregunta==1?"pregunta":"respuesta"),pronunciar.idioma_pregunta)
		
		if(pronunciar.pronuncia_respuesta)
			carga_palabra(lista_preguntas[pregunta_precargada].getAttribute(pronunciar.pronuncia_respuesta==1?"pregunta":"respuesta"),pronunciar.idioma_respuesta)
	}
	
	var minNivelEncontrado=100
	var maxTiempoEncontrado=-1
	var encontrada=null; // Pregunta encontrada
	
	var preguntaAlternativa=-1
	var preguntaAlternativaMinSiguiente=10000000
	
	// Buscamos la pregunta de nivel mas bajo, no nulo, que se acerca mas a 0 sin superarlo
	// También nos quedamos con la que tiene el siguiente más bajo por encima de cero, por si la necesitamos después
	for(var i=0;i<listado_preguntas.length;i++) if(listado_preguntas[i].nivel<max_nivel){
		if(listado_preguntas[i].siguiente<=0){
			if((listado_preguntas[i].nivel < minNivelEncontrado && listado_preguntas[i].nivel!=-1) || 
				((listado_preguntas[i].nivel==minNivelEncontrado) && (maxTiempoEncontrado<listado_preguntas[i].siguiente))){
				maxTiempoEncontrado=listado_preguntas[i].siguiente
				minNivelEncontrado=listado_preguntas[i].nivel
				encontrada=i
			}
		}
		// Si no es menor que cero, pero es menor que la anterior menor que cero, nos quedamos con ella, pues al final podemos usarla como último recurso
		else if(preguntaAlternativa<0 || (listado_preguntas[i].siguiente<listado_preguntas[preguntaAlternativa].siguiente))
			preguntaAlternativa=i
	}
		
	if(encontrada && listado_preguntas[encontrada].nivel>=repaso) num_repasos++;
	
	// si no hay, o hemos superado el número de repasos, buscamos una nueva
	if (encontrada===null || num_repasos>max_repaso)
	for(var i=0;i<listado_preguntas.length;i++)
		if(listado_preguntas[i].nivel==-1) {
			num_repasos=0
			anteriorSabida=false; // Indica que la anterior no era una pregunta sabida
			return i
		}
		
	if(encontrada!==null){
		anteriorSabida=false; // Indica que la anterior no era una pregunta sabida
		return encontrada
	}
	
	// Si antes ya dimos una pregunta sabida, damos ahora una de las que querdan, aunque no toque el turno
	if(anteriorSabida){
		anteriorSabida=false;
		return preguntaAlternativa
	}
	// Si no lo hemos hecho antes, damos una pregunta de las que ya habíamos dado por sabidas
	else {
		anteriorSabida=true
		var num_max=0;
		for(var i=0;i<listado_preguntas.length;i++)
			if(listado_preguntas[i].nivel == max_nivel) num_max++;
			
		var j=Math.random()*num_max
		for(var i=0;i<listado_preguntas.length;i++)
			if(listado_preguntas[i].nivel == max_nivel)
				if(--j<1) return i
	}
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
		
		pronunciar=new Object();
		pronunciar.pronuncia_pregunta=parseInt(preguntas.getAttribute("pronuncia_pregunta"))
		pronunciar.pronuncia_respuesta=parseInt(preguntas.getAttribute("pronuncia_respuesta"))
		pronunciar.idioma_pregunta=parseInt(preguntas.getAttribute("idioma_pregunta"))
		pronunciar.idioma_respuesta=parseInt(preguntas.getAttribute("idioma_respuesta"))
		
		tipo_leccion=preguntas.getAttribute("tipo")
		siguiente_ejercicio=preguntas.getAttribute("siguiente")
		
		tiempo_leccion=preguntas.getAttribute("tiempo")
		fecha_inicio=(new Date()).getTime()-tiempo_leccion*1000;
		setText("tiempo_leccion",Math.floor(tiempo_leccion/60)+":"+(tiempo_leccion%60<10?"0":"")+(tiempo_leccion%60).toFixed(0));
		fallos=preguntas.getAttribute("fallos")
		
		lista_preguntas=preguntas.getElementsByTagName("pregunta");
		
		var cero=0;
		
		if(pronunciar.pronuncia_pregunta || pronunciar.pronuncia_respuesta){
			var div=document.createElement('div')
			div.id='logoForvo';
			div.innerHTML='<p><a href="http://www.forvo.com/" title="Pronunciations by Forvo"><img src="http://api.forvo.com/byforvo.gif" width="120" height="40" alt="Pronunciations by Forvo" style="border:0" /></a></p>';
			document.getElementById('innerWindow').appendChild(div)
		}
		
		// Cargamos las preguntas recibida
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
		//listado_preguntas.sort(function(a,b) {return b.nivel==-1})
		
		// Precargamos las 10 primeras pronunciaciones
		if(pronunciar.pronuncia_pregunta || pronunciar.pronuncia_respuesta)
		for(var i=0;i<lista_preguntas.length && i<10;i++){
			if(pronunciar.pronuncia_pregunta)
				carga_palabra(lista_preguntas[i].getAttribute(pronunciar.pronuncia_pregunta==1?"pregunta":"respuesta"),pronunciar.idioma_pregunta)
			
			if(pronunciar.pronuncia_respuesta)
				carga_palabra(lista_preguntas[i].getAttribute(pronunciar.pronuncia_respuesta==1?"pregunta":"respuesta"),pronunciar.idioma_respuesta)
		}
		
		
		
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

//función inicializadora del script
$(document).ready(function(){
	$("#form_aprender").attr("autocomplete","off");
	Ajax(base_url()+"ejercicio/info/"+url,'','recibe_pregunta')
	//window.onunload = salvaEstado;
	//window.onbeforeunload  = salvaEstado;
});