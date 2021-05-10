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
    );
    abstract static public function verifyPayment();
}
