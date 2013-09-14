<?php
class ControllerReportOrdersStatusesDetailed extends Controller {
	public function index() {     
		$this->load->language('report/orders_statuses_detailed');

		$this->document->setTitle($this->language->get('heading_title'));
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
				
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_date_aggregation'])) {
			$filter_date_aggregation            = $this->request->get['filter_date_aggregation'];
			$url .= '&filter_date_aggregation=' . $this->request->get['filter_date_aggregation'];
		} else {
			$filter_date_aggregation = 'week';
		}

		if (isset($this->request->get['filter_count_type'])) {
			$filter_count_type            = $this->request->get['filter_count_type'];
			$url .= '&filter_count_type=' . $this->request->get['filter_count_type'];
		} else {
			$filter_count_type = 'orders';
		}
		
		if (isset($this->request->get['filter_no_of_records'])) {
			$filter_no_of_records      = (int) $this->request->get['filter_no_of_records'];
			$url .= '&filter_no_of_records=' . $this->request->get['filter_no_of_records'];

			if($filter_no_of_records == 0)
				$filter_no_of_records = 8;
		} else {
			$filter_no_of_records = 8;			
		}

		if (isset($this->request->get['filter_date'])) {
			$filter_date            = $this->request->get['filter_date'];
			$url .= '&filter_date=' . $this->request->get['filter_date'];
		} else {
			$filter_date = 'none';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status            = $this->request->get['filter_status'];
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		} else {
			$filter_status = 0;
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_up'),
			'href'      => $this->url->link('report/orders_statuses', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);		

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('report/orders_statuses_detailed', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);		
		
		$this->load->model('report/sale');
	
		$this->data['orders'] = array();

		# get nice text order statuses for column names
		$this->load->model('localisation/order_status');
		$statusesLocalization = $this->model_localisation_order_status->getOrderStatuses();
		
		foreach($statusesLocalization as $statusData)
		{
			$statusText[$statusData['order_status_id']] = $statusData['name'];
		}
		
		# contains history records sorted by ID (oldest first)
		# These records contain status of the order and date
		# To know if the status has changed we need to know for each order its previous state to compare it with records
		$results = $this->model_report_sale->getOrdersStatuses();

		# Prepare quantity and totals for orders
		$orders = array();
		$ordersResults = $this->model_report_sale->getOrdersListAndTotal();
		foreach ($ordersResults as $result) {
			$orders[$result['order_id']]['name'] = $result['firstname'] . ' ' . $result['lastname'] . ' / ' . $result['shipping_country'];
			$orders[$result['order_id']]['quantity'] = $result['quantity'];
			$orders[$result['order_id']]['total'] = $result['total'];
		}
	
		# key=order_id, data=latest known status of the order
		$orderStatus=array();

		$this->data['orders_before']   = 0;
		$this->data['quantity_before'] = 0;
		$this->data['total_before']    = 0;

		# loop through order history list and collect the report data
		foreach ($results as $result) {
			
			$newStatus = $result['order_status_id'];
			$orderId   = $result['order_id'];
			$ohId      = $result['order_history_id'];
			$comment   = $result['comment'];
			$ohDate    = $result['day_added'];

			if(array_key_exists($orderId, $orderStatus) &&
			   $orderStatus[$orderId] == $newStatus)
			{
				# this is a history record without status change. Skip it.
				continue;
			}

			if($filter_date_aggregation == 'week')        $newDate   = $result['week_added'];
			else if($filter_date_aggregation == 'month')  $newDate   = $result['month_added'];
			else                                          $newDate   = $result['day_added'];

			# Data in the format shown in the report
			$newDateReadable   = $newDate . "-" . date('M', strtotime($result['day_added']));
			
			# we need to create a list of data containing:
			#  orders: orderId,  oh list
			#  oh list contains: ohId, in/out/remains date/status, comment
			# Handling policy:
			#  - add if the order enters requested state
			#      - if date < filter_date: the order remains since date
			#      - if date == filter_date: the order was added on filter_date
			#      - if date > filter_date; break handling; the data is too fresh
			#  - remove if the order leaves requested state and the order is in the list
			
			# break handling; the data is too fresh. It can override our statistics, so stop immediately
			if($newDateReadable > $filter_date)
				break;
			
			if(!array_key_exists($orderId, $orderStatus))
			{
				# this is a new order, so no need to exclude it from some old status
				$orderStatus[$orderId] = $newStatus;

				if($newStatus == $filter_status)
				{
					if($newDateReadable == $filter_date)
						$this->data['oh'][$orderId][$ohId]['state_change'] = "in";
					else
						$this->data['oh'][$orderId][$ohId]['state_change'] = "remains";
					
					$this->data['oh'][$orderId][$ohId]['date'] = $ohDate;
					$this->data['oh'][$orderId][$ohId]['comment'] = $comment;
					$this->data['oh'][$orderId][$ohId]['old_status'] = "-";
					$this->data['oh'][$orderId][$ohId]['new_status'] = $statusText[$newStatus];
				}

			}
			else
			{
				if($orderStatus[$orderId] != $newStatus)
				{
					$oldStatus = $orderStatus[$orderId];

					if($newStatus != $filter_status)
					{
						if(array_key_exists($orderId, $this->data['oh']))
						{
							# we already have this order in the list, so it's there with tag "in" or "remains"
							# if it was entered the state and also left the filter state before the filter_date, 
							# we can safely remove it from the list
							if($newDateReadable != $filter_date)
								unset($this->data['oh'][$orderId]);
							else
							{
								# if it was added to the list before the filter_date,
								# we need to make the counting of before, otherwise 
								# we will reset data to "out" now and will loose the information
								if($oh_data['state_change'] == "remains")
								{
									foreach($this->data['oh'][$orderId] as $tmpOhId => $oh_data)
									{
										unset($this->data['oh'][$orderId][$tmpOhId]);
										$this->data['orders_before']   += 1;
										$this->data['quantity_before'] += $orders[$orderId]['quantity'];
										$this->data['total_before']    += $orders[$orderId]['total'];
										
										break;
									}
								}
										
								# should always be true
								if($oldStatus == $filter_status)
								{
									$this->data['oh'][$orderId][$ohId]['state_change'] = "out";
									$this->data['oh'][$orderId][$ohId]['date'] = $ohDate;
									$this->data['oh'][$orderId][$ohId]['comment'] = $comment;
									$this->data['oh'][$orderId][$ohId]['old_status'] = $statusText[$oldStatus];
									$this->data['oh'][$orderId][$ohId]['new_status'] = $statusText[$newStatus];
								}
							}
						}
					}
					else
					{
						$this->data['oh'][$orderId][$ohId]['date'] = $ohDate;
						$this->data['oh'][$orderId][$ohId]['comment'] = $comment;
						$this->data['oh'][$orderId][$ohId]['old_status'] = $statusText[$oldStatus];
						$this->data['oh'][$orderId][$ohId]['new_status'] = $statusText[$newStatus];

						if($newDateReadable == $filter_date)
							$this->data['oh'][$orderId][$ohId]['state_change'] = "in";
						else
							$this->data['oh'][$orderId][$ohId]['state_change'] = "remains";
					}

					$orderStatus[$orderId] = $newStatus;
				}
			}
		}
		
		$orders_in = 0;
		$orders_out = 0;
		$orders_remains = 0;
		$quantity_in = 0;
		$quantity_out = 0;
		$quantity_remains = 0;
		$total_in = 0;
		$total_out = 0;
		$total_remains = 0;

		# count totals by the end of date range and clean up data
		foreach ($this->data['oh'] as $order_id => $oh_list)
		{
			foreach ($oh_list as $oh_id => $oh_data)
			{
				$this->data['oh'][$order_id][$oh_id]['name'] = $orders[$order_id]['name'];
				
				if($oh_data['state_change'] == "in")
				{
					$orders_in++;
					$quantity_in += $orders[$order_id]['quantity'];
					$total_in    += $orders[$order_id]['total'];
					
					$this->data['oh'][$order_id][$oh_id]['orders']   = "+1";
					$this->data['oh'][$order_id][$oh_id]['quantity'] = "+" . $orders[$order_id]['quantity'];
					$this->data['oh'][$order_id][$oh_id]['total']    = "+" . $orders[$order_id]['total'] + 0;
					$this->data['oh'][$order_id][$oh_id]['state_change'] = $this->language->get('text_in');
				}
				else if($oh_data['state_change'] == "out")
				{
					$orders_out++;
					$quantity_out += $orders[$order_id]['quantity'];
					$total_out    += $orders[$order_id]['total'];

					$this->data['oh'][$order_id][$oh_id]['orders']   = "-1";
					$this->data['oh'][$order_id][$oh_id]['quantity'] = "-" . $orders[$order_id]['quantity'];
					$this->data['oh'][$order_id][$oh_id]['total']    = "-" . $orders[$order_id]['total'] + 0;
					$this->data['oh'][$order_id][$oh_id]['state_change'] = $this->language->get('text_out');
				}
				else
				{
					$orders_remains++;
					$quantity_remains += $orders[$order_id]['quantity'];
					$total_remains    += $orders[$order_id]['total'];

					$this->data['oh'][$order_id][$oh_id]['orders']   = "1";
					$this->data['oh'][$order_id][$oh_id]['quantity'] = $orders[$order_id]['quantity'];
					$this->data['oh'][$order_id][$oh_id]['total']    = $orders[$order_id]['total'] + 0;
					$this->data['oh'][$order_id][$oh_id]['state_change'] = $this->language->get('text_remains');
				}
			}
		}
		
		$this->data['orders_before']   += $orders_remains;
		$this->data['quantity_before'] += $quantity_remains;
		$this->data['total_before']    += $total_remains;

		$this->data['orders_after']   = $this->data['orders_before']   - $orders_out   + $orders_in;
		$this->data['quantity_after'] = $this->data['quantity_before'] - $quantity_out + $quantity_in;
		$this->data['total_after']    = $this->data['total_before']    - $total_out    + $total_in;

		if($orders_in > 0 || $orders_out > 0)
		{
			                                      $this->data['orders_after'] .= " [";
			if($orders_in > 0)                    $this->data['orders_after'] .= "+" . $orders_in;
			if($orders_in > 0 && $orders_out > 0) $this->data['orders_after'] .= "/";
			if($orders_out > 0)                   $this->data['orders_after'] .= "-" . $orders_out;
			                                      $this->data['orders_after'] .= "]";
		}

		if($quantity_in > 0 || $quantity_out > 0)
		{
			                                          $this->data['quantity_after'] .= " [";
			if($quantity_in > 0)                      $this->data['quantity_after'] .= "+" . $quantity_in;
			if($quantity_in > 0 && $quantity_out > 0) $this->data['quantity_after'] .= "/";
			if($quantity_out > 0)                     $this->data['quantity_after'] .= "-" . $quantity_out;
			                                          $this->data['quantity_after'] .= "]";
		}

		if($total_in > 0 || $total_out > 0)
		{
			                                    $this->data['total_after'] .= " [";
			if($total_in > 0)                   $this->data['total_after'] .= "+" . $total_in;
			if($total_in > 0 && $total_out > 0) $this->data['total_after'] .= "/";
			if($total_out > 0)                  $this->data['total_after'] .= "-" . $total_out;
			                                    $this->data['total_after'] .= "]";
		}

	
		# limit the number of records to show
		$this->data['data'] = array_slice($this->data['data'], -$filter_no_of_records, $filter_no_of_records, true);

		# keep the filter value
		$this->data['filter_count_type'] = $filter_count_type;
		$this->data['filter_date_aggregation'] = $filter_date_aggregation;
		$this->data['filter_no_of_records']    = $filter_no_of_records;
		
		$this->data['heading_title'] = $this->language->get('heading_title') . ": " . $filter_date . " & " . $statusText[$filter_status];
		 
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_view'] = $this->language->get('text_view');
		$this->data['text_before'] = $this->language->get('text_before');
		$this->data['text_after'] = $this->language->get('text_after');
		
		$this->data['column_orderId'] = $this->language->get('column_orderId');
		$this->data['column_state_change'] = $this->language->get('column_state_change');
		$this->data['column_date'] = $this->language->get('column_date');
		$this->data['column_old_status'] = $this->language->get('column_old_status');
		$this->data['column_new_status'] = $this->language->get('column_new_status');
		$this->data['column_comment'] = $this->language->get('column_comment');
		$this->data['column_actions'] = $this->language->get('column_actions');
		$this->data['column_orders'] = $this->language->get('column_orders');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_total'] = $this->language->get('column_total') . ", " . $this->config->get('config_currency');
		$this->data['column_name'] = $this->language->get('column_name');
		
		$this->data['button_apply'] = $this->language->get('button_apply');
		$this->data['button_print'] = $this->language->get('button_print');

		$this->data['select_orders'] = $this->language->get('select_orders');
		$this->data['select_quantity'] = $this->language->get('select_quantity');
		$this->data['select_total'] = $this->language->get('select_total') . ", " . $this->config->get('config_currency');
		$this->data['select_day'] = $this->language->get('select_day');
		$this->data['select_week'] = $this->language->get('select_week');
		$this->data['select_month'] = $this->language->get('select_month');
		$this->data['select_no_of_records'] = $this->language->get('select_no_of_records');

		$this->data['token'] = $this->session->data['token'];

		$url = '';		
				
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
				
		$this->data['reset'] = $this->url->link('report/orders_statuses_detailed/reset', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->template = 'report/orders_statuses_detailed.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function reset() {
		$this->load->language('report/orders_statuses_detailed');
		
		$this->load->model('report/product');
		
		$this->model_report_product->reset();
		
		$this->session->data['success'] = $this->language->get('text_success');
		
		$this->redirect($this->url->link('report/orders_statuses_detailed', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>