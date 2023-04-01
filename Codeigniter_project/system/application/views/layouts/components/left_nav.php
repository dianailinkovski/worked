<aside id="left-panel">
    <nav>
        <ul>
            <?php
            
            foreach ($this->left_nav as $key => $nav_item) 
            {
            		//process parent nav
            		$nav_htm = '';
            		
            		$url = isset($nav_item["url"]) ? $nav_item["url"] : "#";
            		
            		$url_target = isset($nav_item["url_target"]) ? 'target="'.$nav_item["url_target"].'"' : "";
            		
            		$icon_badge = isset($nav_item["icon_badge"]) ? '<em>'.$nav_item["icon_badge"].'</em>' : '';
            		
            		$icon = isset($nav_item["icon"]) ? '<i class="fa fa-lg fa-fw '.$nav_item["icon"].'">'.$icon_badge.'</i>' : "";
            		
            		$nav_title = isset($nav_item["title"]) ? $nav_item["title"] : "(No Name)";
            		
            		$label_htm = isset($nav_item["label_htm"]) ? $nav_item["label_htm"] : "";
            		
            		$nav_htm .= '<a href="'.$url.'" '.$url_target.' title="'.$nav_title.'">'.$icon.' <span class="menu-item-parent">'.$nav_title.'</span>'.$label_htm.'</a>';
            
            		if (isset($nav_item["sub"]) && $nav_item["sub"])
            		{
            		    $nav_htm .= process_sub_nav($nav_item["sub"]);
            		}
            		
            		echo '<li '.(isset($nav_item["active"]) ? 'class = "active"' : '').'>'.$nav_htm.'</li>' . "\n";
            }
            
            ?>
        </ul>
    </nav>
			
    <!-- <span class="minifyme" data-action="minifyMenu"><i class="fa fa-arrow-circle-left hit"></i></span> -->

</aside>