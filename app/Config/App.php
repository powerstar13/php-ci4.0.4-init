<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{

	/*
	|--------------------------------------------------------------------------
	| Base Site URL
	|--------------------------------------------------------------------------
	|
	| URL to your CodeIgniter root. Typically this will be your base URL,
	| WITH a trailing slash:
	|
	|	http://example.com/
	|
	| If this is not set then CodeIgniter will try guess the protocol, domain
	| and path to your installation. However, you should always configure this
	| explicitly and never rely on auto-guessing, especially in production
	| environments.
    |
    */
    public $baseURL = '/';

	/*
	|--------------------------------------------------------------------------
	| Index File
	|--------------------------------------------------------------------------
	|
    | 일반적으로 이것은 다른 것으로 이름을 바꾸지 않는 한 당신의 `index.php` 파일이 될 것이다.
    | mod_rewrite를 사용하여 페이지를 제거하려면 이 변수를 공백으로 설정하십시오.
    |
	| - `.htaccess` 통해서 index.php 경로에 제외되도록 처리함을 참고
	*/
	public $indexPage = '';

	/*
	|--------------------------------------------------------------------------
	| URI PROTOCOL
	|--------------------------------------------------------------------------
	|
	| This item determines which getServer global should be used to retrieve the
	| URI string.  The default setting of 'REQUEST_URI' works for most servers.
	| If your links do not seem to work, try one of the other delicious flavors:
	|
	| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
	| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
	| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
	|
	| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
	*/
	public $uriProtocol = 'REQUEST_URI';

	/*
	|--------------------------------------------------------------------------
	| Default Locale
	|--------------------------------------------------------------------------
	|
	| The Locale roughly represents the language and location that your visitor
	| is viewing the site from. It affects the language strings and other
	| strings (like currency markers, numbers, etc), that your program
	| should run under for this request.
	|
	*/
	public $defaultLocale = 'ko';

	/*
	|--------------------------------------------------------------------------
	| Negotiate Locale
	|--------------------------------------------------------------------------
	|
	| If true, the current Request object will automatically determine the
	| language to use based on the value of the Accept-Language header.
	|
	| If false, no automatic detection will be performed.
	|
    */
    // 이 기능이 활성화되면 시스템은 `$supportLocales`에 정의한 로케일 배열을 기반으로 올바른 언어를 자동으로 협상한다.
	public $negotiateLocale = true; // Request 클래스에 로케일을 협상하고 싶다면 `true`

	/*
	|--------------------------------------------------------------------------
	| Supported Locales
	|--------------------------------------------------------------------------
	|
	| If $negotiateLocale is true, this array lists the locales supported
	| by the application in descending order of priority. If no match is
	| found, the first locale will be used.
	|
	*/
	public $supportedLocales = ['ko', 'en']; // 지원하는 언어와 요청한 언어가 일치하지 않으면, 첫 번째 항목이 사용된다.

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| The default timezone that will be used in your application to display
	| dates with the date helper, and can be retrieved through app_timezone()
	|
	*/
	public $appTimezone = 'Asia/Seoul';

	/*
	|--------------------------------------------------------------------------
	| Default Character Set
	|--------------------------------------------------------------------------
	|
	| This determines which character set is used by default in various methods
	| that require a character set to be provided.
	|
	| See http://php.net/htmlspecialchars for a list of supported charsets.
	|
	*/
	public $charset = 'UTF-8';

	/*
	|--------------------------------------------------------------------------
	| URI PROTOCOL
	|--------------------------------------------------------------------------
	|
	| If true, this will force every request made to this application to be
	| made via a secure connection (HTTPS). If the incoming request is not
	| secure, the user will be redirected to a secure version of the page
	| and the HTTP Strict Transport Security header will be set.
	*/
	public $forceGlobalSecureRequests = false;

	/*
	|--------------------------------------------------------------------------
	| Session Variables
	|--------------------------------------------------------------------------
	|
	| 'sessionDriver'
	|
    |	세션 라이브러리는 다음 4가지개의 사용할 수 있는 핸들러 또는 스토리지 엔진을 제공합니다.
    |       - CodeIgniter\Session\Handlers\FileHandler
    |           : 가장 안전한 선택이며, 모든 곳에서 작동할 것으로 예상하기 때문에 세션이 초기화 될 때 기본적으로 사용된다.
    |
    |       - CodeIgniter\Session\Handlers\DatabaseHandler
    |           : `MySQL` 또는 `PostgreSQL`과 같은 관계형 데이터베이스를 사용하여 세션을 저장한다.
    |               - 이는 개발자가 어플리케이션 내에서 세션 데이터에 쉽게 액세스할 수 있기 때문에, 많은 사용자에게 인기있는 선택이다.
    |               - 영구 연결(persistent connection)을 사용할 수 없다.
    |               - 세션 테이블을 만든 다음 `$sessionSavePath`의 값으로 설정해야 한다.
    |                   - ex) 테이블 이름이 `ci_sessions`를 사용하려면
    |                       - pulbic $sessionSavePath = 'ci_sessions';
    |                   - MySQL 테이블 생성
    |                       CREATE TABLE IF NOT EXISTS `ci_sessions` (
    |                           `id` varchar(128) NOT NULL,
    |                           `ip_address` varchar(45) NOT NULL,
    |                           `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    |                           `data` blob NOT NULL,
    |                           KEY `ci_sessions_timestamp` (`timestamp`)
    |                       );
    |                   - 또한 `$sessionMatchIP` 설정에 따라 기본 키를 추가해야 한다.
    |                       - When sessionMatchIP = TRUE
    |                       ALTER TABLE ci_sessions ADD PRIMARY KEY (id, ip_address);
    |
    |                       - When sessionMatchIP = FALSE
    |                       ALTER TABLE ci_sessions ADD PRIMARY KEY (id);
    |
    |                       - To drop a previously created primary key (use when changing the setting)
    |                       ALTER TABLE ci_sessions DROP PRIMARY KEY;
    |
    |       - CodeIgniter\Session\Handlers\MemcachedHandler
    |           - `Memcached`의 잠금 메커니즘에 직접 접근할 수 없으므로, 이 드라이버의 잠금은 최대 300초 동안 유지되는 별도의 값으로 에뮬레이션 된다.
    |           - PHP의 `Memcached` 확장이 PECL과 일부 Linux를 통해 배포되기 때문에 가용성을 제외하고 모든면에서 `RedisHandler` 드라이버와 매우 유사하다.
    |           - 보너스 팁 : 콜론으로 구성된 세 번째(`:weight`) 값으로 옵션 `weight` 매개 변수를 사용하는 다중 서버 구성도 지원되지만, 신뢰할 수 있는지 테스트하지 않았다는 점에 유의해야 한다.
    |               - 서비스의 여러 경로를 쉼표(,)로 구분하여 작성한다.
    |               - public $sessionSavePath = 'localhost:8080:5,127.0.0.1:8080:1';
    |
    |       - CodeIgniter\Session\Handlers\RedisHandler
    |           - `Redis`의 잠금 메커니즘에 직접 접근할 수 없으므로, 이 드라이버의 잠금은 최대 300초 동안 유지되는 별도의 값으로 에뮬레이션 된다.
    |           - 고성능으로 인해 캐싱에 일반적으로 사용되는 스토리지 엔진이다.
    |           - 단점 : 관계형 데이터베이스만큼 편재적이지 않으며, 시스템에 `phppredis` PHP 확장이 설치되어 있어야 하며, PHP 번들로 제공되지 않는다.
    |           - `$sessionSavePath` 설정을 통해 세션의 저장 위치를 구성한다.
    |               - 대부분의 경우, 간단한 `host:port` 쌍만 있어도 충분하다.
    |               - public $sessionPath = 'tcp://localhost:6379';
    |
    |       - CodeIgniter\Session\Handlers\ArrayHandler
    |           : `ArrayHandler`는 테스트할 때 사용되며, PHP 배열에 모든 세션 데이터를 저장하여 데이터가 테스트 이후 유지되는 것을 방지한다.
	|
	| 'sessionCookieName'
	|
	|	The session cookie name, must contain only [0-9a-z_-] characters
	|
	| 'sessionExpiration'
	|
	|	The number of SECONDS you want the session to last.
	|	Setting to 0 (zero) means expire when the browser is closed.
	|
	| 'sessionSavePath'
	|
	|	The location to save sessions to, driver dependent.
	|
	|	For the 'files' driver, it's a path to a writable directory.
	|	WARNING: Only absolute paths are supported!
	|
	|	For the 'database' driver, it's a table name.
	|	Please read up the manual for the format with other session drivers.
	|
	|	IMPORTANT: You are REQUIRED to set a valid save path!
	|
	| 'sessionMatchIP'
	|
	|	Whether to match the user's IP address when reading the session data.
	|
	|	WARNING: If you're using the database driver, don't forget to update
	|	         your session table's PRIMARY KEY when changing this setting.
	|
	| 'sessionTimeToUpdate'
	|
	|	How many seconds between CI regenerating the session ID.
	|
	| 'sessionRegenerateDestroy'
	|
	|	Whether to destroy session data associated with the old session ID
	|	when auto-regenerating the session ID. When set to FALSE, the data
	|	will be later deleted by the garbage collector.
	|
	| Other session cookie settings are shared with the rest of the application,
	| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
	|
	*/
	public $sessionDriver            = 'CodeIgniter\Session\Handlers\DatabaseHandler'; // 세션 저장 방식
	public $sessionCookieName        = 'ci_session'; // 세션키값을 저장할 쿠키 이름
    public $sessionExpiration        = 0; // 세션 유효시간. 브러우저 닫힘과 동시에 세션 종료하려면 `0`
	public $sessionSavePath          = 'ci_sessions'; // DatabaseHandler --> 세션이 저장될 DB 테이블이름
	public $sessionMatchIP           = false; // 아이피 고정 옵션 --> IP가 유동적인 기기의 경우를 위해 `FALSE` 권장
	public $sessionTimeToUpdate      = 7200 * 12; // 세션 갱신 시간. 7200 (2시간) * 12 --> 24시간
    public $sessionRegenerateDestroy = true; // 세션 재 생성 시, 기존 값 삭제 여부
    // public $sessionDBGroup = 'tests'; // 사용할 데이터베이스 그룹 이름을 지정할 수 있다.

	/*
	|--------------------------------------------------------------------------
    | Cookie Related Variables
    | 쿠키 저장에 필요한 정보를 설정한다.
    |
    | - 도메인, 쿠키가 저장될 폴더 위치 등의 정보는 하나의 사이트 안에서 대부분 일괄된 값을 사용하기 때문에, CI는 이러한 정보를 설정파일을 통해 전역적으로 관리할 수 있게 해준다.
	|--------------------------------------------------------------------------
	|
	| 'cookiePrefix'   = Set a cookie name prefix if you need to avoid collisions
	| 'cookieDomain'   = Set to .your-domain.com for site-wide cookies
	| 'cookiePath'     = Typically will be a forward slash
	| 'cookieSecure'   = Cookie will only be set if a secure HTTPS connection exists.
	| 'cookieHTTPOnly' = Cookie will only be accessible via HTTP(S) (no javascript)
	|
	| Note: These settings (with the exception of 'cookie_prefix' and
	|       'cookie_httponly') will also affect sessions.
    |
    */
    // 모든 쿠키변수가 저장될 때, 이름 앞에 자동으로 붙을 단어
    public $cookiePrefix   = '';
    // 쿠키가 인식될 도메인을 명시 (공백인 경우, 현재 URL의 도메인)
    public $cookieDomain   = '';
    // 쿠키가 인식될 URL상의 경로.
    // "/"로 지정할 경우 사이트 전역에서 인식 가능
    public $cookiePath     = '/';
    // 보안설정 --> 기본값 유지
	public $cookieSecure   = false;
	public $cookieHTTPOnly = false;

	/*
	|--------------------------------------------------------------------------
	| Reverse Proxy IPs
	|--------------------------------------------------------------------------
	|
	| If your server is behind a reverse proxy, you must whitelist the proxy
	| IP addresses from which CodeIgniter should trust headers such as
	| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify
	| the visitor's IP address.
	|
	| You can use both an array or a comma-separated list of proxy addresses,
	| as well as specifying whole subnets. Here are a few examples:
	|
	| Comma-separated:	'10.0.1.200,192.168.5.0/24'
	| Array:		array('10.0.1.200', '192.168.5.0/24')
	*/
	public $proxyIPs = '';

	/*
	|--------------------------------------------------------------------------
	| Cross Site Request Forgery
	|--------------------------------------------------------------------------
	| Enables a CSRF cookie token to be set. When set to TRUE, token will be
	| checked on a submitted form. If you are accepting user data, it is strongly
	| recommended CSRF protection be enabled.
	|
	| CSRFTokenName   = The token name
	| CSRFHeaderName  = The header name
	| CSRFCookieName  = The cookie name
	| CSRFExpire      = The number in seconds the token should expire.
	| CSRFRegenerate  = Regenerate token on every submission
	| CSRFRedirect    = Redirect to previous page with error on failure
	*/
	public $CSRFTokenName  = 'csrf_test_name';
	public $CSRFHeaderName = 'X-CSRF-TOKEN';
	public $CSRFCookieName = 'csrf_cookie_name';
	public $CSRFExpire     = 7200;
	public $CSRFRegenerate = true;
	public $CSRFRedirect   = true;

	/*
	|--------------------------------------------------------------------------
	| Content Security Policy
	|--------------------------------------------------------------------------
    | 응답의 콘텐츠 보안 정책을 사용하여 이미지, 스크립트, CSS 파일, 오디오, 비디오 등에 사용할 수 있는 소스를 제한합니다.
    | 이 옵션을 사용하면 응답 개체는 ContentSecurityPolicy.php 파일에서 정책의 기본 값을 채웁니다.
    | 컨트롤러는 항상 런타임에 이러한 제한을 추가할 수 있다.
	|
	| For a better understanding of CSP, see these documents:
	|   - http://www.html5rocks.com/en/tutorials/security/content-security-policy/
	|   - http://www.w3.org/TR/CSP/
	*/
    public $CSPEnabled = false;

    /**
     * ========================================
     * 함수를 통해 수정해야할 설정값들은 생성자에서 처리
     * ========================================
     */
    public function __construct()
    {
        /**
         * ================================
         * 사이트 기본 주소 설정
         *
         * - 현재 접속중인 페이지의 프로토콜을 판별하여 '동적'으로 설정한다.
         * ex) http://projectName-local.com/ http://projectName-dev.com/  https://projectName.com/
         * ================================
         */
        $this->baseURL = (is_https() === TRUE ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/';
    }
}
