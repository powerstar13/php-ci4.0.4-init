<?php namespace Modules\Common\Libraries;

/**
 * 사용자 정의 유효성 검사 라이브러리
 *
 * \app\Config\Validation.php --> `$ruleSets`에 등록 후 사용
 */
class ValidationLib
{
    /**
     * 비밀번호 검사
     *
     * @param string $password
     * @return boolean
     */
    public function passwordCheck($password)
    {
        $count = 0;
        // 특수문자
        if (preg_match("/[\!\"#$%\&'()\*\+,\-\.\/\:;<>=\?@\[\]\\\^\_`\{|\}~]/", $password)) {
            $count++;
        }
        // 숫자
        if (preg_match("/[0-9]/", $password)) {
            $count++;
        }
        // 영어
        if (preg_match("/[a-zA-Z]/", $password)) {
            $count++;
        }

        // 2가지 이상 조합과 최소 8자리 이상
        if ($count >= 2 && strlen($password) >= 8) {
            return true;
        } else {
            return false;
        }
    }
}