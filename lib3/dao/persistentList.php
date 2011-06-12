<?php

define("ORDER_ASC",true);
define("ORDER_DESC",false);


function query($query){
	if(getConfig('sql_benckmark')) $t1=time();
	if(!$data =mysql_query($query))throw new Exception(mysql_errno().' - '.mysql_error().' - query: '.$query);
	if(getConfig('sql_benckmark')) $t2=time();
	
	if(!is_resource($data)) return $data;
	
	$result=array();
	while ($row = mysql_fetch_array($data))
		$result[]=$row;
	if(getConfig('sql_benckmark')) echo '['.floor($t2-$t1).'+'.floor(time()-$t2).'] - '.$query.'<br/>';
	return $result;
}

class persistentList implements Iterator{
	private $persistentObject= null;
	private $name= null;
	private $relation= null;
	private $side= null;
	private $thisSide= null;
	private $listSide= null;
	private $items= null;
	private $limit= array();
	private $order= array();
	private $group= '';
	private $condition=null;
	private $iteration_index=null; //public $table=null;
	private $fields= array();
	
	protected $origin_list=null; // Si está definida indica que los elementos de esta lista son los referenciados por los de la otra. Cogemos la lista original, y por cada elemento obtenemos otro a traves de una referencia
	protected $origin_reference=null; // Campo a partir del cual obtenemos los elementos de esta lista, partiendo de la lista original
	
	public $table=null;
	
	function __construct($persistentObject=null,$name=null){
		if($persistentObject){
			$this->persistentObject=$persistentObject;
			$this->name=$name;
			
			// Get relation
			$this->relation=$persistentObject->getEntity()->sets[$name];
			
			// Get the side
			$this->side=($this->relation->sides[0]->property==$name)?0:1;
			$this->thisSide=$this->relation->sides[$this->side];
			$this->listSide=$this->relation->sides[abs($this->side-1)];
		}
	}
	
	static function getListOf($table){
		$list=new persistentList();
		$list->table=$table;
		return $list;
	}
	
	// Devuelve el elemento relacionado con la entidad recibida
	function with($entity){
		foreach($this->listSide->entity->fields as $field)
			if($field instanceof ReferenceField and $field->name!=$this->listSide->property){
				$name=$field->name;
				break;
			}
		return $this->where(new EqualCondition($name,$entity))->getFirst();
	}
	
	function add(){
		$item = new Persistent($this->listSide->entity->name);
		$field=$this->listSide->property;
		$item->$field=$this->persistentObject->id;
		//$this->items[]=$item;
		return $item;
	}
	
	function deleteAll(){
		$query='delete from '.$this->listSide->entity->name.' where '.$this->listSide->property.'='.$this->persistentObject->id;
		
		query($query);
	}

    public function rewind() {
        $this->iteration_index=0;
    }

    public function current() {
		$this->loadItems();
        return $this->items[$this->iteration_index];
    }

    public function key() {
        return $this->iteration_index;
    }

    public function next() {
		$this->loadItems();
        return $this->items[$this->iteration_index++];
    }

    public function valid() {
		$this->loadItems();
        return $this->iteration_index>=0 and $this->iteration_index<$this->count();
    }
	
	public function getPosition($item){
		$result=query('select count(*) from '.$this->listSide->entity->name.' where '.
				' id<'.$item->id.' and '.
				($this->condition?$this->condition->sql().' and ':'').
				$this->listSide->property.'='.$this->persistentObject->id);
		
		return $result[0]['count(*)'];
	}
	
	public function contains(Persistent $object){
		
			if($this->relation->joinTable)
				$result=query('select count(*) from '.$this->relation->joinTable.' where '.
					$this->persistentObject->getEntity()->name.'='.$this->persistentObject->id.' and '.
					$this->listSide->field_name.'='.$object->id) or die(mysql_error());
			else $result=query('select count(*) from '.$this->entity->name.' where '.$this->field.'='.$this->id.
				' and '.$this->entity->name.'='.$object->id);
					
			return $result[0]['count(*)']>0;
	}
	
	private function loadItems(){
		if($this->items===null){
			
			$result=query($this->getQuery());
			
			$this->items=array();
			foreach ($result as $row){
				if($this->table) $this->items[]=new Persistent($this->table);
				else $this->items[]=new Persistent($this->listSide->entity->name);
				$this->items[count($this->items)-1]->loadData($row);
			}
		}
	}
	
