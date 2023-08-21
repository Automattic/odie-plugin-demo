document.addEventListener( 'DOMContentLoaded', function () {
	function renderOdie() {
		if ( window.Odie && window.Odie.render ) {
            window.Odie.render({
                isRunningInJetpack: wpcomOdieWidget.isRunningInJetpack,
                jetpackXhrParams: wpcomOdieWidget.jetpackXhrParams,
                domNode: document.getElementById('jetpack-odie-root'),
                authToken: wpcomOdieWidget.authToken,
                botJids: wpcomOdieWidget.botJids,
                siteId: wpcomOdieWidget.siteId,
                service: wpcomOdieWidget.service,
                userJid: wpcomOdieWidget.userJid,
            });
        }
	}
	const button = document.createElement( 'button' );
	button.textContent = 'Show Odie';
	button.addEventListener( 'click', renderOdie );

	const widgetContainer = document.getElementById( 'jetpack-odie-root' );
	widgetContainer.appendChild( button );

	// Open on load if ?odie param is set.
	const urlParams = new URLSearchParams(window.location.search);
	if ( urlParams.has('odie') ) { 
		renderOdie();
	}
} );
