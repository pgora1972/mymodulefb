<?php


namespace MyModule\Module\Observer;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Registry;

class AddCartObserver implements ObserverInterface
{
    protected $_productRepository;
    protected $_cart;


    public function __construct(\Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Checkout\Model\Cart $cart){
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
    }
//@var $logger \Zend\Log\
    public function execute(EventObserver $observer)
    {

        //TODO Pamiętaj aby zrobić warunek bo inaczej doda się przy każnym produkcie
$product_id = 2110; //tumczasowo
//$product_id = 1387; //tumczasowo
//$product_id = 1; //tumczasowo
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);;

        $logger->info('value_start POST: ' . print_r($_POST, true));
        $logger->info('value_start GET: ' . print_r($_GET, true));
        $logger->info('value_start SESSION: ' . print_r($_SESSION, true));

        $logger->info("Staram się dodać produkt do koszyka");
        $logger->info("to break 1a");
        $item = $observer->getEvent()->getData("quote_item");
        $logger->info("to break a");
        $product = $observer->getEvent()->getData("product");

        $logger->info("to break b");
        $logger->info("to break 12");
       if($product->getId() != $product_id){
            $logger->info("Jestem w warunku");
           $logger->info("Product id: " . $product_id);
           $params = new \Magento\Framework\DataObject();
           $params->setQty(1);
           $_product = $this->_productRepository->getById($product_id);
            foreach ($_product->getOptions() as $o) {
               $logger->info("Jestem w forach 1");
               foreach ($o->getValues() as $value) {
                   $logger->info("Jestem w forach 2");
                   if($value['option_type_id']==1387) {
                       $options[$value['option_id']] = $value['option_type_id'];

                       //$options[$value['option_id']] = $value['option_type_ id'];
                       $params->setOptions($options); //set the customizable option to the product
                       $logger->info('value: ' . print_r($value->getData(), true));
                       break;
                   }
               }
               }

           $logger->info('Array  $params2 Log'.print_r($params, true));

           $logger->info("Jesten przed addProduct");
           try {
               $this->_cart->addProduct($_product,$params);
           } catch (Exception $e) {
               $logger->info("Error addProduct: " . $e->getMessage());
           }
            $logger->info("Jestem przed save");
            $this->_cart->save();
           $logger->info("A tu chcę zaktualizować koszyc");
           $this->_cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();

       }

        $logger->info("koniec dodawania do koszyka");
        $logger->info('value POST: ' . print_r($_POST, true));
        $logger->info('value GET: ' . print_r($_GET, true));
        $logger->info('value SESSION: ' . print_r($_SESSION, true));
        //$logger->info('value: ' . print_r($observer, true));

    }
}