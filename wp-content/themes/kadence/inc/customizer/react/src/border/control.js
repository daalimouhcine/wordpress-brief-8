import BorderComponent from './border-component.js';

export const BorderControl = wp.customize.KadenceControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<BorderComponent control={control} customizer={ wp.customize }/>,
				control.container[0]
		);
	}
} );
