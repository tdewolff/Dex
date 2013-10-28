<?php

class Form
{
	private $name = '';
	private $data = array();
	private $items = array();
	private $errors = array();

	private $mode = false;
	private $method = 'POST';
	private $redirect = '';

	public function __construct($name)
	{
		Hooks::attach('header', -1, function() {
			Core::addDeferredScript('sha1.js');
			Core::addDeferredScript('form.defer.js');
		});

		Hooks::attach('admin_header', -1, function() {
			Core::addDeferredScript('sha1.js');
			Core::addDeferredScript('form.defer.js');
		});

		$this->name = $name;
	}

	////////////////////////////////////////////////////////////////

	public function makeInline() {
		$this->mode = 'inline';
	}

	public function makeCompact() {
		$this->mode = 'compact';
	}

	public function usePUT($redirect = '') {
		$this->method = 'PUT';
		$this->redirect = $redirect;
	}

	public function usePOST($redirect = '') {
		$this->method = 'POST';
		$this->redirect = $redirect;
	}

	public function useDELETE($redirect = '') {
		$this->method = 'DELETE';
		$this->redirect = $redirect;
	}

	public function setRedirect($redirect) {
		$this->redirect = $redirect;
	}

	////////////////////////////////////////////////////////////////

	public function addSeparator()
	{
		$this->items[] = array(
			'type' => 'separator'
		);
	}

	public function addSection($title, $text)
	{
		$this->items[] = array(
			'type' => 'section',
			'title' => $title,
			'text' => $text
		);
	}

	public function addText($name, $title, $subtitle, $placeholder, $preg)
	{
		$this->items[] = array(
			'type' => 'text',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => $placeholder,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
			'value' => ''
		);
	}

	public function addMultilineText($name, $title, $subtitle, $placeholder, $preg)
	{
		$this->items[] = array(
			'type' => 'multiline_text',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => $placeholder,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
			'value' => ''
		);
	}

