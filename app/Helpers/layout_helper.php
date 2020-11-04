<?php

/**
 * ========================================
 * `layout_helper` 안에서 사용할 함수
 * ========================================
 */

if (! function_exists('getJavascriptPath'))
{
    /**
     * 자바스크립트 파일 호출하기
     *
     * @param [type] $viewpath : 원본 content View
     * @return string $javascriptPath : 자바스크립트 경로 (확장자 .php)
     */
    function getJavascriptPath($viewpath)
    {
        $viewPathExplode = explode('\\', $viewpath); // 경로 조립 위해 추출

        // 주의 : Modules\...\Views\... 자료구조를 통해 작성된 프로젝트에만 사용
        if ($viewPathExplode[0] === 'Modules' && count($viewPathExplode) >= 4) {

            $javascriptFilePath = $javascriptPath = '';

            foreach ($viewPathExplode as $index => $value) {

                if (count($viewPathExplode) === ($index + 1)) {

                    $javascriptFilePath .= "js/{$value}.php";
                    $javascriptPath .= "js\\{$value}";

                } else {
                    $javascriptPath .= "{$value}\\";

                    if ($index === 0) {
                        $javascriptFilePath .= strtolower($value) . '/';

                    } else {
                        $javascriptFilePath .= "{$value}/";
                    }
                }
            }

            // `Views\js\...` ~ `Views\...\js\...` 등 파일이 존재하지 않을 경우 (존재하면 js 파일 경로 반환)
            if (file_exists(ROOTPATH . $javascriptFilePath) === false) {
                $javascriptPath = null;
            }
        }

        return $javascriptPath;
    }
}

/**
 * ===================
 * Layout
 * ===================
 */

if (! function_exists('layoutRender'))
{
    /**
     * 레이아웃 렌더링
     *
     * @param string $layoutDirectory : 레이아웃 프레임
     *     - default, guest
     * @param string $viewpath : 보여질 content view
     * @param array $resource : 전달될 데이터 원본
     * @return view
     */
	function layoutRender(string $layoutDirectory, string $viewpath, array $resource = [])
	{
        if (empty($viewpath)) {
            throw new \RuntimeException('전달된 뷰 경로가 없습니다.', 400);
        }

        $javascript = getJavascriptPath($viewpath); // JS경로 가져오기

        $data = array(
            'viewpath' => $viewpath, // 보여줄 화면경로
            'data' => $resource, // 데이터 원본
            'javascript' => $javascript,
        );

        /**
         * - 전달한 데이터는 호출된 `view`에 대해 한 번만 사용 가능하다.
         * - 단일 요청에서 `view` 함수를 여러번 호출한다면 각 뷰 호출에 데이터를 전달해야 한다.
         *     - 그렇지 않으면, 모든 데이터가 다른 뷰로 `전달`되지 않아 문제가 발생할 수 있다.
         * - `view` 함수의 세 번째 매개 변수 `$option` 배열에 `saveData` 옵션을 사용하여 데이터를 유지할 수 있다.
         */
        return view("Modules\\Common\\Views\\layout\\{$layoutDirectory}\\index", $data, ['saveData' => true]); // 뷰(view) 함수가 데이터를 유지한다.
	}
}
