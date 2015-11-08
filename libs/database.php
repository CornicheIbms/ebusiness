<?php
/**
 *@ --------------------------------------------------
 *@ LICENSE INFORMATION          					 |
 *@ Developer: Corniche van der Burgh                |
 *@ Only to be used for the IBMS course E-business   |
 *@---------------------------------------------------
**/

class Database {
	protected static $instance = null;

	public static function getConnection() 
	{
		if(self::$instance == null) 
		{
			try
			{
				self::$instance = new PDO('mysql:hostname=' . HOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
				return self::$instance;
			}
			catch(PDOException $error) {
				$error->getMessage();
			}
		}
		else
		{
			return self::$instance;
		}
	}

	public static function query($query, $type = 'return', $params = null) 
	{
		if(isset($query)) 
		{
			try 
			{
				$obj = self::getConnection();
				$handle = $obj->prepare($query);
				(($params == null) ? $handle->execute() : $handle->execute($params));
			
				if($type == 'return')
				{
					return $handle->fetch(PDO::FETCH_ASSOC);
				}
			}
			catch(PDOException $error)
			{
				$error->getMessage();
			}
		}
	}

	public static function checkColumn($query, $params = null)
	{
		if(isset($query)) 
		{
			try 
			{
				$obj = self::getConnection();
				$handle = $obj->prepare($query);
				$handle->execute($params);
				$result = $handle->fetchColumn();

				if($result) 
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			catch(PDOException $error)
			{
				$error->getMessage();
			}
		}
	}
}
