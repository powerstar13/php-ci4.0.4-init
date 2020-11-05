<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('Modules'); // 모듈 namespace를 기본으로 설정
$routes->setDefaultController('Common\Controllers\Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // 자동 라우팅을 비활성화하여 정의한 경로만 액세스 할 수 있다.

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
// CodeIgniter는 라우팅 규칙을 위에서 아래로 읽고, 요청과 첫 번째로 일치하는 규칙으로 라우팅합니다.
$routes->get('/', 'Common\Controllers\Home::index'); // 디렉토리를 스캔하지 않아도 되므로 기본 경로를 지정하여 성능이 향상됩니다.

$routes->get('setCookie', 'Common\Controllers\Cookie::set'); // 쿠키 등록

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
