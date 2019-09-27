jQuery(document).ready(function($) {
	jQuery("span.view_become_profile").on('click', function(){
		var data_profile = jQuery(this).attr('data-user');
		//var profile_data = JSON.parse(data_profile) ;
		console.log("profile_data",  data_profile)
		var profile_data = JSON.parse(data_profile) ;
		console.log("profile_datac",  profile_data)

		var popupHtml = `<div id="BecomeHaveModal" class="HaveFunModal">
		<div class="havefun-content"><span class="close">&times;</span>
		<div class="havefun-content-wrap">
			<h3 style="text-align:center">User Details</h3>
			<table class="widefat fixed striped users res-table">

			  <tr><th>Full Name</th><td>`+ profile_data.fullname+`</td></tr><tr>
			    <th>Mobile</th><td>`+ profile_data.mobile+`</td></tr><tr>
			    <th>Biography</th><td>`+ profile_data.biography+`</td></tr><tr>
			    <th>Become Message</th><td>`+ profile_data.become_message+`</td></tr><tr>
			    <th>staduim</th><td>`+ profile_data.staduim +`</td></tr><tr>
			    <th>Team</th><td>`+ profile_data.teams+`</td></tr><tr>
			    <th>Country</th><td>`+ profile_data.country+`</td></tr><tr>
			    <th>City</th><td>`+ profile_data.city+`</td>
			  </tr>
			</table>
		</div> </div></div>`;
		jQuery('body').append(popupHtml);
		jQuery('#BecomeHaveModal').css('display','block');
		jQuery('#BecomeHaveModal .close').live('click', function(){
			jQuery('#BecomeHaveModal').remove();
		})
   		console.log("popupHtml", popupHtml);
	})
		
          

});