

function openWholeSaleTypes(evt, protype) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(protype).style.display = "block";
    evt.currentTarget.className += " active";

    jQuery(".typeofproductwholesale").text(protype.toUpperCase());
}



jQuery(document).ready(function(){
	wholeSaleTableShowData = JSON.parse(wholeSaleTableShowData);
	console.log(wholeSaleTableShowData);
	// Show colors on product page

	jQuery.each(wholeSaleTableShowData, function (key, val) {
		var color = key.split("-")[0];
		var counter = key.split("-")[1];
		var hex = key.split("-")[2];
		if (color == 'proColor' && !hex) {

			jQuery("#ColorDisplayWrapperWholesale").append('<div class="width50 floatleft">\
					<div class="fullwidth">\
						<div class="width20 floatleft">\
							<div class="colorBox" title="'+wholeSaleTableShowData[color+'-'+counter+"-"+"hex"]+'" style="background: '+wholeSaleTableShowData[color+'-'+counter+"-"+"hex"]+'"></div>\
						</div>\
						<div class="width40 floatleft">\
							'+val+'\
						</div>\
						<div style="clear: both;"></div>\
					</div>\
				</div>');
		}
	})
	



	if (jQuery('select[name=wholesaleSelector] option:selected').val() == "20") {
		enterDatainTable(wholeSaleTableShowData, 20);
	}

	jQuery("select[name=wholesaleSelector]").change(function (e) {
		var selector = jQuery('select[name=wholesaleSelector] option:selected').val();
		if (selector == "20") {
			enterDatainTable(wholeSaleTableShowData, 20);
		}else if(selector == "30"){
			enterDatainTable(wholeSaleTableShowData, 30);
		}else if(selector == "50"){
			enterDatainTable(wholeSaleTableShowData, 50);
		}else if(selector == "100"){
			enterDatainTable(wholeSaleTableShowData, 100);
		}else if(selector == "150"){
			enterDatainTable(wholeSaleTableShowData, 150);
		}else if(selector == "500"){
			enterDatainTable(wholeSaleTableShowData, 500);
		}else if(selector == "1000"){
			enterDatainTable(wholeSaleTableShowData, 1000);
		};
	});


});

function enterDatainTable(data, quantity) {
	jQuery("#PlastisolTableData tbody > tr").each(function( index, element ) {
		if(jQuery(element).find("td").length > 1){
			jQuery(element).find("td").not(':first').remove();
		}
	})
	jQuery("#DischargeTableData tbody > tr").each(function( index, element ) {
		if(jQuery(element).find("td").length > 1){
			jQuery(element).find("td").not(':first').remove();
		}
	})
	jQuery("#WaterbaseTableData tbody > tr").each(function( index, element ) {
		if(jQuery(element).find("td").length > 1){
			jQuery(element).find("td").not(':first').remove();
		}
	})
	jQuery.each(data, function (key, val) {
			var typeDecider = key.split("-")[0];
			var selectorValue = key.split("-")[1];
			var increment = key.split("-")[2];
			var fabArray = [
				{lname: "NATURAL", name:"natural", value: data["fab-natural"]},
				{lname: "WHITE", name:"white", value: data["fab-white"]},
				{lname: "HEATHERS", name:"heathers", value: data["fab-heathers"]},
				{lname: "BLACK", name:"black", value: data["fab-black"]},
				{lname: "COLORS", name:"colors", value: data["fab-colors"]}
			]


			// for plastisol table
			if (typeDecider == "p") {

				// 
				if (selectorValue == quantity) {

					// sevice cost of current quantity
					var teeValuewith1color = data[typeDecider+"-"+selectorValue];
					for (var i = 0; i < fabArray.length; i++) {
						var garmentCost = fabArray[i].value;
						var oneteeCost = parseFloat(garmentCost)+ (parseFloat(garmentCost) * .10) + parseFloat(teeValuewith1color);

						// console.log(oneteeCost);

						if (!increment) {
							//without color cost and one color
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number(oneteeCost.toFixed(2))+"</td>");
							
						}else{
							
							//with a extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + parseFloat(val)).toFixed(3))+"</td>");
							//with 2 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number(((oneteeCost + (parseFloat(val)*2)).toFixed(2)))+"</td>");
							//with 3 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*3)).toFixed(2))+"</td>");
							//with 4 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*4)).toFixed(2))+"</td>");
							//with 5 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*5)).toFixed(2))+"</td>");
							//with 6 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*6)).toFixed(2))+"</td>");
							//with 7 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*7)).toFixed(2))+"</td>");
							//with 8 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*8)).toFixed(2))+"</td>");
							//with 9 extra colors
							jQuery("#PlastisolTableData .wholesaleplastisolData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*9)).toFixed(2))+"</td>");
							
						}
					}
					
				}

				// for discharge table
			}else if (typeDecider == "d") {
				if (selectorValue == quantity) {
					// sevice cost of current quantity
					var teeValuewith1color = data[typeDecider+"-"+selectorValue];
					for (var i = 0; i < fabArray.length; i++) {
						var garmentCost = fabArray[i].value;
						var oneteeCost = parseFloat(garmentCost)+ (parseFloat(garmentCost) * .10) + parseFloat(teeValuewith1color);

						// console.log(oneteeCost);


						if (!increment) {
							//without color cost and one color
							
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number(oneteeCost.toFixed(2))+"</td>");
							
						}else{
							
							//with a extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number(oneteeCost + parseFloat(val))+"</td>");
							//with 2 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*2)).toFixed(2))+"</td>");
							//with 3 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*3)).toFixed(2))+"</td>");
							//with 4 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*4)).toFixed(2))+"</td>");
							//with 5 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*5)).toFixed(2))+"</td>");
							//with 6 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*6)).toFixed(2))+"</td>");
							//with 7 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*7)).toFixed(2))+"</td>");
							//with 8 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*8)).toFixed(2))+"</td>");
							//with 9 extra colors
							jQuery("#DischargeTableData .wholesaledischargeData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*9)).toFixed(2))+"</td>");
							
						}
					}
	
				}
			// for waterbase table
			}else if (typeDecider == 'w') {
				if (selectorValue == quantity) {
					// sevice cost of current quantity
					var teeValuewith1color = data[typeDecider+"-"+selectorValue];
					for (var i = 0; i < fabArray.length; i++) {
						var garmentCost = fabArray[i].value;
						var oneteeCost = parseFloat(garmentCost)+ (parseFloat(garmentCost) * .10) + parseFloat(teeValuewith1color);

						// console.log(oneteeCost);


						if (!increment) {
							//without color cost and one color
							
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number(oneteeCost.toFixed(2))+"</td>");
							
						}else{
							
							//with a extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number(oneteeCost + parseFloat(val))+"</td>");
							//with 2 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*2)).toFixed(2))+"</td>");
							//with 3 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*3)).toFixed(2))+"</td>");
							//with 4 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*4)).toFixed(2))+"</td>");
							//with 5 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*5)).toFixed(2))+"</td>");
							//with 6 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*6)).toFixed(2))+"</td>");
							//with 7 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*7)).toFixed(2))+"</td>");
							//with 8 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*8)).toFixed(2))+"</td>");
							//with 9 extra colors
							jQuery("#WaterbaseTableData .wholesalewaterbaseData"+fabArray[i].lname).append("<td>"+Number((oneteeCost + (parseFloat(val)*9)).toFixed(2))+"</td>");
							
						}
					}

					
				}
			}
		});
}




