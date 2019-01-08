<?php
/**
 * Sentry plugin for Craft CMS 3.x
 *
 * Error tracking that helps developers monitor and fix crashes in real time. Iterate continuously. Boost efficiency. Improve user experience.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\sentry\controllers;

use lukeyouell\sentry\Sentry;

use Craft;
use craft\web\Controller;

class SettingsController extends Controller
{
    // Public Properties
    // =========================================================================

    public $plugin;

    public $settings;

    public $overrides;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->plugin = Sentry::$plugin;
        $this->settings = $this->plugin->getSettings();
        $this->overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->plugin->handle));
    }

    public function actionAuthentication()
    {
        $variables = [
          'plugin'    => $this->plugin,
          'settings'  => $this->settings,
          'overrides' => $this->overrides,
        ];

        return $this->renderTemplate('sentry/_settings/authentication/index', $variables);
    }

    public function actionProject()
    {
        $variables = [
          'plugin'    => $this->plugin,
          'settings'  => $this->settings,
          'overrides' => $this->overrides,
          'projects'  => $this->plugin->sentryService->apiGet('/api/0/projects/'),
          'keys'      => $this->plugin->sentryService->apiGet('/api/0/projects/' . $this->settings->project . '/keys/'),
        ];

        return $this->renderTemplate('sentry/_settings/project/index', $variables);
    }

    public function actionExcludedCodes()
    {
        $variables = [
          'plugin'    => $this->plugin,
          'settings'  => $this->settings,
          'overrides' => $this->overrides,
        ];

        return $this->renderTemplate('sentry/_settings/excluded-codes/index', $variables);
    }
}
