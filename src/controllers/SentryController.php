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

use Craft;
use craft\web\Controller;
use lukeyouell\sentry\services\SentryService;

/**
 * @author    Luke Youell
 * @package   Sentry
 * @since     1.0.0
 */
class SentryController extends Controller
{

    /**
     * @return response
     */
    public function actionListProjects()
    {
      $this->requirePostRequest();
      $this->requireAcceptsJson();

      $request = Craft::$app->getRequest();
      $authToken = $request->getRequiredBodyParam('authToken');

      $projects = SentryService::apiGet('/api/0/projects/', $authToken);

      return $this->asJson($projects);
    }

    /**
     * @return response
     */
    public function actionListKeys()
    {
      $this->requirePostRequest();
      $this->requireAcceptsJson();

      $request = Craft::$app->getRequest();
      $authToken = $request->getRequiredBodyParam('authToken');
      $project = $request->getRequiredBodyParam('project');

      $keys = SentryService::apiGet('/api/0/projects/' . $project . '/keys/', $authToken);

      return $this->asJson($keys);
    }

}
