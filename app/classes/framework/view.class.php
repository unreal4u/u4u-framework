<?php
/**
 * Module description
 *
 * @package General
 * @author Camilo Sperberg
 * @license BSD License. Feel free to use and modify
 */
class view {
    /**
     * Constructor
     */
    public function __construct() {
    	#$this->smarty =
    }

	/**
	 * Assigns a variable to the template
	 *
	 * @param string $variableName
	 * @param mixed $variableValue
	 */
	public function assign($variableName, $variableValue=null) {
		if (!empty($variableName)) {
			$this->$variableName = $variableValue;
		}
	}

	/**
	 * Does the definite work of rendering the template
	 *
	 * @param unknown $module
	 */
	public function renderTemplate($module) {
	    ob_start();
	    include(USER_SPACE.'views/'.$module['view']);
	    $contents = ob_get_contents();
	    ob_end_clean();
	    return $contents;
	}
}