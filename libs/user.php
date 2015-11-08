<?php
/**
 *@ --------------------------------------------------
 *@ LICENSE INFORMATION          					 |
 *@ Developer: Corniche van der Burgh                |
 *@ Only to be used for the IBMS course E-business   |
 *@---------------------------------------------------
**/

class User 
{
	 public function betterCrypt($input, $rounds = 7)
     {
        $salt = "";
        $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

        for($i=0; $i < 22; $i++) 
        {
                $salt .= $salt_chars[array_rand($salt_chars)];
        }
        
        return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
    }

    public function hashPassword($password)
    {
        return $this->better_crypt($password);
    }

    public function logIn($email, $password, $security_token = null) 
    {
    	$Email = trim(strip_tags($email), " ");
    	$Password = trim(strip_tags($password), " ");

    	if(isset($Email) && isset($Password)) 
    	{
    		Antiexploit::detectExploit($email, $_SERVER['REMOTE_ADDR']);
    		Antiexploit::detectExploit($password, $_SERVER['REMOTE_ADDR']);

    		/* First check if the user exists */
    		$search = Database::checkColumn("SELECT * FROM users WHERE email = ?", array($Email));

    		if(!$search)
    		{
    			return false;
    		}

    		/* Get the users information to perform multiple checks */
    		$result = Database::query("SELECT * FROM users WHERE email = ?", "return", array($Email));

    		if($result['id_code'] !== null)
    		{
    			Antiexploit::detectExploit($security_token);
    			if($security_token == $result['security_token'] && crypt($Password, $result['password']) == $result['password'])
    			{
    				return true;
    			}
    			else
    			{
    				return false;
    			}
    		}
    		else
    		{
    			if(crypt($Password, $result['password']) == $result['password'])
    			{
    				return true;
    			}
    			else
    			{
    				return false;
    			}
    		}
    	}
    }

    public function register($email, $password, $password_again, $token = null, $street, $number, $zip, $ip) 
    {
    	$Email = Antiexploit::detectExploit($email, $ip);
    	$Password = Antiexploit::detectExploit($password, $ip);
    	$PasswordAgain = Antiexploit::detectExploit($password_again, $ip);
    	$Token = (($token !== null) ? Antiexploit::detectNumericExploit($token, $ip) : null);
    	$Street = strip_tags($street);
    	$Number = Antiexploit::detectNumericExploit($number, $ip);
    	$Zip = Antiexploit::detectExploit($zip, $ip);

    	/* first check if user exists */
    	$handle = Database::checkColumn("SELECT * FROM users WHERE email = ?", array($email));

    	if($handle == true)
    	{
    		return false;
    	}
    	else
    	{
    		/* user doesnt exist, lets insert into the database */
    		if($Password == $PasswordAgain)
    		{
    			$_pass = $this->betterCrypt($Password);
    			Database::query("INSERT INTO users (email, password, id_code, street_name, house_number, zip_code, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)", "insert", array($Email, $_pass, $Token, $Street, $Number, $Zip, $ip));
    			return true;
    		}
    	}
    }

    public function modify($email, $row, $update, $ip)
    {
        $Email = Antiexploit::detectExploit($email, $ip);
        $Update = Antiexploit::detectExploit($update, $ip);

        Database::query("UPDATE users SET $row = ? WHERE email = ?", "update", array($Update, $Email));
    }

    public function isAdmin($email)
    {
        
        $obj = Database::query("SELECT * FROM users WHERE email = ?", "return", array($email));

        if($obj['admin'] == 2) {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>
