<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'tethys.cse.buffalo.edu';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'museum_db';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'pbarsale';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'ChangeMe';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = "xRDoG2DPc8r0mT1IB4TXmSkYmZF4ETLl";
}
