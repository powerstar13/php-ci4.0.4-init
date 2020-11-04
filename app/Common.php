<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

/**
 * ======================================
 * 유용하게 사용할 함수 정의
 * ======================================
 */

// ------------------------------------------------------------------------

if ( ! function_exists('is_https'))
{
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('isLocalServer'))
{
	/**
	 * 현재 로컬에서 작업중인지 검사
     * - 개발자인지 운영서버(또는 테스트서버)인지에 따라 활용가능
	 *
	 * @return	bool
	 */
	function isLocalServer()
	{
        if (in_array($_SERVER['SERVER_NAME'], array('projectName-dev.com', 'projectName.com'))) {
            return FALSE;
        } else {
            return TRUE;
        }
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('isTestServer'))
{
	/**
	 * 현재 테스트서버인지 검사
	 *
	 * @return	bool
	 */
	function isTestServer()
	{
        if ($_SERVER['SERVER_NAME'] === 'projectName-dev.com') {
            return TRUE;
        } else {
            return FALSE;
        }
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('isDeployServer'))
{
	/**
	 * 현재 운영서버인지 검사
	 *
	 * @return	bool
	 */
	function isDeployServer()
	{
        if ($_SERVER['SERVER_NAME'] === 'projectName.com') {
            return TRUE;
        } else {
            return FALSE;
        }
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('formValidMsg'))
{
    /**
     * 폼 입력 결과 errors가 존재할 경우, 에러 메시지 출력
     *
     * @param array $data : 전달된 errors가 담겨 있을 배열
     * @param string $filed : 가져올 메시지의 필드
     * @param string $type : alert | text 타입 구분
     * @return string $msg : 에러 메시지 출력
     */
	function formValidMsg(array $data, string $filed, string $type = 'alert')
	{
        $msg = null;

        if (!isset($data['errors'])) {
            $validation = service('validation');
            $data['errors'] = $validation->getErrors();
        }

        if (isset($data['errors'][$filed])) {
            if ($type === 'alert') {
                if (isset($data['errors']['subMsg']) && !empty(($data['errors']['subMsg']))) {
                    return "customAlert(\"{$data['errors'][$filed]}\", \"{$data['errors']['subMsg']}\");"; // Javascript 에서 출력
                } else {
                    return "customAlert(\"{$data['errors'][$filed]}\", '');"; // Javascript 에서 출력
                }
            } else if($type === 'text') {
                return "<p class='error'>{$data['errors'][$filed]}</p>"; // Form 에서 출력
            }
        }

        return $msg;
	}
}

// ------------------------------------------------------------------------

if (! function_exists('debug'))
{
    /**
     * 데이터 출력
     *
     * - 각 변수, 배열, 객체들의 데이터를 확인하기 위해 print_r() 함수를 사용할 일이 빈번하다.
     * - 하지만 print_r() 함수는 HTML의 `<pre>` 태그와 함께 사용해야만 브라우저에서 내용을 확인하는데 있어 용이하기 때문에, 이 과정을 하나의 함수로 묶어 처리하도록 하면, 앞으로의 결과 확인에 도움이 된다.
     *
     * @param mixed $msg
     * @param string $title
     * @return string html
     */
	function debug($msg, string $title = 'debug')
	{
        $content = print_r($msg, true); // 전달된 내용을 출력형식으로 변환

        echo "
            <div class='debug'>
                <fieldset style='padding: 15px; margin: 10px; border: 1px solid #bce8f1; border-radius: 4px; color: #31708f; background-color: #d9edf7; word-break: break-all; font-size: 15px; font-family: D2Coding,NanumGothicCoding,나눔고딕코딩,Helvetica,굴림'>
                    <legend style='padding: 2px 15px; border: 1px solid #bce8f1; background-color: #fff; font-weight: bold'>"
                        . $title .
                    "</legend>
                    <pre style='margin: 0px; padding: 0; border:0; background: none; white-space: pre-wrap;'>"
                        . htmlspecialchars($content) .
                    "</pre>
                </fieldset>
            </div>
        ";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('p'))
{
	/**
	 * 문자열을 escape 처리 후 출력하기
     * - apache > php.ini > short_open_tag = off 의 경우 유용
	 *
	 * @param mixed $data : echo 가능한 문자열 또는 숫자
     * @param string $context : 사용할 컨텍스트 (html, js, css, url, attr, raw, null)
	 */
	function p($data, string $context = 'html')
	{
		echo esc($data, $context);
	}
}

// ------------------------------------------------------------------------

if (! function_exists('getPageInfo'))
{
    /**
     * 페이지 구현에 필요한 변수값들을 계산한다.
     * @param integer $totalCount - 페이지 계산의 대상이 되는 전체 데이터 수
     * @param integer $nowPage    - 현재 페이지
     * @param integer $listCount  - 한 페이지에 보여질 목록의 수
     * @param integer $groupCount - 페이지 그룹 수
     * @return array $data : 모든 결과값
     *                  - nowPage    : 현재 페이지
     *                  - totalCount : 전체 데이터 수
     *                  - listCount  : 한 페이지에 보여질 목록의 수
     *                  - totalPage  : 전체 페이지 수
     *                  - groupCount : 한 페이지에 보여질 그룹의 수
     *                  - totalGroup : 전체 그룹 수
     *                  - nowGroup   : 현재 페이지가 속해 있는 그룹 번호
     *                  - group_start : 현재 그룹의 시작 페이지
     *                  - groupEnd   : 현재 그룹의 마지막 페이지
     *                  - prevGroupLastPage  : 이전 그룹의 마지막 페이지
     *                  - nextGroupFirstPage : 다음 그룹의 시작 페이지
     *                  - offset      : SQL의 Limit절에서 사용할 데이터 시작 위치
     *
     * View에서 Row number 계산하기
     * 1. 내림차순 : totalCount - offset - index
     * 2. 오름차순 : offset + 1 + index
     */
	function getPageInfo(int $totalCount, int $nowPage = 1, int $listCount = 15, int $groupCount = 5) : array
	{
        /** ===== 계산 ===== */
        // 전체 페이지 수
        $totalPage = intval(($totalCount - 1) / $listCount) + 1;

        // 전체 그룹 수
        $totalGroup = intval(($totalPage - 1) / $groupCount) + 1;

        // 현재 페이지가 속한 그룹
        $nowGroup = intval(($nowPage - 1) / $groupCount) + 1;

        // 현재 그룹의 시작 페이지 번호
        $groupStart = intval(($nowGroup - 1) * $groupCount) + 1;

        // 현재 그룹의 마지막 페이지 번호
        $groupEnd = min($totalPage, $nowGroup * $groupCount);

        // 이전 그룹의 마지막 페이지 번호
        $prevGroupLastPage = 0;
        if ($groupStart > $groupCount) $prevGroupLastPage = $groupStart - 1;

        // 다음 그룹의 시작 페이지 번호
        $nextGroupFirstPage = 0;
        if ($groupEnd < $totalPage) $nextGroupFirstPage = $groupEnd + 1;

        // Limit 절에서 사용할 데이터 시작 위치
        $offset = ($nowPage - 1) * $listCount;

        // 리턴할 데이터들을 배열로 묶기
        $data = array(
            'nowPage' => $nowPage,
            'totalCount' => $totalCount,
            'listCount' => $listCount,
            'totalPage' => $totalPage,
            'groupCount' => $groupCount,
            'totalGroup' => $totalGroup,
            'nowGroup' => $nowGroup,
            'groupStart' => $groupStart,
            'groupEnd' => $groupEnd,
            'prevGroupLastPage' => $prevGroupLastPage,
            'nextGroupFirstPage' => $nextGroupFirstPage,
            'offset' => $offset
        );

        return $data;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('emptyCheckP'))
{
	/**
	 * 빈 값인지 검사 후, 형태에 맞춰 출력
	 *
	 * @param $data : 빈값인지 체크할 데이터
     * @param $text : 사용할 텍스트
     * @param $default : 빈값일 경우 사용할 텍스트
     * @return string : 내용 출력
	 */
	function emptyCheckP($data = null, $text = null, $default = null)
	{
		p(isset($data) && (!empty($data) || "{$data}" === '0') ? $text : $default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('emptyCheckReturn'))
{
	/**
	 * 빈 값인지 검사 후, 형태에 맞춰 리턴
	 *
	 * @param $data : 빈값인지 체크할 데이터
     * @param $text : 사용할 텍스트
     * @param $default : 빈값일 경우 사용할 텍스트
     * @return string : 내용 리턴
	 */
	function emptyCheckReturn($data = null, $text = null, $default = null)
	{
		return (isset($data) && (!empty($data) || "{$data}" === '0') ? $text : $default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('issetCheckP'))
{
	/**
	 * 존재하는 값인 값인지 검사 후, 형태에 맞춰 출력
	 *
	 * @param $data : 존재하는 값인지 체크할 데이터 배열
	 * @param $find : 검사할 값
     * @param $default : 존재하지 않는 값일 경우 사용할 텍스트
     * @return string : 내용 출력
	 */
	function issetCheckP($data = null, $find = null, $default = null)
	{
        p(isset($data[$find]) && (!empty($data[$find]) || "{$data[$find]}" === '0')
            ? $data[$find]
            : $default
        );
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('issetCheckReturn'))
{
	/**
	 * 존재하는 값인 값인지 검사 후, 형태에 맞춰 출력
	 *
	 * @param $data : 존재하는 값인지 체크할 데이터 배열
	 * @param $find : 검사할 값
     * @param $default : 존재하지 않는 값일 경우 사용할 텍스트
     * @return string : 내용 리턴
	 */
	function issetCheckReturn($data = null, $find = null, $default = null)
	{
        return isset($data[$find]) && (!empty($data[$find]) || "{$data[$find]}" === '0')
            ? $data[$find]
            : $default;
	}
}

// ------------------------------------------------------------------------