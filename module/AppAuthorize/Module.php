<?php

namespace AppAuthorize;

// for Acl
use AppAuthorize\Acl\Acl;
use Zend\Session\Container as SessionContainer;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    // FOR Authorization
    public function onBootstrap(\Zend\EventManager\EventInterface $e) // use it to attach event listeners
    {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $em->attach('route', array($this, 'onRoute'), -100);
    }

    // WORKING the main engine for ACL
    public function onRoute(\Zend\EventManager\EventInterface $e) // Event manager of the app
    {
        $session = new SessionContainer('base');

        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $sm = $application->getServiceManager();
        $auth = $sm->get('Zend\Authentication\AuthenticationService');
        $config = $sm->get('Config');
        $acl = new Acl($config);
        // everyone is guest untill it gets logged in
        $role = Acl::DEFAULT_ROLE; // The default role is guest $acl
        // Without Doctrine
        if ($auth->hasIdentity()) {
            $usr = $auth->getIdentity();
            $usrl_id = $usr->usrl_id; // Use a view to get the name of the role

            $isAdmin = false;
            $isGerente = false;
            $isOperador = false;
            $isDispatch = false;

            // TODO we don't need that if the names of the roles are comming from the DB
            switch ($usrl_id) {
                case 1 :
                    $role = 'operador';
                    $isOperador = true;
                    break;
                case 2 :
                    $role = 'scrap';
                    break;
                case 3 :
                    $role = 'tecnico';
                    break;
                case 4 :
                    $role = 'admin';
                    $isAdmin = true;
                    break;
                case 5 :
                    $role = 'gerente';
                    $isGerente = true;
                    break;
                case 6 :
                    $role = 'dispatch';
                    $isDispatch = true;
                    break;
                case 7 :
                    $role = 'coordinador';
                    break;
                default :
                    $role = Acl::DEFAULT_ROLE; // guest
                    break;
            }

            $session->offsetSet('idUser', $usr->usr_id);
            $session->offsetSet('isAdmin', $isAdmin);
            $session->offsetSet('isGerente', $isGerente);
            $session->offsetSet('isOperador', $isOperador);
            $session->offsetSet('isDispatch', $isDispatch);
        }

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        $id = $routeMatch->getParam('id');

        if (!$acl->hasResource($controller)) {
            throw new \Exception('Resource ' . $controller . ' not defined');
        }

        // Si no tiene permiso ...
        if (!$acl->isAllowed($role, $controller, $action)) {

            $session->offsetSet('tController', $controller);
            $session->offsetSet('tAction', $action);
            $session->offsetSet('tId', $id);

            $url = $e->getRouter()->assemble(array(), array('name' => 'auth'));

            $response = $e->getResponse();

            $response->getHeaders()->addHeaderLine('Location', $url);
            // The HTTP response status code 302 Found is a common way of performing a redirection.
            // http://en.wikipedia.org/wiki/HTTP_302
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }
    }
}