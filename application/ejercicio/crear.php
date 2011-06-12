<?php
class UrlController extends PageTemplate{

	function _contenido($apartado){
		$apartado=new Apartado($apartado);
			
		return new vContainer(array(
			$this->migas($apartado),
			new vHeader('Crear ejercicio'),
			$this->form($apartado)
		));
	}
	
	function form($apartado){
		
		$this->page->addScript('js/behaviour.js');
		$this->page->addScript('js/editaEjercicio.js');
	
		$edit_form=new vForm(array(
					($this->error?new vDiv($this->error,'error'):''),
					new vInputText('titulo','Título:'),
					$inputDescripcion=new vInputText('descripcion','Descripción:'),
					$inputTipo=new vInputOptions('tipo','Tipo:'),
					$preguntas=new vTable()
				));
				
		$edit_form->setSubmitText('Guardar');
				
		$inputTipo->add(0,'Normal');
		$inputTipo->add(1,'Opciones');
		$inputTipo->add(2,'Opciones con respuestas');
		$inputTipo->add(3,'Ordenar frases');
		
		$inputDescripcion->setMaxLength(null);
		
		$preguntas->setId('tabla_preguntas');
		$preguntas->addHeader('Pregunta');
		$preguntas->addHeader('Respuesta');
		
		$i=0;
		while(getParam('c0'.$i)){
			$preguntas->add(new vRow(array(
				new vInputText('c0'.$i,''),
				new vInputText('c1'.$i,''),
			)));
			$i++;
		}
			
		$preguntas->add(new vRow(array(
			new vInputText('c0'.$i,''),
			new vInputText('c1'.$i,''),
		)));
			
		return new vDiv($edit_form,'editaEjercicioTab');
	}
	
	function _processForm($apartado){
		$apartado=new Apartado($apartado);
		
		if($this->esResponsable($apartado)){
			if(!getParam('titulo')) $this->error='Debes escribir un título para el ejercicio';
			else{
				$ejercicio=$apartado->ejercicios->add();
				$ejercicio->save();
			
				$i=0;
				while(isset($_POST['c0'.$i])){ 
					if($_POST['c0'.$i]){
						$pregunta=$ejercicio->preguntas->add();
						$pregunta->pregunta=$_POST['c0'.$i];
						$pregunta->respuesta=$_POST['c1'.$i];
						$pregunta->save();
					}
					$i++;
				}
				
				// Guardamos los nuevos valores del ejercicio
				$ejercicio->titulo=$_POST['titulo'];
				$ejercicio->fecha_actualizacion=new DateTime();
				$ejercicio->descripcion=$_POST['descripcion'];
				$ejercicio->tipo=$_POST['tipo'];
				$ejercicio->url=urlencode($_POST['titulo']);
				$this->guardaVersion($ejercicio);
				$ejercicio->save();
				
				redirect(base_url().'aprender/'.$ejercicio->url.'/'.$ejercicio->id);
			}
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