<?php
require 'WP_Controller.php';

class WP_Plugin extends WP_Controller
{
  public $page;
  public $rewrite_rules = array();
  public $query_vars = array();

  public function action_init()
  {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
  }

  public function filter_rewrite_rules_array($rules)
  {
    if(!empty($this->rewriteRules)) {
      foreach($this->rewriteRules as $pattern => $route) {
        $rules = array($this->page.'/'.$pattern => 'index.php?pagename='.$this->page.'&controller='.$route) + $rules;
      }
    }
    return $rules;
  }

  public function filter_query_vars($vars)
  {
    $vars[] = 'controller';
    if(!empty($this->queryVars)) {
      $vars = array_merge($vars, $this->queryVars);
    }
    return $vars;
  }
}
