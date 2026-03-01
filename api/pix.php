<?php
$config = require_once ("../config.php");
$acessToken = $config['acesstoken'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "transaction_amount": 58,
  "description": "Payment for product",
  "external_reference": "MP0001",
  "payment_method_id": "pix",
    "payer": {
  "email": "",
  "first_name": "",
  "last_name": "",
  "identification": {
    "type": "CPF",
    "number": ""
  }
}
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'X-Idempotency-Key: 0d5020ed-1af6-469c-ae06-c3bec19954bb',
    'Authorization: Bearer '.$acessToken,
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$obj = json_decode($response);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/pix.css">
  <title>Pagamento PIX</title>
</head>
<body>
    <div class="pix-container">
        <h1>Finalize seu pagamento via PIX</h1>

        <?php if(isset($obj->id) && $obj != NULL): 
            $copiaCola = $obj->point_of_interaction->transaction_data->qr_code;
            $imgQrCode = $obj->point_of_interaction->transaction_data->qr_code_base64;
            $linkExterno = $obj->point_of_interaction->transaction_data->ticket_url;
        ?>
            <img src="data:image/png;base64, <?= $imgQrCode ?>" width="200" alt="QR Code PIX"/>
            <p>Escaneie o QR Code acima <br> ou copie o código abaixo:</p>
            <textarea rows="4" readonly><?= $copiaCola ?></textarea>
            <a href="<?= $linkExterno ?>" target="_blank">Abrir no App do Banco</a>

            <div class="timer" id="countdown">30:00</div>
        <?php else: ?>
            <p>Erro ao gerar o pagamento.</p>
        <?php endif; ?>
    </div>

<script>
// Contagem regressiva de 30 minutos
let time = 30 * 60;
function updateCountdown() {
  const minutes = Math.floor(time / 60);
  const seconds = time % 60;
  document.getElementById("countdown").innerText =
    `${minutes}:${seconds.toString().padStart(2, '0')}`;
  if (time <= 0) {
    clearInterval(timer);
    document.getElementById("countdown").innerText = "Expirado";
  }
  time--;
}
let timer = setInterval(updateCountdown, 1000);
</script>
</body>
</html>



