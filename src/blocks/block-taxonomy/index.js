/**
 * BLOCK: block-taxonomy
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
													taxonomy: {
														type: 'string',
														default: 'accommodation-type',
															},
															displaylimit: {
																type: 'string',
																default: '9',
																	},
																	hideSingleLink: {
																		type: 'string',
																		default: 'no',
																			},
																			disableSingleLink: {
																				type: 'string',
																				default: 'no',
																					},
																					displayorder: {
																						type: 'string',
																						default: 'ASC',
																							},
																							orderBy: {
																								type: 'string',
																								default: 'none',
																									},
																									carousel: {
																										type: 'string',
																										default: '1',
																											},
																											disableText: {
																												type: 'string',
																												default: '0',
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
																																		'tour-operator/block-taxonomy',
																																		{
																																				// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
																																			title: __( 'LSX Tour Operator Taxonomies' ), // Block title.
																																			icon: 'tag', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
																																			category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
																																			keywords: [
																																					__( 'TO Taxonomies' ),
																																					__( 'tours' ),
																																					__( 'tour-operator' ),
																																				],
																																			attributes: blockAttributes,

																																				edit( { attributes, className, setAttributes } ) {
																																					const { columns, shortcodetitle, shortcodeSubTitle, taxonomy, displaylimit, displayorder, orderBy, carousel, disablePlaceholder, disableText, seeMoreButton, seeMoreButtonText, seeMoreButtonLink, hideSingleLink, disableSingleLink, include } = attributes;

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
																																					const taxonomyOptions = [
																																						{ value: 'travel-style', label: __( 'Travel Styles' ) },
																																						{ value: 'accommodation-type', label: __( 'Accommodation Types' ) },
																																						{ value: 'accommodation-brand', label: __( 'Brands' ) },
																																						{ value: 'facility', label: __( 'Facilities' ) },
																																						{ value: 'continent', label: __( 'Continent' ) },
																																						{ value: 'role', label: __( 'Roles' ) },
																																						// { value: 'region', label: __( 'Regions' ) },
																																						// { value: 'activity', label: __( 'Activities' ) },
																																					];

																																					// See More Button options
																																					const seeMoreButtonOptions = [
																																						{ value: '1', label: __( 'Yes' ) },
																																						{ value: '', label: __( 'No' ) },
																																					];

																																					// Order By options
																																					const orderByOptions = [
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
																																					// Hide Single Link options
																																					const hideSingleLinkOptions = [
																																						{ value: 'yes', label: __( 'Yes' ) },
																																						{ value: 'no', label: __( 'No' ) },
																																					];
																																					// Disable Single Link options
																																					const disableSingleLinkOptions = [
																																						{ value: 'yes', label: __( 'Yes' ) },
																																						{ value: 'no', label: __( 'No' ) },
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
																																									< PanelBody title        = { __( 'Shortcode Settings' ) } >
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
																																								options     = { taxonomyOptions }
																																								value       = { taxonomy }
																																								onChange    = { ( value ) => setAttributes( { taxonomy: value } ) }
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
																																								label       = { __( 'Order By' ) }
																																								description = { __( 'Choose the parameter you wish your items to be ordered by' ) }
																																								options     = { orderByOptions }
																																								value       = { orderBy }
																																								onChange    = { ( value ) => setAttributes( { orderBy: value } ) }
																																										/ >
																																										< SelectControl
																																								label       = { __( 'Carousel' ) }
																																								description = { __( 'Choose if the items will show as carousel' ) }
																																								options     = { carouselOptions }
																																								value       = { carousel }
																																								onChange    = { ( value ) => setAttributes( { carousel: value } ) }
																																										/ >
																																										< SelectControl
																																								label       = { __( 'Remove Taxonomy Title' ) }
																																								description = { __( 'Choose if the title text will show' ) }
																																								options     = { hideSingleLinkOptions }
																																								value       = { hideSingleLink }
																																								onChange    = { ( value ) => setAttributes( { hideSingleLink: value } ) }
																																										/ >
																																										< SelectControl
																																								label       = { __( 'Disable View More Link' ) }
																																								description = { __( 'Choose if the link will show' ) }
																																								options     = { disableSingleLinkOptions }
																																								value       = { disableSingleLink }
																																								onChange    = { ( value ) => setAttributes( { disableSingleLink: value } ) }
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

																																							< h2 className               = "lsx-title" >
																																					{ shortcodetitle } < small > { shortcodeSubTitle } < / small >
																																							< / h2 >
																																							< div className              = "lsx-taxonomy-body" >
																																						[lsx_to_taxonomy_widget taxonomy =&quot;{ taxonomy } & quot; columns =&quot;{ columns } & quot; limit =&quot;{ displaylimit } & quot; disable_placeholder =&quot;{ disablePlaceholder } & quot; disable_text =&quot;{ disableText } & quot; disable_single_link =&quot;0 & quot; order =&quot;{ displayorder } & quot; orderby =&quot;{ orderBy } & quot; carousel =&quot;{ carousel } & quot; include =&quot;{ include } & quot; ]
																																							< / div >
																																						< / div >
																																				);
																																			},

																																				save( { attributes, className } ) {
																																					const { columns, shortcodetitle, shortcodeSubTitle, taxonomy, displaylimit, disablePlaceholder, displayorder, seeMoreButton, seeMoreButtonText, seeMoreButtonLink, hideSingleLink, disableSingleLink, disableText, orderBy, carousel, include } = attributes;

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
																																						< div className                  = { className } >
																																							< h2 className               = "lsx-title" >
																																					{ shortcodetitle } < small > { shortcodeSubTitle } < / small >
																																							< / h2 >
																																							< div className              = { classnames(
																																								'blsx-taxonomy-bodytn',
																																								`hide - title - ${hideSingleLink}`,
																																								`disable - link - ${disableSingleLink}`,
																																							) } >
																																						[lsx_to_taxonomy_widget taxonomy =&quot;{ taxonomy } & quot;  columns =&quot;{ columns } & quot; limit =&quot;{ displaylimit } & quot; disable_placeholder =&quot;{ disablePlaceholder } & quot; disable_text =&quot;{ disableText } & quot; disable_single_link =&quot;0 & quot; order =&quot;{ displayorder } & quot; orderby =&quot;{ orderBy } & quot; carousel =&quot;{ carousel } & quot; include =&quot;{ include } & quot; ]
																																							< / div >
																																							{ seeMore }
																																						< / div >
																																				);
																																			},
																																		}
																																	);
