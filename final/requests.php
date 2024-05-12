<?php

require "const.php";
require "Zatca.php";

$zatca = new Zatca();

$keys = $zatca->generateKeys();

$configDetails = $zatca->createConfigCnf([
    'emailAddress' => 'ceo@asaas.rent',
    'commonName' => 'asaas.rent',
    'country' => 'SA',
    'organizationalUnitName' => 'Dammam Branch',
    'organizationName' => 'Property management Company',
    'serialNumber' => '1-Model|2-3492842|3-49182743421',
    'vatNumber' => '310479697700003',
    'invoiceType' => '1100',
    'registeredAddress' => 'Dammam',
    'businessCategory' => 'Software Development'
]);

$csr = $zatca->createCsr(
  $keys['privateKeyPath'],
  $configDetails['path']
);
$encodedCsr = $csr['base64'];

$otp = isset($argv[1]) ? $argv[1] : die("Please provide otp\n");

$compCsidResponse = $zatca->getCompCsid($encodedCsr, $otp);
if(isset($compCsidResponse["errors"]) && !empty($compCsidResponse["errors"])){
	print_r($compCsidResponse);exit;
}else {
	print_r("Otp verification handled successfully!\n");
	print_r($compCsidResponse);
 }
// Get Production CSID
// $prodCsidResponse = $zatca->getProdCsid($compCsidResponse);

// Getting Error (investigating)
// $zatca->renewProdCsid($compCsidResponse['binarySecurityToken']);

$invoice = $zatca->signXmlInvoice(
  __DIR__.SEP.'Samples'.SEP.'Standard'.SEP.'Invoice'.SEP.'Standard_Invoice_Original.xml', 
  __DIR__.SEP.'PrivateKey.pem'
);
print_r($invoice);
// UUID should be same as the one in invoice

$validateInvoice = $zatca->validateCompXmlInvoice($invoice, '8d487816-70b8-4ade-a618-9d620b73814a', $compCsidResponse);

// $validateInvoice = $zatca->reportXmlInvoice($invoice, '8d487816-70b8-4ade-a618-9d620b73814a', $compCsidResponse);
