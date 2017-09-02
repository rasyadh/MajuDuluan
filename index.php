<?php
require __DIR__ . '/vendor/autoload.php';

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// set false for production
$pass_signature = true;

// set LINE channel_access_token and channel_secret
$channel_access_token = "CBvYlEBbsbRe5F8asDx7wKhbw3Y8Ri60J471DhR+tIC8AtgTmwlOVfS3OvGGk6zEW/s/AYXIHpSbgsHB9mnO99ogX6C6lLJUsqJW12YDA/wmJaVTIXJg3Qf7Hfy2GtDYQuTL8NAo9MbmvwvF7wLOCgdB04t89/1O/w1cDnyilFU=";
$channel_secret = "d4d727a3500b3e58ff5fdb3c75003c9d";

// inisiasi object bot
$http_client = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($http_client, ['channelSecret' => $channel_secret]);

$configs = [
    'settings' => ['displayErrorDetails' => true],
];

$app = new Slim\App($configs);

// route untuk url homepage
$app->get('/', function($req, $res){
    echo '<h3>Siapa yang maju duluan ?<h3><br><p>Chatbot  untuk melakukan pengurutan nomor siapa yang maju duluan.</p><br/>By RSDH.';
});

// route untuk webhook
$app->post('/webhook', function($request, $response) use ($bot, $pass_signature){

    // get request body and line signature header
    $body           = file_get_contents('php://input');
    $signature      = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';

    // log body and signature
    file_put_contents('php://stderr', 'Body: '.$body);

    if ($pass_signature === false){

        // is LINE_SIGNATURE exists in request header ?
        if (empty($signature)){
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE ?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)){
            return $response->withStatus(400, 'Invalid signature');
        }
    }

    // app code goes here !
    $data = json_decode($body, true);
    if (is_array($data['events'])){
        foreach ($data['events'] as $event){
            if ($event['type'] == 'message'){
                if ($event['message']['type'] == 'text'){

                    // reply with replyText()
                    // $result = $bot->replyText($event['replyToken'], $event['message']['text']);

                    // reply with replyMessage()
                    // $textMessageBuilder = new TextMessageBuilder("Tebak Kode");
                    // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);

                    // reply with sticker stickerMessageBuilder()
                    // $packageId = 1; $stickerId = 13;
                    // $stickerMessageBuilder = new StickerMessageBuilder($packageId, $stickerId);
                    // $result = $bot->replyMessage($event['replyToken'], $stickerMessageBuilder);

                    // reply with MultiMessageBuilder()
                    $textMessageBuilder1 = new TextMessageBuilder('Maju Duluan\nSiapa yang maju duluan ?\n\nChatbot  untuk melakukan pengurutan nomor siapa yang maju duluan.\n\n');
                    $textMessageBuilder2 = new TextMessageBuilder('Line Chatbot by RSDH');
                    $stickerMessageBuilder = new StickerMessageBuilder(1, 114);

                    $multiMessageBuilder = new MultiMessageBuilder();
                    $multiMessageBuilder->add($textMessageBuilder1);
                    $multiMessageBuilder->add($textMessageBuilder2);
                    $multiMessageBuilder->add($stickerMessageBuilder);

                    $result = $bot->replyMessage($event['replyToken'], $multiMessageBuilder);

                    return $response->withJson($result->getJSONDecodeedBody(), $result->getHTTPStatus());
                }
            }
        }
    }

});

$app->run();