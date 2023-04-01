<header role="banner" class="clear">
    <div class="container">
    
		    <div class="leftCol clear">
            <div class="logo">
				        <a href="/"><img src="images/logo-sticky.png" alt="TrackStreet" width="135" height="39"></a>
            </div>
            <div class="tagline">
                Smarter Business tools for stickier, expansive business
            </div>
        </div>
        
        <div class="rightCol clearAfter">
				    <div>
                <?php if (isset($this->logged_in)): ?>
    					      Welcome back <?=$user_name?>! &nbsp;|&nbsp;
		    			      <a href="/account/profile">My Account</a> &nbsp;|&nbsp;
					          <a href="<?=site_url('logout')?>">Logout</a>
					      <?php else: ?>
					          <a href="/login">Login</a>
					      <?php endif; ?>    
				    </div>
				    <?php if (isset($this->logged_in)): ?>
    				    <div id="bookmarks" class="dropdown">
    					      <div class="dropdownToggle">
    					          My Bookmarks
    					      </div>
    					      <div class="dropdownMenu">
    						        <div class="dropdownBg">
    							          <?php
    							          if (isset($bookmarks)):
    								            $n = count($bookmarks);
    								            if ($n > 0):
    									              for ($i = 0, $n; $i < $n; $i++): ?>
    										                <a href="<?=$bookmarks[$i]->shortcut_url?>" data-id="<?=$bookmarks[$i]->id?>"><?=$bookmarks[$i]->shortcut_name?></a><?php
    									              endfor;
    								            else:
    								            ?>
    								            <div id="noBM">No Active Bookmarks</div>
    							              <?php endif;?>
                            <?php endif;?>
    						            <div class="shadowBottom"></div>
    					          </div>
    					          <div class="shadowBottomL"></div>
                    </div>
    				        <div id="bookmarkDeleteTemplate" class="hidden">
    					          <span class="bookmarkDelete" title="Remove Bookmark">X</span>
    				        </div>
                </div>
            <?php endif; ?>        
		    </div>
    </div>
</header>