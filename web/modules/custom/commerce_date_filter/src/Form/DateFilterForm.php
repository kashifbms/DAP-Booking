<?php

namespace Drupal\commerce_date_filter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Datetime\DrupalDateTime;

class DateFilterForm extends FormBase {
  protected $renderer;
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }  
  public function getFormId() {
    return 'commerce_date_filter_date_filter_form';
  }

  public static function create(ContainerInterface $container) { // Correct the type hint
    return new static(
      $container->get('renderer')
    );
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
	$current_date = new DrupalDateTime();
	$current_date_str = $current_date->format('Y-m-d');  
    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Select date'),
      '#required' => TRUE,
	  '#attributes' => ['min' => $current_date_str],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected_date = strtotime($form_state->getValue('date'));
    // Perform filtering logic here.
    // Query products based on start and end date.
    $query = \Drupal::entityQuery('commerce_product')
      ->condition('status', 1) // Filter products by status if needed
      ->condition('type', 'tickets'); // Filter by product type

    // Add conditions for validity based on selected date
    $query->condition('field_valid_from', $selected_date, '<=');
    $query->condition('field_valid_to', $selected_date, '>=');

    // Set access check to TRUE
    $query->accessCheck(TRUE);
    $product_ids = $query->execute();

    // Load products
    $products = \Drupal\commerce_product\Entity\Product::loadMultiple($product_ids);
  // dd($products);
    // Set the products as a form state value for further processing
    $url = Url::fromRoute('commerce_date_filter.filtered_products')
    ->setOption('query', ['date' => $selected_date]);
  $response = new RedirectResponse($url->toString());
  $response->send();
    \Drupal::service('session')->set('filtered_products', $products);
}


  protected function buildProductForm($product) {
 $form = [];
    $form['product_title'] = [
      '#markup' => '<h2>' . $product->label() . '</h2>',
    ];
    $form['product_image'] = [
      '#markup' => $this->renderer->render($product->get('field_image')->view()),
    ];
    $form['product_body'] = [
      '#markup' => $this->renderer->render($product->get('body')->view()),
    ];
    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#min' => 1,
      '#required' => TRUE,
    ];
    $form['add_to_cart'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add to Cart'),
      '#submit' => ['::addToCartSubmit'],
      '#attributes' => [
        'data-product-id' => $product->id(),
      ],
    ];
    return $form;
  }

  public function addToCartSubmit(array &$form, FormStateInterface $form_state) {
    $product_id = $form_state->getTriggeringElement()['#attributes']['data-product-id'];
    $quantity = $form_state->getValue('quantity');

    // Add the product to the cart.
    \Drupal\commerce_cart\CartForm::submitForm($form, $form_state);
  }
}
