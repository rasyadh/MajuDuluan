<?php

// reply with replyText()
$bot->replyText($event['replyToken'], $event['message']['text']);

// reply with replyMessage()
$textMessageBuilder = new TextMessageBuilder("Mau tau siapa yang maju duluan ?\r\nKirim aja daftar nama - namanya.\r\ncontoh : Aziz, Ardika, Fatih.");
$bot->replyMessage($event['replyToken'], $textMessageBuilder);

// reply with sticker stickerMessageBuilder()
$packageId = 1; $stickerId = 13;
$stickerMessageBuilder = new StickerMessageBuilder($packageId, $stickerId);
$bot->replyMessage($event['replyToken'], $stickerMessageBuilder);

// reply with MultiMessageBuilder()
$textMessageBuilder1 = new TextMessageBuilder('Maju Duluan\r\nSiapa yang maju duluan ?\r\nChatbot  untuk melakukan pengurutan nomor siapa yang maju duluan.');
$textMessageBuilder2 = new TextMessageBuilder('Line Chatbot by RSDH');
$stickerMessageBuilder = new StickerMessageBuilder(1, 114);

$multiMessageBuilder = new MultiMessageBuilder();
$multiMessageBuilder->add($textMessageBuilder1);
$multiMessageBuilder->add($textMessageBuilder2);
$multiMessageBuilder->add($stickerMessageBuilder);

$bot->pushMessage($event['replyToken'], $multiMessageBuilder);