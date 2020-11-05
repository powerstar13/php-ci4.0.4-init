<?php namespace Modules\Common\Controllers;

use App\Controllers\BaseController;

class Cookie extends BaseController
{
    public function __construct()
    {
        helper('cookie');
    }

    /**
     * 쿠키 저장
     *
     * @param string $name : 쿠키에 저장할 이름
     * @param string $value : 쿠키에 저장할 값
     * @param integer $expire : 쿠키에 유지할 시간 (default '86400' : 24시간)
     * @return View
     */
	public function set()
	{
        $name = $this->request->getGet('name');
        $value = $this->request->getGet('value');
        $expire = $this->request->getGet('expire') ? : '86400';

        if (!empty($value)) {
            /**
             * (1) 입력값이 있다면 쿠키 저장하기
             *
             * 쿠키 파라미터 설정 값 --> $name, $value, $expire, $domain, $path, $prefix, $secure, $httpOnly
             * `$domain`, `$path`가 생략될 경우 `App.php`의 설정값을 사용
             * `$expire`이 생략될 경우 브라우저 종료 시 자동 삭제 (여기서는 $expire초 동안만 유지)
             */
            set_cookie($name, $value, $expire);
        } else {
            /**
             * (2) 입력값이 없다면 쿠키 삭제하기
             *
             * - `$time`을 음수값으로 설정할 경우 쿠키 즉시 삭제함
             * ex) set_cookie($name, false, -1);
             */
            delete_cookie($name); // 쿠키 헬퍼에 내장된 함수를 사용하여 쿠키 삭제
        }

		return redirect()->back()->withCookies();
    }
}
