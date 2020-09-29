jQuery(function(){
	//fancybox init
	//for form
	jQuery('.fboxform').fancybox({
		'type'   : 'iframe',
		'modal'  : true,
		'width'  : '60%',
		'height' : '60%'
	});
	//for image
	jQuery('.fboximg').fancybox({
		'type'   : 'image',
		'width'  : '90%',
		'height' : '90%'
	});
	//for iframe view
	jQuery('.fboxframe').fancybox({
		'type'	 : 'iframe',
		'width'  : '90%',
		'height' : '90%'
	});

    //patterns
    //size50%modal
	jQuery('.fboxmodal50').fancybox({'type':'iframe', 'modal': true, 'width':'50%', 'height':'50%'});
    //size60%modal
	jQuery('.fboxmodal60').fancybox({'type':'iframe', 'modal': true, 'width':'60%', 'height':'60%'});
    //size70%modal
	jQuery('.fboxmodal70').fancybox({'type':'iframe', 'modal': true, 'width':'70%', 'height':'70%'});
    //size80%modal
	jQuery('.fboxmodal80').fancybox({'type':'iframe', 'modal': true, 'width':'80%', 'height':'80%'});
    //size90%modal
	jQuery('.fboxmodal90').fancybox({'type':'iframe', 'modal': true, 'width':'90%', 'height':'90%'});

    //size50%
	jQuery('.fbox50').fancybox({'type':'iframe', 'width':'50%', 'height':'50%'});
    //size60%
	jQuery('.fbox60').fancybox({'type':'iframe', 'width':'60%', 'height':'60%'});
    //size70%
	jQuery('.fbox70').fancybox({'type':'iframe', 'width':'70%', 'height':'70%'});
    //size80%
	jQuery('.fbox80').fancybox({'type':'iframe', 'width':'80%', 'height':'80%'});
    //size90%
	jQuery('.fbox90').fancybox({'type':'iframe', 'width':'90%', 'height':'90%'});
});
