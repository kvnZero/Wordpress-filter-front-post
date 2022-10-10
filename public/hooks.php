<?php
declare(strict_types=1);
/**
 * @author   kvnZero
 * @contact  kvnZero@github.com
 */

const AB_FILTER_CATEGORY_COOKIE_KEY = 'ab_filter_category';
const AB_FILTER_CATEGORY_ADD_PARAMS = 'ab_filter_params_add';
const AB_FILTER_CATEGORY_REMOVE_PARAMS = 'ab_filter_params_remove';

//获取COOKIE里的数据
$filter_category = $_COOKIE[AB_FILTER_CATEGORY_COOKIE_KEY] ?? [];
if (!empty($filter_category) && !is_array($filter_category)) {
	$filter_category = unserialize($filter_category);
}

//如果访问了页面
if (isset($_GET[AB_FILTER_CATEGORY_ADD_PARAMS]) && !in_array($_GET[AB_FILTER_CATEGORY_ADD_PARAMS], $filter_category)) {
	//增加
	$filter_category[] = $_GET[AB_FILTER_CATEGORY_ADD_PARAMS];
}
if (isset($_GET[AB_FILTER_CATEGORY_REMOVE_PARAMS]) && in_array($_GET[AB_FILTER_CATEGORY_REMOVE_PARAMS], $filter_category)) {
	//移除某一个
	foreach ($filter_category as $i => $category) {
		if ($category == $_GET[AB_FILTER_CATEGORY_REMOVE_PARAMS]) {
			unset($filter_category[$i]);
		}
	}
}

setcookie(AB_FILTER_CATEGORY_COOKIE_KEY, serialize($filter_category), 0, COOKIEPATH, '', is_ssl(), true);

$filter_category = array_map( 'absint', array_unique( (array) $filter_category ) );

add_action('pre_get_posts', function (\WP_Query $query) use ($filter_category){
	if (!is_category() && !is_admin() && !empty($filter_category) && empty($query->query_vars['post_type'])) {
		//过滤post查询语句
		$query->query_vars['category__not_in'] = $filter_category;
	}
});


add_action('astra_after_archive_title', function() use ($filter_category){
	if (is_category() && !is_admin()) {
		$current_category_id = get_queried_object_id();
		$current_url = set_url_scheme('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		if (in_array($current_category_id, $filter_category)) {
			//提醒删除
			$current_url = remove_query_arg(AB_FILTER_CATEGORY_ADD_PARAMS, $current_url);
			$extend_args = [AB_FILTER_CATEGORY_REMOVE_PARAMS => $current_category_id];
			$text = '不再屏蔽';
		} else {
			//提醒增加
			$current_url = remove_query_arg(AB_FILTER_CATEGORY_REMOVE_PARAMS, $current_url);
			$extend_args = [AB_FILTER_CATEGORY_ADD_PARAMS => $current_category_id];
			$text = '屏蔽分类';
		}
		$url = add_query_arg(
			$extend_args,
			$current_url
		);
		echo '<a href="'.$url.'">'.$text.'</a>';
	}
});