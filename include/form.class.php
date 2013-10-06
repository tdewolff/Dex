<?php

class Form
{
	private $name = '';
	private $title = '';
	private $text = '';
	private $items = array();
	private $response = '';
	private $errors = array();
	private $mode = false;

	private static $inputTypes = array('name');

	public function __construct($name, $title, $text = '')
	{
		Hooks::attach('header', -1, function() {
			Dexterous::addDeferredScript('resources/scripts/sha1.js');
			Dexterous::addDeferredScript('resources/scripts/form.js');
		});

		Hooks::attach('admin_header', -1, function() {
			Dexterous::addDeferredScript('resources/scripts/sha1.js');
			Dexterous::addDeferredScript('resources/scripts/form.js');
		});

		$this->name = $name;
		$this->title = $title;
		$this->text = $text;
	}

	public function makeInline()
	{
		$this->mode = 'inline';
	}

	public function makeCompact()
	{
		$this->mode = 'compact';
	}

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

	public function addText($name, $title, $subtitle, $preg)
	{
		$this->items[] = array(
			'type' => 'text',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => $preg[0], 'min' => $preg[1], 'max' => $preg[2], 'error' => $preg[3]),
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

	public function addPassword($name, $title, $subtitle)
	{
		$this->items[] = array(
			'type' => 'password',
			'name' => $this->name . '_' . $name,
			'title' => $title,
			'subtitle' => $subtitle,
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => 'Must be alphanumeric'),
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
			'preg' => array('regex' => '[a-zA-Z0-9]*', 'min' => 40, 'max' => 40, 'error' => 'Must be alphanumeric'),
			'value' => ''
		);
	}

	public function addSubmit($name, $title)
	{
		$this->items[] = array(
			'type' => 'submit',
			'name' => $this->name . '_' . $name,
			'title' => $title
		);
	}

	public function allowEmptyTogether($names)
	{
		foreach ($names as $k => $name)
		{
			$names[$k] = $this->name . '_' . $name;
		}

		foreach ($this->items as $k => $item)
		{
			if (isset($item['value']) && in_array($item['name'], $names))
			{
				$this->items[$k]['emptyTogether'] = $names;
			}
		}
	}

	public function submittedBy($name)
	{
		return isset($_SESSION[$this->name . '_salt']) && isset($_POST[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']]);
	}

	public function postToSession()
	{
		foreach ($this->items as $item)
		{
			if (isset($item['value']))
			{
				$_SESSION[$item['name']] = (isset($_POST[$item['name'] . '_' . $_SESSION[$this->name . '_salt']]) ? $_POST[$item['name'] . '_' . $_SESSION[$this->name . '_salt']] : '');
			}
		}
	}

	public function verifyPost()
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
						if ((isset($_POST[$name . '_' . $_SESSION[$this->name . '_salt']]) && strlen($_POST[$name . '_' . $_SESSION[$this->name . '_salt']]))
						 || (isset($_POST[$name . '_' . $_SESSION[$this->name . '_salt'] . '_hash']) && strlen($_POST[$name . '_' . $_SESSION[$this->name . '_salt'] . '_hash'])))
						{
							$allowEmpty = false;
							break;
						}
					}
				}

				$fullname = $item['name'] . '_' . $_SESSION[$this->name . '_salt'] . ($item['type'] == 'password' ? '_hash' : '');
				$value = (isset($_POST[$fullname]) ? $_POST[$fullname] : '');

