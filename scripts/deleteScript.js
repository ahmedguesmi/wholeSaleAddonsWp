function DeleteWholeSaleTable(proId) {
	console.log(proId);
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {"action": "DeleteWholeSaleTable", "productId": proId},
        success: function (data) {
            jQuery("#deleteConfirm").modal('hide');
            console.log(data);
            location.reload(); 
        }
    });
}

function openConfirmModal(proId) {
    jQuery("#deleteConfirm").modal('show');


    jQuery("#deleteConfirmBtn").attr("href", "javascript:DeleteWholeSaleTable("+proId+");");
}