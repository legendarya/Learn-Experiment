<?php
class UrlController extends PageTemplate{

	function _contenido($ejercicio=null,$apartado=null){
		if($ejercicio) $ejercicio=new Ejercicio($ejercicio);
		if($apartado) $apartado=new Apartado($apartado);
			
		return new vContainer(array(
			$this->form($ejercicio,$apartado)
		));
	}
	
	function form($ejercicio=null,$apartado=null){
		
		$this->page->addScript('js/sonido/swfobject.js');
		$this->page->addScript('js/sonido/jkl-js2as.js');
		$this->page->addScript('js/sonido/html5-audio.js');
		
		$this->page->addScript('js/jquery.js');
		$this->page->addScript('js/behaviour.js');
		$this->page->addScript('js/editaEjercicioAbierto.js');
		$this->page->addScript('js/ejercicio_base.js');
		
		
		$this->page->addScript('js/utilidades.js');
		$this->page->addScript('js/ejercicios_adicionales.js');
		$this->page->addScript('js/interfaz.js');
	
		$edit_form=new vForm(array(
			new vDiv(
			'Para crear un ejercicio rellena la tabla de preguntas y respuestas de abajo (puedes usar el tabulador para pasar de un campo a otro)
			y escoje las opciones del ejercicio en el cuadro de abajo. Podrás ver el resultado final en vista previa.'
			,'hidden_info'),
			new vHeader('Crear ejercicio'),
			($this->error?new vDiv($this->error,'error'):''),
						new vInputText('titulo','Título:',$ejercicio?$ejercicio->titulo:''),
						$inputDescripcion=new vInputText('descripcion','Descripción:',$ejercicio?$ejercicio->descripcion:''),
						
					
					new vSection(array(
						new vDiv(
						'Escribe aquí las preguntas y respuestas del ejercicio. También puedes pegar contenido a partir de una tabla o una hoja de 
						Excel (aún no funciona en Firefox)'
						,'hidden_info'),
						new vHeader('Lista de preguntas'),
						new vDiv($preguntas=new vTable(),'lista_preguntas')
					),'lista_preguntas_section'),
						
					
					new vSection(new vDiv(array(
					
					new vDiv(
						'Aquí puedes probar como funcionará el ejercicio una vez que lo guardes. Eso si, aquí no funcionan las pronunciaciones, y siempre
						muestra las preguntas ordenadas, aunque comenzando con la última que hayas editado.'
					,'hidden_info'),
						new vHeader('Vista previa'),
					$this->vistaPrevia($ejercicio),
					
			new vDiv(array(
				'Hay tres tipos de ejercicios:',
				new vList(array(
					'Normal: La respuesta se debe escribir',
					'Opciones: La respuesta se debe escoger de entre cuatro opciones. 
						Una es la respuesta a la pregunta y otras son respuestas a otras preguntas del ejercicio.',
					'Ordenar: Aquí las respuestas deben estar divididas por espacios en blanco, y cada fragmento aparece desordenado, siendo el usuario
					el que debe escoger el orden correcto pulsando sobre cada uno de ellos')),
				'Además puedes escoger si quieres que las preguntas se muestren siempre en el orden que has escogido, aunque suele ser mejor que
				el orden varíe cada vez que el usuario repite el ejercicio.'
			),'hidden_info'),
					
						new vHeader('Opciones del ejercicio'),
						$inputTipo=new vInputOptions('tipo','Tipo:',$ejercicio?$ejercicio->tipo:''),
						new vInputCheckBox('ordenar','Mostrar preguntas en orden',$ejercicio?$ejercicio->ordenar:''),
						
						
			new vDiv(array(
				'El texto de las preguntas puede ser pronuciando al mostrarlas o al responder usando un servicio de la página ',
				new vLink('http://www.forvo.com','Forvo'),
				'. Simplemente debes indicar que quieres que se pronuncie en cada caso (si la pregunta o la respuesta) y el idioma en que 
				se debe de pronunciar'
			),'hidden_info'),
						
						new vHeader('Pronunciar'),
						new vDiv($pronuncia_pregunta=new vInputOptions('pronuncia_pregunta','Al preguntar:',$ejercicio?$ejercicio->pronuncia_pregunta:''),'pronuncia_field'),
						$idioma_pregunta=new vInputOptions('idioma_pregunta','Idioma:',($ejercicio and $ejercicio->idioma_pregunta)?$ejercicio->idioma_pregunta->id:''),
						new vDiv($pronuncia_respuesta=new vInputOptions('pronuncia_respuesta','Al responder:',$ejercicio?$ejercicio->pronuncia_respuesta:''),'pronuncia_field'),
						$idioma_respuesta=new vInputOptions('idioma_respuesta','Idioma:',($ejercicio and $ejercicio->idioma_respuesta)?$ejercicio->idioma_respuesta->id:''),
						new vPreformatedText('<input type="submit" value="Guardar"/>')
					)),'datos_ejercicio')
				));
				
		$pronuncia_pregunta->add(0,'nada');
		$pronuncia_pregunta->add(1,'pronuncia pregunta');
		$pronuncia_pregunta->add(2,'pronuncia respuesta');
		
		$pronuncia_respuesta->add(0,'nada');
		$pronuncia_respuesta->add(1,'pronuncia pregunta');
		$pronuncia_respuesta->add(2,'pronuncia respuesta');
		
		foreach(new Idiomas() as $idioma){
			$idioma_pregunta->add($idioma->id,$idioma->nombre);
			$idioma_respuesta->add($idioma->id,$idioma->nombre);
		}
				
		$edit_form->setSubmitText('Guardar');
				
		$inputTipo->add(0,'Normal');
		$inputTipo->add(1,'Opciones');
		//$inputTipo->add(2,'Opciones con respuestas');
		$inputTipo->add(3,'Ordenar frases');
		
		$inputDescripcion->setMaxLength(null);
		
		$preguntas->setId('tabla_preguntas');
		$preguntas->addHeader('Pregunta');
		$preguntas->addHeader('Respuesta');
		
		//,$ejercicio?$ejercicio->titulo:''
		if($ejercicio) $ejercicio->preguntas->rewind();
		$i=0;
		while(getParam('c0'.$i) or ($ejercicio and $i<$ejercicio->preguntas->count())){
			$preguntas->add(new vRow(array(
				new vInputText('c0'.$i,'',$ejercicio?$ejercicio->preguntas->current()->pregunta:''),
				new vInputText('c1'.$i,'',$ejercicio?$ejercicio->preguntas->next()->respuesta:''),
			)));
			$i++;
		}
			
		$preguntas->add(new vRow(array(
			new vInputText('c0'.$i,''),
			new vInputText('c1'.$i,''),
		)));
			
		return new vDiv($edit_form,'editaEjercicioTab editaEjercicioPage');
	}
	
	function vistaPrevia($ejercicio){
		return new vDiv(
		new vPreformatedText('
		<div class="recuadro_vista_previa">
		<div class="opciones_ejercicio">
			<div class="opciones">
				<a href="" onclick="return pausar_ejercicio()" class="pausa" title=""></a>
				<a href="" onclick="return reiniciar_ejercicio()" class="reiniciar"></a>
			</div>
			<div class="barra_progreso" id="barra_progreso"><span id="porcentaje">0</span>%</div>
			<div class="tiempo"><span id="tiempo_leccion">0:00</span></div>
		</div>
		<div class="ventana_ejercicio">
			<div id="titleQuestion">'.($ejercicio?$ejercicio->titulo:'Título').'</div><div id="name">Pregunta</div>
			<div id="mensaje_respuesta">
				<div id="mensaje_respuesta_fallada">Has fallado. No es <span id="respuesta_fallada"></span></div>
				<div id="mensaje_respuesta_correcta">
					La respuesta correcta es: <div id="respuesta_correcta"></div>
					<div id="form_seguir"><input id="seguir" type="button" value="Seguir" onclick="return siguiente();" /></div>
				</div>
			</div>
			<div id="question">
				<div id="form_aprender">
					<div id="campo_respuesta">Respuesta: <input id="response" name="response" type="text" onkeyup="cierraDialogoDesc(); return pulsaTecla(event);" /></div>
					<input id="ok_button" type="button" value="Aceptar" />
				</div>
				<div id="options"></div>
			</div>
			<div id="audio_interface"></div>
			<div id="enhorabuena_final" class="hidden">
				<div>¡Enhorebuena, has completado la lección!</div>
				<input id="reiniciar" type="button" value="Reiniciar lección" onclick="return reiniciar_leccion();" />
			</div>
			<div id="player_container" style="text-align:center;margin:auto"></div>
		</div>
		</div>')
		,'demo_ejercicio');
	}
	
	function _processForm($ejercicio_editado=null,$apartado=null){
		if($ejercicio_editado){
			$ejercicio_editado=new Ejercicio($ejercicio_editado);
			if(!$this->responsableEjercicio($ejercicio_editado)) redirect();
		}
		if($apartado){
			$apartado=new Apartado($apartado);
			if(!$this->esResponsable($apartado)) redirect();
		}
		
		if(!getParam('titulo')) $this->error='Debes escribir un título para el ejercicio';
		else{
			$ejercicio=array(
				'preguntas'=>array(),
				'titulo'=>$_POST['titulo'],
				'fecha_actualizacion'=>date('Y/m/d H:i:s'),
				'descripcion'=>$_POST['descripcion'],
				'tipo'=>$_POST['tipo'],
				'ordenar'=>getParam('ordenar')=='on',
				'pronuncia_pregunta'=>$_POST['pronuncia_pregunta'],
				'pronuncia_respuesta'=>$_POST['pronuncia_respuesta'],
				'idioma_pregunta'=>$_POST['idioma_pregunta'],
				'idioma_respuesta'=>$_POST['idioma_respuesta']
			);
			
			$i=0;
			while(isset($_POST['c0'.$i])){ 
				if($_POST['c0'.$i]){
					$ejercicio['preguntas'][]=array($_POST['c0'.$i],$_POST['c1'.$i]);
				}
				$i++;
			}
			
			if(!$this->usuario){
				if(!isset($_SESSION['ejercicios'])) $_SESSION['ejercicios']=array();
				$_SESSION['ejercicios'][]=$ejercicio;
				redirect(base_url().'aprender/0/0/'.count($_SESSION['ejercicios']));
			}
			
			$ejercicio=$this->guardaEjercicioTemporal($ejercicio,$apartado,$ejercicio_editado);
			redirect(base_url().'aprender/'.str2url($ejercicio->url).'/'.$ejercicio->id);
		}
	}
	
	function migas($apartado){
		$migas=new vDiv(array(
			'Estás en: ',new vLink(base_url(),'Learn Experiment'),' > Crear Ejercicio'
		),'migas');
		
		$apartado_superior=$apartado;
		while($apartado_superior){
			$migas->add($this->apartadoLink($apartado_superior),2);
			$migas->add(' > ',2);
			$apartado_superior=$apartado_superior->padre;
		}
		
		return $migas;
	}
	
	function apartadoLink($apartado){
		return new vLink(base_url().'cursos/'.str2url($apartado->nombre).'/'.$apartado->id,$apartado->nombre);
	}
	
	function _title(){
		return 'Crear ejercicio';
	}
}
?>