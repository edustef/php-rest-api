<?php

namespace edustef\mvcFrame;

class Application
{
  public static Application $app;
  public static string $ROOT_DIR;

  public Router $router;
  public View $view;
  public Request $request;
  public Response $response;
  public Session $session;
  // Controller is instanciated in Router class 
  public ?Controller $controller = null;

  // CONFIG Variables
  public Database $database;
  public ?DatabaseModel $user = null;
  private string $userClass;

  public function __construct(string $rootPath, array $config)
  {
    self::$ROOT_DIR = $rootPath;
    self::$app = $this;

    $this->request = new Request();
    $this->response = new Response();
    $this->session = new Session();
    $this->view = new View($config['defaultLayout'] ?? '', $config['title'] ?? 'PHP Application');
    $this->router = new Router($this->request, $this->response);

    $this->userClass = $config['userClass'] ?? '';
    $this->database = new Database($config['db']);

    // Get the User from Session if it exists
    $primaryValue = $this->session->get('user');
    if ($this->userClass && $primaryValue) {
      $primaryKey = $this->userClass::primaryKey();
      $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
    }
  }

  public function run()
  {
    try {
      echo $this->router->resolve();
    } catch (\Exception $e) {
      if (is_numeric($e->getCode())) {
        $this->response->setStatusCode($e->getCode());
      }
      echo $this->view->renderView('_error', [
        'exception' => $e
      ]);
    }
  }

  public function login(DatabaseModel $user): bool
  {
    $this->user = $user;
    $primaryKey = $user->primaryKey();
    $primaryValue = $user->{$primaryKey};
    $this->session->set('user', $primaryValue);

    return true;
  }

  public function logout()
  {
    $this->user = null;
    $this->session->remove('user');
  }

  public function isGuest(): bool
  {
    return $this->session->get('user') === false;
  }

  public function getTitle(): string
  {
    return $this->view->title;
  }

  public function setTitle($title)
  {
    $this->view->title = $this->view->title . $title;
  }
}
