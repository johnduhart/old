(function ( $ ) {
	var liveEdit = mw.LiveEdit = {
		cookieSettings: {
			path: '/',
			expires: 1
		},

		checkSession: function() {
			if ( mw.config.get( 'wgAction' ) === 'edit' ) {
				return;
			}

			var killSession = $.cookie( 'killLiveEditSession' );

			if ( killSession === null ) {
				return;
			}

			liveEdit.updateSessionCall( 'aborted', function() {
					$.cookie( 'killLiveEditSession', null, liveEdit.cookieSettings );
				}, true, killSession );
		},

	    getEditingUsers: function( callback ) {
			$.get(
				mw.util.wikiScript( 'api' ), {
					action: 'query',
					prop: 'liveeditsessions',
					titles: mw.config.get( 'wgPageName' ),
					format: 'json'
				}, function( data ) {
					liveEdit.processEditingUsers( data, callback );
				} , 'json'
			);
	    },

		processEditingUsers: function( data, callback ) {
			if ( 'error' in data ) {
				mw.log( 'Got an error processing users' );
				// TODO: Handle it
				return;
			}

			var page = data.query.pages[ mw.config.get( 'wgArticleId' ) ];

			callback( page.liveeditsessions );
		},

		updateSessionCall: function( status, callback, async, sessionid ) {
			sessionid = sessionid || mw.config.get( 'liveEditSessionId' );
			$.ajax({
				type: 'POST',
				url: mw.util.wikiScript( 'api' ),
				success: callback,
				dataType: 'json',
				async: async,
				data: {
					action: 'liveeditsessionupdate',
					sessionid: sessionid,
					state: status,
					format: 'json'
				}
			});
		}
	};

	$( liveEdit.checkSession );
})( jQuery )