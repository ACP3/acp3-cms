<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Router\RouterInterface;

class LoadModule extends AbstractFunction
{
    /**
     * @var ACLInterface
     */
    protected $acl;
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var string
     */
    protected $applicationMode;

    /**
     * LoadModule constructor.
     * @param ACLInterface $acl
     * @param RouterInterface $router
     * @param string $applicationMode
     */
    public function __construct(
        ACLInterface $acl,
        RouterInterface $router,
        string $applicationMode
    ) {
        $this->acl = $acl;
        $this->router = $router;
        $this->applicationMode = $applicationMode;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'load_module';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $pathArray = $this->convertPathToArray($params['module']);
        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

        $response = '';
        if ($this->acl->hasPermission($path) === true) {
            $response = $this->esiInclude($path, $this->parseControllerActionArguments($params));
        }

        return $response;
    }

    /**
     * @param string $resource
     *
     * @return array
     */
    protected function convertPathToArray($resource)
    {
        $pathArray = \explode('/', \strtolower($resource));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        return $pathArray;
    }

    /**
     * @param array $arguments
     * @return array
     */
    protected function parseControllerActionArguments(array $arguments)
    {
        if (isset($arguments['args']) && \is_array($arguments['args'])) {
            return $this->urlEncodeArguments($arguments['args']);
        }

        unset($arguments['module']);

        return $this->urlEncodeArguments($arguments);
    }

    /**
     * @param array $arguments
     * @return array
     */
    protected function urlEncodeArguments(array $arguments)
    {
        return \array_map(
            function ($item) {
                return \urlencode($item);
            },
            $arguments
        );
    }

    /**
     * @param string $path
     * @param array $arguments
     * @return string
     */
    protected function esiInclude($path, array $arguments)
    {
        $routeArguments = '';
        foreach ($arguments as $key => $value) {
            $routeArguments.= '/' . $key . '_' . $value;
        }

        $debug = '';
        if ($this->applicationMode === ApplicationMode::PRODUCTION) {
            $debug = ' onerror="continue"';
        }

        return '<esi:include src="' . $this->router->route($path . $routeArguments, true) . '"' . $debug . ' />';
    }
}
