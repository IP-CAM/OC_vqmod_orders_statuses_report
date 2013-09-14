<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>

  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
	  
    </div>
    <div class="content">
      <table class="list">
        <thead>
          <?php if ($oh) { ?>
          <tr>
		  <td class="left"><?php echo $column_orderId; ?></td>
		  <td class="left"><?php echo $column_name; ?></td>
 		  <td class="left"><?php echo $column_state_change; ?></td>
		  <td class="left"><?php echo $column_orders; ?></td>
		  <td class="left"><?php echo $column_quantity; ?></td>
		  <td class="left"><?php echo $column_total; ?></td>
		  <td class="left"><?php echo $column_date; ?></td>
 		  <td class="left"><?php echo $column_old_status; ?></td>
 		  <td class="left"><?php echo $column_new_status; ?></td>
 		  <td class="left"><?php echo $column_comment; ?></td>
 		  <td class="left"><?php echo $column_actions; ?></td>
		  </tr>
          <?php } ?>
        </thead>
        <tbody>
          <?php if ($oh) { ?>
 			  <tr>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"><?php echo $text_before; ?></td>
			  <td class="right"><?php echo $orders_before; ?></td>
			  <td class="right"><?php echo $quantity_before; ?></td>
			  <td class="right"><?php echo $total_before; ?></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  </tr>

			  <?php foreach ($oh as $order_id => $oh_list) { ?>
			  <?php foreach ($oh_list as $oh_id => $oh_data) { ?>
			  <tr>
			  <td class="left"><?php echo $order_id; ?></td>
			  <td class="left"><?php echo $oh_data['name']; ?></td>
			  <td class="left"><?php echo $oh_data['state_change']; ?></td>
			  <td class="right"><?php echo $oh_data['orders']; ?></td>
			  <td class="right"><?php echo $oh_data['quantity']; ?></td>
			  <td class="right"><?php echo $oh_data['total']; ?></td>
			  <td class="left"><?php echo $oh_data['date']; ?></td>
			  <td class="left"><?php echo $oh_data['old_status']; ?></td>
			  <td class="left"><?php echo $oh_data['new_status']; ?></td>
			  <td class="left"><?php echo $oh_data['comment']; ?></td>
			  <td class="left"><a href="index.php?route=sale/order/info&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>"><?php echo $text_view; ?></a></td>
			  </tr>
			  <?php } ?>
          <?php } ?>
 			  <tr>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"><?php echo $text_after; ?></td>
			  <td class="right"><?php echo $orders_after; ?></td>
			  <td class="right"><?php echo $quantity_after; ?></td>
			  <td class="right"><?php echo $total_after; ?></td>
			  <td class="left"><?php echo $filter_date; ?></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  <td class="left"></td>
			  </tr>

         <?php } else { ?>
          <tr>
            <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
		  </tbody>

      </table>
    </div>

  </div>
</div>


<script type="text/javascript"><!--

function filter() {

	url = 'index.php?route=report/orders_statuses&token=<?php echo $token; ?>';

	var filter_date_aggregation = $('select[name=\'filter_date_aggregation\']').attr('value');

	if (filter_date_aggregation != '*') {
		url += '&filter_date_aggregation=' + encodeURIComponent(filter_date_aggregation);
	}	

	var filter_count_type = $('select[name=\'filter_count_type\']').attr('value');

	if (filter_count_type != '*') {
		url += '&filter_count_type=' + encodeURIComponent(filter_count_type);
	}	

	var filter_no_of_records = $('input[name=\'filter_no_of_records\']').attr('value');

	if (!isNaN(parseFloat(filter_no_of_records)) && isFinite(filter_no_of_records)) {
		url += '&filter_no_of_records=' + encodeURIComponent(filter_no_of_records);
	}	

	location = url;

}

//--></script>  

<script type="text/javascript"><!--

$('#form input').keydown(function(e) {

	if (e.keyCode == 13) {

		filter();

	}

});

//--></script> 



<?php echo $footer; ?>