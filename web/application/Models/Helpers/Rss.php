<?php

class Models_Helpers_Rss{

	public static function getFeed(){
		require_once('config.php');

		$select = Zend_Registry::get('db')->select()
			->from(
				array('a'=>'novinky'),
				array('hp_image', 'platna_od','novinka_id'))
			->join(
				array('b'=>'novinky_jazyk'),
				'a.novinka_id = b.novinka_id',
				array('nadpis','anotace','data'))
			->join(
				array('c'=>'jazyky'),
				'b.lang_id = c.lang_id',
				array('c.iso'))
			->join(
				array('d'=>'controller_convert'),
				'b.lang_id = d.lang_id',
				array('d.req_controller'))
			->where('a.zobrazeno=?','1')
			->where('d.c_id=?',28)
			->where('c.iso=?',$_SESSION['lang'])
			->where('b.zobrazeno_jazyk=?','1')
			->where('a.platna_od<?', date('Y-m-d H:m:s'))
			->where('a.platna_do>?', date('Y-m-d H:m:s'));

			$row=$select->query()->fetchAll();

			$entries=array();
			foreach($row as $key=>$article){
				$entries[$key]['title']       = $article['nadpis'];
				$entries[$key]['link']        = PROTOCOL.BASEDOMAIN.'/'.$article['iso'].'/'.$article['req_controller'].'/index/n/'.$article['novinka_id'];
				$entries[$key]['description'] = $article['anotace'];
				$entries[$key]['content']     = $article['data'];
			}

			$feedData = array(
				'title'       => FEED_TITLE,
				'link'        => PROTOCOL.BASEDOMAIN.'/cs/rss-cs',
				'charset'     => FEED_CHARSET,
				'entries'     => $entries,
			);
			$feed = Zend_Feed::importArray($feedData, 'rss');

			return $feed;
		}
	}
