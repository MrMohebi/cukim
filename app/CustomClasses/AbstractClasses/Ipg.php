<?php


namespace App\CustomClasses\AbstractClasses;


abstract class Ipg
{
    abstract static public function createPaymentLink(
        string $resEnglishName,
        string $userToken,
        string $trackingId,
        int $amount,
        string $itemType,
        array $items
    ):array;
    abstract static public function verifyPayment(
        string $code,
        string $refid,
        string $clientrefid,
        string $cardnumber,
        string $cardhashpan
    ):array;
}
