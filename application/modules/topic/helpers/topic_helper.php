<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_story_topics($story_id, $limit = 3)
{
	$CI =& get_instance();
	$CI->load->model('topic/topic_model', 'topic');

	$topics =  $CI->topic->get_story_topics($story_id, $limit);

	$html = false;

	if ($topics)
	{
		foreach ($topics AS $topic)
		{
			$html .= ' <a href="'.$topic->slug.'">'.$topic->name.'</a>';
		}
	}

	return $html;
}