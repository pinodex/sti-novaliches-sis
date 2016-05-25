<?php

/*
 * This file is part of the online grades system for STI College Novaliches
 *
 * (c) Raphael Marco <raphaelmarco@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use App\Routes;
use App\Routes\Student;
use App\Routes\Dashboard;
use App\Services\View;
use App\Services\Auth;
use App\Services\FlashBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app->mount('/', new Routes\MainRoute);

$app->mount('/student',                     new Student\MainRoute);

$app->mount('/dashboard',                   new Dashboard\MainRoute);
$app->mount('/dashboard/admins',            new Dashboard\AdminsRoute);
$app->mount('/dashboard/heads',             new Dashboard\HeadsRoute);
$app->mount('/dashboard/faculty',           new Dashboard\FacultyRoute);
$app->mount('/dashboard/guidance',          new Dashboard\GuidanceRoute);
$app->mount('/dashboard/departments',       new Dashboard\DepartmentsRoute);
$app->mount('/dashboard/students',          new Dashboard\StudentsRoute);
$app->mount('/dashboard/grades',            new Dashboard\GradesRoute);
$app->mount('/dashboard/sections',          new Dashboard\SectionsRoute);
$app->mount('/dashboard/memos',             new Dashboard\MemosRoute);
$app->mount('/dashboard/settings',          new Dashboard\SettingsRoute);

$app->mount('/dashboard/faculty/import',    new Dashboard\FacultyImportRoute);
$app->mount('/dashboard/students/import',   new Dashboard\StudentsImportRoute);
$app->mount('/dashboard/grades/import',     new Dashboard\GradesImportRoute);

$app->mount('/dashboard/grades/compare',     new Dashboard\GradesCompareRoute);

$app->before(function (Request $request, Application $app) {
    $requestUri = $request->getRequestUri();

    if ($request->getMethod() == 'GET' && strpos($requestUri, '/index.php') === 0) {
        $redirect = substr($requestUri, 10, strlen($requestUri));

        if (empty($redirect)) {
            $redirect = '/';
        }

        return $app->redirect($redirect);
    }

    if (is_array($request->get('_controller')) &&
        !Auth::isAllowed($request->get('_controller'))) {
        
        if (Auth::user()) {
            $app->abort(403);
        }

        return $app->redirect($app->path('site.login', array(
            'next' => urlencode($request->getRequestUri())
        )));
    }
});

$app->after(function (Request $request, Response $response) {
    header_remove('X-Powered-By');

    $response->headers->add(array(
        'X-Frame-Options'   => 'SAMEORIGIN',
        'Pragma'            => 'no-cache',
        'Expires'           => 'Thu, 9 Sept 1999 09:00:00 GMT',
        'Cache-Control'     => 'no-cache, no-store, max-age=0, must-revalidate'
    ));
});

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    if ($code == 404) {
        return View::render('_error/404');
    }

    if ($code == 403) {
        return View::render('_error/403');
    }
});
