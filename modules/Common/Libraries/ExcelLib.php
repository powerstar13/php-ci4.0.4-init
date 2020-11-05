<?php namespace Modules\Common\Libraries;

// Read
use Box\Spout\Reader\ReaderFactory;
// Write
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\CellHorizontalAlignment;
use Box\Spout\Writer\Style\CellVerticalAlignment;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;

class ExcelLib
{
    private $request;

    public function __construct()
    {
        $this->request = service('request');
    }

    /**
     * ==========================
     * Excel Read
     * ==========================
     */
    public function readXlsx(string $filePath)
    {
        $reader = ReaderFactory::create(Type::XLSX);
        // $reader->setShouldPreserveEmptyRows(true); // 비어있는 row도 가져오려면 TRUE
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                debug($row, '[Sheet' . ($sheet->getIndex() + 1) . "] {$sheet->getName()} --> Row{$rowIndex}");
            }
        }

        $reader->close();
    }

    /**
     * ==========================================
     * Excel Write Custom Template
     * ==========================================
     */

    /**
     * 테두리 설정
     *
     * @param string $direction : 적용 영역
     *     - top, right, bottom, left : 한 면만 할 경우
     *     - bothTopRight, bothTopBottom, bothTopLeft, bothRightBottom, bothRightLeft, bothBottomLeft : 두 면만 할 경우
     *     - excludeTop, excludeRight, excludeBottom, excludeLeft : 한 면을 제외한 나머지 면을 할 경우
     *     - all (Another default) : 모든 면을 할 경우
     * @param string $color : 테두리 색
     * @param string $width : 테두리 두께
     * @param string $style : 테두리 유형
     * @return Border
     */
    public function border(
        string $direction = 'all',
        string $color = Color::BLACK,
        string $width = Border::WIDTH_THIN,
        string $style = Border::STYLE_SOLID
    ) {
        $border = new BorderBuilder();

        switch ($direction) {
            case Border::TOP:
                $border->setBorderTop($color, $width, $style);
                break;
            case Border::RIGHT:
                $border->setBorderRight($color, $width, $style);
                break;
            case Border::BOTTOM:
                $border->setBorderBottom($color, $width, $style);
                break;
            case Border::LEFT:
                $border->setBorderLeft($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::TOP) . ucfirst(Border::RIGHT):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderRight($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::TOP) . ucfirst(Border::BOTTOM):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderBottom($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::TOP) . ucfirst(Border::LEFT):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::RIGHT) . ucfirst(Border::BOTTOM):
                $border
                    ->setBorderRight($color, $width, $style)
                    ->setBorderBottom($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::RIGHT) . ucfirst(Border::LEFT):
                $border
                    ->setBorderRight($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'both' . ucfirst(Border::BOTTOM) . ucfirst(Border::LEFT):
                $border
                    ->setBorderBottom($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'exclude' . ucfirst(Border::TOP):
                $border
                    ->setBorderRight($color, $width, $style)
                    ->setBorderBottom($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'exclude' . ucfirst(Border::RIGHT):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderBottom($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'exclude' . ucfirst(Border::BOTTOM):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderRight($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
            case 'exclude' . ucfirst(Border::LEFT):
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderRight($color, $width, $style)
                    ->setBorderBottom($color, $width, $style);
                break;
            default:
                $border
                    ->setBorderTop($color, $width, $style)
                    ->setBorderRight($color, $width, $style)
                    ->setBorderBottom($color, $width, $style)
                    ->setBorderLeft($color, $width, $style);
                break;
        }

        return $border->build();
    }

    /**
     * 서식 스타일 설정
     *
     * @param boolean $fontBold : 글자 두껍게 사용 여부
     * @param integer $fontSize : 글자 크기
     * @param string $fontColor : 글자 색
     * @param string $fontName : 글꼴
     * @param string $backgroundColor : 배경 색
     * @param string $borderDirection : 테두리 방향
     * @param string $borderColor : 테두리 색
     * @param string $borderWidth : 테두리 두께
     * @param string $borderStyle : 테두리 유형
     * @param string $cellHorizontalAlign : 좌우 정렬
     * @param string $cellVerticalAlign : 상하 정렬
     * @param boolean $wrapText : 줄바꿈 여부
     * @return Style
     */
    public function style(
        bool $fontBold = FALSE,
        int $fontSize = 15,
        string $fontColor = Color::BLACK,
        string $fontName = '맑은 고딕',
        string $backgroundColor = Color::WHITE,
        string $borderDirection = 'all',
        string $borderColor = Color::BLACK,
        string $borderWidth = Border::WIDTH_THIN,
        string $borderStyle = Border::STYLE_SOLID,
        string $cellHorizontalAlign = CellHorizontalAlignment::CENTER,
        string $cellVerticalAlign = CellVerticalAlignment::CENTER,
        bool $wrapText = true
    ) {
        $style = new StyleBuilder();

        $border = $this->border($borderDirection, $borderColor, $borderWidth, $borderStyle);
        $style->setBorder($border);

        if ($fontBold) {
            $style->setFontBold();
        }

        return $style->setFontSize($fontSize)
            ->setFontColor($fontColor)
            ->setFontName($fontName)
            ->setBackgroundColor($backgroundColor)
            ->setCellHorizontalAlignment($cellHorizontalAlign)
            ->setCellVerticalAlignment($cellVerticalAlign)
            ->setShouldWrapText($wrapText)
            ->build();
    }

    // ----------------------------------------------------------------------------------------

    /**
     * Sample 템플릿
     *
     * @param string $fileName : 파일명
     * @param array $data : 엑셀 데이터
     * @return file 엑셀 파일
     */
    public function sampleTemplate($fileName = 'test', $data = array())
    {
        /**
         * (1) 문서 속성 지정
         * --> 불필요한 항목 생략 가능
         */
        $headStyle = $this->style(
            TRUE, 11, Color::BLACK, '맑은 고딕', // font
            'FFF2CC', // background
            'all', Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID, // border
            CellHorizontalAlignment::CENTER, CellVerticalAlignment::CENTER, // align
            true // 줄바꿈
        );

        $rowStyle = $this->style(
            FALSE, 11, Color::BLACK, '맑은 고딕', // font
            Color::WHITE, // background
            'all', Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID, // border
            CellHorizontalAlignment::CENTER, CellVerticalAlignment::CENTER, // align
            true // 줄바꿈
        );

        // 마지막 테두리 처리 스타일
        $endStyle = (new StyleBuilder())
            ->setBorder((new BorderBuilder())
                ->setBorderTop(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
                ->setBorderRight('D9D9D9', Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderBottom('D9D9D9', Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderLeft('D9D9D9', Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build())
            ->build();

        // Excel Write
        $saveFileName = sprintf('%s_%s.xlsx', $fileName, date('Ymd_H시i분'));
        if ($this->request->getUserAgent()->getBrowser() === 'Internet Explorer') {
            $saveFileName = iconv('UTF-8', 'EUC-KR', $saveFileName); // IE 에서는 파일명에 utf-8 처리가 안되기에 euc-kr로 변환
        }
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToBrowser($saveFileName);

        // sheet1
        $writer->getCurrentSheet()->setName($fileName);
        $writer->addRowsWithStyle($data['head'], $headStyle);

        $writer->colWidths($fileName, '1', '2', 20); // '1, 2 cell' 너비

        // 양식에 맞게 Rows 데이터 재가공 및 출력
        if (count($data['rows']) === 0) {
            $writer->mergeCells($fileName, 'A2', 'B2'); // '조회된 데이터가 없습니다.' HorizontalMerge(2)
            $writer->addRowWithStyle(array('조회된 데이터가 없습니다.', ''), $rowStyle);
        } else {
            foreach($data['rows'] as $index => $row) {
                $writer->addRowWithStyle(array(
                    number_format($index + 1),
                    $row['데이터 출력']
                ), $rowStyle);
            }
        }

        // 마지막줄 진한 테두리 처리 반영
        $endRow = array();
        for ($i = 1; $i <= count($data['head'][0]); $i++) {
            $endRow = array_merge($endRow, array(''));
        }
        $writer->addRowWithStyle($endRow, $endStyle);

        $writer->close();
        exit;
    }

    // ----------------------------------------------------------------------------------------

}