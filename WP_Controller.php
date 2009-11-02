<?php
class WP_Controller
{
  private $reflection;

  public $wp_filter_id = null;

  public $viewPath = '';

  public $optionGroup = '';
  public $options = array();

  private $_options = array();

  public $widgets = array();

  public function __construct($config = array())
  {
    $this->reflection = new ReflectionClass($this);

    $this->initConfig($config);
    $this->initMethods();
  }

  protected function initConfig($config)
  {
    empty($this->optionGroup) and
      $this->optionGroup = strtolower(get_class($this));

    is_string($config) and
    file_exists($config) and
    is_readable($config) and
      $config = require($config);

    foreach($config as $name => $value) {
      $this->$name = $value;
    }
  }

  protected function initMethods()
  {
    foreach($this->reflection->getMethods() as $method) {
      foreach(array('filter', 'action', 'shortcode') as $type) {
        if(preg_match("/^{$type}_(.+)$/", $method->name, $match)) {
          call_user_func("add_{$type}", $match[1], array($this, $method->name));
        }
      }
    }
  }

  protected function render($view, $data = array(), $return = false)
  {
    $viewPath = implode(DIRECTORY_SEPARATOR, array($this->viewPath, $view)).'.php';
    extract($data);

    $return and ob_start();

    require $viewPath;

    if($return) {
      $rendered = ob_get_contents();
      ob_end_clean();
      return $rendered;
    } else {
      echo $rendered;
    }
  }
  
  public function action_admin_init()
  {
    if(!empty($this->optionGroup) && !empty($this->options)) {
      foreach($this->options as $option) {
        register_setting($this->optionGroup, $this->optionGroup.'-'.$option);
      }
    }
  }

  public function action_widgets_init()
  {
    global $wp_widget_factory;

    foreach($this->widgets as $id => $config) {
      $class = $config['class'];
      $widget = new $class($this->optionGroup.'-'.$id, $config['name'], $config['widget_options'], $config['control_options']);
      $wp_widget_factory->widgets[$config['class']] = $widget;
    }
  }

  public function __get($name)
  {
    if(in_array($name, $this->options)) {
      return get_option($this->optionGroup.'-'.$name);
    }
    throw new Exception("Trying to get invalid property $name");
  }

  public function __set($name, $value)
  {
    if(in_array($name, $this->options)) {
      update_option($this->optionGroup.'-'.$name, $value);
      return $value;
    }
    throw new Exception("Trying to set invalid property $name");
  }
}
