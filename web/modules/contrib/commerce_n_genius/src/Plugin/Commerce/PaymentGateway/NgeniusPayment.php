<?php

namespace Drupal\commerce_n_genius\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_price\Price;

/**
 * Provides N-Genius payment iframe.
 *
 * @CommercePaymentGateway(
 *   id = "n_genius_payment",
 *   label = "N-Genius",
 *   display_label = "N-Genius payment gateway",
 *   forms = {
 *     "offsite-payment" = "Drupal\commerce_n_genius\PluginForm\NgeniusPaymentRedirect",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 * )
 */
class NgeniusPayment extends OffsitePaymentGatewayBase
{

  const IDSERVICEURL = 'https://api-gateway.ngenius-payments.com/transactions/outlets/';
  const RESIDSERVICEURL = 'https://identity.ngenius-payments.com/auth/realms/NetworkInternational/protocol/openid-connect/token';

  const TESTRESIDSERVICEURL = 'https://identity-uat.ngenius-payments.com/auth/realms/ni/protocol/openid-connect/token';
  const TESTIDSERVICEURL = 'https://api-gateway-uat.ngenius-payments.com/transactions/outlets/';

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['outlet_ref'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Outlet ref'),
      '#size' => 60,
      '#id' => 'amount',
      '#maxlength' => 128,
      '#required' => TRUE,
      '#default_value' => $this->configuration['outlet_ref'],
    ];

    $form['apikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#size' => 60,
      '#id' => 'amount',
      '#maxlength' => 128,
      '#required' => TRUE,
      '#default_value' => $this->configuration['apikey'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return parent::defaultConfiguration() + [
      'outlet_ref' => '',
      'apikey' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);
    $values = $form_state->getValue($form['#parents']);
    $this->configuration['outlet_ref'] = $values['outlet_ref'];
    $this->configuration['apikey'] = $values['apikey'];
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request)
  {
    $config = $this->getConfiguration();

    $outletRef = $config['outlet_ref'];
    $apiKey = $config['apikey'];
    $mode = $config['mode'];

    $orderReference = $_GET['ref'];

    if ($mode == 'live') {
      $residServiceURL = self::IDSERVICEURL . $outletRef . '/orders/' . $orderReference;
      $idServiceURL = self::RESIDSERVICEURL;
    } else {
      $residServiceURL = self::TESTIDSERVICEURL . $outletRef . '/orders/' . $orderReference;
      $idServiceURL = self::TESTRESIDSERVICEURL;
    }

    $tokenHeaders = ["Authorization: Basic " . $apiKey, "Content-Type: application/x-www-form-urlencoded"];
    $tokenResponse = $this->invokeCurlRequest('POST', $idServiceURL, $tokenHeaders, http_build_query(['grant_type' => 'client_credentials']));
    $tokenResponse = json_decode($tokenResponse);
    $access_token = $tokenResponse->access_token;
    // dd($tokenResponse);
    $responseHeaders = ['Authorization: Bearer ' . $access_token, 'Content-Type: application/vnd.ni-payment.v2+json', 'Accept: application/vnd.ni-payment.v2+json'];
    $orderResponse = $this->invokeCurlRequest('GET', $residServiceURL, $responseHeaders, http_build_query(['grant_type' => 'client_credentials']));
    $response = json_decode($orderResponse, TRUE);
    // dd($response);
    // dd($response);
    if($mode=="live"){
    if ($response['_embedded']['payment'][0]['state'] == "PURCHASED") {
      $payment_gateway = $order->get('payment_gateway')->getString();
      // Get the payment method from the order.
      $payment_method = $order->get('payment_method')->getString();
      $payment_storage = $payment = \Drupal::entityTypeManager()->getStorage('commerce_payment');
      /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
          $payment = $payment_storage->create([
            'state' => 'completed',
            'amount' => $order->getBalance(),
            'payment_gateway' => $payment_gateway,
            'payment_method' => $payment_method,
            'order_id' => $order->id(),
            'remote_id' => $response['reference'],
            'remote_state' => $response['referrer'],
            ]);
            $payment->save();
           
      $logMessage = 'New Payment Success with response: ' . $orderResponse;
      \Drupal::logger('commerce_n_genius')->info($logMessage);
      }
    else {
       
      $logMessage = 'New Payment Success with no response';
      \Drupal::logger('commerce_n_genius')->error($logMessage);
      throw new PaymentGatewayException('The transaction has been Aborted.');
    }
  }elseif ($mode=="test") {
    if (isset($response['_embedded']['payment'][0]['3ds']['status']) && $response['_embedded']['payment'][0]['3ds']['status'] == 'SUCCESS') {
      // Create Payment for Oreder
      $payment_gateway = $order->get('payment_gateway')->getString();
      // Get the payment method from the order.
      $payment_method = $order->get('payment_method')->getString();
      $payment_storage = $payment = \Drupal::entityTypeManager()->getStorage('commerce_payment');
      /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
      // dd($response);
          $payment = $payment_storage->create([
            'state' => 'completed',
            'amount' => $order->getBalance(),
            'payment_gateway' => $payment_gateway,
            'payment_method' => $payment_method,
            'order_id' => $order->id(),
            'remote_id' => $response['reference'],
            'remote_state' => $response['referrer'],
            ]);
            $payment->save();
           
      $logMessage = 'New Payment Success with response: ' . $orderResponse;
      \Drupal::logger('commerce_n_genius')->info($logMessage);
    } elseif (isset($response['_embedded']['payment'][0]['3ds']['status']) && $response['_embedded']['payment'][0]['3ds']['status'] == 'FAILED') {
      $logMessage = 'New Payment Failure with response: ' . $orderResponse;
      \Drupal::logger('commerce_n_genius')->error($logMessage);
      throw new PaymentGatewayException('The transaction has been Aborted.');
    } else {
      $logMessage = 'New Payment Success with no response';
      \Drupal::logger('commerce_n_genius')->error($logMessage);
      throw new PaymentGatewayException('The transaction has been Aborted.');
    }
  }
  }

  protected function invokeCurlRequest($type, $url, $headers, $post)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if ($type == 'POST') {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }

    $server_output = curl_exec($ch);
    curl_close($ch);
    return $server_output;
  }
}
