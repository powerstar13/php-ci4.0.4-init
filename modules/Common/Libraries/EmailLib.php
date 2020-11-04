<?php namespace Modules\Common\Libraries;

/**
 * 이메일 발송 처리 라이브러리
 */
class EmailLib
{
    private $senderAddr = 'powerstar13@hanmail.net'; // 발신자 주소
    private $senderName = '홍준성'; // 발신자 이름 (생략가능)
    private $email;

    public function __construct()
    {
        $this->email = service('email'); // SMTP 서버와 연동하여 메일 발송 기능을 제공하는 라이브러리 로드
    }

    /**
     * 이메일 유효성 검사
     *
     * @param string $email : 이메일 원본
     * @return array $result : 유효성 검사 결과
     */
    public function emailCheck(string $email = null) : array
    {
        $result = array();

        if (empty($email)) {
            $result['rt'] = 400;
            $result['rtMsg'] = '이메일을 입력해주세요.';
        } else if (!$this->email->isValidEmail($email)) {
            $result['rt'] = 400;
            $result['rtMsg'] = '올바른 이메일 형식이 아닙니다.';
        } else {
            $result['rt'] = 200;
            $result['rtMsg'] = '이메일 형식에 맞습니다.';
        }

        return $result;
    }

    /**
     * 회원가입 인증메일 내용 템플릿
     *
     * @param string $receiverAddr : 수신자 주소
     * @return string $content : 내용
     */
    public function joinTemplate(string $receiverAddr) : string
    {
        $imagePath = 'http://projectName-dev.com/_assets/images/common/ic-mini.png'; // 삽입할 이미지의 절대경로 (서버에 올라간 경로만 가능함)
        if (is_https()) {
            $imagePath = 'https://projectName.com/_assets/images/common/ic-mini.png'; // 실서버 이미지 경로
        }

        $joinPath = base_url('join?email=' . $receiverAddr); // 회원가입 이어가기 경로

        $content = '
            <div id="wrap">
                <!-- container -->
                <div id="container" style="width:100%;">
                    <div class="mail__con" style="width: 500px;margin: 0 auto;border-top: 5px solid #3b89e6;border-left: 1px solid #e3e3e3;border-right: 1px solid #e3e3e3;border-bottom: 1px solid #e3e3e3;border-radius: 5px;overflow: hidden;padding-bottom: 50px;">
                        <div style="padding:10px 15px 0;"><img src="' . $imagePath . '"></div>
                        <p style="margin-top: 35px;font-weight: bold;font-size: 24px;color: #111;text-align: center;">회원가입</p>
                        <p style="margin-top: 20px;text-align: center;font-size: 13px;color: #505050;line-height: 24px;">본인 확인을 위한 인증 메일입니다. <br>회원가입을 이어가시려면 아래 버튼을 눌러주세요.</p>
                        <div style="width: 240px;margin:30px auto 0;text-align: center;">
                            <a href="' . $joinPath . '" style="display: block;line-height: 55px;border-radius: 5px;overflow: hidden;background-color: #4b91e6;color: #fff;text-align: center;font-size: 15px;font-weight: bold;text-decoration: none;">회원가입 이어가기</a>
                        </div>
                    </div> <!--//container_wrap-->
                </div>
                <!-- //container -->
            </div>
        '; // 메일 내용

        return $content;
    }

    /**
     * 이메일 발송
     *
     * @param string $receiverAddr : 수신자 주소
     * @param string $subject : 제목
     * @param string $content : 내용
     * @return array $result : 결과값
     */
    public function sendMail(string $receiverAddr, string $subject = '', string $content = '') : array
    {
        $result = array();

        /** (1) 메일 발송 설정 */
        // 메일 발송 초기화
        $this->email->setNewline("\r\n"); // 메일 발송 시에 사용할 줄바꿈 규칙 설정
        $this->email->clear(); // 혹시 남아 있을 이전 발송 내역을 삭제

        /** (2) 메일 보내기 */
        $this->email->setFrom($this->senderAddr, $this->senderName); // 발신자 주소 + 이름 (이름 생략가능)
        $this->email->setTo($receiverAddr); // 수신자 주소 (배열로 복수지정 가능)
        $this->email->setReplyTo($this->senderAddr, $this->senderName); // 답장 받을 사람 (생략가능)
        $this->email->setSubject($subject); // 제목 설정
        $this->email->setMessage($content); // 내용 설정

        // 발송 후 결과값 전달
        if ($this->email->send()) {
            $result['rt'] = 200;
            $result['rtMsg'] = '발송 성공';
        } else {
            $result['rt'] = 500;
            $result['rtMsg'] = '발송 실패';
            $result['debug'] = $this->email->printDebugger(); // 디버그 메시지 사용하기
        }

        return $result;
    }
}