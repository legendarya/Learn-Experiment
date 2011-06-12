<?php
class UrlController extends PageTemplate{
	
	function migas($ejercicio){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > ',$ejercicio->titulo
		),'migas');
		
		$apartado_superior=$ejercicio->apartado;
		while($apartado_superior){
			$migas->add($this->apartadoLink($apartado_superior),3);
			$migas->add(' > ',4);
			$apartado_superior=$apartado_superior->padre;
		}
		
		return $migas;
	}
	
	function apartadoLink($apartado){
		return new vLink(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre);
	}
	
	function _contenido($url_ejercicio='',$idEjercicio=0,$indiceEjercicio=0){
		if(!$idEjercicio){
			if(!$indiceEjercicio) redirect(base_url().'.');
			$ejercicio=$_SESSION['ejercicios'][$indiceEjercicio-1];
		}
		else $ejercicio=new Ejercicio($idEjercicio);
		
		$this->page->addScript('js/sonido/swfobject.js');
		$this->page->addScript('js/sonido/jkl-js2as.js');
		$this->page->addScript('js/sonido/html5-audio.js');
		
		$this->page->addScript('js/behaviour.js');
		$this->page->addScript('js/jquery.js');
		$this->page->addScript('js/utilidades.js');
		$this->page->addScript('js/ejercicio.js');
		$this->page->addScript('js/ejercicios_adicionales.js');
		$this->page->addScript('js/interfaz.js');
	
		
		//$lecciones=$this->devolverListadoLecciones($ejercicio);
		
		return new vContainer(array(
			(($ejercicio instanceof Ejercicio)?new vContainer(array(
				$this->migas($ejercicio),
				new vDiv(array(
					($ejercicio->previous()?new vLink(base_url().'aprender/'.str2url($ejercicio->previous()->titulo).'/'.$ejercicio->previous()->id,'< '.$ejercicio->previous()->titulo):''),' ',
					($ejercicio->next()?    new vLink(base_url().'aprender/'.str2url($ejercicio->next()->titulo).'/'.$ejercicio->next()->id,$ejercicio->next()->titulo.' >'):'')
				))
			)):''),
			new vPreformatedText('
			<div class="opciones_ejercicio">
				<div class="opciones">
					<a href="" onclick="return pausar_ejercicio()" class="pausa" title=""></a>
					<a href="" onclick="return reiniciar_ejercicio()" class="reiniciar"></a>
				</div>
				<div class="barra_progreso" id="barra_progreso"><span id="porcentaje">0</span>%</div>
				<div class="fallos">Fallos: <span id="porcentaje_fallos">0</span></div>
				<div class="tiempo"><span id="tiempo_leccion">0:00</span></div>
				<div id="debug"></div>
			</div>
			<div class="ejercicio2">
				<div id="expandWindow" >
					<div id="window">
						<div id="innerWindow">
							<h3 id="titleQuestion">'.($ejercicio instanceof Ejercicio?$ejercicio->titulo:$ejercicio['titulo']).'</h3>
							<h2 id="name"></h2>
							<div id="mensaje_respuesta">
								<div id="mensaje_respuesta_fallada">Has fallado. No es
									<span id="respuesta_fallada"></span>
								</div>
								<div id="mensaje_respuesta_correcta">
									La respuesta correcta es:
									<div id="respuesta_correcta"></div>
									<form id="form_seguir">
										<input id="seguir" type="button" value="Seguir" onclick="return siguiente();" />
									</form>
								</div>
							</div>
							<div id="question">
								<form onsubmit="return preguntaRespondida();" id="form_aprender">
									<div id="campo_respuesta">
										Respuesta: <input id="response" name="response" type="text" onkeyup="
											cierraDialogoDesc(); return pulsaTecla(event);" />
									</div>
									<input id="ok_button" type="submit" value="Aceptar" onclick="return preguntaRespondida();" />
								</form>
								<div id="options"></div>
							</div>
							<div id="audio_interface">
							</div>
							<div id="enhorabuena_final">
								<h3>¡Enhorebuena, has completado la lección!</h3>
								<input id="reiniciar" type="button" value="Reiniciar lección" onclick="return reiniciar_leccion();" />
							</div>
						</div>
					</div>
				</div>
				<div id="player_container" style="text-align:center;margin:auto"></div>
				
			</div>'),
			($this->responsableEjercicio($ejercicio)?new vLink(base_url().'ejercicio/crear_abierto/'.$ejercicio->id,'Editar ejercicio','','editarEjercicio buttonLink'):''),
			$this->tabs($ejercicio)
			));
	}
	
	function tabs($ejercicio){
		if($ejercicio instanceof Ejercicio){
			$clasificacion='<table id="clasificacion_usuarios"><thead><tr><th class="usuario">Usuario</th><th class="fallos">Fallos</th><th class="tiempo">Tiempo</th></tr></thead><tbody>';
			foreach ($ejercicio->usuarios->where(new EqualCondition('mejor_porcentaje',100))->orderBy('mejor_fallos')->orderBy('mejor_tiempo')->limit(30) as $estado){
				$clasificacion.='<tr '.(isset($_SESSION['id']) and ($_SESSION['id']==$estado->usuario->id)?'class="actual"':'').'><td class="usuario">'.$estado->usuario->nombre_usuario.'</td><td class="fallos">'.$estado->mejor_fallos.
					'</td><td class="tiempo">'.floor($estado->mejor_tiempo/60).':'.($estado->mejor_tiempo%60).'</td></tr>';
			}
			$clasificacion.='</tbody></table>';
		}
		
		$preferencias=null;
		if($this->usuario) $preferencias=$this->usuario->preferencias->getFirst();
		
		if(!$preferencias){
			$preferencias=new stdClass();
			
			$opciones=array('enter','ignorar_signos');
			if(!isset($_SESSION['preferencias'])) $_SESSION['preferencias']=array();
			
			foreach($opciones as $opcion)
				if(isset($_SESSION['preferencias'][$opcion])) $preferencias->$opcion=$_SESSION['preferencias'][$opcion];
				else $preferencias->$opcion=0;
		}
		
		
		$tabla_preguntas='<table class="lista_preguntas"><tr><th>Pregunta</th><th>Respuesta</th></tr>';
		if($ejercicio instanceof Ejercicio)
		foreach($ejercicio->preguntas->orderBy('id') as $i=>$pregunta)
			$tabla_preguntas.='<tr><td>'.$pregunta->pregunta.'</td><td>'.$pregunta->respuesta.'</td></tr>';
		else foreach($ejercicio['preguntas'] as $i=>$pregunta)
			$tabla_preguntas.='<tr><td>'.$pregunta[0].'</td><td>'.$pregunta[1].'</td></tr>';
		$tabla_preguntas.='</table>';
		
		return new vDiv(array(new vPreformatedText('<ul class="tabs">
					<li class="actual"><a href="#" id="tab0"  onclick="return selTab(event)">Info</a></li>
					'.($ejercicio instanceof Ejercicio?'<li><a id="tab1" href="#" onclick="return selTab(event)">Clasificación</a></li>':'').'
					<li><a href="#" id="tab2"  onclick="return selTab(event)">Opciones</a></li>
					'.($ejercicio instanceof Ejercicio?'<li><a href="#" id="tab3"  onclick="return selTab(event)">Historial</a></li>':'').'
				</ul>
				<div class="topTabs"></div>
				<div class="contenidoTab" id="contentTab0" style="display:block">'.
				$tabla_preguntas.
				$this->parseaDescripcion($ejercicio instanceof Ejercicio?$ejercicio->descripcion:$ejercicio['descripcion']).'</div>
				'.($ejercicio instanceof Ejercicio?'<div class="contenidoTab" id="contentTab1">'.$clasificacion.'</div>':'').'
				<div class="contenidoTab" id="contentTab2">
					<form action="#">
						<div><label><input onclick="cambiaOpcion(event)" type="checkbox" id="ignorar_acentos" '.($preferencias->ignorar_signos?'checked="checked"':'').' /> Ignorar acentos</label></div>
						<div><label><input onclick="cambiaOpcion(event)" type="checkbox" id="correccion_rapida" '.($preferencias->enter?'checked="checked"':'').' /> Corregir sin pulsar "Aceptar" si se escribe la respuesta correcta</label></div>
					</form>
				</div>'),
				($ejercicio instanceof Ejercicio?
					new vDiv(array($this->showHistorial($ejercicio)//,$this->edicionEjercicio($ejercicio)
					),'contenidoTab editaEjercicioTab','contentTab3')
				:''),
				//new vDiv($edit_form,'contenidoTab editaEjercicioTab','contentTab4'),
				new vDiv('','bottomTabs')),'tabsBody');
		
	}
	
	function edicionEjercicio($ejercicio){
		
		$edit_form='';
		if(($ejercicio->director and $this->usuario and $this->usuario->id==$ejercicio->director->id) or $this->esResponsable($ejercicio->apartado)){
			$this->page->addScript('js/editaEjercicio.js');
			
			$edit_form=new vForm(array(
						new vInputText('titulo','Título:',$ejercicio->titulo),
						$inputDescripcion=new vInputText('descripcion','Descripción:',$ejercicio->descripcion),
						$inputTipo=new vInputOptions('tipo','Tipo:',$ejercicio->tipo),
						new vInputCheckBox('ordenar','Mostrar preguntas en orden',$ejercicio->ordenar),
						$inputPronunciar=new vInputOptions('pronunciar','Pronunciar:',$ejercicio->pronuncia_pregunta),
						$link_cambio=new vLink('','Editar preguntas en área de texto'),
						new vDiv($preguntas=new vTable(),'','editor_preguntas')
					),base_url().'ejercicio/editar/'.$ejercicio->id);
					
			$edit_form->setSubmitText('Guardar');
			
			$link_cambio->setId('cambia_a_textarea');
			
			$inputPronunciar->add(0,'No pronunciar');
			$inputPronunciar->add(1,'Pronunciar pregunta al mostrar');
			$inputPronunciar->add(2,'Pronunciar pregunta al responder');
			$inputPronunciar->add(3,'Pronunciar respuesta al mostrar');
			$inputPronunciar->add(4,'Pronunciar respuesta al responder');
					
			$inputTipo->add(0,'Normal');
			$inputTipo->add(1,'Opciones');
			$inputTipo->add(2,'Opciones con respuestas');
			$inputTipo->add(3,'Ordenar frases');
			
			$inputDescripcion->setMaxLength(null);
			
			$preguntas->setId('tabla_preguntas');
			$preguntas->addHeader('Pregunta');
			$preguntas->addHeader('Respuesta');
			
			$i=-1;
			foreach($ejercicio->preguntas->orderBy('id') as $i=>$pregunta){
				$preguntas->add(new vRow(array(
					new vInputText('c0'.$i,'',$pregunta->pregunta),
					new vInputText('c1'.$i,'',$pregunta->respuesta),
				),'','f'.$i));
			}
			
			$preguntas->add(new vRow(array(
				new vInputText('c0'.($i+1),''),
				new vInputText('c1'.($i+1),''),
			)));
		}
		
		return $edit_form;
	}
	
	function showHistorial($ejercicio){
		$result=new vTable();
		$result->addHeader('Fecha');
		$result->addHeader('Autor');
		$result->addHeader('Preguntas');
		foreach($ejercicio->versiones->orderBy('fecha',false) as $version)
			$result->add(new vRow(array($version->fecha->format('d/m/Y'),$version->usuario->nombre_usuario,
				new vContainer(array(count($version->texto['preguntas']),($this->esResponsable($ejercicio->apartado)?
					new vLink(base_url().'ejercicio/recuperar_version/'.$version->id,'Recuperar'):'')))
			),$version->id==$ejercicio->version->id?'actual':''));
			
		return new vSection(array(new vHeader('Historial'),$result),'historial_edicion');
	}
	
	function parseaDescripcion($texto){
		$lineas=explode("\n",$texto);
		
		$en_tabla=false;
		foreach($lineas as $i=>$linea){
			$celdas=explode("\t",$linea);
			if(count($celdas)>1){
				if(!$en_tabla){
					$lineas[$i]="<table><tr><th>".implode("</th><th>",$celdas)."</th></tr>";
					$en_tabla=true;
				}
				else $lineas[$i]="<tr><td>".implode("</td><td>",$celdas)."</td></tr>";
			}
			else {
				if($en_tabla){
					$lineas[$i].="</table>";
					$en_tabla=false;
				}
				else $lineas[$i].="<br/>";
			}
			
		}
		
		if($en_tabla) $lineas[count($lineas)-1].="</table>";
		return implode("",$lineas);
	}
	
	function comentarios_key(){
	}
	
	function _title($url_ejercicio='',$idEjercicio=0,$indiceEjercicio=0){
		if(!$idEjercicio){
			if(!$indiceEjercicio) redirect(base_url().'.');
			if(!isset($_SESSION['ejercicios'][$indiceEjercicio-1])) redirect(base_url().'.');
			return $_SESSION['ejercicios'][$indiceEjercicio-1]['titulo'];
		}
		else{
			$ejercicio=new Ejercicio($idEjercicio);
			return $ejercicio->titulo;
		}
	}
}
?>