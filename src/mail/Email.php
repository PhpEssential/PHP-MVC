<?php

namespace phpessential\mvc\mail;

use phpessential\mvc\utils\LogUtils;
use phpessential\mvc\Config;

class Email {
    private $receiver;
    private $sender;
    private $subject;
    private $message;
    private $charset = "UTF-8";
    private $contentType = "text/plain";

    public function __construct(string $receiver, string $subject, string $message) {
        $this->receiver = $receiver;
        $this->subject = $subject;
        $this->message = $message;
        if (Config::has(Config::EMAIL_DEFAULT_SENDER)) {
            $this->sender = Config::get(Config::EMAIL_DEFAULT_SENDER);
        }
    }

    public function setSender(string $sender) {
        $this->sender = $sender;
    }

    /**
     *
     * @param string $charset
     * @return Email
     */
    public function setCharset(string $charset): Email {
        $this->charset = $charset;
        return $this;
    }

    /**
     *
     * @param string $contentType
     * @return Email
     */
    public function setContentType(string $contentType): Email {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     *
     * @return string
     */
    private function getHeader(): string {
        return 'MIME-Version: 1.0' . "\r\n" . 'Content-type: ' . $this->contentType . '; charset=' . $this->charset . "\r\n" . 'From: '
                . $this->sender . "\r\n" . 'X-Mailer: PHP/' . phpversion();
    }

    /**
     * Send the e-mail
     *
     * @throws \Exception
     */
    public function send() {
        try {
            if (!mail($this->receiver, $this->subject, $this->message, $this->getHeader())) {
                throw new \Exception("failed to send e-mail");
            }
        } catch (\Exception $e) {
            LogUtils::error($e);
            throw $e;
        }
    }

}
