<?php

require_once 'persistentList.php';
require_once 'entity.php';

class NotExistException extends Exception{
}

class FieldNotExistException extends Exception{
}

function mb_unserialize($serial_str) { 
$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str ); 
return unserialize($out); 
} 

class Persistent{
	private $entity= null;
	private $fields= array();
	private $id= null;
	private $language= 'en';
	private $previousReference= array();
	
	function __construct($name,$condition=false){
		// Comprobamos que la entidad exista
		if(!isset(entity::$entities[$name])) throw new Exception('The entity "'.$name.'" do not exists');
		$this->entity=entity::$entities[$name];
		
		if ($condition){
			if($condition instanceof Condition)
				$query='select * from '.$this->entity->name.' where '.$condition->sql();
			else $query='select * from '.$this->entity->name.' where id='.intval($condition);
			
			$result=query($query);
			
			if (count($result))
				$this->loadData($result[0]);
			/*	
			if (count($result)){
				$row=$result[0];
				foreach($this->entity->fields as $field)
					if($field instanceof DateField)
						$this->fields[$field->name]=new DateTime($row[$field->name]);
					else if($field instanceof UniversalTextField)
						$this->fields[$field->name]=array($row[$field->name],null);
					else 
						if(!isset($row[$field->name]))
							throw new FieldNotExistException('Field "'.$field->name.'" do not exists in table "'.$name.'"');
						else $this->fields[$field->name]=$row[$field->name];
				$this->id=$row['id'];
			}*/
			else throw new NotExistException('There is no data that fulfill the conditions ('.$query.')');
		}
		else $this->id=0;
	}
	
	function getEntity(){
		return $this->entity;
	}
	
	function loadData($row){
		foreach($this->entity->fields as $field)
			if($field instanceof DateField)
				$this->fields[$field->name]=new DateTime($row[$field->name]);
			else if($field instanceof StructField){
				if($row[$field->name]){
					$this->fields[$field->name]=mb_unserialize($row[$field->name]);
				}
				else $this->fields[$field->name]='';
			}
			else if($field instanceof UniversalTextField)
				$this->fields[$field->name]=array($row[$field->name],null);
			else 
				if(!isset($row[$field->name])){
					throw new FieldNotExistException('Field "'.$field->name.'" do not exists in table "'.$this->entity->name.'"');
				}
				else {
				$this->fields[$field->name]=$row[$field->name];
				}
		$this->id=$row['id'];
	}
	
	function setLanguage($language){
		$this->language=$language;
	}

	static function getAll($table=null,$order=''){
		// Comprobamos que la entidad exista
		if(!isset(entity::$entities[$table])) throw new Exception('The entity "'.$table.'" do not exists');
		$entity=entity::$entities[$table];
		
		if($order) $order=' order by '.$order;
		
		$result=mysql_query('select * from '.$table.$order) or die(mysql_error());
		$array=array();
		while($row = mysql_fetch_array($result))
			$array[]=new persistent($table,$row['id']);
			
		return $array;
	}

	static function getWhere($table,$conditions,$order=''){
		// Comprobamos que la entidad exista
		if(!isset(entity::$entities[$table])) throw new Exception('La entidad "'.$table.'" no existe');
		$entity=entity::$entities[$table];
		
		if($order) $order=' order by '.$order;
		
		if(is_string($conditions))
			$query='select * from '.$table.' where '.$conditions.$order;
		else $query='select * from '.$table.' where '.$conditions->sql().$order;
		
		$rows=query($query);
		$array=array();
		foreach($rows as $row)
			$array[]=new persistent($table,$row['id']);
			
		return $array;
	}
	