				if (isset($item['name_confirm']))
				{
					// '_confirm' is the first value entered, we are now checking it against the second (current) item
					$fullname_confirm = $item['name_confirm'] . '_' . $_SESSION[$this->name . '_salt'] . ($item['type'] == 'password' ? '_hash' : '');
					$value_confirm = (isset($_POST[$fullname_confirm]) ? $_POST[$fullname_confirm] : '');

					if ($value != $value_confirm)
					{
						$this->items[$k]['error'] = 'Does not confirm';
					}
				}
				else if ($item['preg']['min'] > 0 && !strlen($value))
				{
					if (!$allowEmpty)
					{
						$this->items[$k]['error'] = 'Cannot be empty';
					}
				}
				else if (strlen($value) < $item['preg']['min'])
				{
					$this->items[$k]['error'] = 'Too short, must be atleast ' . $item['preg']['min'] . ' characters long';
				}
				else if (strlen($value) > $item['preg']['max'])
				{
					$this->items[$k]['error'] = 'Too long, must be atmost ' . $item['preg']['max'] . ' characters long';
				}
				else if (!preg_match('/^' . $item['preg']['regex'] . '$/', $value))
				{
					$this->items[$k]['error'] = $item['preg']['error'];
				}

				if (isset($this->items[$k]['error']))
				{
					$valid = false;
				}
			}
		}
		return $valid;
	}

	public function set($name, $value)
	{
		$_SESSION[$this->name . '_' . $name] = $value;
	}

	public function get($name)
	{
		return (isset($_POST[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']])
		            ? $_POST[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt']]
				    : (isset($_POST[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt'] . '_hash'])
					       ? $_POST[$this->name . '_' . $name . '_' . $_SESSION[$this->name . '_salt'] . '_hash']
						   : false));
	}

	public function appendError($error)
	{
		$this->errors[] = $error;
	}

	public function setError($name, $error)
	{
		foreach ($this->items as $k => $item)
			if (isset($item['value']) && $item['name'] == $this->name . '_' . $name)
				$this->items[$k]['error'] = $error;
	}

	public function setResponse($response)
	{
		$this->response = $response;
	}

	public function unsetSession()
	{
		unset($_SESSION[$this->name . '_salt']);
		foreach ($this->items as $item)
			if (isset($item['value']))
				unset($_SESSION[$item['name']]);
	}

	public function sessionToForm()
	{
		foreach ($this->items as $k => $item)
			if (isset($item['value']))
				$this->items[$k]['value'] = (isset($_SESSION[$item['name']]) ? $_SESSION[$item['name']] : '');
	}

	public function renderForm()
	{
		$_SESSION[$this->name . '_salt'] = random(8);

		// make handy variables for js and css
		foreach ($this->items as $k => $item)
		{
			$this->items[$k]['jsEmptyTogether'] = '[]';
			$this->items[$k]['cssEmptyTogether'] = false;

			if (isset($item['emptyTogether']) && count($item['emptyTogether']))
			{
				$js_empty_together = $item['emptyTogether'];
				foreach ($js_empty_together as $k_js_empty => $v_js_empty)
				{
					$js_empty_together[$k_js_empty] .= '_' . $_SESSION[$this->name . '_salt'];
				}
				$this->items[$k]['jsEmptyTogether'] = '[\'' . implode('\', \'', $js_empty_together) . '\']';

				$this->items[$k]['cssEmptyTogether'] = true;
				if (strlen($item['value']))
					$this->items[$k]['cssEmptyTogether'] = false;
				else
					foreach ($item['emptyTogether'] as $name) // go over all items this one is grouped with for emptyness
					{
						foreach ($this->items as $k2 => $item2) // go over all items
							if (isset($item2['emptyTogether']) && $item2['name'] == $name) // to find one with a 'name' from the group
								if (strlen($item2['value']))
								{
									$this->items[$k]['cssEmptyTogether'] = false;
									break;
								}

						if (isset($item['cssEmptyTogether']) && $item['cssEmptyTogether'] == false)
							break;
					}
			}
		}

		$form = array(
			'name' => $this->name,
			'title' => $this->title,
			'text' => $this->text,
			'salt' => $_SESSION[$this->name . '_salt'],
			'items' => $this->items,
			'response' => $this->response,
			'errors' => $this->errors,
			'mode' => $this->mode
		);

		include('templates/form.tpl');
	}
}

?>
