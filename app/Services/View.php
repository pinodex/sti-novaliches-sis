<?php

/*
 * This file is part of the online grades system for STI College Novaliches
 *
 * (c) Raphael Marco <raphaelmarco@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

/**
 * View renderer wrapper for Twig
 */
class View extends Service
{
    /**
     * Render view
     * 
     * @param string $templateName Template name
     * @param array $vars Template variables
     * 
     * @return string
     */
    public static function render($templateName, $vars = array())
    {
        return self::$app['twig']->render($templateName . '.html', $vars);
    }

    /**
     * Simple rendering of view without Twig
     * 
     * @param string $templateName Template name
     * @param array $vars Template variables
     * 
     * @return string
     */
    public static function simpleRender($templateName, $vars = array())
    {
        foreach ($vars as $key => $value) {
            $vars['{{ ' . $key . ' }}'] = $value;
            unset($vars[$key]);
        }

        $path = self::$app['twig.path'] . '/' . $templateName . '.html';

        return strtr(file_get_contents($path), $vars);
    }
}
