<?php

namespace Designnbuy\Book\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class PriceCalculation implements ObserverInterface
{
    protected $_helper;

    public function __construct(
        \Designnbuy\Book\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        
        if ($item->getProduct()->getAttributeSetId() == $this->_helper->getCustomBookAttributeSetId()) {
            $_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

            $myqty = $item->getQty();
            $total = $this->productCalculation($_customOptions, $myqty, $item->getProduct());
            
            $grand_total = $total / $item->getQty();
            $item->setCustomPrice($grand_total);
            $item->setOriginalCustomPrice($grand_total);
            $item->getProduct()->setIsSuperMode(true);
        }

    }

    public function productCalculation($_customOptions, $myqty, $product)
    {
        $options_data = array();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!empty($_customOptions)) {
            if (array_key_exists('options', $_customOptions)) {
                foreach ($_customOptions['options'] as $option) {
                    $customOptions_item = $_objectManager->get('Magento\Catalog\Model\Product\Option')->getCollection()->addFieldToFilter('option_id', $option['option_id'])->getFirstItem();
                    //echo "<pre>"; print_r(get_class_methods($customOptions_item)); exit;
                    if ($customOptions_item->getUniqueFieldSku()) {
                        if (strpos($option['value'], ' x ') !== false) {
                            $exp_option = explode(" x ", $option['value']);
                            $my_option_label = $exp_option[1];
                            if ($option['price_data'] != "") {
                                $my_option_label_ff = explode(" - ", $exp_option[1]);
                                $my_option_label = $my_option_label_ff[0];
                            }
                            $options_data[$customOptions_item->getUniqueFieldSku()]['label'] = $my_option_label;
                            $options_data[$customOptions_item->getUniqueFieldSku()]['value'] = $option['price_data'];
                        } else {
                            $options_data[$customOptions_item->getUniqueFieldSku()]['label'] = $option['value'];
                        }
                    }
                }
            }
        }

