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
use lukeyouell\sentry\variables\SentryVariable;
use lukeyouell\sentry\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class Sentry
 *
 * @author    Luke Youell
 * @package   Sentry
 * @since     1.0.0
 *
 * @property  SentryServiceService $sentryService
 */
class Sentry extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Sentry
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('Sentry', SentryVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                  Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/sentry'))->send();
                }
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_SAVE_PLUGIN_SETTINGS,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                  Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/sentry'))->send();
                }
            }
        );

        Craft::info(
            Craft::t(
                'sentry',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
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

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        // Get the settings that are being defined by the config file
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'sentry/settings',
            [
                'settings' => $settings,
                'overrides' => array_keys($overrides)
            ]
        );
    }
}
