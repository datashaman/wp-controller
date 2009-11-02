WP_Controller: A convenience class which uses the PHP __call method to dynamically define hooks for filters, actions and shortcodes, amongst other things.

For example:

    class MyController extends WP_Controller
    {
      public function settings()
      {
        // Do something with your settings
        // All settings are available as properties on the controller

        $this->setting1 = 'blah';
        echo $this->setting1;

        $this->render('settings');
      }

      public function action_admin_menu()
      {
        add_options_page('MyController Settings', 'MyController', 1, 'MyController', array($this, 'settings'));
      }

      public function filter_the_title($title)
      {
        return 'New Title';
      }
    }

    // Configure using an array
    $controller = new MyController(array(
      'optionGroup' => 'MyController',
      'options' => array(
        'setting1',
        'setting2',
      ),
    ));

    // Or with an included file, which returns an array
    $controller = new MyController('config.php');
    
    config.php
    ==========
    return array(
      'optionGroup' => 'MyController',
      'options' => array(
        'setting1',
        'setting2',
      ),
    );

    MyController.php
    ================
    // Or subclass and set the properties
    class MyController extends WP_Controller
    {
      public $optionGroup = 'MyController';
      public $options => array(
        'setting1',
        'setting2',
      );
    }
