<ul class="tabNav clear">
    <li>
        <div class="tabCornerL"></div><a class="tabItem" href="/account/profile">Profile</a>
    </li>
    <?php if ($this->role_id == 0 || $this->role_id == 2): ?>
        <li>
            <div class="tabCornerL"></div><a class="tabItem" href="/account/team">Team</a>
        </li>
    <?php endif; ?>    
    <li>
        <div class="tabCornerL"></div><a class="tabItem" href="/account/change_password">Change Password</a>
    </li>
</ul>

<div id="tab1" class="tabContent">
    <div class="content clear">
        <div class="topLeft"></div>
        <div class="topRight"></div>
        <div class="whiteArea">
            <div class="topLeft"></div>
            <div class="topRight"></div>
            <section class="clear" id="accounts-main-area">


