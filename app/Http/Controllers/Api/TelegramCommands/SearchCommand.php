<?php

namespace App\Http\Controllers\Api\TelegramCommands;

use App\Http\Controllers\AdController;
use Illuminate\Http\Request;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class SearchCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'buscar';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['search'];

    /**
     * @var string Command Description
     */
    protected $description = 'Buscar anuncios en Bachecubano desde Telegram';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $text = $this->getUpdate()->getMessage()->getText();

        if (!isset($text)) {
            //Verify for search term
            $this->replyWithMessage(['text' => "Debes escribir lo que deseas buscar, ej: /buscar xiaomi"]);
        } else {

            //Explode incoming text
            $incoming_text = explode(" ", $this->getUpdate()->message->text);
            unset($incoming_text[0]);
            $params = implode(" ", $incoming_text);

            //Pretty Reply
            $this->replyWithMessage(['text' => "Buscando " . $params . " ..."]);

            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            //Instantiate Request object
            $request = new Request();
            $request->merge(['s' => $params]);

            //Get Ads from static method getAds
            $ads = AdController::getAds($request, null, 5, null, true);

            if ($ads->count() > 0) {
                foreach ($ads as $ad) {
                    //Pretty Reply
                    $this->replyWithMessage(['text' => $ad->description->title . "\n\n" . ad_url($ad)]);
                }
            } else {
                //Pretty Reply
                $this->replyWithMessage(['text' => "Lo sentimos " . $this->getUpdate()->message->from->username . " pero no hemos encontrado ningún anuncio con esas palabras."]);
            }
        }
    }
}
