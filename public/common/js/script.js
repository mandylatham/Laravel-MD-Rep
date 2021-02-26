/******** Common Function for input *********/
$(".decimal-only").on("keypress",function(evt)
{
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode != 46 && charCode > 31  && (charCode < 48 || charCode > 57))
     return false;

 // '.' decimal point  
  if (charCode === 46) {

    // Allow only 1 decimal point
    if (($(this).val()) && ($(this).val().indexOf('.') >= 0))
      return false;
    else
      return true;
  }
  return true;
});

$(".digit-only").on("keypress",function(evt)
{
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31  && (charCode < 48 || charCode > 57))
     return false;
    if (charCode === 46) {
        return false;
    }

  return true;
});

$(".alpha-space").on('change keypress paste', function(event){

   if( event.type == 'paste'){
        var copied_text =  event.originalEvent.clipboardData.getData('text');
        var remove_special_char = copied_text.replace(/^[a-zA-Z .]+/g, "");
        $(this).val($(this).val()+remove_special_char  );
        event.preventDefault(); 

  }else{
		var inputValue = event.which;
		if(event.key === '.'){
			return true;
		}

        if(!(inputValue >= 65 && inputValue <= 120) && (inputValue != 32 && inputValue != 0) ) { 
              event.preventDefault(); 
        }
   }


});

//Toggle button Loading 
function loadButton(element){
	var element = $(""+element+"");
	var loadStatus = element.data("loading");
	if(loadStatus == "loading"){
		element.data("loading", "normal");
		element.html(element.data("text"));
		element.prop('disabled', false);
	}else{	
		element.prop('disabled', true);
		element.data("text", element.html());
		element.data("loading", "loading");
		element.html('<i class="fas fa-spinner fa-spin"></i> &nbsp;'+element.data("loading-text"));
	}
}

//Log the data
function lg(value){
	console.log(value);
}

//Notification


function initiateNotify(type, message){
	if(notify != ""){
		notify.close();
	}
		notify = $.notify(message,{
						allow_dismiss: true,
						type: type,
						placement: {
							from: 'top',
							align: 'right'
						},
						content: {icon: 'fa fa-bell'},
						time: 10,
					});	
	/* }else{
		notify.update({'type': type, 'message': message});
	} */
	
}


function notifySuccess(message){
	initiateNotify('success', '<strong>Success</strong> '+message);
}

function notifyError(message){
	initiateNotify('error', '<strong>Error!</strong> '+message);
}

function notifyWarning(message){
	initiateNotify('warning', '<strong>Warning!</strong> '+message);
}


/*Function Draw Datatable*/
jQuery.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;

        oSettings.oApi._fnReDraw(oSettings);

        // iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }

    // draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};