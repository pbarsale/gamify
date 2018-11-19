<?php

namespace Core;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{
    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = array())
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = array())
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function getTemplate($template, $args = array())
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('current_user',\App\Auth::getUser());
            $twig->addGlobal('flash_messages',\App\Flash::getMessages());
            $twig->addGlobal('games',\App\Models\Quiz::getAllGames());
            $twig->addGlobal('QUIZ_CONST',4);
            $twig->addGlobal('SCAVENGER_HUNT_CONST',16);
            $twig->addGlobal('avatar_file',\App\Models\User::setUserAvatar());
            $twig->addGlobal('points',\App\Models\LeaderBoard::getPointsOfUser());
            $twig->addGlobal('badges',\App\Models\LeaderBoard::getBadgesOfUser());
        }
        return $twig->render($template, $args);
    }
}
