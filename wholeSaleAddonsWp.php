<?php 

/*
Plugin Name: Whole Sale Product 
Plugin URI: 
Description: A custom plugin for Major Motion Printing
Version: 0.1
Author: Guesmi Ahmed
Author Email: ahmedguesmi.sb@gmail.com
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



class plugin_options_install_wholesale {
	static function install() {
		global $wpdb;
		$db_name = $wpdb->prefix . 'wholesaletables';

		$charset_collate = $wpdb->get_charset_collate();

		if($wpdb->get_var("SHOW TABLES LIKE '$db_name'" ) != $db_name){
			$sql = "CREATE TABLE  " . $db_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			data longtext NOT NULL,
			product_id bigint(20) NOT NULL,
			PRIMARY KEY (id)
			) ". $charset_collate .";";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

		}

		//register a deactivation Hook 
		
	}
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__, array( 'plugin_options_install_wholesale', 'install' ) );


add_action("admin_menu","menu_adding");

function menu_adding()
{
	
	add_menu_page("Product's Tables", "Product's Tables", 4, "product-tables", "productTables", "dashicons-editor-table",56 );
	add_submenu_page("product-tables", "Add Tables", "Add Tables", 4, "add_tables", "AddTablesToProducts");
	
}

function productTables()
{
	prefix_enqueue_bootstrap();
	wp_enqueue_style( "wholesaleStyles", plugins_url("wholesaletable/styles/style.css"));
	wp_enqueue_script('wholesaleScript', plugins_url("wholesaletable/scripts/deleteScript.js"), array('jquery'));
	global $actions;
	global $wpdb;
	$table_name = $wpdb->prefix . 'wholesaletables';
	$productinWholesale = $wpdb->get_results("SELECT product_id FROM $table_name");
	$productsIdArray = array();
	for ($i=0; $i < count($productinWholesale); $i++) { 
		array_push($productsIdArray, $productinWholesale[$i]->product_id);
	}
	if (!empty($productsIdArray)) {
		$args = array(
		    'post_type' => 'product',
		    'posts_per_page' => -1,
		    'post__in' => $productsIdArray
		);
		$All_products = get_posts( $args );
	}
	

	?>
	<!-- Loading View for the plugin for Product Tables-->
	<script>console.log(JSON.parse('<?php echo json_encode($All_products) ?>'))</script>
	<div class="container">
		<div class="row">
			<h2>WholeSale Price Tables To Product Pages</h2>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#addtables">Product's tables</a></li>
				
			</ul>

			<div class="tab-content">
				<div id="deleteTable" class="tab-pane fade in active">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Product Name & description</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							
							<?php
								if (empty($All_products)) {
									echo "<tr><td><h3>You dont setup any table to products yet.</h3></td></tr>";
								}else{
									for ($j=0; $j < count($All_products) ; $j++) { 
										echo "<tr>
												<td><h3 style='margin-top:0;'>".$All_products[$j]->post_title."</h3><p class='fadeOverflow'>".$All_products[$j]->post_content."</p></td>
												<td>
													<a href='javascript:openConfirmModal(".$All_products[$j]->ID.")'>Delete</a>
												</td>
											</tr>";
									}

								}

							?>
						</tbody>
					</table>
				</div>

				
			</div>
		</div>
	</div>
	<div class="modal fade" id="deleteConfirm" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header bg-primary">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Confirm!</h4>
	        </div>
	        <div class="modal-body">
	          <p>Do you really want to delete the Table on this product? Are you Sure?</p>
	          <i>This process cannot be reversible.</i>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	          <a id="deleteConfirmBtn" class="btn btn-danger btn-ok">Delete it!</a>
	        </div>
	      </div>
	      
	    </div>
	  </div>




	<?php
}

function AddTablesToProducts(){
	prefix_enqueue_bootstrap();
	wp_enqueue_style( "wholesaleStyles", plugins_url("wholesaletable/styles/style.css"));
	wp_enqueue_script('wholesale-jscolor', plugins_url("wholesaletable/scripts/jscolor.min.js"), array('jquery'));
	wp_enqueue_script('wholesaleScript', plugins_url("wholesaletable/scripts/adminScript.js"), array('jquery'));

	$wnm_custom = array( 'pluginUrl' => plugins_url("wholesaletable/") );
	wp_localize_script( 'wholesaleScript', 'pluginUrl', $wnm_custom );

	$args = array(
	    'post_type' => 'product',
	    'posts_per_page' => -1
	);
	$products = get_posts( $args );



	//Add a check here from which it will remove the products who already have tables in out database tables. 
	global $wpdb;
	$table_name = $wpdb->prefix . 'wholesaletables';
	$tabless = $wpdb->get_results("SELECT product_id FROM $table_name");

	
	for ($j=0; $j < count($tabless); $j++) { 
		for ($i=0; $i < count($products); $i++) { 
			if ($products[$i]->ID == $tabless[$j]->product_id) {
				array_splice($products, $i, 1);
			}
		}
	}

	?>
	<!-- Loading View for the plugin in Add Tables tab -->
	<div class="container">
		<div class="row">
			<h2>WholeSale Price Tables To Product Pages</h2>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#addtables">Add Tables To Products</a></li>
				<!-- <li><a data-toggle="tab" href="#others">Others</a></li> -->
				
			</ul>

			<div class="tab-content">
				<div id="addtables" class="tab-pane fade in active">
					<h3>Add Tables To Product</h3>
					<div class="col-sm-2">
						<ul class="nav nav-pills nav-stacked">
							<li id="wholeSaleStep1" class="active"><a data-toggle="pill" href="#selectProduct">1: Select Product</a></li>
							<li id="wholeSaleStep2" class="customDisabled disabled"><a data-toggle="pill" href="#addValues">2: Add Values</a></li>
						</ul>
					</div>

					<div class="tab-content col-sm-10 borderLeft">
						<div id="selectProduct" class="tab-pane fade in active">
							<p>Please click on a Product in which you want to add tables. and we ll guide you to next step.</p>
							<div class="list-group">
								<?php 
									if (empty($products)) {
										echo "<p class='list-group-item-text fadeOverflow'>
													<h4>All Products have tables or you dont have any product yet.</h4>
												</p>";
									}else{
										for ($i=0; $i < count($products) ; $i++) { 
											echo '<a href="javascript:processHandlingForWholesale('.$products[$i]->ID.', \''.$products[$i]->post_title.'\')" class="list-group-item">
													<h4 class="list-group-item-heading">
														'.$products[$i]->post_title.'
													</h4>
													<p class="list-group-item-text fadeOverflow">
														'.$products[$i]->post_content.'
													</p>
												</a>
											';
										}
									}
									
									
								?>
								
							</div>
						</div>
						<div id="addValues" class="tab-pane fade">
							<div class="row">
								<h3 class="col-sm-4">Add Values</h3>
								<h4 class="col-sm-8 marginTop20AndrightAligh">Selected Product: <i id="wholeSale_selectedProName"></i></h4>
							</div>

							<ul class="nav nav-tabs">
								<li ><a data-toggle="tab" href="#TeeCost">Fabric Shades and Tee cost</a></li>
								<li ><a data-toggle="tab" href="#plastisol">Plastisol</a></li>
								<li class="active"><a data-toggle="tab" href="#discharge">Discharge</a></li>
								<li><a data-toggle="tab" href="#waterbase">Waterbase</a></li>
								<li><a data-toggle="tab" href="#proColors">Product Colors</a></li>
							</ul>
							<form name="dataFormWholeSale" id="dataFormWholeSale" onsubmit="saveWholeSaleTableData(event)" >
								<div class="tab-content">

								<div id="TeeCost" class="tab-pane fade in active">
										<h3>Actual Cost:</h3>
										<div class="row ">
											<div class="col-sm-12">
												<div class="form-group">
													<label>White</label>
													<input required type="number" class="form-control" name="fab-white" step="0.01">
													<p>This is the Tee cost. 10% and service charges will be added to it. </p>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>Natural</label>
													<input required type="number" class="form-control" name="fab-natural" step="0.01">
													<p>This is the Tee cost. 10% and service charges will be added to it. </p>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>Heathers</label>
													<input required type="number" class="form-control" name="fab-heathers" step="0.01">
													<p>This is the Tee cost. 10% and service charges will be added to it. </p>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>Colors</label>
													<input required type="number" class="form-control" name="fab-colors" step="0.01">
													<p>This is the Tee cost. 10% and service charges will be added to it. </p>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>Black</label>
													<input required type="number" class="form-control" name="fab-black" step="0.01">
													<p>This is the Tee cost. 10% and service charges will be added to it. </p>
												</div>
											</div>
										</div>

										

										<!-- <div class="row">
											<button class="btn btn-default pull-right addColorBtn" type="button">+ Add more color</button>
										</div> -->
									</div>

									<div id="plastisol" class="tab-pane fade in active">

										<div class="row">
											<div class="col-sm-12">
												<table class="table table-hover table-striped">
													<thead>
														<tr>
															<th>Quantities</th>
															<th>Service Cost</th>
															<th>Cost per color after one Color</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>20-29</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-20" required type="number" step="0.01" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" required name="p-20-c" type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>30-49</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-30" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-30-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>50-99</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-50" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-50-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>100-149</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-100" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-100-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>150-499</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-150" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-150-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>500-999</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-500" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-500-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>1000+</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-1000" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="p-1000-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														
													</tbody>
												</table>
											</div>	
										</div>

									</div>

									<div id="discharge" class="tab-pane fade in active">

										<div class="row">
											<div class="col-sm-12">
												<table class="table table-hover table-striped">
													<thead>
														<tr>
															<th>Quantities</th>
															<th>Service Cost</th>
															<th>Cost per color after one Color</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>20-29</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-20" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-20-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>30-49</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-30" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-30-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>50-99</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-50" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-50-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>100-149</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-100" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-100-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>150-499</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-150" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-150-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>500-999</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-500" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-500-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>1000+</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-1000" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="d-1000-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														
													</tbody>
												</table>
											</div>	
										</div>

									</div>

									<div id="waterbase" class="tab-pane fade in active">

										<div class="row">
											<div class="col-sm-12">
												<table class="table table-hover table-striped">
													<thead>
														<tr>
															<th>Quantities</th>
															<th>Service Cost</th>
															<th>Cost per color after one Color</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>20-29</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-20" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-20-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>30-49</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-30" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-30-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>50-99</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-50" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-50-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>100-149</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-100" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-100-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>150-499</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-150" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-150-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>500-999</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-500" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-500-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>1000+</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-1000" required type="number" step="0.01">
																	</div>
																</div>
															</td>
															<td>
																<div class="form-group">
																	<div class="col-sm-5">
																		<input class="form-control" id="ex1" name="w-1000-c" required type="number" step="0.01">
																	</div>
																</div>
															</td>
														</tr>
														
													</tbody>
												</table>
											</div>	
										</div>

									</div>

									<div id="proColors" class="tab-pane fade in active">

										<div class="row colorEditionWrapper">
											<div class="col-sm-6">
												<div class="form-group">
													<label>Name: </label>
													<input required class="form-control" type="text" value="white" name="proColor-1">
													<p>If you change the color, Change the color name also.</p>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Color :</label>
													<input required class="jscolor {uppercase:false,hash:true} form-control"  value="ffffff" name="proColor-1-hex">
													<p class="opacity0">0</p>
												</div>
											</div>
										</div>

										

										<div class="row">
											<button class="btn btn-default pull-right addColorBtn" type="button">+ Add more color</button>
										</div>
									</div>

									<div class="makeitcentered">
										<div class="spinnerrr"></div>
									</div>
									
									<button type="submit" class="btn btn-success btn-lg">Submit</button>
									<p>If submit not work Please Check all the tabs and the input fields. Submit button only works if you fill all the data and colors input</p>
								</div>
								<!-- to save product ID -->
								<input type="hidden" name="productId">
							</form>


						</div>
						
					</div>
					<div class="modal fade" id="responseModal" role="dialog">
					    <div class="modal-dialog">
					    
					      <!-- Modal content-->
					      <div class="modal-content">
					        <div class="modal-header bg-primary">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Response:</h4>
					        </div>
					        <div class="modal-body">
					          <p>Data is saved. you can see the product page for table. </p>
					        </div>
					        <div class="modal-footer">
					          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					        </div>
					      </div>
					      
					    </div>
					  </div>



					

					
				</div>
				<div id="others" class="tab-pane fade">
					<h3>Others</h3>
					<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					
				</div>
				
			</div>
		</div>
	</div>


	<?php
	 
	// echo "<script>console.log(JSON.parse('".json_encode($tabless)."'))</script>";
	// echo "<script>console.log(JSON.parse('".json_encode($products)."'))</script>";
}






function prefix_enqueue_bootstrap() {       
    // JS
    wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');

    // CSS
    wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
}

// handling AJAX
add_action( 'wp_ajax_wholesaleDataInsert', 'wholesaleDataInsert' );
add_action('wp_ajax_nopriv_wholesaleDataInsert', 'wholesaleDataInsert');
add_action( 'wp_ajax_DeleteWholeSaleTable', 'DeleteWholeSaleTable' );
add_action('wp_ajax_nopriv_DeleteWholeSaleTable', 'DeleteWholeSaleTable');
function wholesaleDataInsert() {
	$reponse = array();

    if(!empty($_POST['data'])){
    	global $wpdb;
    	$table_name = $wpdb->prefix . 'wholesaletables';
		$product_id = $_POST['data']["productId"];
		$wpdb->insert( 
			$table_name, 
			array( 
				'data' => json_encode($_POST["data"]), 
				'product_id' => $product_id
			) 
		);

        $response['response'] = "data is saved";
        $response['productID'] = $product_id;
    } else {
        $response['response'] = "Data is missing.";
    }


    header( "Content-Type: application/json" );
    echo json_encode($response);

    //Don't forget to always exit in the ajax function.
    exit();
}
function DeleteWholeSaleTable() {
	$reponse = array();

    if(!empty($_POST['productId'])){
    	global $wpdb;
    	$table_name = $wpdb->prefix . 'wholesaletables';
		$product_id = $_POST["productId"];
		$wpdb->query( 
			 $wpdb->prepare( 
			    "DELETE FROM $table_name
			     WHERE product_id = %d",
			        $product_id
			    )
			);
		

        $response['response'] = "data is Deleted";
        $response['productID'] = $product_id;
    } else {
        $response['response'] = "Data is missing.";
    }


    header( "Content-Type: application/json" );
    echo json_encode($response);

    //Don't forget to always exit in the ajax function.
    exit();
}

// this hook is for adding tables to product page. 
add_action( 'woocommerce_after_single_product_summary','addingShortcodetowocommerce',5);
function addingShortcodetowocommerce(){
	$numm = sprintf("[wholeSale_showingTables pageId=%u]", get_the_ID());
	do_shortcode($numm);
}


// Showing Tables on products. 
add_shortcode( "wholeSale_showingTables", "tablesonProductPage",10,1);
function tablesonProductPage($attr)
{
	$productOnPage = $attr['pageid'];
	if ($productOnPage != 0) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wholesaletables';
		$data = $wpdb->get_results("SELECT data FROM $table_name where product_id=$productOnPage");
		$sendData = $data[0]->data;
		// wp_enqueue_script('jquery-latest', plugins_url("wholesaletable/scripts/jquery-latest.min.js"));
		wp_enqueue_script('wholesaleScript', plugins_url("wholesaletable/scripts/script.js"), array('jquery'));
		wp_localize_script( 'wholesaleScript', 'wholeSaleTableShowData', $sendData );

		if (!empty($sendData)) {

		?>

			<!-- here all the html -->
			<div style="clear: both;"></div>
			<!-- show colors here -->
			<strong>COLORS:</strong>
			<div id="ColorDisplayWrapperWholesale" class="fullwidth marginBottom10">
				<!-- <div class="width50 floatleft">
					<div class="fullwidth">
						<div class="width20 floatleft">
							<div class="colorBox" style="background: black"></div>
						</div>
						<div class="width40 floatleft">
							Black
						</div>
						<div style="clear: both;"></div>
					</div>
				</div> -->
			</div>

			<div style="clear: both;"></div>

			<div class="tab wholesaleTab marginTop5" align="center">
				<button class="tablinks active" onclick="openWholeSaleTypes(event, 'plastisol')">PLASTISOL</button>
				<button class="tablinks" onclick="openWholeSaleTypes(event, 'discharge')">DISCHARGE</button>
				<button class="tablinks" onclick="openWholeSaleTypes(event, 'waterbase')">WATERBASE</button>
			</div>
			<div class="fullwidth marginTop5">
				<div class="width60 floatleft">
					<p style="margin: 12px 0">Select Quantity to show price table of <span class="typeofproductwholesale">PLASTISOL</span></p>
				</div>
				<div align="right" class="width40 floatright ">
					<select name="wholesaleSelector" style="min-width: 150px;">
						<option selected value="20">20-39 Pieces</option>
						<option value="30">30-49 Pieces</option>
						<option value="50">50-99 Pieces</option>
						<option value="100">100-149 Pieces</option>
						<option value="150">150-499 Pieces</option>
						<option value="500">500-999 Pieces</option>
						<option value="1000">1000+ Pieces</option>
					</select>

				</div>
			</div>

			<div id="plastisol" class="tabcontent">
				<div class="fullwidth marginTop5">
					
					<div class="fullwidth" style="overflow-x:auto;">
						<table id="PlastisolTableData">
							<thead>
								<tr>
									<th>Fabric Shade</th>
									<th>1</th>
									<th>2</th>
									<th>3</th>
									<th>4</th>
									<th>5</th>
									<th>6</th>
									<th>7</th>
									<th>8</th>
									<th>9</th>
									<th>10</th>
								</tr>
							</thead>
							<tbody>
								<tr class="wholesaleplastisolDataWHITE">
									<td width="130">
										White
									</td>
									
								</tr>
								<tr class="wholesaleplastisolDataNATURAL">
									<td>
										Natural
									</td>
								</tr>
								<tr class="wholesaleplastisolDataHEATHERS">
									<td>
										Heathers
									</td>
								</tr>
								<tr class="wholesaleplastisolDataCOLORS">
									<td>
										Colors
									</td>
								</tr>
								<tr class="wholesaleplastisolDataBLACK">
									<td>
										Black
									</td>
								</tr>
							</tbody>
							<tfoot></tfoot>
						</table>
					</div>
				</div>
			</div>

			<div id="discharge" class="tabcontent" style="display: none">
				<div class="fullwidth marginTop5">
					<div class="fullwidth" style="overflow-x:auto;">
						<table id="DischargeTableData">
							<thead>
								<tr>
									<th>Fabric Shade</th>
									<th>1</th>
									<th>2</th>
									<th>3</th>
									<th>4</th>
									<th>5</th>
									<th>6</th>
									<th>7</th>
									<th>8</th>
									<th>9</th>
									<th>10</th>
								</tr>
							</thead>
							<tbody>
								<tr class="wholesaledischargeDataWHITE">
									<td width="130">
										White
									</td>
									
								</tr>
								<tr class="wholesaledischargeDataNATURAL">
									<td>
										Natural
									</td>
								</tr>
								<tr class="wholesaledischargeDataHEATHERS">
									<td>
										Heathers
									</td>
								</tr>
								<tr class="wholesaledischargeDataCOLORS">
									<td>
										Colors
									</td>
								</tr>
								<tr class="wholesaledischargeDataBLACK">
									<td>
										Black
									</td>
								</tr>
							</tbody>
							<tfoot></tfoot>
						</table>
					</div>
				</div>
			</div>

			<div id="waterbase" class="tabcontent" style="display: none">
				<div class="fullwidth marginTop5">
					<div class="fullwidth" style="overflow-x:auto;">
						<table id="WaterbaseTableData">
							<thead>
								<tr>
									<th>Fabric Shade</th>
									<th>1</th>
									<th>2</th>
									<th>3</th>
									<th>4</th>
									<th>5</th>
									<th>6</th>
									<th>7</th>
									<th>8</th>
									<th>9</th>
									<th>10</th>
								</tr>
							</thead>
							<tbody>
								<tr class="wholesalewaterbaseDataWHITE">
									<td width="130">
										White
									</td>
									
								</tr>
								<tr class="wholesalewaterbaseDataNATURAL">
									<td>
										Natural
									</td>
								</tr>
								<tr class="wholesalewaterbaseDataHEATHERS">
									<td>
										Heathers
									</td>
								</tr>
								<tr class="wholesalewaterbaseDataCOLORS">
									<td>
										Colors
									</td>
								</tr>
								<tr class="wholesalewaterbaseDataBLACK">
									<td>
										Black
									</td>
								</tr>
							</tbody>
							<tfoot></tfoot>
						</table>
					</div>
				</div>
			</div>
				


			<style type="text/css">
				.tablinks.active{
					background: rgba(0,0,0,0.6)
				}
				.fullwidth{
					width: 100%;
				}
				.wholesaleTab.tab .tablinks{
					margin-top: 5px;
				}
				.width60{
					width: 60%;
				}
				.width40{
					width: 40%;
				}
				.width20{
					width: 20%;
				}
				.floatright{
					float: right;
				}
				.floatleft{
					float: left;
				}
				.marginTop5{
					margin-top: 5px;
				}
				.fullwidth table td:not(:first-child) {
					width: 50px;
				    overflow: hidden;
				    white-space: nowrap;
				}
				.fullwidth table th:not(:first-child){
					/*text-align: center;*/
				}
				.width50{
					width: 50%;
				}
				.colorBox{
					width: 22px;
					height: 22px;
					border: 1px solid black;

				}
				.marginBottom10{
					margin-bottom: 10px;
				}

			</style>

		<?php

		}


	}


}









// function return_custom_price($price, $product) {
//     global $post, $blog_id;
//     $price = get_post_meta($post->ID, '_regular_price');
//     $post_id = $post->ID;
//     $price = (9);
//     return $price;
// }
// add_filter('woocommerce_get_price', 'return_custom_price', 10, 2);
















?>