<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Users\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Users\Views\UsersIndex;
use Sidekick\Applications\Users\Views\UsersSidebar;
use Sidekick\Components\Users\Mappers\User;

class DefaultController extends UsersController
{
  public function preRender()
  {
    parent::preRender();
    $this->nest('sidebar', new UsersSidebar());
  }

  public function renderIndex()
  {
    $users = User::collection()->loadAll();
    return $this->createView(new UsersIndex($users));
  }

  public function renderCreate()
  {
    $form = new Form('usersForm', '/users/create');
    $form->bindMapper(new User());
    return $form;
  }

  public function postCreate()
  {
    $user = new User();
    $user->hydrate($this->request()->postVariables());
    $user->password = password_hash($user->password, PASSWORD_DEFAULT);
    $user->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'User was successfully created';
    Redirect::to('/users')->with('msg', $msg)->now();
  }

  public function renderEdit()
  {
    $userId = $this->getInt('userId');
    $form   = new Form('usersForm', '/users/update');
    $form->bindMapper(new User($userId));
    return $form;
  }

  public function postUpdate()
  {
    $user = new User($this->request()->postVariables("id", 0));
    if($user->exists())
    {
      $existingPassword = $user->password;
      $user->hydrate($this->request()->postVariables());
      if($this->postVariables("password") !== '')
      {
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);
      }
      else
      {
        $user->password = $existingPassword;
      }
      $user->saveChanges();

      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = 'User was successfully updated';
      Redirect::to('/users')->with('msg', $msg)->now();
    }
    else
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Something went wrong :s';
      Redirect::to('/users')->with('msg', $msg)->now();
    }
  }

  public function renderDelete()
  {
    $userId = $this->getInt('userId');
    $user   = new User($userId);
    $user->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'User was successfully deleted';
    Redirect::to('/users')->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return array(
      '/create'         => 'create',
      '/update'         => 'update',
      '/view/:userId'   => 'view',
      '/delete/:userId' => 'delete',
      '/edit/:userId'   => 'edit',
    );
  }
}
