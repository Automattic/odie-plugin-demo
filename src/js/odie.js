document.addEventListener( 'DOMContentLoaded', function () {
	const button = document.createElement( 'button' );
	button.textContent = 'Show Odie';

	button.addEventListener( 'click', function () {
		// console.log( document.getElementById( 'jetpack-odie-root' ) );
		if ( window.Odie && window.Odie.render ) {
			window.Odie.render( {
				isRunningInJetpack: wpcomOdieWidget.isRunningInJetpack,
				jetpackXhrParams: wpcomOdieWidget.jetpackXhrParams,
				domNode: document.getElementById( 'jetpack-odie-root' ),
				authToken: wpcomOdieWidget.authToken,
				botJids: wpcomOdieWidget.botJids,
				siteId: wpcomOdieWidget.siteId,
				service: wpcomOdieWidget.service,
				userJid: wpcomOdieWidget.userJid,
			} );
		}
	} );

	const widgetContainer = document.getElementById( 'jetpack-odie-root' );
	widgetContainer.appendChild( button );
} );
