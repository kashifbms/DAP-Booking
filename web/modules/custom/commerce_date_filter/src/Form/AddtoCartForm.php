<?php

namespace Drupal\commerce_date_filter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AddtoCartForm extends FormBase {
    protected $renderer;
    public function __construct(RendererInterface $renderer) {
      $this->renderer = $renderer;
    }  
    public function getFormId() {
        return 'formadd_to_cart';
      }
      public static function create(ContainerInterface $container) { // Correct the type hint
        return new static(
          $container->get('renderer')
        );
      }
      public function buildForm(array $form, FormStateInterface $form_state) {
        // $pid = \Drupal::request()->query->get('pid');
        // dd($pid);
      $FormPid =  $form_state->get('pid');
    //   dd($FormPid);
        $form['quantity'] = [
            '#type' => 'number',
            '#title' => $this->t('Quantity'.$FormPid),
            '#min' => 1,
            '#required' => TRUE,
          ];
          $form['hidden_pid'] = [
            '#type' => 'textfield',
            '#value' => $this->t($FormPid),
          ];
          $form['add_to_cart'] = [
            '#type' => 'submit',
            '#value' => $this->t('Add to Cart'),
          ];
          return $form;
      }
        /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  { 
    // $FormPid =  $form_state->get('pid');
   
$quantity =$form_state->getValue('quantity');
$pid = $form_state->getValue('hidden_pid');
dd($form_state,$pid);
    // $message = Markup::create("$userName The month of $monthName attendance has been submitted.");
    $response = new RedirectResponse('/add/product/'.$pid.'/'.$quantity);
    $response->send();
    // \Drupal::messenger()->addMessage($message);
    // // dd($form_state);
    // $url = Url::fromRoute('add/product/'); // Replace <route_name> with the route name of the page you want to redirect to
    // $form_state->setRedirectUrl($url);
  }
}