<?php
namespace lukeyouell\sentry;

use Craft;
use craft\base\Plugin;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;

use lukeyouell\sentry\models\Settings;
use lukeyouell\sentry\services\SentryService;

use yii\base\Event;

class Sentry extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'sentry' => SentryService::class,
        ]);

        Event::on(
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function(ExceptionEvent $event) {
                $this->sentry->handleException($event->exception);
            }
        );
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        $settings = $this->getSettings();
        $settings->validate();
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate('sentry/settings',
            [
                'plugin'    => $this,
                'title'     => $this->handle,
                'settings'  => $settings,
                'overrides' => array_keys($overrides)
            ]
        );
    }
}