	private function prepareFields(){
		$values=array();
		foreach($this->fields as $name=>$field) if(!$this->fields[$name] instanceof PersistentList)
			switch(get_class($this->entity->fields[$name])){
				case 'UniversalTextField': 
					if($field[0]){
						if($this->fields[$name][1]!==null)
							mysql_query('update texto set texto="'.mysql_real_escape_string($field[1]).'" where id='.$field[0]);
					}
					else{
						$resource=mysql_query('select max(id) from texto');
						$result = mysql_fetch_row($resource);
						$id=$result[0]+1;
						mysql_query('insert into texto (id,idioma,texto) values ('.$id.',"'.$this->language.'","'.mysql_real_escape_string($field[1]).'")');
						$values[$name]=$id;
					}
				break;
				case 'DateField': 
					$values[$name]='"'.$field->format('Y-m-d H:i:s').'"';
				break;
				case 'StructField': 
					$values[$name]='"'.mysql_real_escape_string(serialize($field)).'"';
				break;
				case 'ReferenceField':
					// Si hemos cambiado la referencia anterior, miramos si hay que reordenar
					if(isset($this->previousReference[$name]) and $this->previousReference[$name] and isset($this->entity->sets[$name])) {
						$set=$this->entity->sets[$name];
						foreach($set->sides as $side)
							if($side->order and $name!=$side->property){
								// Reduciremos el orden de cada elemento superior
								query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
									'.'.$side->order.'-1 where '.
									$this->entity->name.'.'.$side->order.'>'.$this->fields[$side->order].' and '.$this->entity->name.'.'.
									$name.'='.$this->previousReference[$name]);
							}
					}
					
					if($field instanceof Persistent)
						$values[$name]='"'.$field->id.'"';
					else $values[$name]='"'.mysql_real_escape_string($field).'"';
					
					// Si hay orden y hemos cambiado la referencia
					if(isset($this->previousReference[$name])){
						unset($this->previousReference[$name]);
						if($this->entity->fields[$name]->relation){
							foreach($this->entity->fields[$name]->relation->sides as $side)
								if($side->multi and $side->order){
									$order_name=$side->order;
									// Obtenemos el orden actual
									$orden=query('select max(`'.$order_name.'`) from '.$this->entity->name.
										' where '.$name.'='.($field instanceof Persistent?$field->id:$field));
									$this->$order_name=$orden[0]['max(`'.$side->order.'`)']+1;
									
									$values[$order_name]=$this->$order_name;
									break;
								}
						}
					}
					
				break;
				default:
					if(!($this->fields[$name] instanceof PersistentList))
						if(is_object($this->fields[$name]))
							throw new Exception('Field '.$name.' cannot be an object');
						else $values[$name]='"'.mysql_real_escape_string($this->fields[$name]).'"';
			}
			
