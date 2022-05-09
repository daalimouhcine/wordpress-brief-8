import RadioIconComponent from './radio-icon-component.js';

export const RadioIconControl = wp.customize.KadenceControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<RadioIconComponent control={control}/>,
				control.container[0]
		);
	}
} );
