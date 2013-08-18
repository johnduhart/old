<?php

// I stole alot from MyBB's db_mysqli.php

class db_mysqli {
	
	private $con;
	public $tabe_prefix;
	
	function connect($host, $port, $user, $pass, $database) {
		$this->con = mysqli_connect($host, $user, $pass, $database, $port);
		if(!$this->con)
		{
			die('Failed to connect to database');
		}
	}
	
	function query($str) {
		
		$query = mysqli_query($this->con, $str);
		
		return $query;
	}
	
	function fetch_array($query)
	{
		$array = mysqli_fetch_assoc($query);
		return $array;
	}
	
	function fetch_field($query, $field, $row=false)
	{
		if($row !== false)
		{
			$this->data_seek($query, $row);
		}
		$array = $this->fetch_array($query);
		return $array[$field];
	}
	
	function num_rows($query)
	{
		return mysqli_num_rows($query);
	}
	
	function insert_id()
	{
		$id = mysqli_insert_id($this->current_link);
		return $id;
	}
	
	function simple_select($table, $fields="*", $conditions="", $options=array())
	{
		$query = "SELECT ".$fields." FROM ".$this->table_prefix.$table;
		
		if($conditions != "")
		{
			$query .= " WHERE ".$conditions;
		}
		
		if(isset($options['order_by']))
		{
			$query .= " ORDER BY ".$options['order_by'];
			if(isset($options['order_dir']))
			{
				$query .= " ".my_strtoupper($options['order_dir']);
			}
		}
		
		if(isset($options['limit_start']) && isset($options['limit']))
		{
			$query .= " LIMIT ".$options['limit_start'].", ".$options['limit'];
		}
		else if(isset($options['limit']))
		{
			$query .= " LIMIT ".$options['limit'];
		}
		
		return $this->query($query);
	}
	
	function insert_query($table, $array)
	{
		if(!is_array($array))
		{
			return false;
		}
		$fields = "`".implode("`,`", array_keys($array))."`";
		$values = implode("','", $array);
		$this->write_query("
			INSERT 
			INTO {$this->table_prefix}{$table} (".$fields.") 
			VALUES ('".$values."')
		");
		return $this->insert_id();
	}
	
	function update_query($table, $array, $where="", $limit="", $no_quote=false)
	{
		if(!is_array($array))
		{
			return false;
		}
		
		$comma = "";
		$query = "";
		$quote = "'";
		
		if($no_quote == true)
		{
			$quote = "";
		}
		
		foreach($array as $field => $value)
		{
			$query .= $comma."`".$field."`={$quote}{$value}{$quote}";
			$comma = ", ";
		}
		
		if(!empty($where))
		{
			$query .= " WHERE $where";
		}
		
		if(!empty($limit))
		{
			$query .= " LIMIT $limit";
		}
		
		return $this->write_query("
			UPDATE {$this->table_prefix}$table
			SET $query
		");
	}
	
	function delete_query($table, $where="", $limit="")
	{
		$query = "";
		if(!empty($where))
		{
			$query .= " WHERE $where";
		}
		if(!empty($limit))
		{
			$query .= " LIMIT $limit";
		}
		return $this->write_query("DELETE FROM {$this->table_prefix}$table $query");
	}
	
	function escape_string($string)
	{
		if(function_exists("mysqli_real_escape_string"))
		{
			$string = mysqli_real_escape_string($this->con, $string);
		}
		else
		{
			$string = addslashes($string);
		}
		return $string;
	}
	
	function set_table_prefix($prefix)
	{
		$this->table_prefix = $prefix;
	}
	
	function table_exists($table)
	{
		$query = $this->query("
			SHOW TABLES 
			LIKE '{$this->table_prefix}$table'
		");
		$exists = $this->num_rows($query);
		
		if($exists > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}