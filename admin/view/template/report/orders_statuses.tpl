<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<style>  
a.hovertest:link
{
text-decoration:none;
}
a.hovertest:hover
{
text-decoration:underline;
}

</style>

  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons">
	  
	  <select name="filter_count_type">

                <?php if ($filter_count_type == 'orders') { ?>
					<option value="orders" selected="selected"><?php echo $select_orders; ?></option>
				<?php } else { ?>
					<option value="orders" ><?php echo $select_orders; ?></option>
				<?php } ?>
				<?php if ($filter_count_type == 'quantity') { ?>
					<option value="quantity" selected="selected"><?php echo $select_quantity; ?></option>
				<?php } else { ?>
					<option value="quantity" ><?php echo $select_quantity; ?></option>
				<?php } ?>
				<?php if ($filter_count_type == 'total') { ?>
					<option value="total" selected="selected"><?php echo $select_total; ?></option>
				<?php } else { ?>
					<option value="total" ><?php echo $select_total; ?></option>
				<?php } ?>

      </select>

	  <select name="filter_date_aggregation">

                <?php if ($filter_date_aggregation == 'day') { ?>
					<option value="day" selected="selected"><?php echo $select_day; ?></option>
				<?php } else { ?>
					<option value="day" ><?php echo $select_day; ?></option>
				<?php } ?>
				<?php if ($filter_date_aggregation == 'week') { ?>
					<option value="week" selected="selected"><?php echo $select_week; ?></option>
				<?php } else { ?>
					<option value="week" ><?php echo $select_week; ?></option>
				<?php } ?>
				<?php if ($filter_date_aggregation == 'month') { ?>
					<option value="month" selected="selected"><?php echo $select_month; ?></option>
				<?php } else { ?>
					<option value="month" ><?php echo $select_month; ?></option>
				<?php } ?>

      </select>

	  &nbsp<?php echo $select_no_of_records; ?>&nbsp<input size="5" type="text" name="filter_no_of_records" value="<?php echo $filter_no_of_records; ?>" />
	  
	  <a onclick="filter();" class="button"><?php echo $button_apply; ?></a>
	  <a onclick="window.print()" class="button"><?php echo $button_print; ?></a>
	  </div>
	    
    </div>
	
	<div id="placeholder" style="width:1200px;height:400px"></div>

    <div class="content">
      <table class="list">
        <thead>
          <?php if ($columns) { ?>
          <tr>
		  <td class="left"><?php echo $column_date; ?></td>
          <?php foreach ($columns as $name) { ?>
            <td class="left"><?php echo $name; ?></td>
          <?php } ?>
		  <td class="left"><?php echo $column_quantity; ?></td>
          </tr>
          <?php } ?>
        </thead>
        <tbody>
          <?php if ($data) { ?>
		  <?php $detailedReportUrl="index.php?route=report/orders_statuses_detailed&token=" . $token . "&filter_date_aggregation=" .  $filter_date_aggregation . "&filter_no_of_records=" .  $filter_no_of_records; ?>
          <?php foreach ($data as $date => $statuses) { ?>
          <tr>
		  <td class="left" width=100><?php echo $date; ?></td>
          <?php foreach ($statuses as $status => $noOfStatuses) { ?>
            <td class="left"><a href="<?php echo $detailedReportUrl . "&filter_date=" . $date . "&filter_status=" . $status; ?>"  class="hovertest"><?php echo $noOfStatuses; ?></a></td>
          <?php } ?>
		  <td class="left"><?php echo $total[$date]; ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
		  </tbody>

         <thead>
         <?php if ($columns) { ?>
          <tr>
		  <td class="left"><?php echo $column_date; ?></td>
          <?php foreach ($columns as $name) { ?>
            <td class="left"><?php echo $name; ?></td>
          <?php } ?>
		  <td class="left"><?php echo $column_quantity; ?></td>
          </tr>
          <?php } ?>
         </thead>

      </table>
    </div>
	<div><?php echo $text_description; ?></div>
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

<!--[if IE]>

<script type="text/javascript" src="view/javascript/jquery/flot/excanvas.js"></script>

<![endif]--> 

<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script> 

<script type="text/javascript"><!--
var graph = [];
var axis = [];
var tooltip = [];

$(function () {
	var xaxisIx = 0;
	var graphIx = 0;
    <?php foreach ($graph_input_xaxis as $value) { ?>
		axis.push([xaxisIx++, "<?php echo $value ?>"]);
	<?php } ?>
	
    <?php foreach ($graph_input as $status => $data) { ?>
	    graphIx = 0;
		graph[<?php echo $status ?>] = [];
		<?php foreach ($data as $abs) { ?>
		graph[<?php echo $status ?>].push([graphIx++, <?php echo $abs ?>]);
		<?php } ?>
	<?php } ?>

    $.plot($("#placeholder"), [
    <?php foreach ($graph_input as $status => $data) { ?>
        { label: "<?php echo $columns[$status] ?>",  data: graph[<?php echo $status ?>]},
	<?php } ?>
    ], {
        series: {
            lines: { show: true },
            points: { show: true }
        },
        xaxis: {
            show: true,
			ticks: axis
        },
        yaxis: {
            show: true
        },
        grid: {
			hoverable: true,
			backgroundColor: { colors: ["#fff", "#eee"] }
        },
		legend: {
            position: "nw"
		}
    });
});

   function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $("#placeholder").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(0));
        $("#y").text(pos.y.toFixed(0));


            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(0),
                        y = item.datapoint[1].toFixed(0);
                    
                    showTooltip(item.pageX, item.pageY,
                                item.series.label + " :: " + axis[item.dataIndex][1] + " :: " + y);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;            
            }

    });


//--></script> 


<?php echo $footer; ?>