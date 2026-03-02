<?php

namespace Drupal\commerce_n_genius\PluginForm;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\commerce_order\Entity\Order;

/**
 * Provides a base class for payment gateway plugin forms.
 *
 * @see \Drupal\Core\Plugin\PluginFormBase
 */
class NgeniusPaymentRedirect extends BasePaymentOffsiteForm
{

  const IDSERVICEURL = 'https://identity.ngenius-payments.com/auth/realms/NetworkInternational/protocol/openid-connect/token';
  const TXNSERVICEBASEURL = 'https://api-gateway.ngenius-payments.com/transactions/outlets/';

  const TESTIDSERVICEURL = 'https://api-gateway.sandbox.ngenius-payments.com/identity/auth/access-token';
  const TESTTXNSERVICEBASEURL = 'https://api-gateway.sandbox.ngenius-payments.com/transactions/outlets/';

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $payment = $this->entity;
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $paymentGatewayPlugin */
    $paymentGatewayPlugin = $payment->getPaymentGateway()->getPlugin();

    $orderId = \Drupal::routeMatch()->getParameter('commerce_order')->id();
    $order = Order::load($orderId);
    $billingProfile = $order->getBillingProfile();

    if ($billingProfile) {
      $address = $billingProfile->address->first()->getValue();
    }

    $mode = $paymentGatewayPlugin->getConfiguration()['mode'];

    $outletRef = $paymentGatewayPlugin->getConfiguration()['outlet_ref'];
    $apiKey = $paymentGatewayPlugin->getConfiguration()['apikey'];

    $tokenHeaders = ['Authorization: Basic ' . $apiKey, 'Content-Type: application/vnd.ni-identity.v1+json','accept: application/vnd.ni-identity.v1+json'];
    

    if ($mode == 'live') {
      $txnServiceURL = self::TXNSERVICEBASEURL . $outletRef . '/orders';
      $tokenResponse = $this->invokeCurlRequest('POST', self::IDSERVICEURL, $tokenHeaders, http_build_query(['grant_type' => 'client_credentials']));
    } else {
      $txnServiceURL = self::TESTTXNSERVICEBASEURL . $outletRef . '/orders';
      $tokenResponse = $this->invokeCurlRequest('POST', self::TESTIDSERVICEURL, $tokenHeaders, $post=NULL);
    }

    $tokenResponse = json_decode($tokenResponse);
    $accessToken = $tokenResponse->access_token;
    // dd($accessToken);
    $orderObject = [
      'action' => 'PURCHASE',
      'amount' => [
        'currencyCode' => 'AED',
        'value' => $payment->getAmount()->getNumber() * 100,
      ],
      'language' => 'en',
      'billingAddress' => [
        'firstName' => $address['given_name'],
        'lastName' => $address['family_name'],
        'address1' => $address['address_line1'],
        'countryCode' => $address['country_code'],
        'city' => !empty($address['administrative_area']) ? $address['administrative_area'] : $address['locality'],
      ],
      'emailAddress' => $order->getEmail(),
      'merchantOrderReference' => time(),
      'merchantAttributes' => [
        'redirectUrl' => Url::FromRoute('commerce_payment.checkout.return', [
          'commerce_order' => $orderId,
          'step' => 'payment',
        ], ['absolute' => TRUE])->toString(),
        'skipConfirmationPage' => TRUE,
      ],
    ];

    $orderObject = json_encode($orderObject);
    $orderCreateHeaders = ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/vnd.ni-payment.v2+json', 'Accept: application/vnd.ni-payment.v2+json'];
    $orderCreateResponse = $this->invokeCurlRequest('POST', $txnServiceURL, $orderCreateHeaders, $orderObject);
    $orderCreateResponse = json_decode($orderCreateResponse);
    if (!isset($orderCreateResponse->_links)) {
      $paymentLink = Url::FromRoute('commerce_payment.checkout.cancel', [
        'commerce_order' => $orderId,
        'step' => 'payment',
      ], ['absolute' => TRUE])->toString();
    } else {
      $paymentLink = $orderCreateResponse->_links->payment->href;
    }
    // dd($orderCreateResponse, $accessToken, $orderCreateResponse->_links);

    return $this->buildRedirectForm($form, $form_state, $paymentLink, [], []);
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
