var colorCliker = 2;
function processHandlingForWholesale(productId, productTitle) {
	console.log("productId: ", productId);

	jQuery("#wholeSaleStep2").removeClass();
	jQuery('.nav.nav-pills a[href="#addValues"]').tab('show');
	jQuery("#wholeSale_selectedProName").text(productTitle);

	jQuery('.nav.nav-tabs a[href="#TeeCost"]').tab('show');
	
	// .tab('show');
	

	jQuery('.nav.nav-pills a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
		//show selected tab / active
		if(jQuery(e.target).text() == "1: Select Product" ){
			jQuery("#wholeSaleStep2").addClass("customDisabled disabled");
			jQuery("#dataFormWholeSale").trigger("reset");
			jQuery(".colorEditionWrapper").empty();
			colorCliker = 2;
			jQuery(".colorEditionWrapper").append(
				'<div class="col-sm-6">\
					<div class="form-group">\
						<label>Name: </label>\
						<input required class="form-control" type="text" value="white" name="proColor-'+colorCliker+'">\
						<p>If you change the color, Change the color name also.</p>\
					</div>\
				</div>\
				<div class="col-sm-6">\
					<div class="form-group">\
						<label>Color :</label>\
						<input required class="jscolor {uppercase:false,hash:true} form-control"  value="ffffff" name="proColor-'+colorCliker+'-hex">\
						<p class="opacity0">a</p>\
					</div>\
				</div>'
				);
		jscolor.installByClassName("jscolor");

		}
	});

	jQuery("input[name=productId]").val(productId);
}

jQuery(".addColorBtn").click(function(e) {
	console.log("addColor");
	jQuery(".colorEditionWrapper").append(
		'<div class="col-sm-6">\
			<div class="form-group">\
				<label>Name: </label>\
				<input required class="form-control" type="text" value="white" name="proColor-'+colorCliker+'">\
				<p>If you change the color, Change the color name also.</p>\
			</div>\
		</div>\
		<div class="col-sm-6">\
			<div class="form-group">\
				<label>Color :</label>\
				<input required class="jscolor {uppercase:false,hash:true} form-control"  value="ffffff" name="proColor-'+colorCliker+'-hex">\
				<p class="opacity0">a</p>\
			</div>\
		</div>'
		);
	jscolor.installByClassName("jscolor");
	colorCliker++;
});

function saveWholeSaleTableData(e) {
	e.preventDefault();
	console.log(event);


	var array = jQuery(e.target).serializeArray();
	var indexed_array = {};

    jQuery.map(array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });
    jQuery(".makeitcentered").css("display", "flex");
    console.log(indexed_array);

	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: { action: 'wholesaleDataInsert' , data: indexed_array },
		success: function (res) {
			jQuery(".makeitcentered").hide();
			jQuery("#responseModal").modal("show");
			setTimeout(function () {
				jQuery("#responseModal").modal("hide");
				location.reload();
			},3000);

			console.log(res);
		},
		error:function (err) {
			console.log("there is error: ", err);
		}
	});
    


	return false;
}






