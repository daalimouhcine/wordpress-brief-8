import PropTypes from 'prop-types';
const { __ } = wp.i18n;
const { SelectControl } = wp.components;
import ColorControl from '../common/color.js';
const { Component, Fragment } = wp.element;

class ColorLinkComponent extends Component {
	constructor(props) {
		super( props );
		this.handleChangeComplete = this.handleChangeComplete.bind( this );
		this.updateValues = this.updateValues.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault = {
			'highlight': '',
			'highlight-alt': '',
			'highlight-alt2': '',
			'style': 'standard',
		};
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...this.defaultValue,
			...value
		} : this.defaultValue;
		let defaultParams = {
			colors: {
				'highlight': {
					tooltip: __( 'Initial', 'kadence' ),
					palette: true,
				},
				'highlight-alt': {
					tooltip: __( 'Hover', 'kadence' ),
					palette: true,
				},
				'highlight-alt2': {
					tooltip: __( 'Text Alt', 'kadence' ),
					palette: true,
				},
			},
			styles: {
				'standard': {
					label: __( 'Standard (underline)', 'kadence' ),
				},
				'color-underline': {
					label: __( 'Highlight Underline', 'kadence' ),
				},
				'no-underline': {
					label: __( 'No Underline', 'kadence' ),
				},
				'hover-background': {
					label: __( 'Background on Hover', 'kadence' ),
				},
				'offset-background': {
					label: __( 'Offset Background', 'kadence' ),
				},
			},
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		const palette = JSON.parse( this.props.customizer.control( 'kadence_color_palette' ).setting.get() );
		this.state = {
			value: value,
			colorPalette: palette,
		};
	}
	handleChangeComplete( color, isPalette, item ) {
		let value = this.state.value;
		if ( isPalette ) {
			value[ item ] = isPalette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value[ item ] = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value[ item ] = color.hex;
		}
		document.documentElement.style.setProperty('--global-palette-' + item, value[ item ] );
		this.updateValues( value );
	}

	render() {
		const styleOptions = Object.keys( this.controlParams.styles ).map( ( item ) => { 
			return ( { label: this.controlParams.styles[ item ].label, value: item } );
		} );
		return (
			<Fragment>
				<div className="kadence-control-field kadence-color-control kadence-link-color-control">
					<span className="customize-control-title">
							{ __( 'Link Style', 'kadence' ) }
					</span>
					<SelectControl
						value={ this.state.value.style }
						options={ styleOptions }
						onChange={ ( val ) => {
							let value = this.state.value;
							value.style = val;
							this.updateValues( value );
						} }
					/>
				</div>
				<div className="kadence-control-field kadence-color-control kadence-link-color-control">
					{
						this.props.control.params.label &&
						<span className="customize-control-title">
							{ this.props.control.params.label }
						</span>
					}
					{ Object.keys( this.controlParams.colors ).map( ( item ) => {
						if ( ( this.state.value.style === 'standard' || this.state.value.style === 'color-underline' || this.state.value.style === 'no-underline' ) && item === 'highlight-alt2' ) {
							return;
						}
						return (
							<ColorControl
								key={ item }
								presetColors={ this.state.colorPalette }
								color={ ( undefined !== this.state.value[ item ] && this.state.value[ item ] ? this.state.value[ item ] : '' ) }
								usePalette={ ( undefined !== this.controlParams.colors[ item ] && undefined !== this.controlParams.colors[ item ].palette && '' !== this.controlParams.colors[ item ].palette ? this.controlParams.colors[ item ].palette : true ) }
								tooltip={ ( undefined !== this.controlParams.colors[ item ] && undefined !== this.controlParams.colors[ item ].tooltip ? this.controlParams.colors[ item ].tooltip : '' ) }
								onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette, item ) }
								customizer={ this.props.customizer }
							/>
						)
					} ) }
				</div>
			</Fragment>
		);
	}

	updateValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
		} );
	}
}

ColorLinkComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default ColorLinkComponent;
