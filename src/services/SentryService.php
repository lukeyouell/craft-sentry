<?php

namespace lukeyouell\sentry\services;

use Craft;
use craft\base\Component;

use lukeyouell\sentry\Sentry;

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

    public function handleException($exception)
    {
        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof \Twig_Error_Runtime && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        if ($this->canReport($exception->statusCode))
        {
            $dsn = Sentry::$plugin->getSettings()->getClientDsn();
            $environment = Sentry::$plugin->getSettings()->getEnvironment();

            \Sentry\init([
                'dsn' => $dsn,
                'environment' => $environment,
            ]);

            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($exception): void {
                $user = Craft::$app->getUser()->getIdentity();

                if ($user)
                {
                    $scope->setUser([
                        'id'       => $user->id,
                        'username' => $user->username,
                        'admin'    => $user->admin ? 'Yes' : 'No',
                    ]);
                }

                $scope->setTag('app', 'Craft CMS');
                $scope->setTag('status_code', $exception->statusCode);

                $scope->setExtra('Craft Name', Craft::$app->getInfo()->name);
                $scope->setExtra('Craft Edition (licensed)', Craft::$app->getLicensedEditionName());
                $scope->setExtra('Craft Edition (running)', Craft::$app->getEditionName());
                $scope->setExtra('Craft Version', Craft::$app->getInfo()->version);
                $scope->setExtra('Craft Schema Version', Craft::$app->getInfo()->schemaVersion);
            });

            \Sentry\captureException($exception);
        }
    }
    
    private function canReport($statusCode = null, $dsn = null)
    {
        $canReport = true;
        $message = null;
        $excludedStatusCodes = Sentry::$plugin->getSettings()->getExcludedStatusCodes();

        if (!Sentry::$plugin->getSettings()->enabled) {        
            $canReport = false;
            $message = 'Exception not reported to Sentry as the plugin is disabled.';
        }

        if (!Sentry::$plugin->getSettings()->getClientDsn()) {
            $canReport = false;
            $message = 'Failed to report exception due to missing client key (DSN)';
        }

        if ($statusCode && $excludedStatusCodes)
        {
            if (in_array($statusCode, $excludedStatusCodes)) {
                $canReport = false;
                $message = 'Exception status code excluded from being reported to Sentry.';
            }
        }

        if (!$canReport)
        {
            Craft::error($message, Sentry::$plugin->handle);
        }

        return $canReport;
    }
}
