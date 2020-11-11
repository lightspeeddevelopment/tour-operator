/**
 * BLOCK: my-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

// Import block dependencies and components
import classnames from 'classnames';

// Import CSS.
import './styles/style.scss';
import './styles/editor.scss';

const { __ }                = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

const {
	InspectorControls,
} = wp.editor;
const {
	PanelBody,
	RangeControl,
	TextControl,
	SelectControl,
} = wp.components;

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */

const blockAttributes = {
	columns: {
		type: 'number',
		default: 3,
			},
			shortcodetitle: {
				type: 'string',
				default: 'Featured',
					},
					shortcodeSubTitle: {
						type: 'string',
						default: '',
							},
							seeMoreButton: {
								type: 'string',
								default: '',
									},
									seeMoreButtonText: {
										type: 'string',
										default: 'See More',
											},
											seeMoreButtonLink: {
												type: 'string',
												default: '/',
													},
													postType: {
														type: 'string',
														default: 'tour',
															},
															displaylimit: {
																type: 'string',
																default: '9',
																	},
																	disableText: {
																		type: 'string',
																		default: '0',
																			},
																			displayorder: {
																				type: 'string',
																				default: 'ASC',
																					},
																					orderby: {
																						type: 'string',
																						default: 'none',
																							},
																							carousel: {
																								type: 'string',
																								default: '1',
																									},
																									disablePlaceholder: {
																										type: 'string',
																										default: '0',
																											},
																											include: {
																												type: 'string',
																												default: '',
																													},
																													};

																													registerBlockType(
																														'tour-operator/block-post-types',
																														{
																																// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
																															title: __( 'LSX Tour Operator Post Types' ), // Block title.
																															icon: 'admin-site', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
																															category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
																															keywords: [
																																	__( 'TO Post Types' ),
																																	__( 'tours' ),
																																	__( 'tour-operator' ),
																																],
																															attributes: blockAttributes,

																																edit( { attributes, className, setAttributes } ) {
																																	const { columns, shortcodetitle, shortcodeSubTitle, postType, displaylimit, displayorder, orderby, carousel, disablePlaceholder, seeMoreButton, seeMoreButtonText, seeMoreButtonLink, disableText, include } = attributes;

																																	function onChangeTitle( updatedTitle ) {
																																		setAttributes( { shortcodetitle: updatedTitle } );
																																	}

																																	function onChangeSubTitle( updatedSubTitle ) {
																																		setAttributes( { shortcodeSubTitle: updatedSubTitle } );
																																	}

																																	function onChangeseeMoreButtonText( updatedseeMoreButtonText ) {
																																		setAttributes( { seeMoreButtonText: updatedseeMoreButtonText } );
																																	}

																																	function onChangeseeMoreButtonLink( updatedseeMoreButtonLink ) {
																																		setAttributes( { seeMoreButtonLink: updatedseeMoreButtonLink } );
																																	}

																																	function onChangeColumns( updatedColumns ) {
																																		setAttributes( { columns: updatedColumns } );
																																	}

																																	function onChangeLimit( updatedLimit ) {
																																		setAttributes( { displaylimit: updatedLimit } );
																																	}

																																	function onChangeOrder( updatedOrder ) {
																																		setAttributes( { displayorder: updatedOrder } );
																																	}

																																	function onChangeInclude( updatedInclude ) {
																																		setAttributes( { include: updatedInclude } );
																																	}

																																	// Post Type options
																																	const postTypeOptions = [
																																		{ value: 'tour', label: __( 'Tours' ) },
																																		{ value: 'accommodation', label: __( 'Accommodations' ) },
																																		{ value: 'destination', label: __( 'Destinations' ) },
																																		{ value: 'review', label: __( 'Reviews' ) },
																																		{ value: 'special', label: __( 'Specials' ) },
																																	];

																																	// See More Button options
																																	const seeMoreButtonOptions = [
																																		{ value: '1', label: __( 'Yes' ) },
																																		{ value: '', label: __( 'No' ) },
																																	];

																																	// Orderby options
																																	const orderbyOptions = [
																																		{ value: 'none', label: __( 'None' ) },
																																		{ value: 'ID', label: __( 'Post ID' ) },
																																		{ value: 'name', label: __( 'Name' ) },
																																		{ value: 'date', label: __( 'Date' ) },
																																	];

																																	// Carousel options
																																	const carouselOptions = [
																																		{ value: '1', label: __( 'Yes' ) },
																																		{ value: '0', label: __( 'No' ) },
																																	];
																																	// Disable Text options
																																	const disableTextOptions = [
																																		{ value: '1', label: __( 'Yes' ) },
																																		{ value: '0', label: __( 'No' ) },
																																	];

																																	// Disable Placeholder options
																																	const disablePlaceholderOptions = [
																																		{ value: '1', label: __( 'Yes' ) },
																																		{ value: '0', label: __( 'No' ) },
																																	];

																																	return (
																																		< div className                      = { className } >
																																			{
																																				< InspectorControls key      = "inspector" >
																																					< PanelBody title        = { __( 'Shortcode Settings test' ) } >
																																						< TextControl
																																				label                        = { __( 'Title' ) }
																																				type                         = "text"
																																				value                        = { shortcodetitle }
																																				onChange                     = { onChangeTitle }
																																						/ >
																																						< TextControl
																																				label                        = { __( 'Sub Title' ) }
																																				type                         = "text"
																																				value                        = { shortcodeSubTitle }
																																				onChange                     = { onChangeSubTitle }
																																						/ >
																																						< SelectControl
																																				label                        = { __( 'Add "See More Button"' ) }
																																				description                  = { __( 'Add "See More Button" at the bottom' ) }
																																				options                      = { seeMoreButtonOptions }
																																				value                        = { seeMoreButton }
																																				onChange                     = { ( value ) => setAttributes( { seeMoreButton: value } ) }
																																						/ >
																																						{ seeMoreButton && ! ! seeMoreButton.length && (
																																							< fragment >
																																								< TextControl
																																									label    = { __( 'See More Button Text' ) }
																																									type     = "text"
																																									value    = { seeMoreButtonText }
																																									onChange = { onChangeseeMoreButtonText }
																																								/ >
																																								< TextControl
																																									label    = { __( 'See More Button Link' ) }
																																									type     = "text"
																																									value    = { seeMoreButtonLink }
																																									onChange = { onChangeseeMoreButtonLink }
																																								/ >
																																							< / fragment >
																																						) }
																																						< SelectControl
																																				label       = { __( 'Type of Content' ) }
																																				description = { __( 'Choose the parameter you wish your content to be' ) }
																																				options     = { postTypeOptions }
																																				value       = { postType }
																																				onChange    = { ( value ) => setAttributes( { postType: value } ) }
																																						/ >
																																						< RangeControl
																																				label       = { __( 'Columns' ) }
																																				value       = { columns }
																																				onChange    = { onChangeColumns }
																																				min         = { 1 }
																																				max         = { 6 }
																																						/ >
																																						< TextControl
																																				label       = { __( 'Display Limit' ) }
																																				value       = { displaylimit }
																																				onChange    = { onChangeLimit }
																																						/ >
																																						< TextControl
																																				label       = { __( 'Display Order (Choose between ASC or DESC' ) }
																																				value       = { displayorder }
																																				onChange    = { onChangeOrder }
																																						/ >
																																						< TextControl
																																				label       = { __( 'Include only from comma seperated List of IDs' ) }
																																				value       = { include }
																																				onChange    = { onChangeInclude }
																																						/ >
																																						< SelectControl
																																				label       = { __( 'Orderby' ) }
																																				description = { __( 'Choose the parameter you wish your testimonials to be ordered by' ) }
																																				options     = { orderbyOptions }
																																				value       = { orderby }
																																				onChange    = { ( value ) => setAttributes( { orderby: value } ) }
																																						/ >
																																						< SelectControl
																																				label       = { __( 'Carousel' ) }
																																				description = { __( 'Choose if the testimonials will show as carousel' ) }
																																				options     = { carouselOptions }
																																				value       = { carousel }
																																				onChange    = { ( value ) => setAttributes( { carousel: value } ) }
																																						/ >
																																						< SelectControl
																																				label       = { __( 'Disable Excerpt' ) }
																																				description = { __( 'Choose if the text will show' ) }
																																				options     = { disableTextOptions }
																																				value       = { disableText }
																																				onChange    = { ( value ) => setAttributes( { disableText: value } ) }
																																						/ >
																																						< SelectControl
																																				label       = { __( 'Disable Thumbnail' ) }
																																				description = { __( 'Choose if the placeholder will show' ) }
																																				options     = { disablePlaceholderOptions }
																																				value       = { disablePlaceholder }
																																				onChange    = { ( value ) => setAttributes( { disablePlaceholder: value } ) }
																																						/ >
																																					< / PanelBody >
																																				< / InspectorControls >
																																			}

																																			< h2 className                 = "lsx-title" >
																																	{ shortcodetitle } < small > { shortcodeSubTitle } < / small >
																																			< / h2 >
																																			< div className                = "lsx-testimonial-body" >
																																		[lsx_to_post_type_widget post_type =&quot;{ postType } & quot; columns =&quot;{ columns } & quot; limit =&quot;{ displaylimit } & quot; disable_placeholder =&quot;{ disablePlaceholder } & quot; disable_text =&quot;{ disableText } & quot; order =&quot;{ displayorder } & quot; orderby =&quot;{ orderby } & quot; carousel =&quot;{ carousel } & quot; include =&quot;{ include } & quot; ]
																																			< / div >
																																		< / div >
																																);
																															},

																																save( { attributes, className } ) {
																																	const { columns, shortcodetitle, shortcodeSubTitle, postType, displaylimit, disablePlaceholder, displayorder, seeMoreButton, seeMoreButtonText, seeMoreButtonLink, disableText, orderby, carousel, include } = attributes;

																																	let seeMore;

																																	if ( seeMoreButton === '1' ) {
																																		seeMore   = < p className = "lsx-to-widget-view-all" >
																																			< span >
																																				< a
																																		className = { classnames(
																																			'btn',
																																			'border-btn',
																																		) }
																																		href      = { seeMoreButtonLink }
																																		rel       = "noopener noreferrer"
																																				> { seeMoreButtonText } < i className = "fa fa-angle-right" > < / i > < / a >
																																			< / span >
																																		< / p > ;
																																	}

																																	return (
																																		< div className                    = { className } >
																																			< h2 className                 = "lsx-title" >
																																	{ shortcodetitle } < small > { shortcodeSubTitle } < / small >
																																			< / h2 >
																																			< div className                = "lsx-testimonial-body" >
																																		[lsx_to_post_type_widget post_type =&quot;{ postType } & quot;  columns =&quot;{ columns } & quot; limit =&quot;{ displaylimit } & quot; disable_placeholder =&quot;{ disablePlaceholder } & quot; disable_text =&quot;{ disableText } & quot; order =&quot;{ displayorder } & quot; orderby =&quot;{ orderby } & quot; carousel =&quot;{ carousel } & quot; include =&quot;{ include } & quot; ]
																																			< / div >
																																			{ seeMore }
																																		< / div >
																																);
																															},
																														}
																													);
