<!DOCTYPE html>
<html>
<head>
	<title>Betting</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >

</head>
<body>
	<div class="container">
		<div class="row mt-5">
			<div class="col-md-8 offset-md-2">
        		<h1>Betting</h1>
				<hr class="separator">
				<form id="betslip-form">
					<div class="form-group row">
    					<label for="player_id" class="col-sm-4 col-form-label">Player ID</label>
    					<div class="col-sm-8">
    						<input type="text" class="form-control" id="player_id">
    					</div>
					</div>
					<div class="form-group row">
    					<label for="stake_amount" class="col-sm-4 col-form-label">Stake amount</label>
    					<div class="col-sm-8">
    						<input type="text" class="form-control" id="stake_amount">
    					</div>
					</div>
					<hr class="separator">
					<div id="selections">
						<div class="row">
    						<h3 class="col-sm-8">Selections</h3>
    						<div class="col-sm-4">
		    					<button id="add_selection" type="button" class="btn btn-secondary float-right">Add selection</button>
    						</div>
						</div>
    					<hr class="separator">
    					<div class="form-group row">
        					<label for="selection_id_0" class="col-sm-4 col-form-label">Selection ID</label>
        					<div class="col-sm-8">
        						<input type="text" class="form-control" id="selection_id_0">
        					</div>
    					</div>
    					<div class="form-group row">
        					<label for="selection_odds_0" class="col-sm-4 col-form-label">Selection odds</label>
        					<div class="col-sm-8">
        						<input type="text" class="form-control" id="selection_odds_0">
        					</div>
    					</div>
    					<hr class="separator">
					</div>
					<button type="button" id="submit_betslip" class="btn btn-primary mb-2">Submit Betslip</button> 
					<div class="form-check form-check-inline float-right">
						<input class="form-check-input" type="checkbox" id="skip_selections">
						<label class="form-check-label" for="skip_selections">Skip selections</label>
                    </div>
				</form>
				<div id="info" class="alert hidden" role="alert"></div>
			</div>
		</div>
	</div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
	var selection_id = 1;
	$(document).ready(function(){
		$('#add_selection').click(function(){
			var selection = '<div class="form-group row"><label for="selection_id_'+ selection_id +'" class="col-sm-4 col-form-label">Selection ID</label>';
			selection += '<div class="col-sm-8"><input type="text" class="form-control" id="selection_id_'+ selection_id +'"></div></div>';
			selection +='<div class="form-group row"><label for="selection_odds_'+ selection_id +'" class="col-sm-4 col-form-label">Selection odds</label>';
			selection +='<div class="col-sm-8"><input type="text" class="form-control" id="selection_odds_'+ selection_id +'"></div></div><hr class="separator">';
			$(selection).appendTo("#selections");
			selection_id++;
		});
		$('#submit_betslip').click(function(){
			var data = {
				'player_id' : parseInt($('#player_id').val()),
				'stake_amount' : $('#stake_amount').val(),
			};
			var selections = [];
			if (!$('#skip_selections').prop('checked')) {
    			for (i = 0; i < selection_id; i++) {
    				selections.push({
    					'id' : parseInt($('#selection_id_' + i).val()),
    					'odds' : $('#selection_odds_' + i).val(),
    				});
    			}	
			data['selections'] = selections;
			}
			$.ajax({
				url: 'api/bet',
				type: 'POST',
				dataType : 'json',
				contentType: 'application/json;charset=UTF-8',
				data: JSON.stringify(data),
				success: function(data, textStatus, jqXHR) {
					$('#info').removeClass('hidden alert-danger').addClass('alert-success').html('Success!<pre>' + JSON.stringify(data, null, 4) + '</pre>');
				},
				error: function (data, textStatus, errorThrown) {
					$('#info').removeClass('hidden alert-success').addClass('alert-danger').html('Fail!<pre>' + JSON.stringify(data.responseJSON, null, 4) + '</pre>');
				}
			});
		});
	});
</script>
</html>