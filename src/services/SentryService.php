<?php

namespace lukeyouell\sentry\services;

use Craft;
use craft\base\Component;

use lukeyouell\sentry\Sentry;

use Raven_Client;
use Raven_ErrorHandler;

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
        $this->plugin = Sentry::$plugin;
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

        $client = new Raven_Client($dsn);
        $handler = new Raven_ErrorHandler($client);
        $handler->registerExceptionHandler();
        $handler->registerErrorHandler();
        $handler->registerShutdownFunction();

        $user = Craft::$app->getUser()->getIdentity();

        if ($user) {
            $client->user_context([
                'ID'       => $user->id,
                'Username' => $user->username,
                'Email'    => $user->email,
                'Admin'    => $user->admin ? 'Yes' : 'No',
            ]);
        }

        if ($environment) {
            $client->setEnvironment($environment);
        }

        $client->captureException($exception, [
            'extra' => [
                'App Type'               => 'Craft CMS',
                'App Name'               => $this->info->name,
                'App Edition (licensed)' => $this->app->getLicensedEditionName(),
                'App Edition (running)'  => $this->app->getEditionName(),
                'App Version'            => $this->info->version,
                'App Version (schema)'   => $this->info->schemaVersion,
                'PHP Version'            => phpversion(),
                'Status Code'            => $statusCode,
            ],
        ]);
    }
}
