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
     * @var boolean
     */
    public $enabled = true;

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
            ['enabled', 'boolean'],
            [['clientDsn', 'environment', 'excludedCodes'], 'string'],
            [['clientDsn', 'environment'], 'required'],
        ];
    }
}
