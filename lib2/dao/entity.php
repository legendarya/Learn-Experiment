<?php
 
 function print_var($var,$profundidad=3,$inicial=1){
	if(is_object($var) or is_array($var)){
		$result= (is_object($var)?'Object':'Array('.count($var).')');
		if($profundidad>0){
			$result.='<ul>';
			foreach($var as $k=>$v)
				$result.= '<li>'.$k.': '.print_var($v,$profundidad-1,0).'</li>';
			$result.= '</ul>';
		}
	}
	else $result= $var;
	
	if($inicial) echo $result;
	else return $result;
 }
 
class entity{
	public $name= null;
	public $fields= array();
	public $sets= array();
	public static $entities = array();
	
	function __construct($name){
		$this->name=$name;
		self::$entities[$name]=$this;
	}

	function addField(PersistentField $field){
		$this->fields[$field->name]=$field;
	}
	
	function addRelation(Relation $relation){
		$this->sets[$relation->sides[count($relation->sides)-1]->property]=$relation;
		
	}
	
	function addSet($property,$entity,$inverse){
		$relation=new Relation();
		$relation->addSideN($property,$this);
		$relation->addSide1($inverse,$entity);
		$entity->addField(new ReferenceField($inverse,$this));
	}
	
	function addList($property,$entity,$inverse,$order='order'){
		$relation=new Relation();
		$relation->addSideN($property,$this,null,$order);
		$relation->addSide1($inverse,$entity);
		$entity->addField(new ReferenceField($inverse,$this,$relation));
		$entity->addField(new OrderField($order,$relation));
	}
	
	static function createDatabase(){
		foreach(self::$entities as $entity){
			mysql_query('create table if not exists '.$entity->name.' (id integer(11) auto_increment, PRIMARY KEY (id))') or die(mysql_error());
			
			foreach($entity->fields as $field){
				mysql_query('ALTER TABLE '.$entity->name.' ADD '.$field->name.' '.$field->sqlType().';');
			}
		}
	exit;
	}
	
}


class PersistentField{
	public $name=null;
	
	function __construct($name){
		$this->name=$name;
	}
}

class RelationSide{
	public $property;
	public $entity;
	public $field_name;
	public $multi;
	public $order;
	
	function __construct($property, entity $entity, $multi, $field_name=null,$order=null){
		$this->property=$property;
		$this->entity=$entity;
		$this->multi=$multi;
		$this->order=$order;
		if($field_name)
			$this->field_name=$field_name;
		else $this->field_name=$entity->name;
	}
}

class Relation{
	public $sides=array();
	
	function __construct($joinTable=null){
		$this->joinTable=$joinTable;
	}
	
	function addSideN($property, entity $entity, $field_name=null,$order=null){
		$this->sides[]=new RelationSide($property, $entity, true, $field_name,$order);
		$entity->addRelation($this);
	}
	
	function addSide1($property, entity $entity, $field_name=null){
		$this->sides[]=new RelationSide($property, $entity, false, $field_name);
		$entity->addRelation($this);
	}
}

class DateField extends PersistentField{
	function sqlType(){
		return 'datetime';
	}
}

class StructField extends PersistentField{
	function sqlType(){
		return 'text';
	}
}

class UniversalTextField extends PersistentField{
	function sqlType(){
		return 'integer(11)';
	}
}
class TextField extends PersistentField{
	function sqlType(){
		return 'char(100)';
	}
}
class PasswordField extends PersistentField{
	function sqlType(){
		return 'char(100)';
	}
}
class IntegerField extends PersistentField{
	function sqlType(){
		return 'integer(11)';
	}
}
class FloatField extends PersistentField{
	function sqlType(){
		return 'float';
	}
}
class ReferenceField extends PersistentField{
	public $entity=null;
	public $relation=null;
	
	function __construct($name, entity $entity, Relation $relation=null){
		parent::__construct($name);
		$this->entity=$entity;
		$this->relation=$relation;
	}
	
	function sqlType(){
		return 'integer(11)';
	}
}

class OrderField extends PersistentField{
	public $relation=null;
	
	function __construct($name, Relation $relation=null){
		parent::__construct($name);
		$this->relation=$relation;
	}
	
	function sqlType(){
		return 'integer(11)';
	}
}

class Condition{
	public $field=null;
	public $value=null;
	
	function __construct($field,$value){
		if(is_object($value)){
			if($value instanceof Persistent) $value=$value->id;
			else if($value instanceof DateTime) $value=$value->format('Y/m/d H:i:s');
			else  throw new Exception('The value of the condition cannot be a object'); 
		}
		
		$this->field=$field;
		$this->value=$value;
	}
	
	function sql(){
		return $this->field.'"'.mysql_real_escape_string($this->value).'"';
	}
}

class EqualCondition extends Condition{
	public $field=null;
	public $value=null;
	
	function __construct($field,$value){
		if(is_object($value)) throw new Exception('The value of the condition cannot be a object'); 
		$this->field=$field;
		$this->value=$value;
	}
	
	function sql(){
		return $this->field.'="'.mysql_real_escape_string($this->value).'"';
	}
}

class InCondition extends Condition{
	public $field=null;
	public $values=null;
	
	function __construct($field,$values){
		$this->field=$field;
		$this->values=$values;
	}
	
	function sql(){
		if(!$this->values) return $this->field.' in ()';
		return $this->field.' in ("'.implode('","',$this->values).'")';
	}
}

class NotEqualCondition extends EqualCondition{
	function sql(){
		return $this->field.'!="'.mysql_real_escape_string($this->value).'"';
	}
}

class LessThanCondition extends EqualCondition{
	function sql(){
		return $this->field.'<"'.mysql_real_escape_string($this->value).'"';
	}
}

class MoreThanCondition extends EqualCondition{
	function sql(){
		return $this->field.'>"'.mysql_real_escape_string($this->value).'"';
	}
}

class AndCondition extends Condition{
	public $conditions=array();
	
	function __construct(){
		$args=func_get_args ();
		foreach($args as $arg)
			$this->conditions[]=$arg;
	}
	
	function sql(){
		$conditions=array();
		foreach($this->conditions as $condition)
			$conditions[]=$condition->sql();
			
		return implode(' and ',$conditions);
	}
}

class OrCondition extends AndCondition{
	
	function sql(){
		$conditions=array();
		foreach($this->conditions as $condition)
			$conditions[]=$condition->sql();
		return ' ( '.implode(' or ',$conditions).' ) ';
	}
}
?>