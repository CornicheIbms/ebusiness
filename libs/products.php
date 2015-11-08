<?php
/**
 *@ --------------------------------------------------
 *@ LICENSE INFORMATION          					 |
 *@ Developer: Corniche van der Burgh                |
 *@ Only to be used for the IBMS course E-business   |
 *@---------------------------------------------------
**/

class Products {

	public function addProduct($name, $brand, $description, $price) {
		$Name = Antiexploit::detectExploit($name, $_SERVER['REMOTE_ADDR']);
		$Brand = Antiexploit::detectExploit($brand, $_SERVER['REMOTE_ADDR']);
		$Desc = strip_tags($description);
		$Price = strip_tags($price);

		/* first see if this product already exists */
		$handle1 = Database::checkColumn("SELECT * FROM products WHERE product_name = ? AND product_brand = ?", array($Name, $Brand));
		if($handle1) 
		{
			return false;
		}
		else
		{
			/* product doesnt exist, set up a new handle to insert into the database */
			$handle2 = Database::query("INSERT INTO products (product_name, product_brand, product_description, price) VALUES (?, ?, ?, ?)", "insert", array($Name, $Brand, $Desc, $Price));
			return true;
		}
	}

	public function modifyProduct($name, $brand, $rowToChange, $updatedValue) {
		$Name = Antiexploit::detectExploit($name, $_SERVER['REMOTE_ADDR']);
		$Brand = Antiexploit::detectExploit($brand, $_SERVER['REMOTE_ADDR']);
		$Row = Antiexploit::detectExploit($rowToChange, $_SERVER['REMOTE_ADDR']);
		$Val = Antiexploit::detectExploit($updatedValue, $_SERVER['REMOTE_ADDR']);

		Database::query("UPDATE products SET $Row = ? WHERE product_name = ? AND product_brand = ?", "update", array($Val, $Name, $Brand));

	}

	public function setInventory($name, $brand, $level = 0)
	{
		/* 1 means in stock and 0 means out of stock */
		Database::query("UPDATE products SET in_stock = ? WHERE product_name = ? AND product_brand = ?", "update", array($level, $name, $brand));
	}

	public function deleteProduct($name, $brand) {
		Database::query("DELETE FROM products WHERE product_name = ? AND product_brand = ?", "delete", array($name, $brand));
	}

	public function displayProducts() {
		try
		{
			$con = Database::getConnection();
			$obj = $con->prepare("SELECT * FROM products");
			$obj->execute();
			$result = $obj->fetchAll(PDO::FETCH_ASSOC);

			foreach ($result as $var) {
				echo '<p>Name: ' . $var['product_name'] . '<br>';
				echo 'By: ' . $var['product_brand'] . '<br>';
				echo 'Desc: ' . nl2br($var['product_description']) . '<br>';
				echo 'Price: ' . $var['price'] . '<br></p>';
				echo 'Status: ' . (($var['in_stock'] == 1) ? 'Available!' : 'Unavailable');
			}
		}
		catch(PDOException $error)
		{
			$error->getMessage();
		}
	}
}

?>
