<?php
/**
 * Tour Operator - Dashboad holder page config
 *
 * @package   tour_operator
 * @author    LightSpeed
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */

$page = array(
	'page_title'    => esc_html__( 'Dashboard', 'tour-operator' ),
	'menu_title'    => esc_html__( 'Tour Operator', 'tour-operator' ),
	'slug'          => 'tour-operator',
	'menu_position' => 6,
	'capability'    => 'edit_posts',
	'icon'          => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDIwIDIwO2ZpbGw6IzgyODc4YzsiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnIGlkPSJYTUxJRF8xMV8iPjxwYXRoIGlkPSJYTUxJRF8xOF8iIGQ9Ik0xMCwwQzQuNSwwLDAsNC41LDAsMTBzNC41LDEwLDEwLDEwczEwLTQuNSwxMC0xMFMxNS41LDAsMTAsMHogTTEwLDE4LjNjLTQuNiwwLTguMy0zLjctOC4zLTguM2MwLTQuNiwzLjctOC4zLDguMy04LjNjNC42LDAsOC4zLDMuNyw4LjMsOC4zQzE4LjMsMTQuNiwxNC42LDE4LjMsMTAsMTguM3oiLz48cGF0aCBpZD0iWE1MSURfMTlfIiBkPSJNMTAuOCw4LjlMMTAuOCw4LjlMMTAuOCw4LjljLTAuMS0wLjEtMC4yLTAuMS0wLjMtMC4yTDYuMSw2LjFsMi43LDQuNWMwLDAuMSwwLjEsMC4xLDAuMSwwLjJsMCwwbDAsMGMwLjIsMC4zLDAuNiwwLjYsMS4xLDAuNmMwLjgsMCwxLjQtMC42LDEuNC0xLjRDMTEuNCw5LjYsMTEuMiw5LjIsMTAuOCw4Ljl6IE0xMCwxMC43Yy0wLjQsMC0wLjctMC4zLTAuNy0wLjdjMC0wLjQsMC4zLTAuNywwLjctMC43czAuNywwLjMsMC43LDAuN0MxMC43LDEwLjQsMTAuNCwxMC43LDEwLDEwLjd6Ii8+PGcgaWQ9IlhNTElEXzE2XyI+PHJlY3QgaWQ9IlhNTElEXzhfIiB4PSI5LjciIHk9IjIuOSIgd2lkdGg9IjAuNiIgaGVpZ2h0PSIxLjMiLz48cmVjdCBpZD0iWE1MSURfN18iIHg9IjUuMSIgeT0iNC44IiB0cmFuc2Zvcm09Im1hdHJpeCgwLjcwNzEgLTAuNzA3MSAwLjcwNzEgMC43MDcxIC0yLjI1OTQgNS40MTg2KSIgd2lkdGg9IjAuNiIgaGVpZ2h0PSIxLjMiLz48cmVjdCBpZD0iWE1MSURfNl8iIHg9IjEzLjkiIHk9IjUuMSIgdHJhbnNmb3JtPSJtYXRyaXgoMC43MDcxIC0wLjcwNzEgMC43MDcxIDAuNzA3MSAwLjQxNjkgMTEuODc5NykiIHdpZHRoPSIxLjMiIGhlaWdodD0iMC42Ii8+PHJlY3QgaWQ9IlhNTElEXzVfIiB4PSIyLjkiIHk9IjkuNyIgd2lkdGg9IjEuMyIgaGVpZ2h0PSIwLjYiLz48cmVjdCBpZD0iWE1MSURfNF8iIHg9IjE1LjgiIHk9IjkuNyIgd2lkdGg9IjEuMyIgaGVpZ2h0PSIwLjYiLz48cmVjdCBpZD0iWE1MSURfM18iIHg9IjQuOCIgeT0iMTQuMyIgdHJhbnNmb3JtPSJtYXRyaXgoMC43MDcxIC0wLjcwNzEgMC43MDcxIDAuNzA3MSAtOC43MjE0IDguMDk1MikiIHdpZHRoPSIxLjMiIGhlaWdodD0iMC42Ii8+PHJlY3QgaWQ9IlhNTElEXzJfIiB4PSIxNC4yIiB5PSIxMy45IiB0cmFuc2Zvcm09Im1hdHJpeCgwLjcwNzEgLTAuNzA3MSAwLjcwNzEgMC43MDcxIC02LjA0NTEgMTQuNTU2MykiIHdpZHRoPSIwLjYiIGhlaWdodD0iMS4zIi8+PHJlY3QgaWQ9IlhNTElEXzFfIiB4PSI5LjciIHk9IjE1LjgiIHdpZHRoPSIwLjYiIGhlaWdodD0iMS4zIi8+PC9nPjxwYXRoIGlkPSJYTUxJRF80N18iIGQ9Ik0xMS4zLDkuNWMwLTAuMS0wLjEtMC4xLTAuMS0wLjJsMCwwbDAsMGMtMC4yLTAuMy0wLjYtMC42LTEuMS0wLjZjLTAuOCwwLTEuNCwwLjYtMS40LDEuNGMwLDAuNCwwLjIsMC44LDAuNSwxLjFsMCwwbDAsMGMwLjEsMC4xLDAuMiwwLjEsMC4zLDAuMmw0LjQsMi42TDExLjMsOS41eiBNMTAsMTAuN2MtMC40LDAtMC43LTAuMy0wLjctMC43YzAtMC40LDAuMy0wLjcsMC43LTAuN3MwLjcsMC4zLDAuNywwLjdDMTAuNywxMC40LDEwLjQsMTAuNywxMCwxMC43eiIvPjwvZz48L3N2Zz4=',
);

return $page;