// Funciones llamadas desde el interfaz

// Obtenemos la opción actual
function getOpcionInterfaz(opcion){
	if(document.getElementById(opcion))
		return document.getElementById(opcion).checked;
}

function cambiaOpcion(evt){
	var event = evt || window.event;
	var element = event.target || event.srcElement;
	Ajax(base_url()+"componente/ajax_guardaOpcion","opcion="+element.id+"&valor="+(element.checked?1:0)+"&id="+id_componente);
}

// Reiniciamos el ejercicio (pidiendo confirmación antes)
function reiniciar_ejercicio(){
	if(confirm("¿Seguro que quieres reiniciar el ejercicio? Perderás todo lo avanzado en él."))
		reiniciar_leccion();
	return false;
}

// Reiniciamos el ejercicio
function reiniciar_leccion(){
	for(var i=0;i<listado_preguntas.length;i++){
			listado_preguntas[i].siguiente=0;
			listado_preguntas[i].nivel=-1;
	}
	fallos=0;
	fecha_inicio=(new Date()).getTime();
	estadisticas();
	preguntar();
}

// Mostramos la siguiente lección
function siguiente_leccion(){
	window.location="?leccion="+siguiente_ejercicio;
}

var no_identificado=true // Indica si el usuario no está identificado
// Guarda elejercicio, ofreciendo registrarse
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

// Botón de poner en pausa el ejercicio
function pausar_ejercicio(){
	tiempo_leccion = ((new Date()).getTime()-fecha_inicio)/1000;
	clearTimeout(timer);
	timer=null;
	return false
}

// Al escribir el usuario, si la respuesta es correcta, la damos por respondida
function pulsaTecla(e){
	if(getOpcionInterfaz('correccion_rapida') && comprueba_pregunta()) preguntaRespondida();
	return false;
}

var tabActual=0;

// Selecciona pestaña
function selTab(evt){
	var event = evt || window.event;
	var element = event.target || event.srcElement;
	document.getElementById('tab'+tabActual).parentNode.className='';
	document.getElementById('contentTab'+tabActual).style.display='none';
	element.parentNode.className='actual';
	tabActual=element.id.substr(3);
	document.getElementById('contentTab'+tabActual).style.display='block';
	
	return false;
}

/* SONIDO (pronunciación) */
var is_firefox=/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)
if(!is_firefox) HTML5.Audio.Proxy.getProxy({swfPath:base_url()+'swf/html5-audio.swf'});

var palabra_pendiente=null
var lista_sonidos=new Array();
function pronuncia_palabra(palabra,idioma){
	for(var i=0;i<lista_sonidos.length;i++)
		if(lista_sonidos[i][0]==palabra){
			if(lista_sonidos[i][1]) lista_sonidos[i][1].play();
			//alert(1)
			return;
		}
		
	palabra_pendiente=palabra
	carga_palabra(palabra,idioma)
}

function carga_palabra(palabra,idioma){
	Ajax(base_url()+'ejercicio/pronunciacion_general/'+idioma+'/'+palabra,'','recibe_pronunciacion')
}

function recibe_pronunciacion(xmlhttp){
	lista_sonidos.push([xmlhttp.responseXML.firstChild.getAttribute('palabra'),
		(is_firefox?
			(xmlhttp.responseXML.firstChild.getAttribute('ogg')?
				new Audio(xmlhttp.responseXML.firstChild.getAttribute('ogg')):
				0):
			(xmlhttp.responseXML.firstChild.getAttribute('mp3')?
			new HTML5.Audio(xmlhttp.responseXML.firstChild.getAttribute('mp3')):0))]);
			
	// Solo pronunciamos la palabra si estaba pendiente  de pronunciar
	if(xmlhttp.responseXML.firstChild.getAttribute('palabra')==palabra_pendiente)
		pronuncia_palabra(xmlhttp.responseXML.firstChild.getAttribute('palabra'))
	palabra_pendiente=null
}