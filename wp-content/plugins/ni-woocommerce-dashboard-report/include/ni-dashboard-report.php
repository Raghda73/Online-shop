<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_dashboard_report' ) ) {
	class ni_dashboard_report{
		 public function __construct(){
			 add_action('admin_init', array( &$this, 'admin_init'));
		 }
		 function admin_init(){
			add_action( 'wp_dashboard_setup', array( &$this, 'example_add_dashboard_widgets' ));
		 }
		 function example_add_dashboard_widgets(){
			
			wp_add_dashboard_widget(
					 'ni_woocommerce_sales_by_month',         // Widget slug.
					 'Ni WooCommerce Sales By Month Report',         // Title.
					  array( &$this, 'ni_woocommerce_sales_by_month')// Display function.
			);	
			
			wp_add_dashboard_widget(
					 'ni_woocommerce_sales_status',         // Widget slug.
					 'Ni WooCommerce Sales Order Status',         // Title.
					  array( &$this, 'ni_woocommerce_sales_status_widget')// Display function.
			);	
			
			wp_add_dashboard_widget(
					 'ni_woocommerce_recent_order',         // Widget slug.
					 'Ni WooCommerce Recent Order',         // Title.
					  array( &$this, 'ni_woocommerce_recent_order_widget')// Display function.
			);	
			
			wp_add_dashboard_widget(
					 'ni_woocommerce_sales_analysis',         // Widget slug.
					 'Ni WooCommerce Sales Analysis',         // Title.
					  array( &$this, 'ni_woocommerce_sales_analysis_widget')// Display function.
			);	
			
			wp_add_dashboard_widget(
					 'ni_woocommerce_sales_report_pro',         // Widget slug.
					 'Ni WooCommerce Sales Reports Pro',         // Title.
					  array( &$this, 'ni_woocommerce_sales_report_pro_widget')// Display function.
			);	
			
			
		 }
		 function ni_woocommerce_sales_by_month(){
			global $wpdb;
			$order_status =  $this->get_woo_order_status();
			$all_month  = $this->get_months_list();
			 $end_date =date_i18n("Y-m-d");
			
			 $start_date =  date_i18n("Y-m-d", strtotime("-6 months", strtotime($end_date)));
			
			
			$query = "";
			$query = " SELECT ";
			$query .= " SUM(order_total.meta_value) as order_total";
			$query .= ",  date_format( posts.post_date, '%Y-%m')   as month";
			$query .= "  FROM  {$wpdb->prefix}posts as posts ";
				$query  .= " LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID ";
			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type = 'shop_order'";
			$query .= " AND order_total.meta_key = '_order_total'";
			
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
			
			  $query .= " AND posts.post_status IN ('{$order_status}')	";
			
			
			$query .= " GROUP BY YEAR(posts.post_date), MONTH(posts.post_date) ";
			$row = $wpdb->get_results($query);
			$_net_amount = array();
			foreach($row as $key=>$value){
				$_net_amount[$value->month] = $value->order_total;
			}
			
			//$this->print_array(	$_net_amount);
			
			
			$query = "";
			$query = " SELECT ";
			$query .= " SUM(order_itemmeta.meta_value) as order_total";
			$query .= ", date_format( posts.post_date, '%Y-%m')   as month";
			$query .= "  FROM  {$wpdb->prefix}posts as posts ";
			
			$query  .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
			
			$query  .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta ON order_itemmeta.order_item_id=order_items.order_item_id ";
			
			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type = 'shop_order'";
			$query .= " AND order_itemmeta.meta_key = '_line_total'";
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
			 $query .= " AND posts.post_status IN ('{$order_status}')	";
			$query .= " GROUP BY YEAR(posts.post_date), MONTH(posts.post_date) ";
			$row = $wpdb->get_results($query);
			$_gross_amount = array();
			foreach($row as $key=>$value){
				$_gross_amount[$value->month] = $value->order_total;
			}
			
			foreach($all_month as $key=>$value){
				$gross_amount[$key]["Gross"] = isset($_gross_amount[$key])?$_gross_amount[$key]:0;
				$gross_amount[$key]["Net"] = isset($_net_amount[$key])?$_net_amount[$key]:0;
				$gross_amount[$key]["Month"] = $value;
			}
			//$this->print_array(	$gross_amount);
		$gross_amount = 	array_reverse ($gross_amount);
			?>
            <table style="width:100%">
            	<tr>
                	<th>Month Name</th>
                    <th style="text-align:right">Total Gross Sales</th>
                    <th style="text-align:right">Total Net Sales </th>
                </tr>	
			<?php
			foreach($gross_amount as $key=>$value){
			?>
            <tr>
            	<td style="font-weight:bold"><?php echo $value["Month"]; ?></td>
                <td style="text-align:right"><?php echo wc_price($value["Gross"]); ?></td>
                <td style="text-align:right"><?php echo wc_price($value["Net"]); ?></td>
            </tr>
            <?php		
			}
			?>
           	</table>
            <?php
			
		 }
		 function ni_woocommerce_sales_report_pro_widget(){
		 ?>
			<table style="width:100%;" cellpadding="4">
				<tr>
					<td colspan="2" style="font-weight:bold; color:#2cc185">Buy Ni WooCommerce Sales Report Pro @ $24.00</td>
				</tr>
				<tr>
					<td>
						<ul>
							<li>Dashboard order Summary</li>
							<li>Order List - Display order list</li>
							<li>Order Detail - Display Product information</li>
							<li>Customer Sales Report</li>
						</ul>
					</td>
					<td>
						<ul>
							<li>Payment Gateway Sales Report</li>
							<li>Country Sales Report</li>
							<li>Coupon Sales Report</li>
							<li>Order Status Sales Report</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>
						<ul>
							<li><span style="color:#26A69A">Email at: <a href="mailto:support@naziinfotech.com">support@naziinfotech.com</a></span></li>						 <li>Coupon Code: <span style="color:#26A69A">ni10</span> Get 10% OFF</li>
						</ul>
						</td>
					<td>
						<ul>
							 <li><a href="http://demo.naziinfotech.com/wp-admin/" target="_blank">View Demo</a>  </li>
							<li><a href="http://naziinfotech.com/?product=ni-woocommerce-sales-report-pro" target="_blank">Buy Now</a>  </li>
						</ul>
					</td>
				</tr>
			</table>
			
		 <?php
		 }
		 function ni_woocommerce_sales_analysis_widget(){
			 $order_data = $this->get_sales_analysis();
			 //$this->print_array($order_data);	
			 ?>
			 <table style="width:100%;" cellpadding="4">
				<tr>
					<th style="text-align:left;" >Order Interval</th>
					<th style="text-align:right">Order Count</th>
					<th style="text-align:right">Order Total</th>
				</tr>
				<?php foreach($order_data as $key=>$value) { ?>
				<tr>
					<td style="font-weight:bold" ><?php echo $value->order_day;  ?></td>
					<td style="text-align:right"><?php echo $value->order_count;  ?></td>
					<td style="text-align:right"><?php echo wc_price( $value->total_sales);  ?></td>
				</tr>
				<?php } ?>
			 </table>
			 <?php
		 }
		 function ni_woocommerce_sales_status_widget() {
	
			// Display whatever it is you want to show.
			//echo "Hello World, I'm a great Dashboard Widget Anzar Ahmed";
			$this->get_order_status();
			
		}
		function ni_woocommerce_recent_order_widget(){
			$order_data = $this->get_recent_orders();
			//$this->print_array($order_data);	
			?>
			<table style="width:100%;" cellpadding="4">
			<tr>
				<th style="text-align:left">ID</th>
				
				<th style="text-align:left" >Date </th>
				
				<th style="text-align:left" >First Name</th>
				<th style="text-align:left" >Billing Email</th>
				
				<th style="text-align:left">Status</th>
				<th style="text-align:left" >Order Total</th>
			   
			</tr>
			<?php foreach ($order_data as $k=>$v): ?>	
			<tr> 
				 <td><?php echo $v->order_id; ?></td>
				
				 <td><?php echo $v->order_date; ?></td>
				
				 <td><?php echo $v->billing_first_name; ?></td>
				 <td><?php echo $v->billing_email; ?></td>
				
				<td><?php echo ucfirst (str_replace("wc-","", $v->order_status)); ?></td>
				<td style="text-align:right"><?php echo wc_price($v->order_total); ?></td>
			
			 </tr>
			 <?php endforeach; ?>
		</table>
			<?php
		}
		function get_recent_orders(){
			global $wpdb;
			
			$query = " SELECT
					posts.ID as order_id
					,posts.post_status as order_status
					, date_format( posts.post_date, '%Y-%m-%d') as order_date 
					
					FROM {$wpdb->prefix}posts as posts	";
					
					$query .= " WHERE 1=1  ";
					$query .= " AND  posts.post_type = 'shop_order'";
					
					$query .= "order by posts.post_date DESC";	
					$query .= " LIMIT 5";	
					$order_data = $wpdb->get_results( $query);	
					//$this->print_array($order_data);				
					if(count($order_data)> 0){
						foreach($order_data as $k => $v){
							
							/*Order Data*/
							$order_id =$v->order_id;
							$order_detail = $this->get_order_post_meta($order_id);
							foreach($order_detail as $dkey => $dvalue)
							{
									$order_data[$k]->$dkey =$dvalue;
								
							}
						}
						
						return $order_data;
					}
					else
					{
						echo "No Record Found";
					}
									
					
					
		}
		function get_woo_order_status(){
		//$order_status = array('wc-pending','wc-processing','wc-on-hold', 'wc-completed','wc-refunded');
		$order_status =  implode("','", array('wc-processing','wc-on-hold', 'wc-completed','wc-refunded'));
		
		return $order_status;
		}
		function get_sales_analysis(){
			$query = "";
			$order_status =  $this->get_woo_order_status();
			
			$today = date_i18n("Y-m-d");
			global $wpdb;	
				$query = "SELECT
						SUM(order_total.meta_value)as 'total_sales'
						,count(*) as order_count
						,'Today' as 'order_day'
						,'#AD1457' as 'color'
						FROM {$wpdb->prefix}posts as posts			
						LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
						
						WHERE 1=1
						AND posts.post_type ='shop_order' 
						AND order_total.meta_key='_order_total' ";
				
				 $query .= " AND   date_format( posts.post_date, '%Y-%m-%d') = date_format('{$today}', '%Y-%m-%d')"; 
				  $query .= " AND posts.post_status IN ('{$order_status}')	";
				
				$query .= " UNION ALL ";
				
				/*Yesterday*/
				$query .= "SELECT
						SUM(order_total.meta_value)as 'total_sales'
						,count(*) as order_count
						,'Yesterday' as 'order_day'
						,'#6A1B9A' as 'color'
						FROM {$wpdb->prefix}posts as posts			
						LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
						
						WHERE 1=1
						AND posts.post_type ='shop_order' 
						AND order_total.meta_key='_order_total' ";
				
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 WEEK) "; 
				
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') =  SUBDATE(date_format('{$today}' , '%Y-%m-%d'),1) "; 
				
				$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') = DATE_SUB(date_format('{$today}' , '%Y-%m-%d'), INTERVAL 1 DAY) "; 
				
				    $query .= " AND posts.post_status IN ('{$order_status}')	";
				
				$query .= " UNION ALL ";
				
				/*Week*/
				$query .= "SELECT
						SUM(order_total.meta_value)as 'total_sales'
						,count(*) as order_count
						,'This Week' as 'order_day'
						,'#6A1B9A' as 'color'
						FROM {$wpdb->prefix}posts as posts			
						LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
						
						WHERE 1=1
						AND posts.post_type ='shop_order' 
						AND order_total.meta_key='_order_total' ";
				
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 WEEK) "; 
				
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
		  WEEK(date_format( posts.post_date, '%Y-%m-%d')) = WEEK(CURRENT_DATE()) ";
				  $query .= " AND posts.post_status IN ('{$order_status}')	";
				$query .= " UNION ALL ";
				/*Month*/
				$query .= "SELECT
						SUM(order_total.meta_value)as 'total_sales'
						,count(*) as order_count
						,'This Month' as 'order_day'
						,'#1565C0' as 'color'
						FROM {$wpdb->prefix}posts as posts			
						LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
						
						WHERE 1=1
						AND posts.post_type ='shop_order' 
						AND order_total.meta_key='_order_total' ";
				
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 MONTH) "; 
				
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
		  MONTH(date_format( posts.post_date, '%Y-%m-%d')) = MONTH(CURRENT_DATE()) ";
				  $query .= " AND posts.post_status IN ('{$order_status}')	";
				$query .= " UNION ALL ";
				/*Year*/
				$query .= "SELECT
						SUM(order_total.meta_value)as 'total_sales'
						,count(*) as order_count
						,'This Year' as 'order_day'
						,'#FF5722' as 'color'
						FROM {$wpdb->prefix}posts as posts			
						LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
						
						WHERE 1=1
						AND posts.post_type ='shop_order' 
						AND order_total.meta_key='_order_total' ";
				
				$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
				
				  $query .= " AND posts.post_status IN ('{$order_status}')	";
			
			$order_data = $wpdb->get_results( $query);	
			
			return $order_data;
		}
		function get_order_status(){
			global $wpdb;
			$query = "SELECT ";
			$query .= "  post.post_status as order_status";
			$query .= " , SUM(postmeta.meta_value) as order_total";
			$query .= " , COUNT(*) as order_count";
			$query .= " FROM {$wpdb->prefix}posts as post  ";
			
			$query .= "LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=post.ID ";
			
			$query .= " WHERE 1=1  ";
			//$query .= " AND  post.post_status = 'publish'";
			$query .= " AND  post.post_type = 'shop_order'";
			
			$query .= " AND  postmeta.meta_key = '_order_total'";
			
		   $query .= " GROUP BY post.post_status ";
			 $query .= " ORDER  BY order_total DESC";
			
			$results = $wpdb->get_results( $query);	
			//$this->print_array($results);	
		?>
		<style>
		
		</style>
		<table style="width:100%;"  cellpadding="4">
			<tr>
				<th style="text-align:left">Order Status</th>
				<th style="text-align:left">Count</th>
				<th style="text-align:left" >Total</th>
				<th style="text-align:left" >Action</th>
			</tr>
			<?php foreach ($results as $k=>$v): ?>	
			<tr> 
				<td><?php echo ucfirst (str_replace("wc-","", $v->order_status)); ?></td>
				<td><?php echo $v->order_count; ?></td>
				<td><?php echo wc_price($v->order_total); ?></td>
				<td><a href="<?php echo admin_url()."edit.php?post_type=shop_order&post_status=".$v->order_status ?>">view</a></td>
			 </tr>
			 <?php endforeach; ?>
		</table>
		<?php
		}
		function print_array($ar = NULL,$display = true){
				if($ar){
				$output = "<pre>";
				$output .= print_r($ar,true);
				$output .= "</pre>";
				
				if($display){
					echo $output;
				}else{
					return $output;
				}
				}
		}
		function get_order_post_meta($order_id)
		{
			$order_detail	= get_post_meta($order_id);
			$order_detail_array = array();
			foreach($order_detail as $k => $v)
			{
				$k =substr($k,1);
				$order_detail_array[$k] =$v[0];
			}
			return 	$order_detail_array;
		}
		function get_months_list($amount_column = true){
			
			$cross_tab_end_date			=  date_i18n("Y-m-d");
			$cross_tab_start_date		=  date_i18n("Y-m-d", strtotime("-6 months", strtotime($cross_tab_end_date)));
			
			$startDate = strtotime($cross_tab_start_date);
			$endDate   = strtotime($cross_tab_end_date);
			$currentDate = $startDate;
			$this->months = array();
			if($amount_column){					
			
				while ($currentDate <= $endDate) {
					
					$month = date('Y-m',$currentDate);
					$this->months[$month] = date('F',$currentDate);
					$currentDate = strtotime( date('Y/m/01/',$currentDate).' 1 month');
				}
			}else{
				while ($currentDate <= $endDate) {
					$month = date('Y-m',$currentDate);
					$this->months[$month."_total"] = date('M',$currentDate)." Amt.";
					$this->months[$month."_quantity"] = date('M',$currentDate)." Qty.";
					$currentDate = strtotime( date('Y/m/01/',$currentDate).' 1 month');
				}
			}
				
			
			//$this->print_array(	$this->months);
			return $this->months;
		}
	}
}