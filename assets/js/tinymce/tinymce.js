function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertPHPLeague() {
	var tagtext;
	var tables 	 = document.getElementById('tables_panel');
	var fixtures = document.getElementById('fixtures_panel');
	var clubs    = document.getElementById('clubs_panel');

	// Table mode
	if (tables.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('league_id').value;
		var style 	 = document.getElementById('style').value;
		
		if (leagueId != 0)
			tagtext = "[phpleague id=" + leagueId + " type=table style=" + style + "]";
		else
			tinyMCEPopup.close();
	}
	
	// Fixtures mode
	if (fixtures.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('league_id').value;
		
		if (leagueId != 0)
			tagtext = "[phpleague id=" + leagueId + " type=fixtures]";
		else
			tinyMCEPopup.close();
	}
	
	// Clubs mode
	if (clubs.className.indexOf('current') != -1) {
		var clubId = document.getElementById('club_id').value;
		
		if (clubId != 0)
			tagtext = "[phpleague id=" + clubId + " type=club]";
		else
			tinyMCEPopup.close();
	}
	
	if (window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}