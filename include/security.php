<?php

function random($len)
{
	if (@is_readable('/dev/urandom'))
	{
		$f = fopen('/dev/urandom', 'r');
		$urandom = fread($f, $len);
		fclose($f);
	}

	$return = '';
	for ($i = 0; $i < $len; ++$i)
	{
		if (!isset($urandom))
		{
			if ($i % 2 == 0)
				mt_srand(time() % 2147 * 1000000 + (double) microtime() * 1000000);
			$rand = 48 + mt_rand() % 62;
		} else
			$rand = 48 + ord($urandom[$i]) % 64;

		if ($rand > 57)
			$rand += 7;
		if ($rand > 90)
			$rand += 6;

		/*if ($rand == 123)
			$rand = 45;
		if ($rand == 124)
			$rand = 46;*/
		$return .= chr($rand);
	}
	return $return;
}

class Bcrypt
{
	private $rounds;
	public function __construct($rounds = 12)
	{
		if (CRYPT_BLOWFISH != 1)
		{
			user_error('Bcrypt not supported in this installation', ERROR);
		}
		$this->rounds = $rounds;
	}

	public function hash($input)
	{
		$hash = crypt($input, $this->getSalt());
		if (strlen($hash) > 13)
		{
			return $hash;
		}
		return false;
	}

	public function verify($input, $existingHash)
	{
		$hash = crypt($input, $existingHash);
		return $hash === $existingHash;
	}
	
	////////////////

	private function getSalt()
	{
		$salt = sprintf('$2a$%02d$', $this->rounds);
		$bytes = $this->getRandomBytes(16);
		$salt .= $this->encodeBytes($bytes);
		return $salt;
	}

	private $randomState;	
	private function getRandomBytes($count)
	{
		$bytes = '';
		if (function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
		{ // OpenSSL slow on Win
			$bytes = openssl_random_pseudo_bytes($count);
		}

		if ($bytes === '' && is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE)
		{
			$bytes = fread($hRand, $count);
			fclose($hRand);
		}

		if (strlen($bytes) < $count)
		{
			$bytes = '';
			if ($this->randomState === null)
			{
				$this->randomState = microtime();
				if (function_exists('getmypid'))
				{
					$this->randomState .= getmypid();
				}
			}

			for ($i = 0; $i < $count; $i += 16)
			{
				$this->randomState = md5(microtime() . $this->randomState);
				if (PHP_VERSION >= '5') {
					$bytes .= md5($this->randomState, true);
				} else {
					$bytes .= pack('H*', md5($this->randomState));
				}
			}
			$bytes = substr($bytes, 0, $count);
		}
		return $bytes;
	}

	private function encodeBytes($input)
	{
		// The following is code from the PHP Password Hashing Framework
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$output = '';
		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16)
			{
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);
		return $output;
	}
}

?>