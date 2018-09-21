<?php
/**
 * Sentry plugin for Craft CMS 3.x
 *
 * Error tracking that helps developers monitor and fix crashes in real time. Iterate continuously. Boost efficiency. Improve user experience.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\sentry;

use lukeyouell\sentry\services\SentryService;
use lukeyouell\sentry\models\Settings;
use lukeyouell\sentry\variables\SentryVariable;

use Craft;
use craft\base\Plugin;
use craft\events\ExceptionEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\ErrorHandler;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

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

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['settings/sentry/authentication'] = 'sentry/settings/authentication';
                $event->rules['settings/sentry/project'] = 'sentry/settings/project';
                $event->rules['settings/sentry/excluded-codes'] = 'sentry/settings/excluded-codes';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('Sentry', SentryVariable::class);
            }
        );

        Event::on(
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function(ExceptionEvent $event) {
                SentryService::handleException($event->exception);
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
        return Craft::$app->view->renderTemplate('sentry/settings');
    }
}
