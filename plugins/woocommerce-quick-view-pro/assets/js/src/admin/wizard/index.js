/**
 * Listen to value changes into the setup wizard
 * and toggle steps when needed.
 */
window.addEventListener('barn2_setup_wizard_changed', (dispatchedEvent) => {
    const display_type = dispatchedEvent.detail.display_type;

    const showStep = dispatchedEvent.detail.showStep
    const hideStep = dispatchedEvent.detail.hideStep

    if ( 'details_only' !== display_type ) {
        showStep( 'images' )
    } else {
        hideStep( 'images' )
    }
}, false);