<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Designnbuy\Book\Controller\Book;

use Magento\Framework\App\Action\Action;

/**
 * Class Update.
 * This controller updates options stock message on the product page
 */
class Quote extends Action
{
    public function execute()
    {
        $myqty = $this->getRequest()->getPost('custom_qty');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($this->getRequest()->getPost('product_id'));
        if($this->getRequest()->getPost('type_of_book_you_want') == 'Standard Colour Printing' && $this->getRequest()->getPost('color_page_count') != ""){

            //Multi Color
            
            $page_count = $this->getRequest()->getPost('color_page_count'); //H22
            
            
            $inside_paper_cost_for_mono_color_paper_required = ($page_count/8)*$myqty; // K46
            
            $inside_paper_cost_for_mono_color = $inside_paper_cost_for_mono_color_paper_required * $this->getRequest()->getPost('color_paper_stock'); // H46
            
            $cover_paper_cost_paper_required = $myqty/2; // K48
            $cover_paper_cost = $cover_paper_cost_paper_required * $this->getRequest()->getPost('cover_stock'); // H48
            $no_of_8p_format_one_side = $page_count/8; // K50
            $no_of_8p_format_two_side = $no_of_8p_format_one_side * $myqty; // L50
            
            $inside_mono_colour_printing_cost = $no_of_8p_format_two_side * $this->getRequest()->getPost('type_of_book_you_want_price'); // H50
            
            $total_cover_per_sheet_1 = ($myqty/1)+5; // K52
            $total_cover_per_sheet_2 = $total_cover_per_sheet_1 * 2; // L52
            
            $cover_printing_cost = $total_cover_per_sheet_2 * $product->getCoverPrintingCost(); // H52
            //$total_sheet_for_lamination = ($myqty/2)+5; // K54
            $total_sheet_for_lamination = 55; // K54
            
            $lamination_cost = $total_sheet_for_lamination * $this->getRequest()->getPost('lamination_cost'); // H54
            
            $binding_cost = $myqty * $this->getRequest()->getPost('binding_style'); // H56
            
            $additional_service_cost = $product->getAdditionalServiceCost(); // H58

            $distance_50_KM = $product->getDistanceKm(); // K60
            $courier_cost = $distance_50_KM * $product->getCourierCost(); // H60
            
            $packing = $myqty * $product->getPacking(); // H62

            $total = $inside_paper_cost_for_mono_color + $cover_paper_cost + $inside_mono_colour_printing_cost + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing;
            //$GST = $total * 0.05;
            if($this->getRequest()->getPost('email_data') != null){
                $this->sentEmail($this->getRequest()->getPost('email'),$this->getRequest()->getPost('email_data'),$priceHelper->currency($total, true, false),$this->getRequest()->getPost('custom_qty'));
            }
            echo $priceHelper->currency($total, true, false); exit;

            } else if($this->getRequest()->getPost('type_of_book_you_want') == 'Standard Mono Printing' && $this->getRequest()->getPost('black_and_white_page_count') != ""){
                //Multi Color
                
                    $page_count = $this->getRequest()->getPost('black_and_white_page_count');
                
                
                
                $inside_paper_cost_for_mono_color_paper_required = ($page_count/4)*$myqty;
                
                $inside_paper_cost_for_mono_color = $inside_paper_cost_for_mono_color_paper_required * $this->getRequest()->getPost('black_and_white_paper_stock');
                
                $cover_paper_cost_paper_required = $myqty/1;
                
                $cover_paper_cost = $cover_paper_cost_paper_required * $this->getRequest()->getPost('cover_stock');
                
                $no_of_8p_format_one_side = $page_count/4;
                
                $no_of_8p_format_two_side = $no_of_8p_format_one_side * $myqty;
                
                $inside_mono_colour_printing_cost = $no_of_8p_format_two_side * $this->getRequest()->getPost('type_of_book_you_want_price');
                
                $total_cover_per_sheet_1 = ($myqty/1)+5;
                $total_cover_per_sheet_2 = $total_cover_per_sheet_1 * 2;
                
                $cover_printing_cost = $total_cover_per_sheet_2 * $product->getCoverPrintingCost();
                
                $total_sheet_for_lamination = 55;
                
                $lamination_cost = $total_sheet_for_lamination * $this->getRequest()->getPost('lamination_cost');
                
                $binding_cost = $myqty * $this->getRequest()->getPost('binding_style');
                
                $additional_service_cost = $product->getAdditionalServiceCost();

                $distance_50_KM = $product->getDistanceKm();
                $courier_cost = $distance_50_KM * $product->getCourierCost();
                
                $packing = $myqty * $product->getPacking();

                $total = $inside_paper_cost_for_mono_color + $cover_paper_cost + $inside_mono_colour_printing_cost + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing;
                //$GST = $total * 0.05;
                if($this->getRequest()->getPost('email_data') != null){
                    $this->sentEmail($this->getRequest()->getPost('email'),$this->getRequest()->getPost('email_data'),$priceHelper->currency($total, true, false),$this->getRequest()->getPost('custom_qty'));
                }
                echo $priceHelper->currency($total, true, false); exit;
                
            } else if($this->getRequest()->getPost('type_of_book_you_want') == 'Mono Printing with Colour Pages' && $this->getRequest()->getPost('black_and_white_page_count') != ""){
                $number_of_mono_pages = $this->getRequest()->getPost('black_and_white_page_count'); //H12
                 
                $inside_paper_cost_for_mono_colour_paper_required = ($number_of_mono_pages/4)*$myqty; //K40
                $inside_paper_cost_for_mono_colour = $inside_paper_cost_for_mono_colour_paper_required * $this->getRequest()->getPost('black_and_white_paper_stock'); //H40
                $inside_paper_cost_for_colour_page_paper_required = $myqty * 8; //K42
                $inside_paper_cost_for_colour_page = $inside_paper_cost_for_colour_page_paper_required * $this->getRequest()->getPost('color_paper_stock'); //H42
                $cover_paper_cost_paper_required = $myqty / 1; //K44
                $cover_paper_cost = $cover_paper_cost_paper_required * $this->getRequest()->getPost('cover_stock'); //H44
                $inside_mono_colour_printing_cost_no_of_4p_first = $number_of_mono_pages / 4; //K46
                $inside_mono_colour_printing_cost_no_of_4p_second = $inside_mono_colour_printing_cost_no_of_4p_first * $myqty; //L46
                $inside_mono_colour_printing_cost = $inside_mono_colour_printing_cost_no_of_4p_second * $this->getRequest()->getPost('type_of_book_you_want_price'); //H46
                $inside_colour_printing_no_of_8_page_first = 8; //K48
                $inside_colour_printing_no_of_8_page_second = $inside_colour_printing_no_of_8_page_first * 50; //L48
                $inside_colour_printing = $inside_colour_printing_no_of_8_page_second * 20; //H48
                $cover_printing_cost_per_sheet_first = ($myqty/1)+5; //K50
                $cover_printing_cost_per_sheet_second = $cover_printing_cost_per_sheet_first * 2; //L50
                $cover_printing_cost = $cover_printing_cost_per_sheet_second * $product->getCoverPrintingCost(); //H50
                $lamination_cost_total_sheet = 55; //K52
                $lamination_cost = $lamination_cost_total_sheet * $this->getRequest()->getPost('lamination_cost'); //H52
                $binding_cost = $myqty * $this->getRequest()->getPost('binding_style'); //H54
                $additional_service_cost = $product->getAdditionalServiceCost(); //H56
                $courier_cost_distance_50_km = $product->getDistanceKm(); //K58
                $courier_cost = $courier_cost_distance_50_km * $product->getCourierCost(); //H58
                $packing = $myqty * $product->getPacking(); //H60
                $total = $inside_paper_cost_for_mono_colour + $inside_paper_cost_for_colour_page + $cover_paper_cost + $inside_mono_colour_printing_cost + $inside_colour_printing + $cover_printing_cost + $lamination_cost + $binding_cost + $additional_service_cost + $courier_cost + $packing; //H62 
                if($this->getRequest()->getPost('email_data') != null){
                    $this->sentEmail($this->getRequest()->getPost('email'),$this->getRequest()->getPost('email_data'),$priceHelper->currency($total, true, false),$this->getRequest()->getPost('custom_qty'));
                }
                echo $priceHelper->currency($total, true, false); exit;
            }
    }
    protected function sentEmail($email,$option_data,$price,$qty_selected){
        
        $to = $email;
        $subject = "Reminder of your quotation";

        $message = "
            <html>
            <head>
            <title>Reminder of your quotation</title>
            </head>
            <body>
            <p>Reminder of your quotation</p>
            ".$option_data."
            <div><h3>Qty : </h3><span>".$qty_selected."</span></div>
            <div><h3>Price : </h3><span>".$price."</span></div>
            </body>
            </html>
            ";
            
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            //$headers .= 'From: <webmaster@example.com>' . "\r\n";
            //$headers .= 'Cc: myboss@example.com' . "\r\n";

            mail($to,$subject,$message,$headers);

    }
}