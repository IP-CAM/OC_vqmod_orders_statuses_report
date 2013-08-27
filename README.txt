Orders statuses report

This report allows to track progress of your store.
It parses the history of your orders and shows the statistics about state changes of your orders.

You'll get the answers on these and many other questions:
 - How many orders have I received on this and last week / month?
 - How fast are the orders shipped? 
 - In which states the orders are hanging?
 - Are we getting better in handling of our orders?
 - What have been done today / this week / this month?
 
The report provides statistics in:
 - number of orders
 - number of ordered products
 - total sales in default currency

The report provides 3 forms of aggregation: daily, weekly and monthly.

The report takes care about all the states ever used in your store: basic or custom.

Cells contain data in the format A[+B/-C]:
  A - an absolute value; the total number of orders by the end of the period
  B - a change to the previous period; the number of orders entered this state
  C - a change to the previous period; the number of orders left this state

You can limit the report by the number of records. In this case, the oldest records are skipped.

The extension requires VQMOD. No files are overwritten.

Tested for opencart 1.5.4.1.
Please add in the comments if it's working for other releases. I'll add this information.

Comments are welcome!


Installation
=====================================================
Requires VQMOD.
Copy the admin and vqmod directories to your store. That's all!

Setup
=====================================================
* Log into the admin section of your openCart Site e.g www.yoursite.com/admin.
* Add access rights: Select System - > Users -> Users groups. Choose Top Administrators -> Edit. Add access and modify permissions for report/orders_statuses.

Usage
=========
Go to admin part.
Go to Reports -> Sales -> Orders Statuses
If you get an error about access rights, make sure you followed the setup section :-)

To add a language
=====================================================
1. Copy contents of 
admin\language\english
to
admin\language\[other language]
Then modify 
admin\language\[other language]\report\orders_statuses.php

2. To set the report name in the header menu:
Make a copy this section in vqmod\xml\orders_statuses_report.xml 
	<file name="admin/language/english/common/header.php">
		<operation>
			<search position="before"><![CDATA[$_['text_report_sale_coupon']          = 'Coupons';]]></search>
			<add><![CDATA[$_['text_report_orders_statuses']  = 'Orders Statuses';
		]]></add>
		</operation>
	</file>
Change 'english' in the file name you the other language.
Change 'Orders Statuses' to your text.
Change 'Coupons' to the proper name in your langiuage. You can check admin/language/[other language]/common/header.php to make sure that you change it correctly.



Need Help?
=========
Please get in touch with me in comment

ChangeLog
=======
Current version: 1.1.2

1.1.2
-fixed the report for the number of products and sales in case when an order has no products
1.1.1
-fixed form width for iPad
1.1.0
-added statistics types: orders, products, sales
1.0.0
-init release

