<?php
require "vendor/autoload.php";

use Paranoia\Payment\Factory;
use Paranoia\Payment\Request;
use Paranoia\Communication\Exception\CommunicationFailed;
use Paranoia\Payment\Exception\UnexpectedResponse;
// Iletisim sirasinda gonderilen ve alinan verileri dinlemek icin asagidaki
// satir(lar)da yeralan yorum isaretlerini kaldiriniz.
 use Paranoia\EventManager\Listener\CommunicationListener;

$config  = json_decode(file_get_contents('tests/Resources/config/config.json'));

$adapter = Factory::createInstance($config, 'estbank');

// Iletisim sirasinda gonderilen ve alinan verileri dinlemek icin asagidaki
// satir(lar)da yeralan yorum isaretlerini kaldiriniz.
 $listener = new CommunicationListener();
 $adapter->getConnector()->addListener('BeforeRequest', $listener);
 $adapter->getConnector()->addListener('AfterRequest', $listener);

$request = new Request();
$request->setCardNumber('5406675406675403')
        ->setSecurityCode('000')
        ->setExpireMonth(12)
        ->setExpireYear(2015)
        ->setOrderId('ORDER000000' . time())
        ->setAmount(100.35)
        ->setCurrency('TRY');

try {
    $response = $adapter->sale($request);
    if($response->isSuccess()) {
        print "Odeme basariyla gerceklestirildi." . PHP_EOL;
    } else {
        print "Odeme basarisiz." . PHP_EOL;
    }
} catch(CommunicationFailed $e) {
    print "Baglanti saglanamadi." . PHP_EOL;
} catch(UnexpectedResponse $e) {
    print "Banka beklenmedik bir cevap dondu." . PHP_EOL;
} catch(Exception $e) {
    print "Beklenmeyen bir hata olustu." . PHP_EOL;
}