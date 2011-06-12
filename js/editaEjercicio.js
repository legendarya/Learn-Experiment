
var modificado=true;
	var myrules = {
		'#cambia_a_textarea' : function(element){
			element.onclick = function(evt){
				return cambia_a_textarea();
			}
		},
		'td input' : function(element){
			element.onkeydown = function(evt){
				pulsaCampoPreguntas(evt)
			},
			element.onpaste = function(evt){
				modificado=true;
				var event = evt || window.event;
				var element = event.target || event.srcElement;
				
				var fila=parseInt(element.id.charAt(2));
				
				var ve='';
				for(v in event)
					ve+=v+'\n'
					
				var clipboardData=window.clipboardData|| event.clipboardData
				var lineas=clipboardData.getData('text').split('\n');
				
				
				var j=fila;
				for(var i=0;i<lineas.length;i++)
					if(lineas[i]!=''){
						if(!document.getElementById('f'+j))
							creaFila(j);
						var celdas=lineas[i].split('\t')
						document.getElementById('c0'+j).value=celdas[0]
						if(celdas[1]) document.getElementById('c1'+j).value=celdas[1]
						j++
					}
				if(!document.getElementById('f'+j))
					creaFila(j);
					
				return false;
			}
		}
	};
	
	function cambia_a_textarea(){
		var ta=document.createElement('textarea');
		ta.id="cuadro_preguntas"
		ta.name="cuadro_preguntas"
		ta.onkeydown=interceptaTab;
		
		var preguntas='';
		var i=0;
		while(document.getElementById('c0'+i)){
			preguntas+=(i?'\n':'')+document.getElementById('c0'+i).value+'\t'+document.getElementById('c1'+i).value
			i++
		}
		ta.value=preguntas
		
		while(document.getElementById('editor_preguntas').firstChild)
			document.getElementById('editor_preguntas').removeChild(document.getElementById('editor_preguntas').firstChild)
		document.getElementById('editor_preguntas').appendChild(ta);
		
		document.getElementById('cambia_a_textarea').innerHTML='Cambia a tabla';
		document.getElementById('cambia_a_textarea').onclick=cambia_a_tabla;
		return false;
	}
	
	function cambia_a_tabla(){
		var HTMLtabla='<table id="tabla_preguntas"><tr><th>Pregunta</th><th>Respuesta</th></tr>'
		var preguntas=document.getElementById('cuadro_preguntas').value.split('\n')
		for(var i=0;i<preguntas.length;i++){
			var pregunta=preguntas[i].split('\t')
			HTMLtabla+='<tr><td><input type="text" id="c0'+i+'" name="c0'+i+'" value="'+pregunta[0]+'" /></td>'+
				'<td><input type="text" id="c1'+i+'" name="c1'+i+'" value="'+pregunta[1]+'" /></td></tr>'
		}
		HTMLtabla+='</table>'
		
		document.getElementById('editor_preguntas').innerHTML=HTMLtabla;
		
		document.getElementById('cambia_a_textarea').innerHTML='Editar preguntas en área de texto';
		document.getElementById('cambia_a_textarea').onclick=cambia_a_textarea;
		return false;
	}
	
	function creaFila(fila){
		var tabla_preguntas=document.getElementById('tabla_preguntas');
		var tr=document.createElement('tr');
		tr.id='f'+fila;
		var td=document.createElement('td');
		var input=document.createElement('input');
		input.id='c0'+fila;
		input.name='c0'+fila;
		input.onkeyup=pulsaCampoPreguntas
		td.appendChild(input);
		tr.appendChild(td);
		td=document.createElement('td');
		input=document.createElement('input');
		input.id='c1'+fila;
		input.name='c1'+fila;
		input.onkeyup=pulsaCampoPreguntas
		
		td.appendChild(input);
		tr.appendChild(td);
		tabla_preguntas.appendChild(tr);
	}
	
	function pulsaCampoPreguntas(evt){
		modificado=true;
		var event = evt || window.event;
		var element = event.target || event.srcElement;

		var KeyID = event.keyCode;
		
		var columna=parseInt(element.id.charAt(1));
		var fila=parseInt(element.id.substr(2));
		
		if(KeyID==37 && element.selectionStart==0 && columna==1){ // Flecha izquierda
			var celda=document.getElementById('c0'+fila)
			celda.focus();
			celda.selectionStart=0;
			return
		}
		
		if(KeyID==38){ // Flecha arriba
			var celda=document.getElementById('c'+columna+(fila-1))
			celda.focus();
			return
		}
		
		if(KeyID==39 && element.selectionStart==element.value.length && columna==0){ // Flecha derecha
			var celda=document.getElementById('c1'+fila)
			celda.focus();
			celda.selectionStart=0;
			return
		}
		
		if(KeyID==40){ // Flecha abajo
			var celda=document.getElementById('c'+columna+(fila+1))
			if(celda) celda.focus();
			return
		}
		
		if(element.value.length>0){
			if(!document.getElementById('c0'+(1+fila))){ // Si estamos al final, añadimos preguntas
				creaFila(fila+1)
			}
		}
	}
	
	Behaviour.register(myrules);
	
function obtenEjercicio(){
	if(xmlhttp.readyState==4){
		
		var preguntas=xmlhttp.responseXML.documentElement;
		var j=0;
		document.getElementById('tipo').value=preguntas.getAttribute('tipo')
		document.getElementById('teclado').value=preguntas.getAttribute('teclado')
		
		for(var i=0;i<preguntas.childNodes.length;i++) if(preguntas.childNodes[i].nodeType!=3){
			if(!document.getElementById('f'+j))
				creaFila(j);
				
			document.getElementById('c0'+j).value=preguntas.childNodes[i].getAttribute('pregunta')
			document.getElementById('c1'+j).value=preguntas.childNodes[i].getAttribute('respuesta')
			j++
		}
		var ultima=j
		
		while(document.getElementById('f'+j)){
			document.getElementById('f'+j).parentNode.removeChild(document.getElementById('f'+j))
			j++
		}
		creaFila(ultima);
	}
}
	
function ajax(query,params,func){
	if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest()
	else if (window.ActiveXObject) xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
	if (xmlhttp!=null){
		xmlhttp.open("GET", query, true);
		xmlhttp.onreadystatechange =func;
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

function interceptaTab(event) {
    var key = event.keyCode ? event.keyCode : event.which ? 
        event.which : event.charCode;
    if (key == 9) {
      mostrarTab(); 
      return false;
    } else return key;
}

function mostrarTab() {
	obj=document.getElementById('cuadro_preguntas');
    rolling=obj.scrollTop;
    if(typeof obj.selectionStart == 'number') {
        // Resto de navegadores
        var start = obj.selectionStart;
        var end = obj.selectionEnd;
        obj.value = obj.value.substring(0, start)+"\t"+obj.value.substring(start, obj.value.length);
        obj.focus();
        obj.selectionStart =  obj.selectionEnd= end + 1;
    } 
    else if(document.selection) {
        // Internet Explorer
        obj.focus();
        var range = document.selection.createRange();
        if(range.parentElement() != obj) return false;
        if (range.text != "") {
            if(typeof range.text == 'string'){
                document.selection.createRange().text ="\t"+range.text;
            }
            else obj.value += "\t";
        }
        else
            obj.value += "\t";
    }
    obj.scrollTop=rolling;
}