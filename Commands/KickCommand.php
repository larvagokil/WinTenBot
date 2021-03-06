<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 10.43
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class KickCommand extends UserCommand
{
    /**
     * Execute command
     *
     * @return void
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $from_id = $message->getFrom()->getId();
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $isAdmin = Grup::isAdmin($from_id, $chat_id);
        $isSudoer = Grup::isSudoer($from_id);
        if ($isAdmin || $isSudoer) {
            $kick_data = [
                'chat_id' => $chat_id,
                'user_id' => $repMssg->getFrom()->getId()
            ];

            $kick = Request::kickChatMember($kick_data);
            $text = '<b>Success : </b> ' . $kick->isOk();

            if ($kick->isOk()) {
                Request::unbanChatMember($kick_data);
                $text = '<b>Success : </b> True';
            } else {
                $text .= "\n<b>Code : </b>" . $kick->getErrorCode() .
                    "\n<b>Desc : </b> " . $kick->getDescription();
            }
        } else {
            $text = '🚫 <i>Kamu tidak memiliki akses /kick</i>';
        }

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

        $data = [
            'chat_id'    => $chat_id,
            'text'       => $text . $time,
            'parse_mode' => 'HTML'
        ];

        Request::sendMessage($data);
    }
}
