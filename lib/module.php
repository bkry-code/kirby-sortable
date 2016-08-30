<?php

namespace Kirby\Field\Modules;

// Kirby dependencies
use Brick;
use Url;
use Tpl;

class Module extends \Kirby\Modules\Module {
  public $origin;
  public $field;
  public $page;

  public $redirect = false;
  public $preview = true;
  public $delete = true;
  public $limit = null;
  public $edit = true;

  public function __construct($field, $page) {
    $this->field = $field;
    $this->page = $page;

    // Set all options
    foreach ($field->options($page) as $property => $value) {
      if(property_exists($this, $property)) $this->{$property} = $value;
    }

    // Call parent constructor
    parent::__construct($page);
  }

  public function preview() {
    $name = $this->name();

    $template = \Kirby\Modules\Modules::directory() . DS . $name . DS . $name . '.preview.php';

    if(!is_file($template)) return null;

    $preview = new Brick('div');
    $preview->addClass('modules-entry-preview');
    $preview->data('module', $name);
    $preview->html(tpl::load($template, array('module' => $this->page())));

    return $preview;
  }

  public function limit() {
    $count = $this->page()->siblings()->filter(function($page) {
      return $page->isVisible() && $page->intendedTemplate() == $this->template();
    })->count();

    $counter = new Brick('span');
    $counter->addClass('counter');
    $counter->html('( ' . $count . ' / ' . $this->limit . ' )');

    return $counter;
  }

  public function url($action) {
    switch($action) {
      case 'delete':
        $query = array(
          'uid' => $this->page()->uid(),
        );

        $page = $this->field()->page()->uri('edit');
        $origin = $this->field()->origin()->uri('edit');

        if ($page != $origin) {
          $query['_redirect'] = $page;
        }

        $query = url::queryToString($query);

        return purl($this->field()->model(), 'field/' . $this->field()->name() . '/modules/' . $action . '?' . $query);
        break;
      case 'edit':
        return $this->page->url('edit');
        break;
    }
  }
}
