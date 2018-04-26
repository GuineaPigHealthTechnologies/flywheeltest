<?php

class SGRB_Input
{
	private static $source = array();

	public static function get($key, $null_val = null)
	{
		$var = @self::$source[$key];
		if ($var === null || $var == '') {
			return $null_val;
		}

		return $var;
	}

	public static function getStripSlashed($key, $null_val = null)
	{
		$var = stripslashes(@self::$source[$key]);
		if ($var === null) {
			return $null_val;
		}

		return sanitize_text_field($var);
	}

	public static function isIsset($key)
	{
		$var = @self::$source[$key];
		if ($var) {
			$issetVar = isset($var);
			if ($issetVar) {
				return 1;
			}
		}
		return $var;
	}

	public static function setSource($source)
	{
		if (is_array($source)) {
			self::$source = $source;
		}
	}

	public static function getSource()
	{
		return self::$source;
	}
}
