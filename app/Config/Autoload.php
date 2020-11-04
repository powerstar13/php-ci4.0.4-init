<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTO-LOADER
 * -------------------------------------------------------------------
 * This file defines the namespaces and class maps so the Autoloader
 * can find the files as needed.
 *
 * NOTE: If you use an identical key in $psr4 or $classmap, then
 * the values in this file will overwrite the framework's values.
 */
class Autoload extends AutoloadConfig
{

	/**
	 * -------------------------------------------------------------------
	 * Namespaces
	 * -------------------------------------------------------------------
	 * 이 맵핑은 응용프로그램의 이름 공간 위치를 파일 시스템의 위치로 매핑합니다.
     * 이것들은 자동 로더에 의해 처음 인스턴스화되었을 때 파일을 찾는 데 사용된다.
     *
     * '/app' 및 '/system' 디렉토리가 이미 매핑되었습니다.
     * 원하는 경우 'App' 네임스페이스의 이름을 변경할 수 있지만, 네임스페이스 클래스를 만들기 전에 이 작업을 수행해야 합니다.
     * 그렇지 않으면 이 작업이 수행되도록 모든 클래스를 수정해야 합니다.
	 *
	 * Prototype:
	 *
	 *   $psr4 = [
	 *       'CodeIgniter' => SYSTEMPATH,
	 *       'App'	       => APPPATH
	 *   ];
	 *
	 * @var array
	 */
	public $psr4 = [
		APP_NAMESPACE => APPPATH, // For custom app namespace
        'Config'      => APPPATH . 'Config',
        'Modules'     => ROOTPATH . 'modules', // 모듈 경로 등록
	];

	/**
	 * -------------------------------------------------------------------
	 * Class Map
	 * -------------------------------------------------------------------
	 * 클래스 맵은 클래스 이름과 드라이브의 정확한 위치를 매핑합니다.
     * 이러한 방식으로로드된 클래스는 네임 스페이스를 통해 자동로드되는 경우처럼 하나 이상의 디렉토리 내에서 검색할 필요가 없기 때문에 성능이 약간 빨라집니다.
	 *
	 * Prototype:
	 *
	 *   $classmap = [
	 *       'MyClass'   => '/path/to/class/file.php'
	 *   ];
	 *
	 * @var array
	 */
	public $classmap = [];
}
