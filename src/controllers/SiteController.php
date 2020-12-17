<?php

namespace app\controllers;

use edustef\mvcFrame\Controller;
use edustef\mvcFrame\Request;

class SiteController extends Controller
{

  public function home()
  {
    $params = [
      'name' => 'bla bla bla'
    ];

    return $this->render('home', $params);
  }

  public function contact(Request $request)
  {
    if ($request->isPost()) {
      return  'handle contact';
    }
    $params = [
      'name' => 'bla bla bla'
    ];

    return $this->render('contact', $params);
  }
}
