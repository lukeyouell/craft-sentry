<?php
/**
 * Sentry plugin for Craft CMS 3.x
 *
 * Error tracking that helps developers monitor and fix crashes in real time. Iterate continuously. Boost efficiency. Improve user experience.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\sentry\services;

use lukeyouell\sentry\Sentry;

use Craft;
use craft\base\Component;
use Raven_Client;
use Raven_ErrorHandler;

/**
 * @author    Luke Youell
 * @package   Sentry
 * @since     1.0.0
 */
class SentryService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public static function handleException($exception)
    {
      // Get settings
      $settings = Sentry::$plugin->getSettings();

      if ($settings->clientDsn === null)
      {
        return;
      }

      $user = Craft::$app->getUser()->getIdentity();

      $sentryClient = new Raven_Client($settings->clientDsn);

      $error_handler = new Raven_ErrorHandler($sentryClient);
      $error_handler->registerExceptionHandler();
      $error_handler->registerErrorHandler();
      $error_handler->registerShutdownFunction();

      if ($user) {

        $sentryClient->user_context([
          'id' => $user->id,
          'username' => $user->username,
          'email' => $user->email,
          'admin' => $user->admin ? 'Yes' : 'No'
        ]);

      }

      $sentryClient->captureException($exception, [
        'extra' => [
          'App Type' => 'Craft CMS',
          'App Version' => Craft::$app->getVersion(),
          'Environment' => CRAFT_ENVIRONMENT,
          'PHP Version' => phpversion()
        ]
      ]);
    }
}
