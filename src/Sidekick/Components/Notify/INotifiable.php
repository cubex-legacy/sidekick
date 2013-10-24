<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify;


interface INotifiable
{
  public function __construct($ids);
  public function contains($user);
  public function getNotifiableUsers();
}
