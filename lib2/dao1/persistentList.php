<?php

define("ORDER_ASC",true);
define("ORDER_DESC",false);


function query($query){
	if(!$data =mysql_query($query))throw new Exception($query.' - '.mysql_error());
	
	if(!is_resource($data)) return $data;
	
	$result=array();
	while ($row = mysql_fetch_array($data))
		$result[]=$row;
		
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
	private $condition=null;
	private $iteration_index=null;
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
		query('delete from '.$this->listSide->entity->name.' where '.$this->listSide->property.'='.$this->persistentObject->id);
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
			
			if($this->table)
				$result=query('select * from '.$this->table.($this->condition?' where '.$this->condition->sql():'').$order.$limit);
			else if($this->relation->joinTable)
				$result=query('select '.$this->entity->name.'.* from '.$this->joinTable.','.$this->entity->name.' where '.
					($this->condition?$this->condition->sql().' and ':'').
					$this->joinTable.'.'.$this->persistentObject->getEntity()->name.'='.$this->persistentObject->id.' and '.
					$this->joinTable.'.'.$this->entity->name.'='.$this->entity->name.'.id'.
					$limit);
			else $result=query('select * from '.$this->listSide->entity->name.' where '.
				($this->condition?$this->condition->sql().' and ':'').
				$this->listSide->property.'='.$this->persistentObject->id.$order.$limit);
			
			$this->items=array();
			foreach ($result as $row){
				if($this->table) $this->items[]=new Persistent($this->table);
				else $this->items[]=new Persistent($this->listSide->entity->name);
				$this->items[count($this->items)-1]->loadData($row);
			}
		}
	}
	
	function getFirst(){
		if(!$this->items) $this->loadItems();
		if(isset($this->items[0]))
			return $this->items[0];
	}
	
	function count(){
		if($this->items)
			return count($this->items);
		else{
			$limit='';
			if(count($this->limit))
				$limit=' limit '.$this->limit[1].','.$this->limit[0];
				
			if($this->table)
				$result=query('select count(*) from '.$this->table.($this->condition?' where '.$this->condition->sql():'').$limit);
			else if($this->relation->joinTable)
				$result=query('select count(*) from '.$this->relation->joinTable.' where '.
					($this->condition?$this->condition->sql().' and ':'').
					$this->thisSide->property.'='.$this->persistentObject->id.$limit) or die(mysql_error());
			else $result=query('select count(*) from '.$this->listSide->entity->name.' where '.
				($this->condition?$this->condition->sql().' and ':'').
				$this->listSide->property.'='.$this->persistentObject->id.$limit) or die(mysql_error());
			return $result[0]['count(*)'];
		}
	}
	
	function orderBy($field,$asc=true){
		if(!$this->table and $field!='id' and !isset($this->listSide->entity->fields[$field])) 
			throw new Exception('There is no field "'.$field.'" in entity "'.$this->listSide->entity->name.'"');
		$list=new PersistentList($this->persistentObject,$this->name);
		$list->order=$this->order;
		$list->order[]=array($field,$asc);
		$list->limit=$this->limit;
		$list->condition=$this->condition;
		$list->table=$this->table;
		return $list;
	}
	
	function limit($number,$page=0){
		$list=new PersistentList($this->persistentObject,$this->name);
		$list->order=$this->order;
		$list->condition=$this->condition;
		$list->limit=array($number,$page*$number);
		$list->table=$this->table;
		return $list;
	}
	
	function limit2($number,$pos){
		$list=new PersistentList($this->persistentObject,$this->name);
		$list->order=$this->order;
		$list->condition=$this->condition;
		$list->limit=array($number,$pos);
		$list->table=$this->table;
		return $list;
	}
	
	function where($condition){
		$list=new PersistentList($this->persistentObject,$this->name);
		$list->order=$this->order;
		$list->condition=$condition;
		$list->limit=$this->limit;
		$list->table=$this->table;
		return $list;
	}
}

?>
