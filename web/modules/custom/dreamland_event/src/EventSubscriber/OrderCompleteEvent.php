<?php

namespace Drupal\dreamland_event\EventSubscriber;

use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Core\Site\Settings;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Render\RendererInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
// use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Mail\MailManagerInterface; // Import added
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Class OrderCompleteSubscriber.
 *
 * @package Drupal\dreamland_event
 */
class OrderCompleteEvent implements EventSubscriberInterface
{
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  protected $logger;
  protected $mailManager;
  protected $fileSystem;
  public function __construct(EntityTypeManagerInterface $entityTypeManager, MailManagerInterface $mail_manager, FileSystemInterface $file_system)
  {
    $this->entityTypeManager = $entityTypeManager;
    $this->mailManager = $mail_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    $events['commerce_order.place.post_transition'] = ['orderCompleteHandler'];
    return $events;
  }

  /**
   * Generate Food voucher PDF using mPDF library.
   *
   * Uses a different header layout than the ticket PDF.
   */
  public static function generateFoodPdf2($html, $file, $fullName, $orderDate, $orderNumber)
  {
    $mpdf = new \Mpdf\Mpdf([
      'margin_left' => 10,
      'margin_right' => 10,
      'margin_top' => 30,
      'margin_bottom' => 0,
      'margin_header' => 0,
      'margin_footer' => 10,
      'mode' => 'c',
    ]);

    // Custom header for Food voucher PDF: logo left, QR center, info + barcode right.
    $mpdf->SetHTMLHeader('<table width="100%" cellspacing="0" style="border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; width:33%;">
            <img src="https://booking.dreamlanduae.com/sites/default/files/logo_0.png" width="120">
          </th>
          <th style="text-align:center; width:34%;">
            <img src="' . $file . '" width="80">
          </th>
          <th style="text-align:right; width:33%; font-size:11px;">
            <div><strong>Guest Name:</strong> ' . htmlspecialchars($fullName) . '</div>
            <div><strong>Issue Date:</strong> ' . date("d/m/Y", strtotime($orderDate)) . '</div>
            <div><strong>Order Number:</strong> ' . htmlspecialchars($orderNumber) . '</div>
          </th>
        </tr>
      </thead>
    </table>');

    $mpdf->SetDisplayMode('fullpage');

    // LOAD the same stylesheet as tickets for typography/base styles.
    $stylesheet = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/modules/custom/dreamland_event/css/style-pdf.css");
    $mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($html);
    unset($html);
    $mpdf->SetTitle("FOOD VOUCHER");
    $mpdf->debug = false;
    return $mpdf->Output('food-tickets.pdf', 'S');
  }
  /**
   * Generate PDF using TCPDF library.
   */
  public static function generatePdf(array $data)
  {

    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information.
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('sfsd');
    $pdf->SetTitle('Tickets');
    $pdf->SetSubject('PDF Subject');
    $pdf->SetKeywords('PDF, TCPDF, table');
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // Set header and footer fonts.
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font.
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins.
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks.
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor.
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page.
    $pdf->AddPage();

    // Set font.
    $pdf->SetFont('helvetica', '', 10);

    // Set table data.
    $html = '<table border="1">';
    foreach ($data as $row) {
      $html .= '<tr>';
      foreach ($row as $cell) {
        $html .= '<td>' . htmlspecialchars($cell) . '</td>';
      }
      $html .= '</tr>';
    }
    $html .= '</table>';

    // Write the HTML content to the PDF.
    $pdf->writeHTML($html, true, false, true, false, '');
    // dd($pdf);

    // Close and output PDF.
    // $pdf->Output('/var/www/html/dreamland-uae/web/sites/default/files/example.pdf', 'F');
    return $pdf->Output('tickets.pdf', 'S');
    // return $pdf;
  }

  public static function generatePdf2($html, $file)
  {



    $mpdf = new \Mpdf\Mpdf([
      'margin_left' => 10,
      'margin_right' => 10,
      'margin_top' => 30,
      'margin_bottom' => 0,
      'margin_header' => 0,
      'margin_footer' => 10,
      'mode' => 'c'
    ]);

    // $mpdf->SetHTMLHeader('');

    $mpdf->SetHTMLHeader('<table width="100%" cellspacing="0">
    <thead>
    <tr>
    <th><img src="sites/default/files/PDFImages/logo.png" width="100"></th>
    <th col=><img src="sites/default/files/PDFImages/e-ticket.jpg" width="100"></th>
    <th><img src="' . $file . '" width="100"></th>
    
    </tr>
    </thead>
    </table>');
    $mpdf->SetDisplayMode('fullpage');

    // LOAD a stylesheet
    $stylesheet = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/modules/custom/dreamland_event/css/style-pdf.css");
    $mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($html);
    unset($html);
    $mpdf->SetTitle("E-TICKET");
    $mpdf->debug = false;
    return $mpdf->Output('tickets.pdf', 'S');
  }



  /**
   * This method is called whenever the commerce_order.place.post_transition event is
   * dispatched.
   *
   * @param WorkflowTransitionEvent $event
   */


  public function orderCompleteHandler(WorkflowTransitionEvent $event)
  {

    $new_order = $event->getEntity();

    $order_id = $new_order->id();
    $order = Order::load($order_id);
    $order_number = $order->get('order_number')->value;
    if ($order) {
      $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
      $payments = $payment_storage->loadByProperties(['order_id' => $order_id]);
      foreach ($payments as $payment) {
        $tractionId = $payment->get('remote_id')->value;
        // dd($tractionId);
      }
      $order_items = $order->getItems();
      $billingProfile = $order->get('billing_profile')->entity;
      $order_date =  $order->get('field_order_date')->value;
      $order_date = new DrupalDateTime($order->get('field_order_date')->value);
      $orderDate = new DrupalDateTime($order_date);
      $lastDayOfMonth = new \DateTime('last day of ' . $orderDate->format('F Y'));
      // dd($orderDate, $lastDayOfMonth->format('d/m/Y'));
      $order->set('placed', strtotime($order_date));
      // dd($lastDayOfMonth->format('Y-m-d'));
      $order->set('field_order_expiry_date', $lastDayOfMonth->format('Y-m-d'));
      $fullName = '';
      if ($billingProfile) {
        $fullName = $billingProfile->get('field_first_name')->value . ' ' . $billingProfile->get('field_last_name')->value;
      }
      $ticketOrder = [];
      $foodOrder = [];
      $foodOrderItems = [];
      $html = "";
      $foodHtml = "";
      $countTicket = 0;
      $countFood = 0;
      $totalQuantity = 0;
      foreach ($order_items as $index => $order_item) {
        // dd($order_item);
        $quantity = $order_item->get('quantity')->value;
        $purchased_entity = $order_item->getPurchasedEntity();
        $product = $purchased_entity->getProduct();
        $bundle = $product->bundle();
        $quantityNumber = (int) $quantity;
        // Handle Food products separately: they do not participate in ticket
        // availability and numbering logic.
        if ($bundle === 'food') {
          for ($i = 0; $i < $quantityNumber; $i++) {
            $foodOrder[$countFood]['title'] = $product->get('title')->value;
            $foodOrder[$countFood]['orderNumber'] = $order_number;
            $foodOrder[$countFood]['fullName'] = $fullName;
            $foodOrder[$countFood]['price'] = $order_item->get('unit_price')->number;
            $foodOrder[$countFood]['orderDate'] = $order_date;
            $foodOrder[$countFood]['body'] = $product->get('body')->value;
            // Store Food image URI if available to render as hero image.
            $image_field = $product->get('field_food_image');
            if (!$image_field->isEmpty() && $image_field->entity) {
              $foodOrder[$countFood]['image_uri'] = $image_field->entity->getFileUri();
            }
            $countFood++;
          }
          // Track Food order item IDs so we can attach the Food PDF file entity.
          $foodOrderItems[] = $order_item->id();
          // Skip the ticket-specific logic for Food items.
          continue;
        }

        $bookingTickets = (int) $product->get('field_booking_tickets')->value;

        $visualId =  (int) $product->get('field_visual_id_start')->value;
        $number = $product->get('field_visual_id_start')->value;
        $leadingZeros = substr($number, 0, strspn($number, '0'));
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $enablesDays = [];
        foreach ($product->get('field_enable_days')->referencedEntities() as $key => $value) {
          $name = $value->get('name')->value;
          $enablesDays[] = $name;
        }
        for ($i = 0; $i < $quantityNumber; $i++) {
          $ticketOrder[$countTicket]['ticket_id'] = $countTicket;
          $ticketOrder[$countTicket]['title'] = $product->get('title')->value;
          $ticketOrder[$countTicket]['orderNumber'] = $order_number;
          $ticketOrder[$countTicket]['fullName'] = $fullName;
          $ticketOrder[$countTicket]['cat_id'] = $product->id();
          $ticketOrder[$countTicket]['visual_id'] =  $leadingZeros . $visualId + $bookingTickets + $i;
          $ticketOrder[$countTicket]['body'] = $product->get('body')->value;
          $ticketOrder[$countTicket]['price'] = $order_item->get('unit_price')->number;
          $ticketOrder[$countTicket]['field_valid__from'] = $product->get('field_valid__from')->value;
          $ticketOrder[$countTicket]['field_valid__to'] = $product->get('field_valid__to')->value;
          $ticketOrder[$countTicket]['orderDate'] = $order_date;
          $ticketOrder[$countTicket]['expiryDate'] = $lastDayOfMonth->format('Y-m-d');
          foreach ($weekDays as $key => $weekday) {
            if (in_array($weekday, $enablesDays)) {
              $ticketOrder[$countTicket][$weekday] = 1;
            } else {
              $ticketOrder[$countTicket][$weekday] = 0;
            }
          }
          $CreatedDate = $product->get('created')->value;
          $ticketOrder[$countTicket]['created'] = date('Y-m-d H:i:s', $CreatedDate);
          $updateDate = $product->get('changed')->value;
          $ticketOrder[$countTicket]['update'] = date('Y-m-d H:i:s', $updateDate);
          $totalBookingTickets = $bookingTickets + $quantityNumber;
          $totalTickets = (int) $product->get('field_total_tickets')->value;
          $availableTickets = $totalTickets - ($bookingTickets + $quantityNumber);
          $product->set('field_booking_tickets', $totalBookingTickets);
          if ($totalTickets) {
            $avaibleTicktes = $totalTickets - $totalBookingTickets;
            $product->set('field_available_tickets', $availableTickets);
          }
          // $product->set('field_available_tickets', )
          $productTitle = $product->get('title')->value;
          $order_item->set('title', $productTitle);
          // $order_item->set('field_demo', "ldsajflkdsjf");
          $order_item->save();
          // dd($ticketOrder);
          $html .= $this->getPDFBody($i, $fullName, $ticketOrder[$countTicket], $quantityNumber);
          $product->save();
          $countTicket++;
        }
        // $text =  $tractionId;

        // $path =  "sites/default/files/PDFImages/";
        // $file = $path . $order_number . ".png";

        // $ecc = 'L';
        // $pixel_Size = 5;
        // $frame_Size = 5;

        // // Generates QR Code and Stores it in directory given
        // QRcode::png($text, $file, $ecc, $pixel_Size, $frame_Size);
        $options = new QROptions;
        $options->outputInterface  = QRFpdf::class;
        $options->scale            = 5;
        $options->fpdfMeasureUnit  = 'mm'; // pt, mm, cm, in
        $options->bgColor          = [222, 222, 222]; // [R, G, B]
        $options->drawLightModules = false;
        $out  = (new QRCode($options))->render($tractionId);
        $pdf_content = $this->generatePdf2($html, $out);

        $private_file_path = 'private://Tickets';
        $fileName = "Order_" . $order_number . "_item_" . $index . ".pdf";

        if (!$this->fileSystem->prepareDirectory($private_file_path, FileSystemInterface::CREATE_DIRECTORY)) {
          // @todo Log an error.
          return FALSE;
        }

        $this->fileSystem->saveData($pdf_content, $private_file_path . "/" . $fileName, FileSystemInterface::EXISTS_REPLACE);
        // dd($order);
        $uid = $order->get('uid')->value;
        // Create and save a new file entity.
        // @see FileRepository::createOrUpdate().
        $new_file = \Drupal\file\Entity\File::create(['uri' => $private_file_path . '/' . $fileName]);
        $new_file->setOwnerId($uid);
        $new_file->setPermanent();
        $new_file->save();
        $order_item->set("field_pdf_file", [
          'target_id' => $new_file->id(),
        ]);
        $order_item->save();
        $html = "";
      }

      // Generate combined Ticket PDF per order only if ticket items exist.
      if (!empty($ticketOrder)) {
        $html = "";
        foreach ($ticketOrder as $key => $ticket) {
          $html .= $this->getPDFBody($key, $fullName, $ticket, sizeof($ticketOrder));
        }
        // Ensure QR output exists before generating the combined ticket PDF.
        if (!isset($out)) {
          $options = new QROptions;
          $options->outputInterface  = QRFpdf::class;
          $options->scale            = 5;
          $options->fpdfMeasureUnit  = 'mm'; // pt, mm, cm, in
          $options->bgColor          = [222, 222, 222]; // [R, G, B]
          $options->drawLightModules = false;
          $out  = (new QRCode($options))->render($tractionId);
        }
        $pdf_content = $this->generatePdf2($html, $out);
        // $file_path = "../private/Tickets";
        // $private_file_system = \Drupal::service('file_system');

        // $private_file_path = $private_file_system->realpath('private://Tickets');
        $private_file_path = 'private://Tickets';
        $fileName = "Order_" . $order_number . ".pdf";
        if (!$this->fileSystem->prepareDirectory($private_file_path, FileSystemInterface::CREATE_DIRECTORY)) {
          // @todo Log an error.
          return FALSE;
        }

        $this->fileSystem->saveData($pdf_content, $private_file_path . "/" . $fileName, FileSystemInterface::EXISTS_REPLACE);

        $new_file = \Drupal\file\Entity\File::create(['uri' => $private_file_path . "/" . $fileName]);
        $new_file->setOwnerId(2);
        $new_file->setPermanent();
        $new_file->save();
        $order->set("field_pdf_file_order", [
          'target_id' => $new_file->id(),
        ]);
        // $order->set('placed', strtotime($orderDate));
        $order->save();
      }
      // Generate combined Food PDF per order if any Food items exist.
      if (!empty($foodOrder)) {
        $foodHtml = "";
        foreach ($foodOrder as $key => $foodTicket) {
          $foodHtml .= $this->getFoodPDFBody($key, $fullName, $foodTicket, sizeof($foodOrder));
        }
        // Reuse the last generated QR code output ($out) for the Food PDF header.
        if (!isset($out)) {
          $options = new QROptions;
          $options->outputInterface  = QRFpdf::class;
          $options->scale            = 5;
          $options->fpdfMeasureUnit  = 'mm';
          $options->bgColor          = [222, 222, 222];
          $options->drawLightModules = false;
          $out  = (new QRCode($options))->render($tractionId);
        }
        $food_pdf_content = $this->generateFoodPdf2($foodHtml, $out, $fullName, $order_date, $order_number);
        $food_file_name = "Order_" . $order_number . "_food.pdf";
        if ($this->fileSystem->prepareDirectory($private_file_path, FileSystemInterface::CREATE_DIRECTORY)) {
          $this->fileSystem->saveData($food_pdf_content, $private_file_path . "/" . $food_file_name, FileSystemInterface::EXISTS_REPLACE);
          // Create a file entity for the Food PDF and attach it to all Food order items
          // via the existing field_pdf_file field.
          $uid = $order->get('uid')->value;
          $food_file_entity = \Drupal\file\Entity\File::create(['uri' => $private_file_path . "/" . $food_file_name]);
          $food_file_entity->setOwnerId($uid);
          $food_file_entity->setPermanent();
          $food_file_entity->save();
          foreach ($foodOrderItems as $food_order_item_id) {
            $food_order_item = OrderItem::load($food_order_item_id);
            if ($food_order_item) {
              $food_order_item->set('field_pdf_file', [
                'target_id' => $food_file_entity->id(),
              ]);
              $food_order_item->save();
            }
          }
        }
      }
    }
  }

  public static function getPDFBody($key, $fullName, $ticket, $size)
  {
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $price_float = (float)$ticket['price'];
    $price_formatted = number_format($price_float, 2);
    $pageBreak = "";
    if ($key > 0) {
      $pageBreak = 'style="page-break-before: always"';
    }

    $html = '<section ' . $pageBreak . '>
            <div>
                <div style="float: left; width: 50%;">
                  <div class="e-ticket-box" style="border: 1px solid #000; margin-top:30px;">
                  
                    <table style="border-collapse: collapse;width: 100%;border-spacing: 30px;">
                          <tr>
                            <td>Guest Name</td>
                            <td>' . $fullName . '</td>
                          </tr>
                            <tr>
                                <td style="padding-top:10px">Description :</td>
                                  <td style="padding-top:10px">' . $ticket['body'] . '</td>
                  
                          </tr>
                            <tr>
                                <td style="padding-top:10px">Order Number :</td>
                                  <td style="padding-top:10px">' . $ticket['orderNumber'] . '</td>
                  
                          </tr>
                            <tr>
                                <td style="padding-top:10px">E-Ticket Price :</td>
                                  <td style="padding-top:10px">AED' . $price_formatted . '</td>
                  
                          </tr>
                            <tr>
                                <td style="padding-top:10px">Issue Date :</td>
                                  <td style="padding-top:10px">' . date("d/m/Y", strtotime($ticket['orderDate'])) . '</td>
                  
                          </tr>
                          <tr>
                            <td style="padding-top:10px">Count :</td>
                                <td style="padding-top:10px">' . $key + 1 . ' / ' . $size . '</td>
                      </tr>
                      <tr>
                                  <td colspan="2" style="padding-top:10px">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($ticket['visual_id'], $generator::TYPE_CODE_128)) . '">
                                  </td>
                          </tr>
                    </table>
                  
                  </div>
                      
                </div>

                <div style="float: right; width: 50%;">
                    <img src="sites/default/files/PDFImages/pdf-right.jpg">
                </div>

                <div style="clear: both; margin: 0pt; padding: 0pt; "></div>
            </div>
            <div>
                <h3><u>General Terms and Conditions</u></h3>
                <ul>
                    <li>This voucher is valid for 1 single entry. Multiple entry/exits are not allowed after checking in.</li>
                    <li>The tickets are non-refundable and cannot be exchanged for cash in part or full & absolutely no refund.</li>
                    <li>This voucher is not valid on UAE Public Holidays unless mentioned in the description above.</li>
                    <li>Guests exceeding a height of 120cm will be classified and charged as Adults.</li>
                    <li>Guests with height of 120cm or below will be classified and charged as Children.
                    </li>
                    <li>If a child ticket is purchased but the child is above 120cm in height, an additional supplement of AED 45 per ticket will be required at the gate. </li>
                    <li>Children below 80cm in height enters for free. A height measurement stand is available at the gate to assess the height of the children. 
</li>
                    <li>This Voucher is only valid till the Expiry Date shown on the e-ticket. Supplement of AED 75 plus vat per voucher will apply for one month extension. Subject to management approval.</li>
                    <li>The management retains the right to reject any voucher that has been tampered with or found in any way unacceptable.</li>
                    <li>This voucher cannot be used in conjunction with any other discount, promotions/voucher.</li>
                    <li>The voucher holder accepts to comply with Park Safety, Rules & Regulations.</li>
                    <li>Swimwear Policy: All guests are required to wear proper swimwear at all times while using water games. Only nylon and/or polyester materials are permitted. Pants, cut-off shorts, abaya, denim and/or cotton garments are absolutely not allowed. Swimwear with buckles or metal ornaments are also not allowed. You might be asked to change or leave the park premises in case of inappropriate swimwear.Babies are required to wear waterproof diapers while using the pools.</li>
                    <li>Food, drinks, shisha, picnic hampers and personal floatation devices as well as pets are not allowed. </li>
                    <li> All terms and conditions, including admission prices, are subject to change without prior notice at the discretion of the management.</li>
                    <li>For more details/inquiry contact us at 067681888/0506362217. Or visit our website:https://www.dreamlanduae.com</li>
                    <li> The Management reserves the right to refuse the admission for any guest to control occupancy when required. </li>
                </ul>
            </div>
          </section>';


    return $html;
    //  <p>' . $ticket['visual_id'] . '</p>
  }

  /**
   * Build Food voucher PDF body HTML.
   */
  public static function getFoodPDFBody($key, $fullName, $ticket, $size)
  {
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $price_float = (float)$ticket['price'];
    $price_formatted = number_format($price_float, 2);
    $pageBreak = "";
    if ($key > 0) {
      $pageBreak = 'style="page-break-before: always"';
    }

    $barcodeImg = 'data:image/png;base64,' . base64_encode($generator->getBarcode($ticket['orderNumber'], $generator::TYPE_CODE_128));

    $imageUrl = '';
    if (!empty($ticket['image_uri'])) {
      $imageUrl = \Drupal::service('file_url_generator')->generateAbsoluteString($ticket['image_uri']);
    }

    $html = '<section ' . $pageBreak . '>
      <div style="margin-top:40px;">
        <div style="margin-top:10px; text-align:center;">
          ' . (!empty($imageUrl) ? '<img src="' . $imageUrl . '" style="width:100%; max-height:350px; object-fit:cover;">' : '') . '
        </div>

        <div style="background-color:#ffc72c; color:#000; text-align:center; padding:5px 5px; margin-top:10px;">
          <div style="font-size:20px; font-weight:bold; letter-spacing:1px;">' . strtoupper($ticket['title']) . '</div>
          <div style="font-size:14px; margin-top:3px;">FOOD VOUCHER</div>
        </div>

        <div style="text-align:center; margin-top:5px; font-size:26px; font-weight:bold;">
          AED ' . $price_formatted . '
        </div>

        <div style="margin-top:0px; font-size:14px;">
          <div>' . (!empty($ticket['body']) ? $ticket['body'] : '') . '</div>
        </div>

        <div style="margin-top:10px; text-align:center;">
          <img src="' . $barcodeImg . '" style="height:50px;">
        </div>

        <div style="margin-top:10px;">
          <h3 style="font-size:13px; margin-bottom:5px;"><u>Terms &amp; Conditions</u></h3>
          <ul style="font-size:12px;">
    <li>
       Your online food voucher will be exchanged for a physical meal voucher at the park entrance. Please present physical meal
voucher at the chosen outlet to redeem your selected meal.
    </li>
    <li>
        Meals are available only at participating outlets within the park. Availability may vary by location.
    </li>
    <li>
        Each outlet offers three meal options, and each guest is entitled to one meal per person, which must be selected from the
chosen outlet.
    </li>
    <li>
	Each combo meal voucher is valid for the visiting guest only and cannot be transferred or shared.
    </li>
    <li>
Each combo meal voucher is valid for the visiting guest only and cannot be transferred or shared.

    </li>
    <li>
Meals must be redeemed on the same day of your visit.
    </li>
    <li>
If a meal voucher is not redeemed, no refund or compensation will be offered.
    </li>
    <li>
This offer cannot be combined with other discounts, promotions, or vouchers.
    </li>
    <li>
Last orders at participating outlets are taken at 5:30 PM.
    </li>
    <li>
All general park rules and regulations apply.
    </li>
   
</ul>
        </div>

        <div style="margin-top:10px; text-align:center; font-size:10px;">
          Voucher ' . ($key + 1) . ' of ' . $size . '
        </div>
      </div>
    </section>';

    return $html;
  }
}
