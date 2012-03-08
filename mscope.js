jQuery(function($){
	$(window).load(function(event){
		var usr = $('#user').val();
		if(typeof mscope[usr] == 'object'){
			$('#menu-scope input[type="checkbox"]').removeAttr('checked');
			$.each(mscope[usr], function(i, val){
				$('#menu-scope input[value="'+val+'"]').attr('checked', 'true');
			});
			$('#menu-scope input[type="checkbox"]:not(:checked)').siblings('ul').find('input[type="checkbox"]').attr('disabled', 'disabled');
		}else{
			$('#menu-scope input[type="checkbox"]').attr('checked', 'true');
		}

		// setup order
		var ord = [];
		for(i in mscope[usr][1]){
			ord[mscope[usr][1][i]-1] = $('#menu-scope > ul > li > input[name="pos['+i+']"]').parent('li').remove();
		}
		for(i in ord){
			ord[i].appendTo('#menu-scope > ul');
		}
		ord = null;
	});

	$('#user').change(function(event){
		var usr = $(this).val();
		if(typeof mscope[usr][0] == 'object'){
			$('#menu-scope input[type="checkbox"]').removeAttr('checked');
			$.each(mscope[usr], function(i, val){
				$('#menu-scope input[value="'+val+'"]').attr('checked', 'true').siblings('ul').find('input[type="checkbox"]').removeAttr('disabled');
			});
		}else{
			$('#menu-scope input[type="checkbox"]').attr('checked', 'true');
		}

		// setup order
		var ord = [];
		for(i in mscope[usr][1]){
			ord[mscope[usr][1][i]-1] = $('#menu-scope > ul > li > input[name="pos['+i+']"]').parent('li').remove();
		}
		for(i in ord){
			ord[i].appendTo('#menu-scope > ul');
		}
		ord = null;
	});

	$('#menu-scope input[type="checkbox"]').change(function(event){
		var t = $(this);
		if(t.is(':checked')){
			t.siblings('ul').find('input[type="checkbox"]').removeAttr('disabled');
		}else{
			t.siblings('ul').find('input[type="checkbox"]').attr('disabled', 'disabled');
		}
	});

	$('#menu-scope > ul').sortable({
		axis: 'y',
		update: function(event, ui){
			$(this).children('li').children('input[type="text"]').each(function(i){
				$(this).val(i+1);
			});
		}
	});//, #menu-scope > ul  ul
});