	public function addEmail($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'email',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => 'user@domain.com',
			'preg' => array('regex' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}', 'min' => 6, 'max' => 50, 'error' => 'Invalid emailaddress'),
			'value' => ''
		);
	}

	public function addTel($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'tel',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => '+99 (9) 9999-9999',
			'preg' => array('regex' => '\+?[0-9- \(\)]*', 'min' => 10, 'max' => 20, 'error' => 'Invalid telephone number'),
			'value' => ''
		);
	}

	public function addPassword($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'password',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => 'password',
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => 'Unknown error'),
			'value' => ''
		);
	}

	public function addPasswordConfirm($name, $name_confirm, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'password',
			'name' => $this->name . '_' . $name,
			'name_confirm' => $this->name . '_' . $name_confirm,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => 'password',
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => 'Unknown error'),
			'value' => ''
		);
	}

	public function addMarkdown($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'markdown',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => '(?s)(.*)', 'min' => 0, 'max' => 65535, 'error' => 'Unknown error'),
			'value' => ''
		);
	}

	public function addDropdown($name, $title, $subtitle, $options)
	{
		$this->items[] = array(
			'type' => 'dropdown',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => '.*', 'min' => 0, 'max' => 50, 'error' => 'Invalid dropdown item'),
			'value' => '',
			'options' => $options
		);
	}

	public function addArray($name, $title, $subtitle, $preg)
	{
		$this->items[] = array(
			'type' => 'array',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
			'value' => ''
		);
	}

	public function addParameters($name, $title, $subtitle, $preg)
	{
		$this->items[] = array(
			'type' => 'parameters',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
			'value' => ''
		);
	}

	public function addSubmit($name, $title, $response_success = '', $reponse_error = '')
	{
		$this->items[] = array(
			'type' => 'submit',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'response_success' => $response_success,
			'response_error' => $reponse_error
		);
	}

	public function allowEmpty($names)
	{
		if (is_array($names))
		{
			foreach ($names as $name)
				foreach ($this->items as $k => $item)
					if (isset($item['value']) && $item['name'] == $this->name . '_' . $name)
						$this->items[$k]['emptyTogether'] = array($this->name . '_' . $name);
		}
		else
			foreach ($this->items as $k => $item)
				if (isset($item['value']) && $item['name'] == $this->name . '_' . $names)
					$this->items[$k]['emptyTogether'] = array($this->name . '_' . $names);
	}

	public function allowEmptyTogether($names)
	{
		foreach ($names as $k => $name)
			$names[$k] = $this->name . '_' . $name;

		foreach ($this->items as $k => $item)
			if (isset($item['value']) && in_array($item['name'], $names))
				$this->items[$k]['emptyTogether'] = $names;
	}

	////////////////////////////////////////////////////////////////

	private function retrieveInput()
	{
		$this->data = Common::getMethodData();
	}

	public function submittedBy($name)
	{
		$this->retrieveInput();

		return isset($_SESSION[$this->name . '_salt']) && isset($this->data[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']]);
	}

	public function validateInput()
	{
		$valid = true;
		foreach ($this->items as $k => $item)
		{
			if (isset($item['value']))
			{
				$allowEmpty = false;
				if (isset($item['emptyTogether']))
				{
					$allowEmpty = true;
					foreach ($item['emptyTogether'] as $name)
					{
						if (isset($this->data[$name . '_' . $_SESSION[$this->name . '_salt']]) && strlen($this->data[$name . '_' . $_SESSION[$this->name . '_salt']]))
						{
							$allowEmpty = false;
							break;
						}
					}
				}

				$fullname = $item['name'] . '_' . $_SESSION[$this->name . '_salt'];
				$value = (isset($this->data[$fullname]) ? $this->data[$fullname] : '');

				if (!$allowEmpty || ($allowEmpty && strlen($value)))
				{
					if (isset($item['name_confirm']))
					{
						// '_confirm' is the first value entered, we are now checking it against the second (current) item
						$fullname_confirm = $item['name_confirm'] . '_' . $_SESSION[$this->name . '_salt'];
						$value_confirm = (isset($this->data[$fullname_confirm]) ? $this->data[$fullname_confirm] : '');

						if ($value != $value_confirm)
							$this->items[$k]['error'] = 'Does not confirm';
					}
					else if ($item['preg']['min'] > 0 && strlen($value) == 0)
						$this->items[$k]['error'] = 'Cannot be empty';
					else if (strlen($value) < $item['preg']['min'])
						$this->items[$k]['error'] = 'Too short, must be atleast ' . $item['preg']['min'] . ' characters long';
					else if (strlen($value) > $item['preg']['max'])
						$this->items[$k]['error'] = 'Too long, must be atmost ' . $item['preg']['max'] . ' characters long';
					else if (!preg_match('/^' . $item['preg']['regex'] . '$/', $value))
						$this->items[$k]['error'] = $item['preg']['error'];
				}

				if (isset($this->items[$k]['error']))
					$valid = false;
			}
		}
		return $valid;
	}

	private function inputToSession()
	{
		foreach ($this->items as $item)
			if (isset($item['value']))
				$_SESSION[$item['name']] = (isset($this->data[$item['name'] . '_' . $_SESSION[$this->name . '_salt']]) ? $this->data[$item['name'] . '_' . $_SESSION[$this->name . '_salt']] : '');
	}

	private function sessionToForm()
	{
		foreach ($this->items as $k => $item)
			if (isset($item['value']))
				$this->items[$k]['value'] = (isset($_SESSION[$item['name']]) ? $_SESSION[$item['name']] : '');
	}

	public function unsetSession()
	{
		unset($_SESSION[$this->name . '_salt']);
		foreach ($this->items as $item)
			if (isset($item['value']))
				unset($_SESSION[$item['name']]);
	}

	public function returnJSON()
	{
		if (isset($_SESSION[$this->name . '_salt'])) // if not set, unsetSession() has been called
			$this->inputToSession();

		echo json_encode(array(
			'items' => $this->items,
			'errors' => $this->errors,
			'redirect' => $this->redirect
		));
		exit;
	}

	// set value of input element
	public function set($name, $value)
	{
		$_SESSION[$this->name . '_' . $name] = $value;
	}

	// get value of input element, use after verify()
	public function get($name)
	{
		return (isset($this->data[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']])
		            ? $this->data[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']]
						: false);
	}

	// set error for input element
	public function setError($name, $error)
	{
		foreach ($this->items as $k => $item)
			if (isset($item['value']) && $item['name'] == $this->name . '_' . $name)
				$this->items[$k]['error'] = $error;
	}

	// add error for entire form
	public function appendError($error)
	{
		$this->errors[] = $error;
	}

	public function setResponse($response)
	{
		$this->response = $response;
	}

	public function renderForm()
	{
		$this->sessionToForm();
		$_SESSION[$this->name . '_salt'] = random(8);

		// make handy variables for js and css
		foreach ($this->items as $k => $item)
			if (isset($item['value']))
			{
				if (!isset($item['unused']))
				{
					$this->items[$k]['unused'] = true;
					if (strlen($item['value']) || $item['preg']['min'] > 0)
						$this->items[$k]['unused'] = false;
					else if (!isset($item['emptyTogether']))
						$this->items[$k]['emptyTogether'] = array($item['name']);

					if (isset($this->items[$k]['emptyTogether']))
					{
						// find non-empty in emptyTogether
						$unused = true;
						foreach ($this->items as $k2 => $item2)
							if (isset($item2['value']) && strlen($item2['value']) && in_array($item2['name'], $this->items[$k]['emptyTogether']))
							{
								$unused = false;
								break;
							}

						$emptyTogetherArray = $this->items[$k]['emptyTogether'];
						foreach ($emptyTogetherArray as $k2 => $item2)
							$emptyTogetherArray[$k2] = $item2 . '_' . $_SESSION[$this->name . '_salt'];
						$emptyTogetherArray = json_encode($emptyTogetherArray);

						// set others in emptyTogether to the same!
						foreach ($this->items as $k2 => $item2)
							if (isset($item2['value']) && in_array($item2['name'], $this->items[$k]['emptyTogether']))
							{
								$this->items[$k2]['unused'] = $unused;
								$this->items[$k2]['emptyTogetherArray'] = $emptyTogetherArray;
							}
					}
				}

				if (!isset($this->items[$k]['emptyTogetherArray']))
					$this->items[$k]['emptyTogetherArray'] = json_encode(array());
			}

		$form = array(
			'name' => $this->name,
			'salt' => $_SESSION[$this->name . '_salt'],
			'items' => $this->items,
			'errors' => $this->errors,
			'mode' => $this->mode,
			'method' => $this->method
		);

		include('core/templates/form.tpl');
	}
}

?>
