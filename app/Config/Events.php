<?php namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * 이벤트를 통해 코어 파일을 수정하거나 확장하지 않고 프로그램 실행을 누를 수 있습니다.
 * 이 파일은 이벤트를 정의하는 중앙 위치를 제공하지만 필요한 경우 런타임에 항상 추가할 수 있습니다.
 * 이벤트에 `on()` 메서드를 사용하여 구독하여 실행할 수 있는 코드를 만듭니다.
 * 이것은 이벤트가 트리거 될 때 실행되는 폐쇄를 포함하여 호출 가능한 모든 형식을 허용합니다.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 *
 * 이벤트 포인트
 *
 * - 다음은 CI 핵심(core) 코드에서 사용 가능한 이벤트 포인트 목록이다.
 *
 * - pre_system
 *     - 시스템 실행중에 매우 일찍 호출된다.
 *     - 벤치 마크 및 이벤트 클래스만 로드되고, 라우팅이나 다른 프로세스가 발생하지 않았다.
 * - post_controller_constructor
 *     - 컨트롤러가 인스턴스화 된 직후, 메소드 호출이 발생하기 전에 호출된다.
 * - post_system
 *     - 최종 렌더링된 페이지가 브라우저로 전송된 후, 최종 데이터가 브라우저로 전송된 후 시스템 실행이 끝날 때 호출된다.
 * - email
 *     - `CodeInginter\Email\Email`에서 보낸 이메일이 성공적으로 전송된 후 호출된다.
 *     - 매개 변수로 `Email` 클래스의 속성 배열을 받는다.
 */

Events::on('pre_system', function () {
	if (ENVIRONMENT !== 'testing')
	{
		if (ini_get('zlib.output_compression'))
		{
			throw FrameworkException::forEnabledZlibOutputCompression();
		}

		while (ob_get_level() > 0)
		{
			ob_end_flush();
		}

		ob_start(function ($buffer) {
			return $buffer;
		});
	}

	/*
	 * --------------------------------------------------------------------
	 * Debug Toolbar Listeners.
	 * --------------------------------------------------------------------
	 * If you delete, they will no longer be collected.
	 */
	if (ENVIRONMENT !== 'production')
	{
		Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
		Services::toolbar()->respond();
    }

    // 페이지가 시작되었음을 로그로 기록
    log_message('debug', '--------------------- Page Start ---------------------');
    log_message('debug', sprintf('[%s] %s', $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']));
});

Events::on('post_controller_constructor', function() {
    $request = service('request');
    $router = service('router');
    $agent = $request->getUserAgent();

    // 사용자 정보 (IP주소, 플랫폼, 브라우저(버전), 모바일 단말명)
    log_message('debug', sprintf('[client] %s / %s / %s(%s) / %s',
        $request->getIPAddress(),
        $agent->getPlatform(),
        $agent->getBrowser(),
        $agent->getVersion(),
        $agent->isMobile() ? $agent->getMobile() : 'PC'
    ));

    // 실행된 소스의 경로, 클래스 이름, 함수 이름
    log_message('debug', sprintf('[source] %s/%s::%s',
        $router->directory(),
        $router->controllerName(),
        $router->methodName()
    ));
});

Events::on('post_system', function() {

    // 페이지가 종료되었음을 로그로 기록
    log_message('debug', '--------------------- Page Finish ---------------------' . PHP_EOL);
});