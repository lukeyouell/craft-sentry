<?php

namespace lukeyouell\sentry\models;

use Craft;
use craft\base\Model;

use lukeyouell\sentry\Sentry;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $clientDsn;

    /**
     * @var string
     */
    public $environment = '$ENVIRONMENT';

    /**
     * @var string
     */
    public $excludedCodes = '404';

    // Public Methods
    // =========================================================================

    public function rules()
    {
        return [
            [['clientDsn', 'environment', 'excludedCodes'], 'string'],
            [['clientDsn', 'environment'], 'required'],
        ];
    }
}
