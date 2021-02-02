<?php
namespace DigitalPenguin\Commerce_ImageMeld\Modules;

use DigitalPenguin\Commerce_ImageMeld\Fields\OrderItemMeld;
use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\OrderItemDetail;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\OrderState;
use modmore\Commerce\Events\OrderItem;
use modmore\Commerce\Events\Cart\Item as CartItem;
use modmore\Commerce\Frontend\Steps\Cart;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class ImageMeld extends BaseModule {
    private $productUrl = null;

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_imagemeld:default');
        return $this->adapter->lexicon('commerce_imagemeld');
    }

    public function getAuthor()
    {
        return 'Murray Wood';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_imagemeld.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_imagemeld:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_imagemeld', $path);

        // Add template path to twig
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Inject styling in mgr context. Added here because css isn't loaded the first time on order view (most likely due to JS loading)
        if($this->commerce->modx->context->key === 'mgr') {
            $this->commerce->modx->regClientCSS($this->adapter->getOption('commerce_imagemeld.assets_url') . 'mgr/css/commerce-imagemeld.css');
        }

        // Add composer libraries to the about section (v0.12+)
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);

        // Handles adding custom images to the order when product is added to cart
        $dispatcher->addListener(\Commerce::EVENT_ITEM_ADDED_TO_CART, [$this,'addedToCart']);

        // Check for an order number
        $dispatcher->addListener(\Commerce::EVENT_ORDERITEM_ADDED, [$this,'addedOrderItem']);

        // Display custom images and functions on order admin page
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_ORDER_ITEM_DETAIL, array($this, 'showOnDetailRow'));
    }

    /**
     * Once and order has been established (customer needs to visit checkout once) update meld record with order id
     * @param OrderItem $event
     */
    public function addedOrderItem(OrderItem $event){
        $item = $event->getItem();
        $this->updateOrderId($item);
    }

    /**
     * Runs just before an item is added to the customer's cart
     * @param CartItem $event
     */
    public function addedToCart(CartItem $event) {

        // Return early if this is not an imagemeld product
        if(!isset($_POST['commerce_imagemeld_input_meld'])) return;


        $order = $event->getOrder();
        $item = $event->getItem();
        $itemId = $item->get('id');
        //$this->commerce->modx->log(MODX_LOG_LEVEL_ERROR,print_r($item->toArray(),true));
        //$this->commerce->modx->log(MODX_LOG_LEVEL_ERROR,print_r($order->toArray(),true));

        $imageSrc = null;
        $imageMeld = null;
        $productLink = null;


        // Sanitize inputs
        if(isset($_POST['commerce_imagemeld_input_src']) && !empty($_POST['commerce_imagemeld_input_src'])) {
            $imageSrc = filter_input(INPUT_POST,'commerce_imagemeld_input_src',FILTER_SANITIZE_STRING);
        }
        if(isset($_POST['commerce_imagemeld_input_meld']) && !empty($_POST['commerce_imagemeld_input_meld'])) {
            $imageMeld = filter_input(INPUT_POST,'commerce_imagemeld_input_meld',FILTER_SANITIZE_STRING);
        }
        if(isset($_POST['commerce_imagemeld_product_link']) && !empty($_POST['commerce_imagemeld_product_link'])) {
            $productLink = filter_input(INPUT_POST,'commerce_imagemeld_product_link',FILTER_SANITIZE_NUMBER_INT);
        }
        if(isset($_POST['commerce_imagemeld_product_id']) && !empty($_POST['commerce_imagemeld_product_id'])) {
            $productId = filter_input(INPUT_POST,'commerce_imagemeld_product_id',FILTER_SANITIZE_NUMBER_INT);
        }
        $this->productUrl = $this->commerce->modx->makeUrl($productLink,'','','full');

        // Redirect back to product with validation error if any submitted values are missing.
        if(!$productId || !$imageSrc || !$imageMeld || !$productLink) $this->commerce->modx->sendRedirect($this->productUrl . '?cim_err=1');

        // Make sure this order item is for the imagemeld product, otherwise ignore it.
        if((int)$productId === (int)$item->get('product')) {
            // Generate path for file based on item id and system setting commerce_imagemelds.melds_path
            $newImgPath = $this->adapter->getOption('commerce_imagemeld.melds_path') . $itemId . '/';

            // Convert base64 to image file and save on server
            $meldedFilename = $this->saveImage($imageMeld, $newImgPath);
            $srcFilename = $this->saveImage($imageSrc, $newImgPath, true);

            // Index images in database (useful for cleanup later if need be)
            if (!$this->addToDatabase($item, $order, $meldedFilename, $srcFilename))
                $this->commerce->modx->log(MODX_LOG_LEVEL_ERROR, 'Failed adding melded image data to database.');

            // cartitemid represents the \comOrderItem object that is added when a product is added to cart
            // not to be confused with the \comOrderItem object that is added when a customer moves from cart to checkout
            $item->setProperty('imagemeld.cartitemid', $item->get('id'));
            $item->setProperty('imagemeld.meldfilename', $meldedFilename);
            $item->setProperty('imagemeld.srcfilename', $srcFilename);

            // Overwrite standard product image with newly melded image
            if (!$this->overwriteItemImage($item, $meldedFilename))
                $this->commerce->modx->log(MODX_LOG_LEVEL_ERROR, 'Unable to add newly melded image to item object.');

        }
    }

    /**
     * Allows for customer generated meld image to be displayed in cart etc.
     * @param \comOrderItem $item
     * @param string $meldedImage
     * @return bool
     */
    function overwriteItemImage(\comOrderItem $item, string $meldedImage) : bool
    {
        $item->set('image', $this->adapter->getOption('commerce_imagemeld.melds_url') . $item->get('id') . '/' . $meldedImage);
        return $item->save();
    }

    /**
     * @param \comOrderItem $item
     * @param \comOrder $order
     * @param string $meldFile
     * @param string $srcFile
     * @return bool
     */
    function addToDatabase(\comOrderItem $item, \comOrder $order, string $meldFile, string $srcFile) : bool
    {
        $properties = [
            'session_id'    =>  session_id(),
            'ip_address'    =>  $_SERVER['REMOTE_ADDR'],
            'cart_item_id'  =>  $item->get('id'),
            'product_id'    =>  $item->get('product'),
            'order_id'      =>  $order->get('id') ?? '',
            'melded_file'   =>  $meldFile,
            'source_file'   =>  $srcFile
        ];
        $meldObj = $this->adapter->newObject('cimMeld');
        if(!$meldObj instanceof \cimMeld) return false;

        $meldObj->fromArray($properties);
        return $meldObj->save();
    }

    /**
     * Once an order id is available, add it to the existing meld record
     * @param \comOrderItem $item
     */
    function updateOrderId(\comOrderItem $item) : void
    {
        // Return early if this item hasn't been assigned to an order yet
        if(!$item->get('order')) return;

        $cartItemId = $item->getProperty('imagemeld.cartitemid');

        $meldObj = $this->adapter->getObject('cimMeld',[
            'session_id'    =>  session_id(),
            'ip_address'    =>  $_SERVER['REMOTE_ADDR'],
            'cart_item_id'  =>  $cartItemId
        ]);
        if($meldObj instanceof \cimMeld) {
            $meldObj->set('order_id',$item->get('order'));
            $meldObj->set('order_item_id',$item->get('id'));
            $meldObj->save();
        }
    }

    /**
     * Saves image to the specified directory and returns filename
     * @param string $base64String
     * @param string $newImgPath
     * @param bool $isSrc
     * @return string
     */
    function saveImage(string $base64String, string $newImgPath, bool $isSrc = false) : string
    {
        $filename = 'output-meld.png';
        $data = explode(',', $base64String);
        $decodedImg = base64_decode($data[1]);
        if($isSrc) {
            $this->checkMinimumSize($base64String);
            $filename = $this->determineFileType($decodedImg);
        }
        $fullPath = $newImgPath . $filename;

        if (!file_exists($newImgPath)) {
            mkdir($newImgPath, 0777, true);
        }
        $ifp = fopen($fullPath, 'wb');
        fwrite($ifp, $decodedImg);
        fclose($ifp);
        return $filename;
    }

    /**
     * Checks for values in the min_width and min_height system settings. If empty, the sizing is ignored.
     * If settings have a value it is compared with image sizing and redirects to product page with param ?cim_err=3
     * @param $base64String
     */
    function checkMinimumSize($base64String) {
        // Check min size requirements
        list($width, $height, $type, $attr) = getimagesize($base64String);
        $minWidth = $this->adapter->getOption('commerce_imagemeld.min_width');
        $minHeight = $this->adapter->getOption('commerce_imagemeld.min_height');

        if($minWidth) {
            if($minWidth > $width) {
                $this->commerce->modx->sendRedirect($this->productUrl . '?cim_err=3');
            }
        }
        if($minHeight) {
            if($minHeight > $height) {
                $this->commerce->modx->sendRedirect($this->productUrl . '?cim_err=3');
            }
        }
    }

    /**
     * Takes the decoded image, finds the mimetype and then returns the filename with the appropriate file extension
     * @param $decodedImg
     * @return string
     */
    function determineFileType($decodedImg) : string
    {
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $decodedImg, FILEINFO_MIME_TYPE);
        //$this->adapter->log(MODX_LOG_LEVEL_ERROR,'MIME_Type: '.$mimeType);
        switch($mimeType) {
            case 'image/png':
                $filename = 'uploaded-image.png';
                break;
            case 'image/jpeg':
                $filename = 'uploaded-image.jpg';
                break;
            default:
                $this->commerce->modx->sendRedirect($this->productUrl . '?cim_err=2');
                return false;
        }

        return $filename;
    }

    public function showOnDetailRow(OrderItemDetail $event)
    {
        $item = $event->getItem();
        // Early return if item is not an image meld.
        if(!$item->getProperty('imagemeld.cartitemid')) return;

        $path = $this->adapter->getOption('commerce_imagemeld.melds_url') . $item->getProperty('imagemeld.cartitemid') . '/';
        $values = [
            'melded_image'  =>  [
                'alt'   =>  $this->adapter->lexicon('commerce_imagemeld.admin.melded_image'),
                'img'   =>  $path . $item->getProperty('imagemeld.meldfilename')
            ],
            'source_image'  =>  [
                'alt'   =>  $this->adapter->lexicon('commerce_imagemeld.admin.uploaded_image'),
                'img'   =>  $path . $item->getProperty('imagemeld.srcfilename')
            ]
        ];
        $output = '<div class="commerce-imagemeld"><h5>'
            . $this->adapter->lexicon('commerce_imagemeld.admin.custom_design')
            . '</h5><div class="commerce-imagemeld-row">';
        foreach ($values as $value) {
            $field = new OrderItemMeld($this->commerce, $value['alt'], $value['img']);
            $output .= $field->renderForAdmin();
        }
        $output .= '</div></div>';

        $event->addRow($output);

    }



    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];
        return $fields;
    }

    public function addLibrariesToAbout(PageEvent $event)
    {
        $lockFile = dirname(__DIR__, 2) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_imagemeld'),
                'introduction' => '',
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }
}
