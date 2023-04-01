<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    function fetch_url($url,$flag=0)
	{
        $images = array();
        $image_counter = 0;
            try
		{
			$imgs_list = array();
			//$url = $_REQUEST['url'];
			$pattern = "#^(http:\/\/|https:\/\/|www\.)(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+).*$#i";
			$ck_url = parse_url($url);
			if(!isset($ck_url['scheme']))
			{
				$url = 'http://'.$url;
			}

			if(!preg_match($pattern,$url))
			{
				echo json_encode(array('result'=>'error','data'=>'1'));
				exit();
			}


			$is_video = false;
			$youtube_vimeo = false;
			$daily_metacafe = false;
			$youtube_or_vimeo = '';

			$url = checkValues($url);
			$url_info = parse_url($url);
			if($url_info['host'] == 'www.youtube.com' || $url_info['host'] == 'www.vimeo.com' || $url_info['host'] == 'youtube.com' || $url_info['host'] == 'vimeo.com')
			{
				$is_video = true;
				$youtube_vimeo = true;
				if($url_info['host'] == 'www.youtube.com' || $url_info['host'] == 'youtube.com')
				{
					$youtube_or_vimeo = 'youtube';
				}
				elseif($url_info['host'] == 'www.vimeo.com' || $url_info['host'] == 'vimeo.com')
				{
					$youtube_or_vimeo = 'vimeo';
				}
			}elseif($url_info['host'] == 'www.dailymotion.com' || $url_info['host'] == 'www.metacafe.com' || $url_info['host'] == 'metacafe.com' || $url_info['host'] == 'dailymotion.com')
			{
				$is_video = true;
				$daily_metacafe = true;
			}
			else
			{
				$is_video = false;
				$youtube_vimeo = false;
				$daily_metacafe = false;
			}

			//echo "<br>".$url;	
			$html = file_get_contents_curl($url);
			$html123 = $html;
			if($html=='')
			{
				echo json_encode(array('result'=>'error','data'=>'2'));
				return $images;
				//exit();
			}
			$doc = new DOMDocument();
//			$doc->preserveWhiteSpace = FALSE;
//			@$doc->loadHTMLFile($url);
//			@$doc->normalizeDocument();

			@$doc->loadHTML($html);
			$nodes = $doc->getElementsByTagName('title');

			if($nodes->length > 0)
			{
				$title = $nodes->item(0)->nodeValue;
			}

			$metas = $doc->getElementsByTagName('meta');
			$links = $doc->getElementsByTagName('link');
			//get and display what you need:

			$html2 ='<div class="link_images">';
			$html_imgs ='';
			$furl = '';
			$k =1;
			$video_url = '';
			$description  = '';
			$og_title = false;
			$og_desc = false;
                        $meta = '';
			$isFetch = false;
			
			if($flag == 0)// ADDED BY AKBAR WE NEED ONLY IMAGES SO NO NEED TO EUN THESE TWO LOOPS
			{
					// title and description on all pages all posible tags
					for ($i = 0; $i < $metas->length; $i++)
					{
						$meta = $metas->item($i);
						if(($meta->getAttribute('name') == 'description' || $meta->getAttribute('name') =='DESCRIPTION') && !$og_desc)
						{
							$description = $meta->getAttribute('content');
						}
						if($meta->getAttribute('property')== 'og:description')
						{
							$description = $meta->getAttribute('content');
							$og_desc = true;
						}
						if($meta->getAttribute('name') == 'title' && !$og_title)
						{
							$title = $meta->getAttribute('content');
						}
						if($meta->getAttribute('property')== "og:title")
						{
							$title = $meta->getAttribute('content');
							$og_title = true;
						}
						if($meta->getAttribute('property') == 'og:image')
						{
							$html_imgs .= "<img src='".$meta->getAttribute('content')."' width='100' id='".$k."' >";
							if($k == 1)
							{
								$isFetch = true;
								$furl =$meta->getAttribute('content');
								if($youtube_or_vimeo == 'youtube')
								{
									for($more_img = 0 ; $more_img < 4 ; $more_img++)
									{
										$handle = @fopen(str_replace('default',$more_img,$meta->getAttribute('content')),'r');
										if($handle !== false)
										{
											if($more_img == 0)
											{
												$html_imgs ='';
												$furl = str_replace('default',$more_img,$meta->getAttribute('content'));
											}
											$html_imgs .= "<img src='".str_replace('default',$more_img,$meta->getAttribute('content'))."' width='100' id='".$k."' >";
											$k++;
										}
									}
								}
								else
								{
									$k++;
								}
							}
						}
						if($meta->getAttribute('property') == 'og:url')
						{
							if($youtube_or_vimeo == 'youtube')
							{
								$video_url_temp = $meta->getAttribute('content');
								$parsed_link = parse_url($video_url_temp);
								parse_str($parsed_link['query'],$var_array);
								$video_url = 'http://www.youtube.com/v/'.(isset($var_array['v'])?$var_array['v']:'').'&autoplay=1';
							}
							elseif($youtube_or_vimeo == 'vimeo')
							{
								$video_url_temp = $meta->getAttribute('content');
								$parsed_link = parse_url($video_url_temp);
								$vid = str_replace('/','',$parsed_link['path']);
								$video_url = 'http://vimeo.com/moogaloop.swf?clip_id='.$vid;
							}
						}
					}
					for ($i = 0; $i < $links->length; $i++)
					{
						$link = $links->item($i);
						if($link->getAttribute('rel') == 'image_src')
						{
												$html_imgs .= "<img src='".$link->getAttribute('href')."' width='100' id='".$k."' >";
							if($k == 1)
							{
								$furl =$link->getAttribute('href');
								$isFetch = true;
							}
							$k++;
						}
						if($link->getAttribute('rel') == 'video_src')
						{
							$video_url = $link->getAttribute('href');
						}
						if(!$isFetch)
						{
							if($meta->getAttribute('rel') == 'videothumbnail')
							{
								$html_imgs .= "<img src='".$meta->getAttribute('href')."' width='100' id='".$k."' >";
								if($k == 1)
								{
									$furl =$meta->getAttribute('href');
									$isFetch = true;
								}
								$k++;
							}
						}
					}
					
			}
			if($is_video == false)
			{
				
                $imgs = $doc->getElementsByTagName('img');
				$count = $imgs->length;

				if($count>20)
				{
					$count = 20;
				}
				for ($i = 0; $i < $count; $i++)
				{
					$meta = $imgs->item($i);
					$src = $meta->getAttribute('src');
					$img_src_info = parse_url($src);
					if(!isset($img_src_info['host']))
					{
						$com_src = 'http://'.$url_info['host'].$src;
					}
					else
					{
						$com_src = $src;
					}
					if($com_src !='' )
						{
							$images[$image_counter++] = $com_src;
                                                        return $images;
							list($width, $height, $type, $attr) = @getimagesize($com_src);
							if($width >= 30 && $height >= 30 )
							{
                                                                
								$html_imgs .= "<img src='".$com_src."' width='100' id='".$k."' >";
								if($k == 1)
								{
									$furl =$com_src;
								}
								$k++;
							}
						}
				}
				
				/*echo'<pre>';
						print_r($images);
				echo '</pre>';*/
			}
			elseif($is_video == true)
			{
				if(!$isFetch)
				{
					$imgs = $doc->getElementsByTagName('img');
					$count = $imgs->length;
					if($count>12)
					{
						$count = 12;
					}
					for ($i = 0; $i < $count; $i++)
					{
						$meta = $imgs->item($i);
						$src = $meta->getAttribute('src');
						$img_src_info = parse_url($src);
						if(!isset($img_src_info['host']))
						{
							$com_src = 'http://'.$url_info['host'].$src;
						}
						else
						{
							$com_src = $src;
						}
						if($com_src !='' )
							{
                                                            $images[$image_counter++] = $com_src;
                                                            return $images;
								list($width, $height, $type, $attr) = @getimagesize($com_src);
								if($width >= 30 && $height >= 30 )
								{
                                                                    
                                                                    $html_imgs .= "<img src='".$com_src."' width='100' id='".$k."' >";
									if($k == 1)
									{
										$furl =$com_src;
									}
									$k++;
								}
							}
					}

				}
			}
			
			if($flag == 0)
			{

			if($html_imgs!='')
			{
				$html2 .= $html_imgs;
			}
			else
			{
				$html2 = '<div style="display:none">';
			}
			$html2 .= '<input type="hidden" name="total_images" id="total_images_img" value="'. --$k.'" />
					<input type="hidden" name="cur_image" id="cur_image"  value="'.($furl!=''?1:0).'" />
					<input type="hidden" name="url_image" id="url_image" value="'.(isset($furl)?$furl:'').'" />
					<input type="hidden" name="url_video" id="url_video" value="'.urlencode($video_url).'" />
				</div>';

			$total_img = 0;
			$total_img = $k;
			if($title=='')
			{
				$title = $url;
			}
			$html= '<div class="link_info">
				<label id="link_title_lable" class="title">
				'. @strip_tags($title).'</label>
				<input type="text" style="display:none; width:300px;" name="link_feed_title" id="link_feed_title" value="'.@strip_tags($title).'"/>

				<br clear="all" />
				<label class="url">'.substr($url ,0,200).'</label>
				<br clear="all" /><br clear="all" />
				<label id="link_desc_lable" class="desc">'.@strip_tags($description).'</label>
				<textarea style="display:none" rows="3" cols="45" name="link_feed_desc" id="link_feed_desc">'.@strip_tags($description).'</textarea>
				<span id="nav_img_btn" style="display:'.(($k>=1)?'block':'none').'">
				<br clear="all" /><br clear="all" />
				<label style="float:left;width:53px"><img src="'.base_url().'imgs/prev_lite.png" id="prev" onclick="prev_imgs();" alt="" /><img src="'.base_url().'imgs/'.(($k==1)?'next_lite.png':'next.png').'" id="next" onclick="next_imgs();"  alt="" /></label>
				<label class="totalimg"><span style="color:#000000"><span id="selected_image">1</span> of '.($total_img).'</span> Choose a Thumbnail</label>
				<br clear="all" />
				</span>
				<span id="no_thumb" style="display:'.(($k>=1)?'block':'none').'">
				<label style="float:left;padding-top: 5px;"><input type="checkbox" value="1" name="no_thumbnail" id="no_thumbnail"/></label> <label class="totalimg">No-Thumbnail</label>
				<br clear="all" />
				</span>

				<script language="javascript">
				$(document).ready(function () {
					$("#no_thumbnail").click(function(){
						if($("#no_thumbnail").attr("checked"))
						{
							$("#nav_img_btn").hide();
							$(".link_images img").hide();
						}
						else
						{
							$(".link_images img").hide();
							$("#nav_img_btn").show();
							$("img#1").fadeIn();

						}
						});
				$("#link_title_lable").click(function()
					{
						$("#link_title_lable").hide();
						$("#link_feed_title").show();
						$("#link_feed_title").focus();
					});
					$("#link_feed_title").blur(function(){
						$("#link_title_lable").text($("#link_feed_title").val());
						$("#link_title_lable").show();
						$("#link_feed_title").hide();

						});
					$("#link_desc_lable").click(function()
					{
						$("#link_desc_lable").hide();
						$("#link_feed_desc").show();
						$("#link_feed_desc").focus();
					});
					$("#link_feed_desc").blur(function(){
						$("#link_desc_lable").text($("#link_feed_desc").val());
						$("#link_desc_lable").show();
						$("#link_feed_desc").hide();
						});
					});
				</script>
				</div>

				';

				$fhtml = $html2.$html;
				
			}
//				echo json_encode(array('result'=>'success','data'=> $fhtml));
//				exit();
		}catch (Exception $e)
		{
//		    echo json_encode(array('result'=>'error','data'=>$e->getMessage()));
//			exit();
		}
		return $images;
	}
        function checkValues($value)
	{
		$value = trim($value);
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		$value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
		$value = strip_tags($value);
		$value = htmlspecialchars($value);
		return $value;
	}
        function file_get_contents_curl($url)
	{

		$userAgent ="Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/419.2.1 (KHTML, like Gecko) Safari/419.3" ;
		//$userAgent ="Googlebot/2.1 (http://www.googlebot.com/bot.html)" ;
		$userAgent ="Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)";
		$userAgent ="Googlebot-Image/1.0 ( http://www.googlebot.com/bot.html)";
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		$data = curl_exec($ch);
		$response = curl_getinfo( $ch );
	    curl_close($ch);
	    return $data;
	}
?>
