//función inicializadora del script
$(document).ready(function(){
	$("#form_aprender").attr("autocomplete","off");
	Ajax(base_url()+"ejercicio/info/"+url,'','recibe_pregunta')
	window.onunload = salvaEstado;
	window.onbeforeunload  = salvaEstado;
});