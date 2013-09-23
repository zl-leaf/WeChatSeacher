<?php 
	/**
	* 教师实体类
	*/
	class Teacher {
		var $position;
		var $name;
		var $phone;
		var $email;
		var $office;
		function __construct() {
		}

		public static function parse(DOMElement $row) {
			
			$cells_list = $row->getElementsByTagName('td'); 
			if($cells_list->length != 5) {
				return null;
			}

			$teacher = new Teacher();
			
			foreach ($cells_list as $cell) {
				$div_list = $cell->getElementsByTagName('div');

				$div = $div_list->item(0);
				
				$class = $div->getAttribute('class');
				if (strpos($class, 'ofc') !== false) {
					$teacher->office = $div->nodeValue;
				}
				else if (strpos($class, 'name') !== false) {
					$teacher->name = $div->nodeValue;
				}
				else if (strpos($class, 'type') !== false) {
					$teacher->position = $div->nodeValue;
				}
				else if (strpos($class, 'max-col') !== false) {
					$teacher->email = $div->nodeValue;
				}
				else {
					$teacher->phone = $div->nodeValue;
				}
				
			}
			
			return $teacher;
			
		}
	}
 ?>