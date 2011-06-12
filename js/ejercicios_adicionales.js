/*******************************
Preguntas de texto (escribir la respuesta)
********************************/

function mostraPreguntaDeTexto(pregunta_actual){
	document.getElementById("response").focus();
	document.getElementById("mensaje_respuesta").style.display="none";
	document.getElementById("campo_respuesta").style.display=""
}

/*******************************
Preguntas de opciones (Escoger la correcta entre cuatro de las otras elegidas al azar)
********************************/

function inicializamosLasPreguntaDeOpciones(){
	$("#options").empty();
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
	
	// Iniciamos la tabla de comparaciones
	for(var j=0;j<listado_preguntas.length;j++){
		inicializaTablaComparaciones(j)
	}
}

function inicializaTablaComparaciones(elemento_actual){
	listado_preguntas[elemento_actual].comparaciones=new Object()
	var comps=listado_preguntas[elemento_actual].comparaciones;
	for(var i=0;i<listado_preguntas.length;i++){
		comps[listado_preguntas[i].respuesta]=new Object();
		comps[listado_preguntas[i].respuesta].apariciones=0;
		comps[listado_preguntas[i].respuesta].error=0;
	}
}

function mostraPreguntaDeOpciones(pregunta_actual){
	if(!listado_preguntas[pregunta_actual]) return
	var comps=listado_preguntas[pregunta_actual].comparaciones;
	
	var opciones=new Array();
	opciones[0]=listado_preguntas[pregunta_actual].respuesta;
	
	for(var i=0;i<listado_preguntas.length;i++) if(i!=pregunta_actual){
		if(opciones.length<4) opciones.push(listado_preguntas[i].respuesta);
		else{
			var difApariciones=comps[listado_preguntas[i].respuesta].apariciones-comps[listado_preguntas[i].respuesta].error*3
				
			for(var j=opciones.length-1;j>0;j--)
				if(difApariciones<comps[opciones[j]].apariciones-comps[opciones[j]].error*3){
					opciones[j]=listado_preguntas[i].respuesta;
					break;
				}
		}
	}
	
	opciones.sort(function() {return 0.5 - Math.random()})
	
	for(var i=0;i<opciones.length;i++){
		$("#respuesta"+i).unbind();
		if(opciones[i]==listado_preguntas[pregunta_actual].respuesta) 
			$("#respuesta"+i).click(respuesta_correcta)
		else{
			$("#respuesta"+i).click(respuesta_erronea)
			if(opciones[i] && listado_preguntas[pregunta_actual].comparaciones[opciones[i]])
				listado_preguntas[pregunta_actual].comparaciones[opciones[i]].apariciones++
		}
			
		setItem("respuesta"+i,opciones[i],1)
	}
}

/*******************************
Preguntas de ordenar
********************************/

function inicializamosLasPreguntaDeOrdenar(){
	document.getElementById("form_aprender").style.display="none";
	$("#options").empty();
	
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

// Evento que responde a la acciónd e poner o quitar palabras de la ordenación
function ponerPalabra(evt){
	cierraDialogoDesc();
	var event = evt || window.event;
	var element = event.target || event.srcElement;

	// Cambiamos de posiciÃ³n la palabra
	if(element.parentNode.id=='destino_palabras')
		document.getElementById('origen_palabras').appendChild(element);
	else document.getElementById('destino_palabras').appendChild(element);
	
	// Si hemos escogido todas la palabras comprobamos si es correcto o no
	if(!document.getElementById('origen_palabras').childNodes.length){
		var palabras=listado_preguntas[pregunta_actual].respuesta.split(' ')
		for(var i=0;i<document.getElementById('destino_palabras').childNodes.length;i++)
			if(document.getElementById('destino_palabras').childNodes[i].firstChild.nodeValue!=palabras[i]){
				respuesta_erronea()
				return
			}
		respuesta_correcta()
	}
	
	return false;
}

function mostramosPreguntaDeOrdenar(pregunta_actual){
	vaciar(document.getElementById('origen_palabras'))
	vaciar(document.getElementById('destino_palabras'))
	
	var palabras=randOrder(listado_preguntas[pregunta_actual].respuesta.split(' '));
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

/*******************************
Carga vídeo
********************************/
function cargaVideo(video){
	if(video && video!='0'){
		document.getElementById('player_container').innerHTML=
			'<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="368" height="232">'+
			'	<param name="movie" value="'+base_url()+'files/videos/player.swf" />'+
			'	<param name="allowfullscreen" value="true" />'+
			'	<param name="allowscriptaccess" value="always" />'+
			'	<param name="flashvars" value="file='+video+'" />'+
			'	<embed'+
			'		type="application/x-shockwave-flash"'+
			'		id="player2"'+
			'		name="player2"'+
			'		src="'+base_url()+'files/videos/player.swf" '+
			'		width="368" '+
			'		height="232"'+
			'		allowscriptaccess="always" '+
			'		allowfullscreen="true"'+
			'		flashvars="file='+video+'" '+
			'	/>'+
			'</object>';
	}
}

/*******************************
Carga contenido externo
********************************/
function cargaContenidoExterno(contenido_externo){
	if(contenido_externo){
		document.getElementById('player_container').innerHTML=
			'<p>Contenido enlazado de la página <a href="http://www.nhk.or.jp/lesson/spanish/">NHK World</a>:</p><iframe width="500" height="400" src="'+contenido_externo+'" style="width:500px;height:400px" ></iframe>';
	}
}

/*******************************
Opciones de teclados virtuales
********************************/

// Genera el teclado virtual
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

// Pulsa una tecla en el teclado virtual
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