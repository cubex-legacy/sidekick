<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 12:03
 */

namespace Sidekick\Components\Notify;

class NotifyConfig
{
  /**
   * @var NotifyConfigItem[]
   */
  public $items = [];

  /**
   * @param NotifyConfigItem $item
   */
  public function addItem($item)
  {
    $this->items[$item->eventKey] = $item;
  }

  /**
   * @return NotifyConfigItem
   */
  public function getItem($key)
  {
    return idx($this->items, $key);
  }

  /**
   * @return array|NotifyConfigItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
}
