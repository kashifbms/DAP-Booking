<?php

namespace Drupal\commerce_date_filter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_price\Price;
use Drupal\commerce_cart;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\LineItem;



/**
 * Controller routines for products routes.
 */
class CartsController extends ControllerBase
{

  /**
   * The cart manager.
   *
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected $cartManager;

  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * Constructs a new CartController object.
   *
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   */
  public function __construct(CartManagerInterface $cart_manager, CartProviderInterface $cart_provider)
  {
    $this->cartManager = $cart_manager;
    $this->cartProvider = $cart_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('commerce_cart.cart_manager'),
      $container->get('commerce_cart.cart_provider')
    );
  }
  public function addToCart($productId)
  {
    $user = \Drupal::currentUser();
    $isAuthenticated = $user->isAuthenticated();
    // dd($user);
    $destination = \Drupal::service('path.current')->getPath();
    $productObj = Product::load($productId);

    $product_variation_id = $productObj->get('variations')
      ->getValue()[0]['target_id'];

    $storeId = $productObj->get('stores')->getValue()[0]['target_id'];
    // dd($storeId);
    $variationobj = \Drupal::entityTypeManager()
      ->getStorage('commerce_product_variation')
      ->load($product_variation_id);

    $store = \Drupal::entityTypeManager()
      ->getStorage('commerce_store')
      ->load($storeId);
    $cart = $this->cartProvider->getCart('default', $store);
    // dd($cart);
    if ($isAuthenticated) {
      $userId = $user->id();
      $query = \Drupal::entityQuery('commerce_order')
        ->condition('uid', $userId)
        ->condition('state', 'draft')
        ->accessCheck(TRUE);
      $orderIds = $query->execute();

      // Load the orders.
      $orders = \Drupal\commerce_order\Entity\Order::loadMultiple($orderIds);
      if ($orders) {
        foreach ($orders as $order) {

          if ($order->getState()->value == 'draft') {
            // This is the user's cart.
            $cart = $order;
          }
        }
        if ($cart->getItems()) {
          $totalPrice = new Price('0', $cart->getTotalPrice()->getCurrencyCode());
          foreach ($cart->getItems() as $item) {
            if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation' && $item->getPurchasedEntity()->getProductId() == $productId) {
              if (isset($_POST['updateQuantity']) && $_POST['updateQuantity'] == 'reduce') {
                $new_quantity = max(0, $item->getQuantity() - 1);
                $item->setQuantity($new_quantity);
                $item->save();
              } elseif (isset($_POST['updateQuantity']) && $_POST['updateQuantity'] == 'add') {
                $new_quantity = max(0, $item->getQuantity() + 1);
                $item->setQuantity($new_quantity);
                $item->save();
              }
              $lineItemTotalPrice = $item->getTotalPrice();
              $totalPrice = $totalPrice->add($lineItemTotalPrice);
              if ($new_quantity == 0) {
                $item->delete();
              }
              $cart->setData('total_price', $totalPrice);
              $cart->save();
            } else {
              $cart_manager = \Drupal::service('commerce_cart.cart_manager');
              $line_item = $cart_manager->addEntity($cart, $variationobj);
            }
          }
        } else {
          $cart_manager = \Drupal::service('commerce_cart.cart_manager');
          $line_item = $cart_manager->addEntity($cart, $variationobj);
          $cartTotalPrice['orderItem'] = $cart->get('order_id')->value;
        }
      } else {
        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        $line_item = $cart_manager->addEntity($cart, $variationobj);
        $cartTotalPrice['orderItem'] = $cart->get('order_id')->value;
      }
      if ($cart->getTotalPrice()) {
        $cartTotalPrice['price'] = $cart->getTotalPrice()->getNumber();
        $cartTotalPrice['currency_code'] = $cart->getTotalPrice()->getCurrencyCode();
      }
    } else {
      if (!$cart) {
        $cart = $this->cartProvider->createCart('default', $store);
      }
      if (isset($_POST['updateQuantity']) && $_POST['updateQuantity'] == 'reduce') {
        $totalPrice = new Price('0', $cart->getTotalPrice()->getCurrencyCode());
        foreach ($cart->getItems() as $item) {
          if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation' && $item->getPurchasedEntity()->getProductId() == $productId) {
            $new_quantity = max(0, $item->getQuantity() - 1);
            $item->setQuantity($new_quantity);
            $item->save();
            $lineItemTotalPrice = $item->getTotalPrice();
            $totalPrice = $totalPrice->add($lineItemTotalPrice);
            if ($new_quantity == 0) {
              $item->delete();
            }
            $cart->setData('total_price', $totalPrice);
            $cart->save();
          }
        }
      } elseif (isset($_POST['updateQuantity']) && $_POST['updateQuantity'] == 'add') {

        $line_item_type_storage = \Drupal::entityTypeManager()
          ->getStorage('commerce_order_item_type');
        // Process to place order programatically.
        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        $line_item = $cart_manager->addEntity($cart, $variationobj);
        $cartTotalPrice['orderItem'] = $cart->get('order_id')->value;
      } else {
        foreach ($cart->getItems() as $item) {
          if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation' && $item->getPurchasedEntity()->getProductId() == $productId) {
            $cartTotalPrice['productQuantity'] = $item->getQuantity();
          }
        }
      }
      if ($cart->getTotalPrice()) {
        $cartTotalPrice['price'] = $cart->getTotalPrice()->getNumber();
        $cartTotalPrice['currency_code'] = $cart->getTotalPrice()->getCurrencyCode();
      }
    }
    return new JsonResponse($cartTotalPrice);
  }
  static function CartUpdated($cart = null, $productId = null, $quantity = null, $cartProvider = null, $date = null, $storeProductArray = null)
  {
    $productObj = Product::load($productId);
    $product_variation_id = $productObj->get('variations')
      ->getValue()[0]['target_id'];
    $user = \Drupal::currentUser();
    $isAuthenticated = $user->isAuthenticated();
    $storeId = $productObj->get('stores')->getValue()[0]['target_id'];

    $variationobj = \Drupal::entityTypeManager()
      ->getStorage('commerce_product_variation')
      ->load($product_variation_id);
    $store = \Drupal::entityTypeManager()
      ->getStorage('commerce_store')
      ->load($storeId);
    if ($cart != null && $productId != null && $quantity != null) {
      if ($date != null) {
        $cart->set('field_order_date',  $date);
        $cart->save();
      }
      $cart_manager = \Drupal::service('commerce_cart.cart_manager');
      $line_item = $cart_manager->addEntity($cart, $variationobj, $quantity);
    } else {
      if ($cartProvider != null  && $productId != null && $quantity != null) {
        // $cart = $cartProvider->createCart('default', $store);
        $cart = $cartProvider->getCart('default', $store);
        if (!$isAuthenticated) {
          if ($cart) {
            if ($cart->hasItems() != null) {
              if ($storeProductArray != null) {
                foreach ($cart->getItems() as $key => $item) {
                  if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation' && $item->getPurchasedEntity()->getProductId() == $productId) {
                    $storeProductId[] = $productId;
                    $item->setQuantity(0);
                    $item->save();
                    if ($quantity == 0) {
                      $item->delete();
                    }
                  } else {
                    if (!in_array($item->getPurchasedEntity()->getProductId(), $storeProductArray)) {
                      $item->delete();
                    }
                  }
                }
              }
            }
          }
        }
        if (!$cart) {
          $cart = $cartProvider->createCart('default', $store);
        }
        if ($date != null) {
          $cart->set('field_order_date',  $date);
          $cart->save();
        }
        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        $line_item = $cart_manager->addEntity($cart, $variationobj, $quantity);
      }
    }
  }
  public function addToCartMultiple()
  {
    if (isset($_POST['Product'])) {
      $allProduct = $_POST['Product'];
      $user = \Drupal::currentUser();
      $isAuthenticated = $user->isAuthenticated();
      if ($isAuthenticated) {
        $userId = $user->id();
        $query = \Drupal::entityQuery('commerce_order')
          ->condition('uid', $userId)
          ->condition('state', 'draft')
          ->condition('locked', '0')
          ->accessCheck(TRUE);
        $orderIds = $query->execute();
        // dd($orderIds);
        $orders = \Drupal\commerce_order\Entity\Order::loadMultiple($orderIds);

        // dd($allProudctId);
        if ($orders) {
          foreach ($orders as $order) {
            if ($order->getState()->value == 'draft') {
              $cart = $order;
            }
          }
          if ($cart->get('locked')->value == "1") {
            foreach ($allProduct as $key => $product) {
              $this->CartUpdated(null, $product['pid'], $product['quantity'], $this->cartProvider, $_POST['Date']);
            }
          } else {
            $allProudctId = [];
            foreach ($allProduct as $key => $product) {
              $allProudctId[] = $product['pid'];
            }
            $allItemProduct = [];
            foreach ($cart->getItems() as $item) {
              if ($cart->getItems()) {
                if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation') {
                  $allItemProduct[] = $item->getPurchasedEntity()->getProductId();
                }
              }
            }
            $removeCartItem = [];
            foreach ($allItemProduct as $key => $cartItem) {
              if (!in_array($cartItem, $allProudctId)) {
                $removeCartItem[] = $cartItem;
              }
            }
            // dd($removeCartItem);
            foreach ($allProduct as $key => $product) {
              if (in_array($product['pid'], $allItemProduct)) {
                foreach ($cart->getItems() as $item) {
                  $totalPrice = new Price('0', $cart->getTotalPrice()->getCurrencyCode());
                  $new_quantity = 0;
                  if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation' && $item->getPurchasedEntity()->getProductId() == $product['pid']) {
                    $allItemProduct[] = $item->getPurchasedEntity()->getProductId();
                    $new_quantity = max(0, $product['quantity']);
                    $item->setQuantity($new_quantity);
                    $item->save();
                    $lineItemTotalPrice = $item->getTotalPrice();
                    $totalPrice = $totalPrice->add($lineItemTotalPrice);
                    if ($new_quantity == 0) {
                      $item->delete();
                    }
                    if (isset($_POST['Date'])) {
                      $date = $_POST['Date'];

                      $cart->set('field_order_date',  $date);
                    }
                    $cart->setData('total_price', $totalPrice);
                    $cart->save();
                  } else {
                    $cartProduct = $item->getPurchasedEntity()->getProductId();
                    if (in_array($cartProduct, $removeCartItem)) {
                      $item->delete();
                    }
                  }
                }
              } else {
                if ($product['quantity'] != 0) {
                  $this->CartUpdated($cart, $product['pid'], $product['quantity'], null, $_POST['Date']);
                }
                foreach ($cart->getItems() as $item) {
                  $cartItem =  $item->getPurchasedEntity()->getProductId();
                  if (in_array($cartItem, $removeCartItem)) {
                    $item->delete();
                  }
                }
              }
            }
          }
        } #check order have or not
        else {
          foreach ($allProduct as $key => $product) {
            $this->CartUpdated(null, $product['pid'], $product['quantity'], $this->cartProvider, $_POST['Date']);
          }
        } #check user is authenticated or not
      } #chechk uthenticated user or not
      else {
        foreach ($allProduct as $key => $product) {
          $productId[] = $product['pid'];
          $this->CartUpdated(null, $product['pid'], $product['quantity'], $this->cartProvider, $_POST['Date'], $productId);
        }
      }
    }
    return new JsonResponse("yes added");
  }
  public function getCart()
  {
    $user = \Drupal::currentUser();
    $isAuthenticated = $user->isAuthenticated();
    if ($isAuthenticated) {
      $userId = $user->id();
      $query = \Drupal::entityQuery('commerce_order')
        ->condition('uid', $userId)
        ->condition('state', 'draft')
        ->accessCheck(TRUE);
      $orderIds = $query->execute();
      $orders = \Drupal\commerce_order\Entity\Order::loadMultiple($orderIds);
      foreach ($orders as $key => $order) {
        $cart = $order;
      }
      $allCart = [];
      if (isset($_POST['Date'])) {
        $date = $_POST['Date'];
        if ($date == $cart->get('field_order_date')->value) {
          foreach ($cart->getItems() as $key => $item) {
            if ($item->getPurchasedEntity()->getEntityTypeId() == 'commerce_product_variation') {
              $allCart[$key]['pid'] = $item->getPurchasedEntity()->getProductId();
              $allCart[$key]['quantity'] = $item->get('quantity')->value;
              $allCart[$key]['TotalPrice'] =  $cart->get('total_price')->number;
              $allCart[$key]['TotalCurrency'] =  $cart->get('total_price')->currency_code;
            }
          }
        }
        return new JsonResponse($allCart);
      } else {
        return new JsonResponse([]);
      }
    }
  }
  public function  test1()
  {
    $request = \Drupal::request();
    $postData = $request->request->all();
    \Drupal::logger('hellos')->notice(print_r($postData, true));
    die;
  }
}
