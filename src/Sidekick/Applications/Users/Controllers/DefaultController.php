<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Users\Controllers;

use Cubex\Facade\Auth;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Sidekick\Applications\Users\Views\UsersIndex;
use Sidekick\Components\Users\Mappers\User;

class DefaultController extends UsersController
{
  public function renderIndex()
  {
    $users = User::collection()->loadAll();
    return $this->createView(new UsersIndex($users));
  }

  public function renderCreate()
  {
    if(Auth::user()->getDetails()->user_role != 'administrator') {
      Redirect::to('/P/users')->now();
    }

    $form = new Form('usersForm', $this->appBaseUri() . '/create');
    $form->bindMapper(new User());
    return $form;
  }

  public function postCreate()
  {
    $msg = new \StdClass();
    if(Auth::user()->getDetails()->user_role == 'administrator') {
      $user = new User();
      $user->hydrate($this->request()->postVariables());
      $user->password = password_hash($user->password, PASSWORD_DEFAULT);
      $user->saveChanges();

      $msg->type = 'success';
      $msg->text = 'User was successfully created';
    } else {
      $msg->type = 'error';
      $msg->text = 'Your not allowed to do that.';
    }

    Redirect::to($this->appBaseUri())->with('msg', $msg)->now();
  }

  public function renderEdit()
  {
    $userId = $this->getInt('userId');
    $form   = new Form('usersForm', $this->appBaseUri() . '/update');
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
      Redirect::to($this->appBaseUri())->with('msg', $msg)->now();
    }
    else
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Something went wrong :s';
      Redirect::to($this->appBaseUri())->with('msg', $msg)->now();
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
    Redirect::to($this->appBaseUri())->with('msg', $msg)->now();
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
