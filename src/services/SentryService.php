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
    public function handleException($exception)
    {
        $app = Craft::$app;
        $info = $app->getInfo();
        $plugin = SentryPlugin::$plugin;
        $settings = $plugin->getSettings();

        if (!$settings->enabled) {
            Craft::info('Exception not reported to Sentry as the plugin is disabled.', $plugin->handle);
            return;
        }

        if (!$settings->clientDsn) {
            Craft::error('Failed to report exception due to missing client key (DSN)', $plugin->handle);
            return;
        }

        $dsn = $settings->clientDsn ? Craft::parseEnv($settings->clientDsn) : null;
        $environment = $settings->environment ? Craft::parseEnv($settings->environment) : null;

        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof \Twig_Error_Runtime && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        $statusCode = $exception->statusCode ?? null;
        $excludedStatusCodes = array_map('trim', explode(',', $settings->excludedCodes));

        if (in_array($statusCode, $excludedStatusCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        Sentry\init([
            'dsn' => $dsn,
            'environment' => $environment,
        ]);

        $user = Craft::$app->getUser()->getIdentity();

        Sentry\configureScope(function (Scope $scope) use ($app, $info, $plugin, $settings, $user, $statusCode) {
            if ($user) {
                $scope->setUser([
                    'ID'       => $user->id,
                    'Username' => $user->username,
                    'Email'    => $user->email,
                    'Admin'    => $user->admin ? 'Yes' : 'No',
                ]);
            }

            $scope->setExtra('App Type', 'Craft CMS');
            $scope->setExtra('App Name', $info->name);
            $scope->setExtra('App Edition (licensed)', $app->getLicensedEditionName());
            $scope->setExtra('App Edition (running)', $app->getEditionName());
            $scope->setExtra('App Version', $info->version);
            $scope->setExtra('App Version (schema)', $info->schemaVersion);
            $scope->setExtra('PHP Version', phpversion());
            $scope->setExtra('Status Code', $statusCode);
        });
    }
}
