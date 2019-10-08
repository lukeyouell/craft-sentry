<?php

namespace lukeyouell\sentry\services;

use Craft;
use craft\base\Component;

use lukeyouell\sentry\Sentry as SentryPlugin;

use Sentry;
use Sentry\State\Scope;

use yii\base\Exception;

class SentryService extends Component
{
    // Public Properties
    // =========================================================================

    public $app;

    public $info;

    public $plugin;

    public $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->app = Craft::$app;
        $this->info = $this->app->getInfo();
        $this->plugin = SentryPlugin::$plugin;
        $this->settings = $this->plugin->getSettings();
    }

    public function handleException($exception)
    {
        if (!$this->settings->enabled) {
            Craft::info('Exception not reported to Sentry as the plugin is disabled.', $this->plugin->handle);
            return;
        }

        if (!$this->settings->clientDsn) {
            Craft::error('Failed to report exception due to missing client key (DSN)', $this->plugin->handle);
            return;
        }

        $dsn = $this->settings->clientDsn ? Craft::parseEnv($this->settings->clientDsn) : null;
        $environment = $this->settings->environment ? Craft::parseEnv($this->settings->environment) : null;

        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof \Twig_Error_Runtime && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        $statusCode = $exception->statusCode ?? null;
        $excludedStatusCodes = array_map('trim', explode(',', $this->settings->excludedCodes));

        if (in_array($statusCode, $excludedStatusCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $this->plugin->handle);
            return;
        }

        Sentry\init([
            'dsn' => $dsn,
            'environment' => $environment,
        ]);

        $user = Craft::$app->getUser()->getIdentity();

        Sentry\configureScope(function (Scope $scope) {
            if (isset($user)) {
                $scope->setUser([
                    'ID'       => $user->id,
                    'Username' => $user->username,
                    'Email'    => $user->email,
                    'Admin'    => $user->admin ? 'Yes' : 'No',
                ]);
            }

            $scope->setExtra('App Type', 'Craft CMS');
            $scope->setExtra('App Name', $this->info->name);
            $scope->setExtra('App Edition (licensed)', $this->app->getLicensedEditionName());
            $scope->setExtra('App Edition (running)', $this->app->getEditionName());
            $scope->setExtra('App Version', $this->info->version);
            $scope->setExtra('App Version (schema)', $this->info->schemaVersion);
            $scope->setExtra('PHP Version', phpversion());
            $scope->setExtra('Status Code', $statusCode);
        });
    }
}
