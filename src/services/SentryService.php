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
    public static function apiGet($path = null, $authToken = null)
    {
      $settings = Sentry::$plugin->getSettings();

      if ($path === null)
      {
        return [
          'error' => true,
          'reason' => 'Missing values'
        ];
      }

      if ($authToken === null) {

        $authToken = $settings->authToken;

      }

      $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://app.getsentry.com',
        'http_errors' => false,
        'timeout' => 5,
        'headers' => [
          'Authorization' => 'Bearer ' . $authToken
        ]
      ]);

      try {

        $response = $client->request('GET', $path);

        $body = json_decode($response->getBody());

        if ($response->getStatusCode() === 200) {

          return $body;

        } else {

          return [
            'error' => true,
            'reason' => $body->detail
          ];

        }

      } catch (\Exception $e) {

        return [
          'error' => true,
          'reason' => $e->getMessage()
        ];

      }
    }

    /*
     * @return mixed
     */
    public static function handleException($exception)
    {
      $settings = Sentry::$plugin->getSettings();
      $statusCode = isset($exception->statusCode) ? $exception->statusCode : null;
      $excludedCodes = array_map(function($code) {
        return trim($code);
      }, explode(',', $settings->excludedCodes));

      if (($settings->clientDsn === null) or (in_array($statusCode, $excludedCodes)))
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
          'Environment' => defined(CRAFT_ENVIRONMENT) ? CRAFT_ENVIRONMENT : 'undefined',
          'PHP Version' => phpversion(),
          'Status Code' => $statusCode
        ]
      ]);
    }
}