	function update($set){
		$limit='';
		if(count($this->limit))
			$limit=' limit '.$this->limit[0];
		
		$order='';
		if(count($this->order)){
			$order_info=array();
			foreach($this->order as $order)
				$order_info[]=(($this->listSide and $this->listSide->entity->name)?
					$this->listSide->entity->name:$this->table).'.'.$order[0].($order[1]?'':' desc');
			$order=' order by '.implode(',',$order_info);
		}
		
		if(!$order and $this->thisSide and $this->thisSide->order) {
			if($this->table) $order=' order by '.$this->table.'.'.$this->thisSide->order;
			else if($this->relation->joinTable)
				$order=' order by '.$this->joinTable.'.'.$this->thisSide->order;
			else $order=' order by '.$this->listSide->entity->name.'.'.$this->thisSide->order;
		}
		
		query('update '.$this->getQueryTables().' set '.$set.' '.$this->getQueryWhere().$order.$limit);
	}
	
	function getQuery(){
		$limit='';
		if(count($this->limit))
			$limit=' limit '.$this->limit[1].','.$this->limit[0];
		
		$order='';
		if(count($this->order)){
			$order_info=array();
			foreach($this->order as $order)
				$order_info[]=(($this->listSide and $this->listSide->entity->name)?
					$this->listSide->entity->name:$this->table).'.'.$order[0].($order[1]?'':' desc');
			$order=' order by '.implode(',',$order_info);
		}
		
		if(!$order and $this->thisSide and $this->thisSide->order) {
			if($this->table) $order=' order by '.$this->table.'.'.$this->thisSide->order;
			else if($this->relation->joinTable)
				$order=' order by '.$this->joinTable.'.'.$this->thisSide->order;
			else $order=' order by '.$this->listSide->entity->name.'.'.$this->thisSide->order;
		}
		
		$group='';
		if($this->group) $group=' group by '.$this->group;
		
		return 'select '.$this->getQuerySelect().' from '.$this->getQueryTables().$this->getQueryWhere().$group.$order.$limit;
	}
	
	private function getListTable(){
		if($this->table) return $this->table;
		else if($this->relation->joinTable) return $this->entity->name;
		else return $this->listSide->entity->name;
	}
	
	private function getQueryTables(){
		if(!$this->table and $this->relation->joinTable) return $this->joinTable.','.$this->entity->name;
		else return $this->getListTable();
	}
	
	private function getQuerySelect(){
		if(count($this->fields)){
			$fields=array();
			foreach($this->fields as $field){
				$fields[]=$field[0].($field[1]?' '.$field[1]:'');
			}
			return implode(',',$fields);
		}
		else if(!$this->table and $this->relation->joinTable) return $this->getListTable().'.*';
		else return '*';
	}
	
	private function getQueryWhere(){
		return (($conditions=$this->getQueryConditions())?' where '.$conditions:'');
	}
	
	private function getQueryConditions($table_prefix=''){
		if($table_prefix) $table_prefix=$table_prefix.'.';
	
		if($this->origin_list) return ($this->condition?$this->condition->sql().' and ':'').
			'id in (select * from ('.$this->origin_list->getField($this->origin_reference)->getQuery().') alias)';
		else if($this->table) return ($this->condition?$this->condition->sql():'');
		else if($this->relation->joinTable) return 
					($this->condition?$this->condition->sql().' and ':'').
					$this->joinTable.'.'.$this->persistentObject->getEntity()->name.'='.$this->persistentObject->id.' and '.
					$this->joinTable.'.'.$this->entity->name.'='.$this->entity->name.'.id';
		else return ($this->condition?$this->condition->sql().' and ':'').
				$table_prefix.$this->listSide->property.'='.$this->persistentObject->id;
	}

	function __get($name){
		if($this->table) $entity=entity::$entities[$this->table];
		else $entity=$this->listSide->entity;
		
		if(isset($entity->fields[$name]) and $entity->fields[$name] instanceof ReferenceField){
			$list=new persistentList();
			$list->origin_list=$this;
			$list->origin_reference=$name;
			$list->table=$entity->fields[$name]->entity->name;
			
			return $list;
		}
	}
	
	function getFirst(){
		if(!$this->items) $this->loadItems();
		if(isset($this->items[0]))
			return $this->items[0];
	}
	
	function getLast(){
		if(!$this->items) $this->loadItems();
		if(count($this->items))
			return $this->items[count($this->items)-1];
	}
	
	function count(){
		if($this->items)
			return count($this->items);
		else{
			$limit='';
			if(count($this->limit))
				$limit=' limit '.$this->limit[1].','.$this->limit[0];
				
			$result=query('select count(*) from '.$this->getQueryTables().$this->getQueryWhere().$limit);
			return $result[0]['count(*)'];
		}
	}
	
