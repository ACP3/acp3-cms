<?php
namespace ACP3\Core\WYSIWYG;


/**
 * Description of Textarea
 *
 * @author goratsch
 */
class Textarea extends AbstractWYSIWYG {
	public function __construct($id, $name, $value = '', $toolbar = '', $advanced = false, $height = '') {
		$this->id = $id;
		$this->name = $name;
		$this->value = $value;
		$this->advanced = (bool) $advanced;
		$this->config['toolbar'] = $toolbar === 'simple' ? 'Basic' : 'Full';
		$this->config['height'] = $height . 'px';
	}

	protected function configure() {
		return;
	}

	public function display() {
		$out = '';
		if (\ACP3\Core\Modules::isActive('emoticons') === true) {
			$out.= \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsList($this->id);
		}
		$out.= '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="50" rows="6" class="span6">' . $this->value . '</textarea>';
		return $out;

	}
}