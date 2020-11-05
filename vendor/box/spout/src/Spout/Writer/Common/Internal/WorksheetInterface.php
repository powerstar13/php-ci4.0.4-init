<?php

namespace Box\Spout\Writer\Common\Internal;

/**
 * Interface WorksheetInterface
 *
 * @package Box\Spout\Writer\Common\Internal
 */
interface WorksheetInterface
{
    /**
     * @return \Box\Spout\Writer\Common\Sheet The "external" sheet
     */
    public function getExternalSheet();

    /**
     * @return int The index of the last written row
     */
    public function getLastWrittenRowIndex();

    /**
     * ===================================================================================================
     * Style을 포함한 Row 추가 시, 세 번째 매개 변수에 배열 $custom 전달하여 `height` 설정 가능
     * ===================================================================================================
     *
     * Adds data to the worksheet.
     *
     * @param array $dataRow Array containing data to be written.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row. NULL means use default style.
     * @param array $custom : Key 값으로 'height' 명시하고 value 로 높이 지정
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the data cannot be written
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    public function addRow($dataRow, $style, $custom);

    /**
     * =================================
     * cell | row --> Merge 기능
     *
     * @author 홍준성 <powerstar13@kai-i.com>
     * @param string $sheetName : 시트명
     * @param string $start : 시작 cell | row
     * @param string $end : 종료 cell | row
     * =================================
     */
    public function mergeCells($sheetName = 'sheet1', $start = '', $end = '');

    /**
     * =================================
     * Cell 너비 설정 기능
     *
     * @param string $sheetName : 적용할 Sheet 이름
     * @param string $min : 적용 시작할 Cell (A1 --> 1)
     * @param string $max : 적용 종료할 Cell (D1 --> 4)
     * @param string $width : 적용할 Cell의 너비값
     * @return void
     * =================================
     */
    public function colWidths($sheetName = 'sheet1', $min = '', $max = '', $width = '');

    /**
     * Closes the worksheet
     *
     * @return void
     */
    public function close();
}
