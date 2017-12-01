<?php
/**
 * Sentry plugin for Craft CMS 3.x
 *
 * Error tracking that helps developers monitor and fix crashes in real time. Iterate continuously. Boost efficiency. Improve user experience.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\sentry\variables;

use lukeyouell\sentry\Sentry;
use craft\elements\Entry;
use lukeyouell\sentry\services\SentryService;

use Craft;

/**
 * @author    Luke Youell
 * @package   Sentry
 * @since     1.0.0
 */
class SentryVariable
{
    // Public Methods
    // =========================================================================

    /**
     */
    public function listProjects() {

        return SentryService::apiGet('/api/0/projects/');

    }

    /**
     */
    public function listKeys($project) {

        return SentryService::apiGet('/api/0/projects/' . $project . '/keys/');

    }
}
