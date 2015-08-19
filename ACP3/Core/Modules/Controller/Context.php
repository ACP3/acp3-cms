<?php
namespace ACP3\Core\Modules\Controller;

use ACP3\Core\ACL;
use ACP3\Core\User;
use ACP3\Core\Config;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Lang;
use ACP3\Core\Modules;
use ACP3\Core\Router;
use ACP3\Core\View;

/**
 * Class Context
 * @package ACP3\Core\Modules\Controller
 */
class Context
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\ACL                   $acl
     * @param \ACP3\Core\User                  $user
     * @param \ACP3\Core\Lang                  $lang
     * @param \ACP3\Core\Modules               $modules
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Router                $router
     * @param \ACP3\Core\View                  $view
     * @param \ACP3\Core\Config                $config
     */
    public function __construct(
        ACL $acl,
        User $user,
        Lang $lang,
        Modules $modules,
        RequestInterface $request,
        Router $router,
        View $view,
        Config $config
    )
    {
        $this->acl = $acl;
        $this->user = $user;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->config = $config;
    }

    /**
     * @return \ACP3\Core\ACL
     */
    public function getACL()
    {
        return $this->acl;
    }

    /**
     * @return \ACP3\Core\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \ACP3\Core\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return \ACP3\Core\Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return \ACP3\Core\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \ACP3\Core\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \ACP3\Core\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
