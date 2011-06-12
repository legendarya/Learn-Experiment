var modificado=true;

	var myrules = {
		'.formato_excel input' : function(element){
			element.onkeydown = function(evt){
				pulsaCampoPreguntas(evt)
			},
			element.onpaste = function(evt){
				return pegar(evt);
			}
		}
	};
	
	function pegar(evt){	
				modificado=true;
				var event = evt || window.event;
				var element = event.target || event.srcElement;
				
				var fila=parseInt(element.id.substr(2));
				var columna=parseInt(element.id.charAt(1));
				
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
						
						for(var k=0;k<celdas.length;k++){
							if(!document.getElementById('c'+(k+columna)+j)) creaColumna(k+columna)
							document.getElementById('c'+(k+columna)+j).value=celdas[k]
						}
						
						j++
					}
				if(!document.getElementById('f'+j))
					creaFila(j);
					
				return false;
	}
	
	function creaFila(fila){
		var tabla_preguntas=document.getElementById('tabla_equivalencia');
		var tr=document.createElement('tr');
		tr.id='f'+fila;
		
		i=0;
		while(document.getElementById('c'+i+'0')){
			var td=document.createElement('td');
			var input=document.createElement('input');
			input.id='c'+i+fila;
			input.name='c'+i+fila;
			input.onkeydown=pulsaCampoPreguntas
			input.onpaste=pegar
			td.appendChild(input);
			tr.appendChild(td);
			i++
		}
		
		tabla_preguntas.appendChild(tr);
	}
	
	function creaColumna(columna){
		var fila_act=0
		var f_act=null;
		while(f_act=document.getElementById('f'+fila_act)){
			if(fila_act) var nueva_celda=document.createElement('td');
			else var nueva_celda=document.createElement('th');
			var input=document.createElement('input');
			input.id='c'+columna+fila_act;
			input.name='c'+columna+fila_act;
			input.type='text'
			input.onkeydown=pulsaCampoPreguntas
			input.onpaste=pegar
			nueva_celda.appendChild(input)
			f_act.appendChild(nueva_celda)
			fila_act++;
		}
	}
	
	function pulsaCampoPreguntas(evt){
		modificado=true;
		var event = evt || window.event;
		var element = event.target || event.srcElement;

		var KeyID = event.keyCode;
		
		var columna=parseInt(element.id.charAt(1));
		var fila=parseInt(element.id.substr(2));
		
		if(KeyID==37 && element.selectionStart==0 && columna>0){ // Flecha izquierda
			columna--
			var celda=document.getElementById('c'+columna+fila)
			celda.focus();
			celda.selectionStart=0;
			return
		}
		
		if(KeyID==38){ // Flecha arriba
			var celda=document.getElementById('c'+columna+(fila-1))
			celda.focus();
			return
		}
		
		if(KeyID==39 && element.selectionStart==element.value.length){ // Flecha derecha
			columna++;
			var celda=document.getElementById('c'+columna+fila)
			if(!celda){
				creaColumna(columna)
				celda=document.getElementById('c'+columna+fila)
			}
			
			celda.focus();
			celda.selectionStart=0;
			return
		}
		
		if(KeyID==40){ // Flecha abajo
			var celda=document.getElementById('c'+columna+(fila+1))
			if(!celda) creaFila(fila+1)
			celda=document.getElementById('c'+columna+(fila+1))
			celda.focus();
			return
		}
		
		if(element.value.length>0){
			if(!document.getElementById('f'+(1+fila))){ // Si estamos al final, añadimos preguntas
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