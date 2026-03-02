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
class TicketProduct extends ControllerBase
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
    public function content()
    {

        dd("yes connect");
        return $build;
    }
}
