WP_Controller: A convenience class which uses the PHP __call method to dynamically define hooks for filters, actions and shortcodes, amongst other things.

For example:

    class MyController extends WP_Controller
    {
      // This is the root of the views folder, which helps the controller
      // find views when using the render() method
      public $viewPath = 'somepath';

      public function someOtherMethod()
      {
        // Do something with your settings
        // All settings are available as properties on the controller
        // using PHP __get and __set. Settings are stored in WP
        // as "optionGroup-optionName" and can be retrieved by the
        // standard method as get_option("optionGroup-optionName")

        $this->setting1 = 'blah';
        echo $this->setting1;
        ...
      }

      public function settings()
      {
        // Renders somepath/settings.php with assoc array $data provided as local variables in the view
        // The view is rendered in the context of the controller, so views can access the controller
        // as $this.
        // You can specify a third boolean parameter, which controls if it returns the value or echoes it.
        // It echoes by default.
        $data = array(
          'user' => 'bob',
          ...
        );
        $this->render('settings', $data);
      }

      // Any methods defined as action_* become action hooks
      public function action_admin_menu()
      {
        add_options_page('MyController Settings', 'MyController', 1, 'MyController', array($this, 'settings'));
      }

      // Any methods defined as filter_* become filter hooks
      public function filter_the_title($title)
      {
        return 'New Title';
      }

      // Any methods defined as shortcode_* become shortcode hooks
      public function shortcode_sometag($attributes)
      {
        ...
      }
    }

    // Subclass and set the properties
    class MyController extends WP_Controller
    {
      public $optionGroup = 'MyController';
      public $options => array(
        'setting1',
        'setting2',
      );
    }

    // or configure using an array
    $controller = new MyController(array(
      'optionGroup' => 'MyController',
      'options' => array(
        'setting1',
        'setting2',
      ),
    ));

    // Or configure with an included file which returns an array
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
