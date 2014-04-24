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
		Hooks::attach('header', -1, function () {
			Core::addDeferredScript('vendor/sha1.min.js');
			Core::addDeferredScript('form.js');
		});

		Hooks::attach('admin-header', -1, function () {
			Core::addDeferredScript('vendor/sha1.min.js');
			Core::addDeferredScript('form.js');
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
		$preg[3] = isset($preg[3]) ? $preg[3] : _('Unknown error');
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
		$preg[3] = isset($preg[3]) ? $preg[3] : _('Unknown error');
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
			'placeholder' => _('user@domain.com'),
			'preg' => array('regex' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}', 'min' => 6, 'max' => 50, 'error' => _('Invalid email address')),
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
			'preg' => array('regex' => '\+?[0-9- \(\)]*', 'min' => 10, 'max' => 20, 'error' => _('Invalid telephone number')),
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
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => _('Unknown error')),
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
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => _('Unknown error')),
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
			'preg' => array('regex' => '([a-zA-Z0-9\s_\\\\\/\[\]\(\)\|\?\+\-\*\{\},:\^=!\<\>#\$]*\/)?', 'min' => 0, 'max' => 50, 'error' => _('Must be a valid local URL without special characters')),
			'value' => ''
		);
	}

	public function addRadios($name, $title, $subtitle, $options)
	{
		$this->items[] = array(
			'type' => 'radios',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => '.*', 'min' => 1, 'max' => 50, 'error' => _('Unknown error')),
			'value' => '',
			'options' => $options
		);
	}

	public function addDropdown($name, $title, $subtitle, $options)
	{
		$this->items[] = array(
			'type' => 'dropdown',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => '.*', 'min' => 1, 'max' => 50, 'error' => _('Unknown error')),
			'value' => '',
			'options' => $options
		);
	}

	public function addArray($name, $title, $subtitle, $placeholder, $preg)
	{
		$preg[3] = isset($preg[3]) ? $preg[3] : 'Unknown error';
		$this->items[] = array(
			'type' => 'array',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'placeholder' => json_encode($placeholder),
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

			foreach ($this->items as $item) // input to session
				if (isset($item['value']))
					$_SESSION[$item['name']] = (isset($this->data[$item['name']]) ? $this->data[$item['name']] : '');
			return true;
		}
		return false;
	}

	public function validate()
	{
		if (!isset($this->data['nonce']) || !isset($_SESSION['form_nonce_' . $this->name]) || $this->data['nonce'] != $_SESSION['form_nonce_' . $this->name])
		{
			$this->errors[] = _('Form submission from external source is forbidden');
			return false;
		}

		foreach ($this->items as $k => $item)
		{
			if (isset($item['value']))
			{
				$value = (isset($this->data[$item['name']]) ? $this->data[$item['name']] : '');

				$optional = false;
				foreach ($this->optionals as $optionals)
					if (in_array($item['name'], $optionals))
					{
						$optional = true;
						foreach ($optionals as $optionals_name)
							if (isset($this->data[$optionals_name]) && strlen($this->data[$optionals_name]))
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
						$value_confirm = (isset($this->data[$item['name_confirm']]) ? $this->data[$item['name_confirm']] : '');

						if ($value != $value_confirm)
							$error = 'Does not confirm';
					}
					else if ($item['preg']['min'] > 0 && strlen($value) == 0)
						$error = _('Cannot be empty');
					else if ($item['type'] == 'password' && $value == 'tooshort')
						$error = _('Too short, must be atleast 8 characters long');
					else if ($item['type'] == 'password' && $value == 'incomplex')
						$error = _('Needs at least one lowercase, one uppercase and one numeric character');
					else if (strlen($value) < $item['preg']['min'])
						$error = _('Too short, must be atleast %s characters long', $item['preg']['min']);
					else if (strlen($value) > $item['preg']['max'])
						$error = _('Too long, must be atmost %s characters long', $item['preg']['max']);
					else if (!preg_match('/^' . $item['preg']['regex'] . '$/', $value))
						$error = $item['preg']['error'];

					if ($error)
						$this->item_errors[] = array('name' => $item['name'], 'error' => $error);
				}
			}
		}
		return count($this->item_errors) == 0;
	}

	private function clearSession()
	{
		foreach ($this->items as $item)
			if (isset($item['value']))
				unset($_SESSION[$item['name']]);
	}

	public function finish()
	{
		if (!count($this->errors) && !count($this->item_errors))
			$this->clearSession();

		echo json_encode(array(
			'errors' => $this->errors,
			'item_errors' => $this->item_errors,
			'response' => $this->response,
			'redirect' => $this->redirect
		));
		exit;
	}

	public function render()
	{
		$_SESSION['form_nonce_' . $this->name] = random();
		foreach ($this->items as $k => $item) // session to form
			if (isset($item['value']))
				$this->items[$k]['value'] = (isset($_SESSION[$item['name']]) ? $_SESSION[$item['name']] : '');

		$form = array(
			'name' => $this->name,
			'items' => $this->items,
			'submit' => $this->submit,
			'optionals' => json_encode($this->optionals),
			'nonce' => $_SESSION['form_nonce_' . $this->name]
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
		return (isset($this->data[$this->name . '_' . $name])
					? $this->data[$this->name . '_' . $name]
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
		$this->item_errors[] = array('name' => $this->name . '_' . $name, 'error' => $error);
	}

	public function appendError($error)
	{
		$this->errors[] = $error;
	}
}