	function orderBy($field,$asc=true){
		if(!$this->table and $field!='id' and !isset($this->listSide->entity->fields[$field])) 
			throw new Exception('There is no field "'.$field.'" in entity "'.$this->listSide->entity->name.'"');
		$list=clone $this;
		$list->order[]=array($field,$asc);
		return $list;
	}
	
	function groupBy($field){
		if(!$this->table and $field!='id' and !isset($this->listSide->entity->fields[$field])) 
			throw new Exception('There is no field "'.$field.'" in entity "'.$this->listSide->entity->name.'"');
		$list=clone $this;
		$list->group=$field;
		return $list;
	}
	
	function paginate($number,$page=0){
		return $this->limit($number,$page*$number);
	}
	
	function limit($number,$pos=0){
		$list=clone $this;
		$list->limit=array(intval($number),intval($pos));
		return $list;
	}
	
	function where($condition){
		$list=clone $this;
		if($this->condition)
			$list->condition=new AndCondition($this->condition,$condition);
		else $list->condition=$condition;
		return $list;
	}
	
	
	function getField($field,$alias=''){
		$list=clone $this;
		$list->fields[]=array($field,$alias);
		return $list;
	}
	
	function getFields($fields){
		$list=clone $this;
		foreach($fields as $field)
			$list->fields[]=array((is_array($field)?$field[0]:$field),((is_array($field) and isset($field[1]))?$field[1]:''));
		return $list;
	}
	
	function __call($name,$arguments){
		if(substr($name,-2)=='Is'){
			$field=substr($name,0,strlen($name)-2);
			
			if($this->table) $entity=entity::$entities[$this->table];
			else $entity=$this->listSide->entity;
			if(!isset($entity->fields[$field])) throw new Exception('There is no field "'.$field.'" on entity "'.$entity->name.'"');
		
			if(count($arguments)>1) return $this->where(new InCondition($field,$arguments));
			if(is_array($arguments[0])) return $this->where(new InCondition($field,$arguments[0]));
			if($arguments[0] instanceof Persistent) return $this->where(new EqualCondition($field,$arguments[0]->id));
			else return $this->where(new EqualCondition($field,$arguments[0]));
		}
		if(substr($name,-4)=='Like'){
			$field=substr($name,0,strlen($name)-4);
			
			if($this->table) $entity=entity::$entities[$this->table];
			else $entity=$this->listSide->entity;
			if(!isset($entity->fields[$field])) throw new Exception('There is no field "'.$field.'" on entity "'.$entity->name.'"');
		
			if($arguments[0] instanceof Persistent) return $this->where(new LikeCondition($field,$arguments[0]->id));
			else return $this->where(new LikeCondition($field,$arguments[0]));
		}
		elseif(substr($name,-5)=='IsNot'){
			$field=substr($name,0,strlen($name)-5);
			
			if($this->table) $entity=entity::$entities[$this->table];
			else $entity=$this->listSide->entity;
			if(!isset($entity->fields[$field])) throw new Exception('There is no field "'.$field.'" on entity "'.$entity->name.'"');
		
			if($arguments[0] instanceof Persistent) return $this->where(new NotEqualCondition($field,$arguments[0]->id));
			else return $this->where(new NotEqualCondition($field,$arguments[0]));
		}
		elseif(substr($name,-8)=='LessThan'){
			$field=substr($name,0,strlen($name)-8);
			
			if($this->table) $entity=entity::$entities[$this->table];
			else $entity=$this->listSide->entity;
			if(!isset($entity->fields[$field])) throw new Exception('There is no field "'.$field.'" on entity "'.$entity->name.'"');
		
			if($arguments[0] instanceof Persistent) return $this->where(new LessThanCondition($field,$arguments[0]->id));
			else return $this->where(new LessThanCondition($field,$arguments[0]));
		}
		elseif(substr($name,-8)=='MoreThan'){
			$field=substr($name,0,strlen($name)-8);
			
			if($this->table) $entity=entity::$entities[$this->table];
			else $entity=$this->listSide->entity;
			if(!isset($entity->fields[$field])) throw new Exception('There is no field "'.$field.'" on entity "'.$entity->name.'"');
		
			if($arguments[0] instanceof Persistent) return $this->where(new MoreThanCondition($field,$arguments[0]->id));
			else return $this->where(new MoreThanCondition($field,$arguments[0]));
		}
		else throw new Exception('Fatal error: Call to undefined method '.$name.'() on a PersistentList');
	}
}

?>