        if (!empty($options_data)) {


            if ($options_data['type_of_book_you_want']['label'] == 'Standard Colour Printing') {

                //Multi Color
                if (array_key_exists('black_and_white_page_count', $options_data)) {
                    $page_count = $options_data['black_and_white_page_count']['label']; //H22
                } else {
                    $page_count = $options_data['color_page_count']['label']; //H22
                }

                $inside_paper_cost_for_mono_color_paper_required = ($page_count / 8) * $myqty; // K46

                $inside_paper_cost_for_mono_color = $inside_paper_cost_for_mono_color_paper_required * $options_data['color_paper_stock']['value']; // H46

                $cover_paper_cost_paper_required = $myqty / 2; // K48
                $cover_paper_cost = $cover_paper_cost_paper_required * $options_data['cover_stock']['value']; // H48
                $no_of_8p_format_one_side = $page_count / 8; // K50
                $no_of_8p_format_two_side = $no_of_8p_format_one_side * $myqty; // L50

                $inside_mono_colour_printing_cost = $no_of_8p_format_two_side * $options_data['type_of_book_you_want']['value']; // H50

                $total_cover_per_sheet_1 = ($myqty / 1) + 5; // K52
                $total_cover_per_sheet_2 = $total_cover_per_sheet_1 * 2; // L52

                $cover_printing_cost = $total_cover_per_sheet_2 * $product->getCoverPrintingCost(); // H52
                //$total_sheet_for_lamination = ($myqty/2)+5; // K54
                $total_sheet_for_lamination = 55; // K54

                $lamination_cost = $total_sheet_for_lamination * $options_data['lamination_cost']['value']; // H54

                $binding_cost = $myqty * $options_data['binding_style']['value']; // H56

                $additional_service_cost = $product->getAdditionalServiceCost(); // H58

                $distance_50_KM = $product->getDistanceKm(); // K60
                $courier_cost = $distance_50_KM * $product->getCourierCost(); // H60

                $packing = $myqty * $product->getPacking(); // H62

                $total = $inside_paper_cost_for_mono_color + $cover_paper_cost + $inside_mono_colour_printing_cost + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing;
                //$GST = $total * 0.05;

                return $total;

            } else if ($options_data['type_of_book_you_want']['label'] == 'Standard Mono Printing') {
                //Multi Color
                if (array_key_exists('black_and_white_page_count', $options_data)) {
                    $page_count = $options_data['black_and_white_page_count']['label'];
                } else {
                    $page_count = $options_data['color_page_count']['label'];
                }

                $inside_paper_cost_for_mono_color_paper_required = ($page_count / 4) * $myqty;

                $inside_paper_cost_for_mono_color = $inside_paper_cost_for_mono_color_paper_required * $options_data['black_and_white_paper_stock']['value'];

                $cover_paper_cost_paper_required = $myqty / 1;

                $cover_paper_cost = $cover_paper_cost_paper_required * $options_data['cover_stock']['value'];

                $no_of_8p_format_one_side = $page_count / 4;

                $no_of_8p_format_two_side = $no_of_8p_format_one_side * $myqty;

                $inside_mono_colour_printing_cost = $no_of_8p_format_two_side * $options_data['type_of_book_you_want']['value'];

                $total_cover_per_sheet_1 = ($myqty / 1) + 5;
                $total_cover_per_sheet_2 = $total_cover_per_sheet_1 * 2;

                $cover_printing_cost = $total_cover_per_sheet_2 * $product->getCoverPrintingCost();

                $total_sheet_for_lamination = 55;

                $lamination_cost = $total_sheet_for_lamination * $options_data['lamination_cost']['value'];

                $binding_cost = $myqty * $options_data['binding_style']['value'];

                $additional_service_cost = $product->getAdditionalServiceCost();

                $distance_50_KM = $product->getDistanceKm();
                $courier_cost = $distance_50_KM * $product->getCourierCost();

                $packing = $myqty * $product->getPacking();

                $total = $inside_paper_cost_for_mono_color + $cover_paper_cost + $inside_mono_colour_printing_cost + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing;
                //$GST = $total * 0.05;
                return $total;

            } else if ($options_data['type_of_book_you_want']['label'] == 'Mono Printing with Colour Pages') {
                $number_of_mono_pages = $options_data['black_and_white_page_count']['label']; //H12

                $inside_paper_cost_for_mono_colour_paper_required = ($number_of_mono_pages / 4) * $myqty; //K40
                $inside_paper_cost_for_mono_colour = $inside_paper_cost_for_mono_colour_paper_required * $options_data['black_and_white_paper_stock']['value']; //H40
                $inside_paper_cost_for_colour_page_paper_required = $myqty * 8; //K42
                $inside_paper_cost_for_colour_page = $inside_paper_cost_for_colour_page_paper_required * $options_data['color_paper_stock']['value']; //H42
                $cover_paper_cost_paper_required = $myqty / 1; //K44
                $cover_paper_cost = $cover_paper_cost_paper_required * $options_data['cover_stock']['value']; //H44
                $inside_mono_colour_printing_cost_no_of_4p_first = $number_of_mono_pages / 4; //K46
                $inside_mono_colour_printing_cost_no_of_4p_second = $inside_mono_colour_printing_cost_no_of_4p_first * $myqty; //L46
                $inside_mono_colour_printing_cost = $inside_mono_colour_printing_cost_no_of_4p_second * $options_data['type_of_book_you_want']['value']; //H46
                $inside_colour_printing_no_of_8_page_first = 8; //K48
                $inside_colour_printing_no_of_8_page_second = $inside_colour_printing_no_of_8_page_first * 50; //L48
                $inside_colour_printing = $inside_colour_printing_no_of_8_page_second * 20; //H48
                $cover_printing_cost_per_sheet_first = ($myqty / 1) + 5; //K50
                $cover_printing_cost_per_sheet_second = $cover_printing_cost_per_sheet_first * 2; //L50
                $cover_printing_cost = $cover_printing_cost_per_sheet_second * $product->getCoverPrintingCost(); //H50
                $lamination_cost_total_sheet = 55; //K52
                $lamination_cost = $lamination_cost_total_sheet * $options_data['lamination_cost']['value']; //H52
                $binding_cost = $myqty * $options_data['binding_style']['value']; //H54 
                $additional_service_cost = $product->getAdditionalServiceCost(); //H56
                $courier_cost_distance_50_km = $product->getDistanceKm(); //K58
                $courier_cost = $courier_cost_distance_50_km * $product->getCourierCost(); //H58
                $packing = $myqty * $product->getPacking(); //H60
                $total = $inside_paper_cost_for_mono_colour + $inside_paper_cost_for_colour_page + $cover_paper_cost + $inside_mono_colour_printing_cost + $inside_colour_printing + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing; //H62 

                return $total;
            }
        }
    }

}

?>