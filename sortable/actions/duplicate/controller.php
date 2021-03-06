<?php

class DuplicateActionController extends Kirby\Sortable\Controllers\Action {

  /**
   * Duplicate a entry
   *
   * @param string $uid
   * @param int $to
   */
  public function duplicate($uid, $to) {

    $entries = $this->field()->entries();
    $parent  = $this->field()->origin();
    $page    = $entries->find($uid);

    if($parent->ui()->create() === false) {
      throw new PermissionsException();
    }

    $page = $this->copy($page, $parent);

    $entries->add($page->uid());
    $this->sort($page->uid(), $to);
    $this->notify(':)');
    $this->redirect($this->model());

  }

}
