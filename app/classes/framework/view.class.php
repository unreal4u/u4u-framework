<?php
/**
 * Module description
 *
 * @package General
 * @version $Rev$
 * @copyright $Date$
 * @author $Author$
 * @license BSD License. Feel free to use and modify
 */

class view {
    public $pageTitle;

	public function __constructor() {
		// Nothing
	}

	/**
	 * Assigns a variable to the template
	 */
	public function assign($variableName, $variableValue=null) {
		if (!empty($variableName)) {
			$this->$variableName = $variableValue;
		}
	}

	//public function
}