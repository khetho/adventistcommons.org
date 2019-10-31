<?php

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| Should be rewritten in Controller
|
*/
$config["base_url"] = "";

/*
|--------------------------------------------------------------------------
| Total Rows
|--------------------------------------------------------------------------
|
| This number represents the total rows in the result set you are creating pagination for.
| Typically this number will be the total rows that your database query returned.
|
| Should be rewritten in Controller
|
*/
$config["total_rows"] = "";

/*
|--------------------------------------------------------------------------
| Per Page
|--------------------------------------------------------------------------
|
| The number of items you intend to show per page. In the above example, you would be showing 20 items per page.
|
*/
$config["per_page"] = 5;

/*
|--------------------------------------------------------------------------
| Use Page Numbers
|--------------------------------------------------------------------------
|
| By default, the URI segment will use the starting index for the items you are paginating. If you prefer to show the the actual page number, set this to TRUE.
|
*/
$config["use_page_numbers"] = true;

/*
|--------------------------------------------------------------------------
| Query String Segment
|--------------------------------------------------------------------------
|
| Get-query parameter name
|
*/
$config['query_string_segment'] = 'p';

/*
|--------------------------------------------------------------------------
| Page Qquery String
|--------------------------------------------------------------------------
|
| By default, the pagination library assume you are using URI Segments.
| If you have $config['enable_query_strings'] set to TRUE your links will automatically be re-written using Query Strings.
|
*/
$config['page_query_string'] = true;

/*
| Html-murkup settings
 */
$config["first_tag_open"] = '<li class="page-item">';

$config["first_tag_close"] = "</li>";

$config["last_tag_open"] = '<li class="page-item">';

$config["last_tag_close"] = '</li>';

$config["next_tag_open"] = '<li class="page-item">';

$config["next_tag_close"] = '</li>';

$config["prev_tag_open"] = '<li class="page-item">';

$config["prev_tag_close"] = '</li>';

$config["cur_tag_open"] = '<li class="page-item disabled"><a class="page-link" href="#">';

$config["cur_tag_close"] = '<span class="sr-only">(current)</span></a></li>';

$config['attributes'] = array('class' => 'page-link');
