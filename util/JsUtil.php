<?php

/**
 * Class JsUtil
 * Util functions to use with javascript.
 */
class JsUtil
{
    /**
 	 * Parses the php global GET varaibles into a javscript object.
	 * @param string $jsObjectName name to use for the javscript object.
	 * @return string The javascript code that contains the GET variables converted into a javascript object.
	 */
	static public function PhpGetVarsToJs($jsObjectName = "_get") {
		$js = "var $jsObjectName={};";
		foreach($_GET as $name => $value)
		{
			$js .= "$jsObjectName.$name=\"$value\";";
		}
		return $js;
	}
}