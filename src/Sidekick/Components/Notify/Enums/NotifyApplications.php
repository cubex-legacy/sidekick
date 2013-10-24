<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Enums;


use Cubex\Type\Enum;

class NotifyApplications extends Enum
{
  const __default = "CLI";
  const CLI = "CLI";
  const PROJECTS = "Projects";
  const PHUSE = "Phuse";
  const REPOSITORY = "Repository";
  const CONFIGURATOR = "Configurator";
  const FORTIFY = "Fortify";
  const DIFFUSE = "Diffuse";
  const DISPATCHER = "Dispatcher";
  const SCRIPTURE = "Scripture";
  const DOCS = "Docs";
  const USERS = "Users";
  const NOTIFY = "Notify";
}
