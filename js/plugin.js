$ = jQuery;

$(document).ready( function () {

	// switch ad spot
	$('#eam_ads option').change( function (e) {
	
		var eam_ad_ID = $(this).attr('id');
	
		$.post(
			ajaxurl,
			{
				action: 'get_selected',
				post_id: 'id'
			},
			function (response) {
				// give eam_ad_options_container ad_id='ad_id'
			}
		);
	});

	// Switch the ad type
	$('#eam_options li').click( function (e) {
		
		// Change li selected
		$('#eam_options .selected').removeClass('selected');
		$(this).addClass('selected');

		// Change body selected
		$('.eam_option').removeClass('selected');

		if ($(this).attr('name') === 'Single') {
			$('#eam_single').addClass('selected');
		}
		else if ($(this).attr('name') === 'Rotate') {
			$('#eam_rotate').addClass('selected');
		}
		else if ($(this).attr('name') === 'Alt') {
			$('#eam_alt').addClass('selected');
		}
	});

	// New ad
	$('input[name=create_new]').click( function (e) {
	
		// Clear out all the options except submit buttons
		$('input').not(':button').not(':submit').val('');

		// Remove the previous ad's ad_id
		$('#eam_ad_options_container').attr('ad_id', undefined); 
	
	});

	// Save the ad or update it
	$('#eam_save').click( function (e) {
		
		// If this ad exists, update
		if ($('#eam_ad_options_container').attr('ad_id') !== undefined) {
			updateAd();
		}
		// Else add it
		else {
			saveNewAd();
		}
	});

	// Pull in the data from the ad to save in the database.
	// *** Add validation
	function setupAdData() {

		var ad_details,
                    data,
                    expires,
                    exp_date,
                    enabled;

        // This ad enabled?
        if( $('#eam_options .selected').attr('name') === 'Disable' ) {

            enabled = false;

        } else {
            
            enabled = true;
        
        }

        // Alt or img
        if ( $('#eam_options .selected').attr('name') === 'Alt') {
        
            data = $('.eam_alt').val();
        
        } else if ( $('#eam_options .selected').attr('name') === 'Single') {
        
            data = $('#eam_img').val();
        
        }

        // Expires
        if ( $('#eam_options .selected').attr('name') === 'Single') {
        
            expires = $('.expires input[type=checkbox]').is(':checked');
            
            // there doesn't seem to be a way to grab the date
            exp_date = null;
        
        } else {
        
            expires = false;
            exp_date = null;
        
        }

        ad_details = {
                id : $('#eam_ad_options_container').attr('ad_id'),
				name : $('input[name=name]').val(),
                type : $('#eam_options .selected').attr('name'),
                enabled : enabled,
                link : $('#eam_link').val(),
                data : data,
                expires : expires,
                exp_date : exp_date
        };

		return ad_details;
	}

	// Save a new ad
	function saveNewAd() {
		
		var ad_details = setupAdData();

		$.post(
			ajaxurl,
			{
				action: 'create_new_ad',
				ad_details: ad_details
			},
			function (response) {

				// Add the new ad to the dropdown
				$('select[name=ads]').append('<option ad_id="' + response + '">' + ad_details.name + '</options>');
			}
		);

	}

	// Remove an ad
	$('#eam_remove').click( function (e) {
		
		// if it exists, remove from db
		if ($('#eam_ad_options_container').attr('id') !== undefined) {
			
		}
		// else just clear the inputs
		else {
			$('input').not(':button').not(':submit').val('');
		}
	});
	
	// If this ad exists, update its data
	function updateAd () {

		var ad_details = setupAdData();

		$.post(
			ajaxurl,
			{
				action: 'update_ad',
				ad_details: ad_details
			},
			function (response) {
				console.log('Updated: ' + response);
			}
		);
	}
});
