<?php

namespace Box\Spout\Writer;

/**
 * Interface WriterInterface
 *
 * @package Box\Spout\Writer
 */
interface WriterInterface
{
    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be written to a file.
     *
     * @param  string $outputFilePath Path of the output file that will contain the data
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened or if the given path is not writable
     */
    public function openToFile($outputFilePath);

    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be outputted directly to the browser.
     *
     * @param  string $outputFileName Name of the output file that will contain the data. If a path is passed in, only the file name will be kept
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened
     */
    public function openToBrowser($outputFileName);

    /**
     * ===================================================================================================
     * 단순 Row 추가 시, 두 번째 매개 변수에 배열 $custom 전달하여 `height` 설정 가능
     * ===================================================================================================
     *
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @param  array $dataRow Array containing data to be streamed.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param array $custom : Key 값으로 'height' 명시하고 value 로 높이 지정
     * @return WriterInterface
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRow(array $dataRow, array $custom = array());

    /**
     * ===================================================================================================
     * Style을 포함한 Row 추가 시, 세 번째 매개 변수에 배열 $custom 전달하여 `height` 설정 가능
     * ===================================================================================================
     *
     * Write given data to the output and apply the given style.
     * @see addRow
     *
     * @param array $dataRow Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the row.
     * @param array $custom : Key 값으로 'height' 명시하고 value 로 높이 지정
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowWithStyle(array $dataRow, $style, array $custom = array());

    /**
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @param  array $dataRows Array of array containing data to be streamed.
     *          Example $dataRow = [
     *              ['data11', 12, , '', 'data13'],
     *              ['data21', 'data22', null],
     *          ];
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRows(array $dataRows);

    /**
     * Write given data to the output and apply the given style.
     * @see addRows
     *
     * @param array $dataRows Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the rows.
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowsWithStyle(array $dataRows, $style);

    /**
     * =========================================
     * 현재 처리중인 Sheet 가져오기
     * =========================================
     */
    public function getCurrentSheet();
    /**
     * =========================================
     * 새로운 Sheet 추가, 그리고 현재 처리중인 흐름 추가
     * =========================================
     */
    public function addNewSheetAndMakeItCurrent();

    /**
     * =====================================
     * cell | row --> Merge 기능
     * 
     * @author 홍준성 <powerstar13@kai-i.com>
     * @param string $sheetName : 시트명
     * @param string $start : 시작 cell | row
     * @param string $end : 종료 cell | row
     * =====================================
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
     * Closes the writer. This will close the streamer as well, preventing new data
     * to be written to the file.
     *
     * @return void
     */
    public function close();
}