		return $values;
	}
	
	function save(){
		//Si el identificador esta definido, actualizamos la fila
		$values=array();
		if ($this->id){
			foreach($this->prepareFields() as $name=>$field)
				$values[$name]='`'.$name.'`='.$field;
				
			//hace una consulta a la base de datos que actualiza los valores que ya estes creados
			if(count($values)) query('update '.$this->entity->name.' set '.implode(',',$values).' where id='.$this->id) or die(mysql_error());
		}
		else{
			$values=$this->prepareFields();
			//hace una consulta a la base de datos que inserta los valores que no estes creados en la base de datos
			query('insert into '.$this->entity->name.' ('.(!count($values)?'':'`'.implode('`,`',array_keys($values)).
				'`').') values ('.implode(',',$values).')') or die(mysql_error());
			$this->id=mysql_insert_id();
		}
	}
	
	function delete (){
		if ($this->id){
			// Buscamos si está ordenada, para mantener el orden
			foreach($this->entity->sets as $name=>$set){
				foreach($set->sides as $side)
					if($side->order and $name!=$side->property){
						// Reduciremos el orden de cada elemento superior
						query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
							'.'.$side->order.'-1 where '.
							$this->entity->name.'.'.$side->order.'>'.$this->fields[$side->order].' and '.$this->entity->name.'.'.
							$name.'='.$this->fields[$name]);
					}
			}
				
			mysql_query('delete from '.$this->entity->name.' where id='.$this->id);
		}
	}
	
	function moveForward(){
		// Buscamos la lista por la que se ordena
		foreach($this->entity->sets as $name=>$set){
			foreach($set->sides as $side)
				if($side->order and $name!=$side->property){
					$field=($this->fields[$name] instanceof Persistent?$this->fields[$name]->id:$this->fields[$name]);
					if(count(query('select * from '.$this->entity->name.' where '.
							$this->entity->name.'.'.$side->order.'='.$this->fields[$side->order].'+1 and '.$this->entity->name.'.'.
							$name.'='.$field))){
						// Reduciremos el orden de cada elemento superior
						query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
							'.'.$side->order.'-1 where '.
							$this->entity->name.'.'.$side->order.'='.$this->fields[$side->order].'+1 and '.$this->entity->name.'.'.
							$name.'='.$field);
							
						query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
							'.'.$side->order.'+1 where id='.$this->id);
					}
					return;
				}
		}
	}
	
	function last(){
		foreach($this->entity->sets as $name=>$set)
			foreach($set->sides as $side)
				if($side->order and $name!=$side->property)
					return !count(query('select * from '.$this->entity->name.' where '.
							$this->entity->name.'.'.$side->order.'='.$this->fields[$side->order].'+1 and '.$this->entity->name.'.'.
							$name.'='.$this->fields[$name]));
		return false;
	}
	
	function first(){
		return $this->fields['order']==1;
	}
	
	function previous(){
		foreach($this->entity->sets as $name=>$set)
			foreach($set->sides as $side)
				if($side->order and $name!=$side->property){
					try{
						if(is_object($this->fields[$name])){
							$value=$this->fields[$name]->id;
						}
						else $value=$this->fields[$name];
							
						return new Persistent($this->entity->name,new AndCondition(
							new EqualCondition($this->entity->name.'.'.$side->order,$this->fields[$side->order]-1),
							new EqualCondition($this->entity->name.'.'.$name,$value)));
					}
					catch(NotExistException $e) {
						return null;
					}
				}
		return null;
	}
	
	function next(){
		foreach($this->entity->sets as $name=>$set)
			foreach($set->sides as $side)
				if($side->order and $name!=$side->property){
					try{
						if(is_object($this->fields[$name])){
							$value=$this->fields[$name]->id;
						}
						else $value=$this->fields[$name];
							
						return new Persistent($this->entity->name,new AndCondition(
							new EqualCondition($this->entity->name.'.'.$side->order,$this->fields[$side->order]+1),
							new EqualCondition($this->entity->name.'.'.$name,$value)));
					}
					catch(NotExistException $e) {
						return null;
					}
				}
		return null;
	}
	
	function moveBackward(){
		// Buscamos la lista por la que se ordena
		foreach($this->entity->sets as $name=>$set){
			foreach($set->sides as $side){
				if($side->order and $name!=$side->property and $this->fields[$side->order]>1){
					// Reduciremos el orden de cada elemento superior
					query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
						'.'.$side->order.'+1 where '.
						$this->entity->name.'.'.$side->order.'='.$this->fields[$side->order].'-1 and '.$this->entity->name.'.'.
						$name.'='.($this->fields[$name] instanceof Persistent?$this->fields[$name]->id:$this->fields[$name]));
						
					query('update '.$this->entity->name.' set '.$this->entity->name.'.'.$side->order.'='.$this->entity->name.
						'.'.$side->order.'-1 where id='.$this->id);
					return;
				}
			}
		}
	}
	
	/*
	Modifica el valor del campo name por el valor que le pasamos en el parametro value
	*/
	function __set ($name, $value ){
		if($name=='id')
			$this->id=$value;
		else if(!isset($this->entity->fields[$name])) throw new Exception('There is no field "'.$name.'" in entity "'.$this->entity->name.'"');
		else if(($this->entity->fields[$name] instanceof DateField) and !($value instanceof DateTime))
				throw new Exception('"'.$name.'" type must be DateTime');
		else if($this->entity->fields[$name] instanceof PasswordField)
			$this->fields[$name]=md5($value);
		else if($this->entity->fields[$name] instanceof UniversalTextField){
			if(!$this->fields[$name]) $this->fields[$name]=array(0,$value);
			else $this->fields[$name][1]=$value;
		}
		else if($this->entity->fields[$name] instanceof ReferenceField){
			if(!isset($this->previousReference[$name])) 
				if(isset($this->fields[$name]) and $this->fields[$name]){
					if($this->fields[$name] instanceof Persistent)
						$this->previousReference[$name]=$this->fields[$name]->id;
					else $this->previousReference[$name]=$this->fields[$name];
				}
				else $this->previousReference[$name]=0;
			$this->fields[$name]=$value;
		}
		else $this->fields[$name]=$value;
	}
	
	/*
	Devuelve el elemento que se encuentra en el campo $name
	*/
	function __get ($name){
		//si el nombre es el identificador devuelve el identificador
		if($name=='id')
			return $this->id;
		else if(isset($this->entity->fields[$name])) // Si el campo existe
			if(!isset($this->fields[$name]))//si la variable no tiene asignada un valor
				return null;
			else if($this->entity->fields[$name] instanceof UniversalTextField){
				if($this->fields[$name][1]!==null)
					return $this->fields[$name][1];
				else{
					$result=query('select * from texto where idioma="es" and id='.$this->fields[$name][0]);
					if(count($result))
						return $result[0]['texto'];
					else return null;
				}
			}
			else if($this->entity->fields[$name] instanceof ReferenceField) // Si el campo es referencia, devolvemos el elemento relacionado
				if(!$this->fields[$name]) return null;
				else if($this->fields[$name] instanceof Persistent)
					return $this->fields[$name];
				else
					try{
						return $this->fields[$name]=new Persistent($this->entity->fields[$name]->entity->name,$this->fields[$name]);
					}
					catch (NotExistException $e){
						return null;
					}
			else return $this->fields[$name];// devuelve el campo de la base de datos determinado por $name
		else{
			if(isset($this->entity->sets[$name]))
				if(!isset($this->fields[$name]))
					return $this->fields[$name]=
						new  persistentList($this,$name);
				else return $this->fields[$name];
			else throw new Exception('There is no field "'.$name.'" in entity "'.$this->entity->name.'"');
		}
	}
}

?>