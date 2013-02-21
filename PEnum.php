<?php

namespace Poundation;

abstract class PEnum extends PObject {

	static $_constants = false;
	
	static function allConstants() {

		if (self::$_constants === false) {
			self::$_constants = new \Poundation\PDictionary();
		}
		
		$classname = get_called_class();
		$prefix = $classname;
		
		if (!isset(self::$_constants[$prefix])) {
			self::$_constants[$prefix] = new \Poundation\PDictionary();

			$reflection = false;
			try {
				$reflection = new \ReflectionClass($classname);
			} catch (\Exception $e) {}
		
			if ($reflection !== false) {
				$constants = $reflection->getConstants();
				foreach ($constants as $name=>$value) {
					if (__($name)->hasPrefix('ROLE_')) {
						self::$_constants[$prefix]->setValueForKey($value, $name);
					}
				}
			}
		}
		
		return self::$_constants[$prefix] ;
	}
	
}

?>