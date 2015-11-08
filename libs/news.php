<?php
/**
 *@ --------------------------------------------------
 *@ LICENSE INFORMATION          					 |
 *@ Developer: Corniche van der Burgh                |
 *@ Only to be used for the IBMS course E-business   |
 *@---------------------------------------------------
**/

class News 
{
	public function displayLatest()
	{
		$handle = Database::query("SELECT * FROM news ORDER BY DESC LIMIT 1", "return");
		return $handle;
	}

	public function newsList()
	{
		try
		{
			$handle = Database::getConnection();
			$obj = $handle->prepare("SELECT * FROM news ORDER BY DESC");
			$result = $obj->fetchAll(PDO::FETCH_ASSOC);

			return $result;
		}
		catch(PDOException $error)
		{
			$error->getMessage();
		}
	}
}
?>
