# magento2-extension

## Installation
Prior to installing the extension, please check the latest tag on https://magento2connector.unific.com/

* Step 1: composer config repositories.unific composer https://magento2connector.unific.com/
* Step 2: composer require unific/connector:<latest-tag>

###Proceed with the Magento 2 upgrade commands
* Step 3: bin/magento setup:upgrade
* Step 4: ...

## Troubleshooting

###Composer installation fails after updating from github to composer
* Step 1: composer remove unific/connector
* Step 2: Open composer.json and remove the old github repository from Unific
* Step 3: Restart this installation process from step 1

###Deploying to Satis
* Step 1: Push the latest stable code to github and it will push to Satis

##Releases

### 1.4.25
1. Bug fix - Checkout Page failing for Magento ver 2.4 for guest users

### 1.4.24
1. Support for Magento version 2.4 with PHP 2.4

### 1.4.23
1. Send order/create webhook even if the payment method sets processing state
2. Send shipment webhooks for shipments saved via shipment repository

### 1.4.22
1. Send order notes added from admin with the order information

### 1.4.21
1. Removed Iterable keyword to support historicals for PHP version 7.0
2. Sending category ids in the product information webhooks

### 1.4.20
1. Fixed Not able to send Reset Password link - Subscribed Customers

### 1.4.19
1. Bug fix - Order processing after saving the entity - shipments

### 1.4.18
1. Custom Attributes and Extension Attributes

### 1.4.17
1. Fixed error that occurred while putting order on-hold

### 1.4.16
1. Bug fix added for shipment not getting created through API.
2. Unific Reports and Unific Historical API Access Resources added.

### 1.4.15
1. Sending more shipment information with the order including historicals
2. Sending more payment information with the order including historicals
3. Sending correct store metadata in historical

### 1.4.14
1. Better Historical Process - Send to Unific endpoint or can save to file on the Magento store server

### 1.4.13
1. Moved Unific Logs menu inside Store Configurations
2. Sending shipment carrier and tracking details with orders
3. Sending payment title and card type used for payment

### 1.4.12
1. Redirect customer to checkout cart page after abandoned product id restored to cart
2. Whitelisted Fax in address entity

### 1.4.11
1. Generate the Checkout Abandonded Recovery URL with Magento standards
2. Added note for Unific Integration Identifier in the admin section
3. Send store domain in the webhook headers

### 1.4.10
1. Better Queue Processing
2. Marketplace checkstyle fixes (needs resubmission)

### 1.4.9
1. Send the correct updated connector version in the webhooks

### 1.4.8
1. Reduced live webhooks per minute to 50, historical webhooks per minute to 25 and historcical page size to 50 - to handle locking issue

### 1.4.7
1. Added Storeview to the abandoned cart URL that is sent with checkout webhooks.

### 1.4.6
1. Abandoned Cart Recovery URL is now sent with the checkout webhooks - Done

### 1.4.5
1. Wrong product information being sent in product webhooks - fixed

2. Not able to save a configurable product with simple product having images - fixed


### 1.4.4
1. Queue Locking

2. Schema Mediumtext for unific_connector_message_queue

3. Hubspot UTK Support

4. Website, store, view code

5. Skip bad webhooks

6. Unset the address for historical address bug

7. Product URL suffix

8. Webhooks priority

9. Customer Group

10. Order Invoice updated_at date

11. Order Shipment updated_at date

12. Customer Address webhook

13. Enable/Disable Connector on Store View level 

14. Checkout Update webhooks are not always sent
