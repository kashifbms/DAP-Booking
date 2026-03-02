<?php

namespace Drupal\commerce_date_filter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormState;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_date_filter\Form\AddtoCartForm;
use Drupal\Core\Form\FormStateInterface;
/**
 * Controller for displaying filtered products.
 */
class FilteredProductsController extends ControllerBase
{

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a FilteredProductsController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(RendererInterface $renderer)
  {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('renderer')
    );
  }

  /**
   * Renders filtered products.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array
   *   A render array.
   */
  public function content(Request $request)
  {
    // Get Custom Form 
    $form = \Drupal::formBuilder()->getForm('Drupal\commerce_date_filter\Form\DateFilterForm');
    // Retrieve the selected date from the query parameter.
    $selected_date = $request->query->get('date');
    if ($selected_date) {
      $query = \Drupal::entityQuery('commerce_product')
      ->condition('status', 1) // Filter products by status if needed
      ->condition('type', 'tickets'); // Filter by product type

      // Add conditions for validity based on selected date
      $query->condition('field_valid_from', $selected_date, '<=');
      $query->condition('field_valid_to', $selected_date, '>=');

      // Set access check to TRUE
      $query->accessCheck(TRUE);
      $product_ids = $query->execute();

    }else{
      $current_date = strtotime(new DrupalDateTime('now'));
      $query = \Drupal::entityQuery('commerce_product')
      ->condition('status', 1) // Filter products by status if needed
      ->condition('type', 'tickets'); // Filter by product type

      // Add conditions for validity based on selected date
      $query->condition('field_valid_from', $current_date, '<=');
      $query->condition('field_valid_to', $current_date, '>=');

      // Set access check to TRUE
      $query->accessCheck(TRUE);
      $product_ids = $query->execute();
    }

    // Load products
    $products = \Drupal\commerce_product\Entity\Product::loadMultiple($product_ids);
    
    // Retrieve the filtered products from the session.
    // $products = \Drupal::service('session')->get('filtered_products');

    // Check if there are filtered products
    if (!empty($products)) {
      // Convert the product entities to render arrays
      $rendered_products = [];
      foreach ($products as $product) {
        $title = $product->getTitle();
        $product_id = $product->id();
        $image_render_array = $product->get('field_image')->view();
        $body_render_array = $product->get('body')->view();
        // dd($product);
        $variations_field = $product->get('variations')->target_id;
        $variation = ProductVariation::load($variations_field);
        // dd($variation);
        if ($variation instanceof ProductVariation) {
          $sku = $variation->getSku(); // Example: Using SKU as part of the add to cart format

          // Construct the add to cart format
          $add_to_cart_format = 'SKU: ' . $sku; 
          $add_to_cart_formats[] = $add_to_cart_format;
          // dd($add_to_cart_formats);
        }
        // $pid = (new FormState())->set('pid', $product_id);
        // $addtoCartForm = $this->formBuilder()->buildForm(AddtoCartForm::class, $pid);
        // dd($form);
        $rendered_products[] = [
          'title' => $title,
          'product_id' => $product_id,
          'image' => $this->renderer->render($image_render_array),
          'body' => $this->renderer->render($body_render_array),
          'addToCart' => $this->renderer->render($addtoCartForm)
          // 'test' => $this->renderer->render($variation)->view,
          // Add more fields as needed
        ];
      }

      // Pass the rendered products to the template
      $build = [
        '#theme' => 'product_display',
        '#products' => $rendered_products,
        '#form'    => $form,
      ];
    } else {
      // Return a message if no products found
      $build = [
        '#theme' => 'product_display',
        '#products' => [],
        '#form'    => $form
      ];
    }

    return $build;
  }
}
