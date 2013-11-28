<?php

class Form
{
	private $name = '';
	private $items = array();
	private $submit = false;
	private $response = array('success' => '', 'errors' => '');
	private $optionals = array();

	private $data = array();
	private $errors = array();
	private $item_errors = array();
	private $redirect = '';

	public function __construct($name)
	{
		Hooks::attach('header', -1, function() {
			Core::addDeferredScript('vendor/sha1.min.js');
			Core::addDeferredScript('include/form.min.js');
		});

		Hooks::attach('admin-header', -1, function() {
			Core::addDeferredScript('vendor/sha1.min.js');
			Core::addDeferredScript('include/form.min.js');
		});

		$this->name = $name;
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
		$preg[3] = isset($preg[3]) ? $preg[3] : 'Unknown error';
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
		$preg[3] = isset($preg[3]) ? $preg[3] : 'Unknown error';
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

	public function addLinkUrl($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'text',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => '',
			'preg' => array('regex' => '([a-zA-Z0-9\s_\\\\\/\[\]\(\)\|\?\+\-\*\{\},:\^=!\<\>#\$]*\/)?', 'min' => 0, 'max' => 50, 'error' => 'Must be valid local URL'),
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
			'preg' => array('regex' => '.*', 'min' => 1, 'max' => 50, 'error' => 'Invalid dropdown item'),
			'value' => '',
			'options' => $options
		);
	}

	public function addArray($name, $title, $subtitle, $preg)
	{
		$preg[3] = isset($preg[3]) ? $preg[3] : 'Unknown error';
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
		$preg[3] = isset($preg[3]) ? $preg[3] : 'Unknown error';
		$this->items[] = array(
			'type' => 'parameters',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
			'value' => ''
		);
	}

	////////////////////////////////////////////////////////////////

	public function setSubmit($title)
	{
		$this->submit = $title;
	}

	public function setResponse($success, $error)
	{
		$this->response = array(
			'success' => $success,
			'error' => $error
		);
	}

	public function setRedirect($redirect) {
		$this->redirect = $redirect;
	}

	public function setId($name, $id)
	{
		foreach ($this->items as $i => $item)
			if (isset($item['name']) && $item['name'] == $this->name . '_' . $name)
			{
				$this->items[$i]['id'] = $id;
				break;
			}
	}

	public function setClass($name, $class)
	{
		foreach ($this->items as $i => $item)
			if (isset($item['name']) && $item['name'] == $this->name . '_' . $name)
			{
				$this->items[$i]['class'] = $class;
				break;
			}
	}

	////////////////////////////////////////////////////////////////

	public function optional($names)
	{
		if (is_array($names))
			foreach ($names as $name)
				$this->optionals[] = array($this->name . '_' . $name);
		else
			$this->optionals[] = array($this->name . '_' . $name);
	}

	public function optionalTogether($names)
	{
		foreach ($names as $k => $name)
			$names[$k] = $this->name . '_' . $name;
		$this->optionals[] = $names;
	}

	////////////////////////////////////////////////////////////////

	public function submitted()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			parse_str(file_get_contents("php://input"), $this->data); // retrieve input

			$_SESSION[$this->name . '_salt'] = $this->data['salt'];
			foreach ($this->items as $item) // input to session
				if (isset($item['value']))
					$_SESSION[$item['name']] = (isset($this->data[$item['name'] . '_' . $_SESSION[$this->name . '_salt']]) ? $this->data[$item['name'] . '_' . $_SESSION[$this->name . '_salt']] : '');
			return true;
		}
		return false;
	}

	public function validate()
	{
		foreach ($this->items as $k => $item)
		{
			if (isset($item['value']))
			{
				$name = $item['name'] . '_' . $_SESSION[$this->name . '_salt'];
				$value = (isset($this->data[$name]) ? $this->data[$name] : '');

				$optional = false;
				foreach ($this->optionals as $optionals)
					if (in_array($item['name'], $optionals))
					{
						$optional = true;
						foreach ($optionals as $optionals_name)
							if (isset($this->data[$optionals_name . '_' . $_SESSION[$this->name . '_salt']]) && strlen($this->data[$optionals_name . '_' . $_SESSION[$this->name . '_salt']]))
							{
								$optional = false;
								break;
							}
					}

				if (!$optional)
				{
					$error = false;
					if (isset($item['name_confirm']))
					{
						// '_confirm' is the first value entered, we are now checking it against the second (current) item
						$value_confirm = (isset($this->data[$item['name_confirm'] . '_' . $_SESSION[$this->name . '_salt']]) ? $this->data[$item['name_confirm'] . '_' . $_SESSION[$this->name . '_salt']] : '');

						if ($value != $value_confirm)
							$error = 'Does not confirm';
					}
					else if ($item['preg']['min'] > 0 && strlen($value) == 0)
						$error = 'Cannot be empty';
					else if (strlen($value) < $item['preg']['min'])
						$error = 'Too short, must be atleast ' . $item['preg']['min'] . ' characters long';
					else if (strlen($value) > $item['preg']['max'])
						$error = 'Too long, must be atmost ' . $item['preg']['max'] . ' characters long';
					else if (!preg_match('/^' . $item['preg']['regex'] . '$/', $value))
						$error = $item['preg']['error'];

					if ($error)
						$this->item_errors[] = array('name' => $name, 'error' => $error);
				}
			}
		}
		return count($this->item_errors) == 0;
	}

	public function clearSession()
	{
		unset($_SESSION[$this->name . '_salt']);
		foreach ($this->items as $item)
			if (isset($item['value']))
				unset($_SESSION[$item['name']]);
	}

	public function finish()
	{
		echo json_encode(array(
			'errors' => $this->errors,
			'item_errors' => $this->item_errors,
			'redirect' => $this->redirect
		));
		exit;
	}

	public function render()
	{
		$_SESSION[$this->name . '_salt'] = Common::random(8);

		foreach ($this->items as $k => $item) // session to form
			if (isset($item['value']))
				$this->items[$k]['value'] = (isset($_SESSION[$item['name']]) ? $_SESSION[$item['name']] : '');

		$form = array(
			'name' => $this->name,
			'salt' => $_SESSION[$this->name . '_salt'],
			'items' => $this->items,
			'submit' => $this->submit,
			'response' => $this->response,
			'optionals' => json_encode($this->optionals)
		);

		include('core/templates/include/form.tpl');
	}

	////////////////////////////////////////////////////////////////

	// set value of input element
	public function set($name, $value)
	{
		$_SESSION[$this->name . '_' . $name] = $value;
	}

	public function setAll($all)
	{
		foreach ($all as $name => $value)
			$_SESSION[$this->name . '_' . $name] = $value;
	}

	// get value of input element, use after validate()
	public function get($name)
	{
		return (isset($this->data[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']])
		            ? $this->data[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']]
						: false);
	}

	public function getAll()
	{
		$all = array();
		foreach ($this->items as $item)
			if (isset($item['value']))
			{
				$name = substr($item['name'], strlen($this->name . '_'));
				$all[$name] = self::get($name);
			}
		return $all;
	}

	public function setError($name, $error)
	{
		$this->item_errors[] = array('name' => $this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt'], 'error' => $error);
	}

	public function appendError($error)
	{
		$this->errors[] = $error;
	}
}

?>
