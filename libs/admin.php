<?php
/**
 *@ --------------------------------------------------
 *@ LICENSE INFORMATION          					 |
 *@ Developer: Corniche van der Burgh                |
 *@ Only to be used for the IBMS course E-business   |
 *@---------------------------------------------------
**/
class Admin extends Products {

	public function addNews($title, $body, $author) 
	{
		$Title = strip_tags($title);
		$Body = strip_tags($body);
		$Date = date("d-m-y");

		database::query("INSERT INTO news (title, body, author, date) VALUES (?, ?, ?, ?"), "insert", array($Title, $Body, $author, $Date));
	}

	public function deleteNews($id)
	{
		Database::query("DELETE * FROM news WHERE ID = ?")->execute(array($id));
	}

	public function setRank($email, $level) {
		/* Level 0 = user, level 1 = support, level 2 = admin */
		database::query("UPDATE users SET admin = ? WHERE email = ?", "update", array($level, $email));
	}

	public function setCoupon($code, $discount, $time = 7)
	{
		/* time stands for time in days, discount is a whole percentage ex: 15 */
		$Code = Antiexploit::detectExploit($code, $_SERVER['REMOTE_ADDR']);
		$Discount = Antiexploit::detectNumericExploit($discount, $_SERVER['REMOTE_ADDR']);
		$Time = Antiexploit::detectNumericExploit($time, $_SERVER['REMOTE_ADDR']);
		$current_date = Date("d-m-y");
		$expires = Date("d-m-y", strtotime($current_date. '+ '. $Time . ' days'));

		Database::query("INSERT INTO coupons (code, discount, expires) VALUES (?, ?, ?)", "insert", array($Code, $Discount, $expires));
	}

	public function listCoupons() 
	{
		/* Display all current coupons */
		try
		{
			$handle = Database::getConnection();
			$current_date = date("d-m-y");
			$obj = $handle->prepare("SELECT * FROM coupons WHERE expires <= $current_date");
			$data = $obj->fetchAll(PDO::FETCH_ASSOC);

			foreach($data as $val)
			{
				echo '<p>Coupon code: ' . $val['code'] . '<br>';
				echo 'Discount: ' . $val['discount'] . '%<br>';
				echo 'Expires: ' . $val['expires'] . '</p>';
			}
		}
		catch(PDOException $error)
		{
			$error->getMessage();
		}
	}

	public function deleteCoupon($code)
	{
		/*Delete a specific coupon */
		$Code = Antiexploit::detectExploit($code);
		Database::query("DELETE FROM coupons WHERE code = ?", "delete", array($Code));
	}

	public function extendCoupon($code, $time = 7)
	{
		/* Extend a current promotion for a week */
		$Code = Antiexploit::detectExploit($code, $_SERVER['REMOTE_ADDR']);
		$Time = Antiexploit::detectNumericExploit($time, $_SERVER['REMOTE_ADDR']);
		$obj = Database::query("SELECT * FROM coupons WHERE code = ?", "return", array($Code));

		/* extend the current date */
		$new_date = date("d-m-Y", strtotime($obj['expires'] . ' + '.$Time.' days'));

		/* Update the database */
		$handle = Database::query("UPDATE coupons SET expires = ? WHERE code = ?", "update", array($new_date, $Code));
	}
}
?>
