jQuery(function($){
	$(window).load(function(event){
		var usr = $('#user').val();
		if(typeof mscope[usr] == 'object'){
			$('#menu-scope input[type="checkbox"]').removeAttr('checked');
			$.each(mscope[usr], function(i, val){
				$('#menu-scope input[value="'+val+'"]').attr('checked', 'true').siblings('ul').find('input[type="checkbox"]').attr('disabled', 'disabled');
			});
		}else{
			$('#menu-scope input[type="checkbox"]').attr('checked', 'true');
		}
	});

	$('#user').change(function(event){
		var usr = $(this).val();
		if(typeof mscope[usr] == 'object'){
			$('#menu-scope input[type="checkbox"]').removeAttr('checked');
			$.each(mscope[usr], function(i, val){
				$('#menu-scope input[value="'+val+'"]').attr('checked', 'true');
			});
		}else{
			$('#menu-scope input[type="checkbox"]').attr('checked', 'true');
		}
	});

	$('#menu-scope input[type="checkbox"]').change(function(event){
		var t = $(this);
		if(t.is(':checked')){
			t.siblings('ul').find('input[type="checkbox"]').removeAttr('disabled');
		}else{
			t.siblings('ul').find('input[type="checkbox"]').attr('disabled', 'disabled');
		}
	});
});