<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------



/**
 * PDO Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		Dready
 * @link		http://dready.jexiste.fr/dotclear/
 */

class CI_DB_pdo_driver extends CI_DB {

	var $class_version = '0.1';

	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_connect()
	{
		$conn_id = false;
		try {
			$conn_id = new PDO ($this->database, $this->username, $this->password);
			log_message('debug', "connecting ".$this->database);
		} catch (PDOException $e) {
			log_message('debug','merde');
			log_message('error', $e->getMessage());
			if ($this->db_debug)
            		{
				$this->display_error($e->getMessage(), '', TRUE);
            		}
		}
		log_message('debug',print_r($conn_id,true));
		if ( $conn_id ) {
			log_message('debug','connection ok');
		}
		return $conn_id;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_pconnect()
	{
		try {
			$conn_id = new PDO ($this->database, $this->username, $this->password, array(PDO::ATTR_PERSISTENT => true) );
		} catch (PDOException $e) {
			log_message('error', $e->getMessage());
			if ($this->db_debug)
            		{
				$this->display_error($e->getMessage(), '', TRUE);
            		}
		}		
		return $conn_id;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */	
	function db_select()
	{
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	resource
	 */	
	function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		log_message('debug','SQL : '.$sql);
		return @$this->conn_id->query($sql);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @access	private called by execute()
	 * @param	string	an SQL query
	 * @return	string
	 */	
    function &_prep_query($sql)
    {
		return $sql;
    }


	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 * 
	 * @access	public
	 * @param	string
	 * @return	integer		 
	 */	
	function escape($str)
	{	
		switch (gettype($str))
		{
			case 'string'	:	$str = $this->escape_str($str);
				break;
			case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
				break;
			default			:	$str = ($str === NULL) ? 'NULL' : $str;
				break;
		}

		return $str;
	}




	
	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function escape_str($str)	
	{
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str); 
		}
		return $this->conn_id->quote($str);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @access	public
	 * @param	resource
	 * @return	void
	 */
	function destroy($conn_id)
	{
		$conn_id = null;
	}
	

	
	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @access	public
	 * @return	integer
	 */
	function insert_id()
	{
		return @$this->conn_id->lastInsertId();
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function count_all($table = '')
	{
		if ($table == '')
			return '0';
	
		$query = $this->query("SELECT COUNT(*) AS numrows FROM `".$table."`");
		
		if ($query->num_rows() == 0)
			return '0';

		$row = $query->row();
		return $row->numrows;
	}
	
	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access	public
	 * @return	string
	 */
	function error_message()
	{
		$infos = $this->conn_id->errorInfo();
		return $infos[2];
	}
	
	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access	public
	 * @return	integer
	 */
	function error_number()
	{
		$infos = $this->conn_id->errorInfo();
		return $infos[1];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @access	public
	 * @return	string
	 */
	function version()
	{
		return $this->conn_id->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
	}
	
	// --------------------------------------------------------------------

	/**
	 * Escape Table Name
	 *
	 * This function adds backticks if the table name has a period
	 * in it. Some DBs will get cranky unless periods are escaped
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function escape_table($table)
	{
		if (stristr($table, '.'))
		{
			$table = preg_replace("/\./", "`.`", $table);
		}
		
		return $table;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	object
	 */
	function _field_data($table)
	{
		$sql = "SELECT * FROM ".$this->escape_table($table)." LIMIT 1";
		$query = $this->query($sql);
		return $query->field_data();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	function _insert($table, $keys, $values)
	{	
		return "INSERT INTO ".$this->escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @return	string
	 */
	function _update($table, $values, $where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
	
		return "UPDATE ".$this->escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".implode(" ", $where);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @return	string
	 */	
	function _delete($table, $where)
	{
		return "DELETE FROM ".$this->escape_table($table)." WHERE ".implode(" ", $where);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access	public
	 * @return	string
	 */
	function _show_tables()
	{
		return "SELECT name from sqlite_master WHERE type='table'";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Show columnn query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function _show_columns($table = '')
	{
		// Not supported
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @access	public
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	function _limit($sql, $limit, $offset)
	{	
		if ($offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ", ";
		}
		
		return $sql."LIMIT ".$offset.$limit;
	}

}

?>