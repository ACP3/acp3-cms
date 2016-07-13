<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Model;


use ACP3\Core\Filesystem;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;
use ACP3\Installer\Core\I18n\Translator;
use ACP3\Installer\Modules\Install\Helpers\Install;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallModel
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var Install
     */
    protected $installHelper;
    /**
     * @var ApplicationPath
     */
    protected $appPath;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;

    /**
     * InstallModel constructor.
     * @param ContainerInterface $container
     * @param ApplicationPath $appPath
     * @param Secure $secure
     * @param Translator $translator
     * @param Install $installHelper
     */
    public function __construct(
        ContainerInterface $container,
        ApplicationPath $appPath,
        Secure $secure,
        Translator $translator,
        Install $installHelper)
    {
        $this->container = $container;
        $this->appPath = $appPath;
        $this->secure = $secure;
        $this->translator = $translator;
        $this->installHelper = $installHelper;
    }

    /**
     * @param string $configFilePath
     * @param array $formData
     */
    public function writeConfigFile($configFilePath, array $formData)
    {
        $configParams = [
            'parameters' => [
                'db_host' => $formData['db_host'],
                'db_name' => $formData['db_name'],
                'db_table_prefix' => $formData['db_pre'],
                'db_password' => $formData['db_password'],
                'db_user' => $formData['db_user'],
                'db_driver' => 'pdo_mysql',
                'db_charset' => 'utf8'
            ]
        ];

        $this->installHelper->writeConfigFile($configFilePath, $configParams);
    }

    /**
     * @param RequestInterface $request
     */
    public function updateContainer(RequestInterface $request)
    {
        $this->container = ServiceContainerBuilder::create(
            $this->appPath,
            $request->getSymfonyRequest(),
            $this->container->getParameter('core.environment'),
            true
        );
    }

    /**
     * @throws \Exception
     */
    public function installModules()
    {
        $modules = array_merge(['system', 'users'], Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/'));
        $alreadyInstalled = [];

        foreach ($modules as $module) {
            $module = strtolower($module);
            if (!in_array($module, $alreadyInstalled)) {
                if ($this->installHelper->installModule($module, $this->container) === false) {
                    throw new \Exception("Error while installing module {$module}.");
                }

                $alreadyInstalled[] = $module;
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function installAclResources()
    {
        foreach (Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/') as $module) {
            if ($this->installHelper->installResources($module, $this->container) === false) {
                throw new \Exception("Error while installing ACL resources for the module {$module}.");
            }
        }
    }

    /**
     * Set the module settings
     *
     * @param array $formData
     */
    public function configureModules(array $formData)
    {
        $settings = [
            'system' => [
                'date_format_long' => $this->secure->strEncode($formData['date_format_long']),
                'date_format_short' => $this->secure->strEncode($formData['date_format_short']),
                'date_time_zone' => $formData['date_time_zone'],
                'maintenance_message' => $this->translator->t('install', 'offline_message'),
                'lang' => $this->translator->getLocale(),
                'design' => $formData['design']
            ],
            'seo' => [
                'title' => !empty($formData['title']) ? $formData['title'] : 'ACP3'
            ],
            'users' => [
                'mail' => $formData['mail']
            ],
            'newsletter' => [
                'mail' => $formData['mail'],
                'mailsig' => $this->translator->t('install', 'sincerely') . "\n\n" . $this->translator->t('install',
                        'newsletter_mailsig')
            ],
            'contact' => [
                'mail' => $formData['mail'],
                'disclaimer' => $this->translator->t('install', 'disclaimer')
            ]
        ];

        foreach ($settings as $module => $data) {
            $this->container->get('core.config')->setSettings($data, $module);
        }
    }

    /**
     * @throws \Exception
     */
    public function installSampleData()
    {
        if ($this->installModuleSampleData() === false) {
            throw new \Exception("Error while installing module sample data.");
        }
    }

    /**
     * @param array $formData
     * @throws \Exception
     */
    public function createSuperUser(array $formData)
    {
        /** @var \ACP3\Core\Database\Connection db */
        $this->db = $this->container->get('core.db');

        $salt = $this->secure->salt(UserModel::SALT_LENGTH);
        $currentDate = gmdate('Y-m-d H:i:s');

        $queries = [
            "INSERT INTO
                `{pre}users`
            VALUES
                (1, 1, {$this->db->getConnection()->quote($formData["user_name"])}, '{$this->secure->generateSaltedPassword($salt, $formData["user_pwd"], 'sha512')}', '{$salt}', '', 0, '', '1', '', 0, '{$formData["mail"]}', 0, '', '', '', '', '', '', '', '', 0, 0, {$this->db->getConnection()->quote($formData["date_format_long"])}, {$this->db->getConnection()->quote($formData["date_format_short"])}, '{$formData["date_time_zone"]}', '{$this->translator->getLocale()}', '20', '{$currentDate}');",
            "INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (1, 4);"
        ];

        if ($this->container->get('core.modules.schemaHelper')->executeSqlQueries($queries) === false) {
            throw new \Exception("Error while creating the super user.");
        }
    }

    /**
     * @return bool
     */
    private function installModuleSampleData()
    {
        foreach (Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/') as $module) {
            $module = strtolower($module);
            $sampleDataInstallResult = $this->installHelper->installSampleData(
                $module,
                $this->container,
                $this->container->get('core.modules.schemaHelper')
            );

            if ($sampleDataInstallResult === false) {
                return false;
            }
        }

        return true;
    }
}
