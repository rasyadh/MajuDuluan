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
    echo '<h3>Siapa yang maju duluan ?<h3><p>Chatbot  untuk melakukan pengurutan nomor siapa yang maju duluan.</p><br/>By RSDH.';
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
            if ($event['type'] == 'follow'){

                $textMessageBuilder1 = new TextMessageBuilder('Maju Duluan\nChatbot untuk melakukan pengurutan nomor siapa yang maju duluan.\n\nMau tau siapa yang maju duluan ?\nKirim aja daftar nama - namanya.');
                $textMessageBuilder2 = new TextMessageBuilder('Penggunaan chatbot seperti ini :\nmaju: (Acak urutan maju) , contoh : Maju: Aziz, Ardika, Fatih\nduluan: (Menampilkan cara penggunaan).');
                $textMessageBuilder3 = new TextMessageBuilder('Line Chatbot by RSDH');
                $stickerMessageBuilder = new StickerMessageBuilder(1, 114);

                $multiMessageBuilder = new MultiMessageBuilder();
                $multiMessageBuilder->add($textMessageBuilder1);
                $multiMessageBuilder->add($textMessageBuilder2);
                $multiMessageBuilder->add($textMessageBuilder3);
                $multiMessageBuilder->add($stickerMessageBuilder);
                
                $result = $bot->replyMessage($event['replyToken'], $multiMessageBuilder);
            
                return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());                
            }
            if ($event['type'] == 'message'){
                if ($event['message']['type'] == 'text'){

                    $text = $event['message']['text'];

                    if (strpos($text, 'maju:') !== false){
                        
                        // reply for key maju:
                        $text = str_replace("maju:", "", $text);
                        $text = trim($text);
                        
                        if (strpos($text, ',') !== false){
                            $temp[] = explode(",", $text);
                            $list = [];
                            
                            for ($i = 0; $i < sizeof($temp[0]); $i++){
                                $list[$i] = preg_replace('/\s+/', '', $temp[0][$i]);
                            }

                            sort($list);

                            $sorted_text = '';
                            for ($i = 0; $i < sizeof($list); $i++){
                                $sorted_text = $sorted_text.($i + 1).'. '.$list[$i].'\n';
                            }

                            $result = $bot->replyText($event['replyToken'], $sorted_text);

                            return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                        }
                        else {
                            $result = $bot->replyText($event['replyToken'], 'Penggunaan maju:\ncontoh : Maju: Aziz, Ardika, Fatih');

                            return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                        }
                    }
                    else if (strpos($text, 'duluan:') !== false){

                        // reply for key duluan:
                        $textMessageBuilder1 = new TextMessageBuilder('Maju Duluan\nChatbot untuk melakukan pengurutan nomor siapa yang maju duluan.\n\nMau tau siapa yang maju duluan ?\nKirim aja daftar nama - namanya.');
                        $textMessageBuilder2 = new TextMessageBuilder('Penggunaan chatbot seperti ini :\maju: (Acak urutan maju) , contoh : Maju: Aziz, Ardika, Fatih\nduluan (Menampilkan cara penggunaan).');
                        $textMessageBuilder3 = new TextMessageBuilder('Line Chatbot by RSDH');

                        $multiMessageBuilder = new MultiMessageBuilder();
                        $multiMessageBuilder->add($textMessageBuilder1);
                        $multiMessageBuilder->add($textMessageBuilder2);
                        $multiMessageBuilder->add($textMessageBuilder3);
                        
                        $result = $bot->replyMessage($event['replyToken'], $multiMessageBuilder);
                    
                        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }
                }
            }
        }
    }

});

$app->get('/pushmessage', function($req, $res) use ($bot){

    // send push message to user
    $userId = 'Ua3aaca316b8b53a1632f7abfc1e6872c';  // id user linebot majuduluan

    $textMessageBuilder = new TextMessageBuilder("Push Message");
    
    $result = $bot->pushMessage($userId, $textMessageBuilder);
   
    return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
});

$app->get('/multicast', function($req, $res) use ($bot)
{
    // list of users
    $userList = [
        'Ua3aaca316b8b53a1632f7abfc1e6872c',
        'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'];

    // send multicast message to user
    $textMessageBuilder = new TextMessageBuilder('Multicasr Message');
    $result = $bot->multicast($userList, $textMessageBuilder);
   
    return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
});

$app->run();