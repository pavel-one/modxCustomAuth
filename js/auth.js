jQuery(document).ready(function($) {
	$('#register form').submit(function(event) {
		var th = $(this);
		var data = th.serialize();
	    $.ajax({
	        url: 'assets/components/customAuth/reg.php',
	        type: 'POST',
	        dataType: 'json',
	        data: data,
	        success: function(response) {
	        	if (response.success) {
	        		modPNotify.Message.success('Успешно',response.msg);
	        		th[0].reset();
	        		$.magnificPopup.close();
	        	} else {
	        		modPNotify.Message.error('Ошибка',response.msg);
	        	}
	        	
	        }
	    })

		return false;
	});

	$('#sign-in form').submit(function(event) {
		var th = $(this);
		var data = th.serialize();
	    $.ajax({
	        url: 'assets/components/customAuth/auth.php',
	        type: 'POST',
	        dataType: 'json',
	        data: data,
	        success: function(response) {
	        	if (response.success) {
	        		modPNotify.Message.success('Успешно',response.msg);
	        		th[0].reset();
	        		//$.magnificPopup.close();
	        		setTimeout(function() {
	        			window.location = response.link
	        		}, 1500);
	        	} else {
	        		modPNotify.Message.error('Ошибка',response.msg);
	        	}
	        	
	        }
	    })
		return false;
	});

	$('#no-password form').submit(function(event) {
		var th = $(this);
		var data = th.serialize();
	    $.ajax({
	        url: 'assets/components/customAuth/remember.php',
	        type: 'POST',
	        dataType: 'json',
	        data: data,
	        success: function(response) {
	        	if (response.success) {
	        		modPNotify.Message.success('Успешно',response.msg);
	        		th[0].reset();
	        		$.magnificPopup.close();
	        	} else {
	        		modPNotify.Message.error('Ошибка',response.msg);
	        	}
	        	
	        }
	    })
		return false;
	});

	$('#rememberme').submit(function(event) {
		var th = $(this);
		var data = th.serialize();
	    $.ajax({
	        url: '/assets/components/customAuth/recovery.php',
	        type: 'POST',
	        dataType: 'json',
	        data: data,
	        success: function(response) {
	        	if (response.success) {
	        		modPNotify.Message.success('Успешно',response.msg);
	        		th[0].reset();
	        		//$.magnificPopup.close();
	        		setTimeout(function() {
	        			window.location = response.link
	        		}, 1500);
	        	} else {
	        		modPNotify.Message.error('Ошибка',response.msg);
	        	}
	        	
	        }
	    })
		return false;
	});
});	