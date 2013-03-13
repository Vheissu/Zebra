<!DOCTYPE HTML>  
<html lang="en">  
<head>  
    <meta charset="utf-8"> 
    <base href="{base_url()}">

    <title>{$page.title|default:$site.name}</title> 

    {if $page.description}
    <meta name="description" content="{$page.description}">  
    {/if}
 
    {css('zebra.css')}
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/flick/jquery-ui.css" type="text/css" media="all">

    {block name=header_styles}{/block}

    <script type="text/javascript">
        var base_url   = "{base_url()}";
    </script>

    {block name=header_scripts}{/block}

    <!--[if lt IE 9]>  
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>  
    <![endif]-->  
</head>  
<body>

    <div id="container">

        <header>
            <a href="{base_url()}" id="logo">{$site.name}</a>

            <nav>
              <ul>
                  <li><a href="{base_url('stories/new')}" {if $current_segment == 'new'}class="current"{/if}>New</a></li>
                  <li><a href="{base_url('comments/latest')}" {if $current_segment == 'comments'}class="current"{/if}>Comments</a></li>
                  <li><a href="{base_url('story/submit')}" {if $current_segment == 'submit'}class="current"{/if}>Submit</a></li>
              </ul>   
            </nav>

            <nav id="meta-links">
            {if logged_in()}
            <a href="user/{strtolower(get_userdata('username'))}">{get_userdata('nice_username')} ({get_user_karma()})</a> | <a href="logout">Logout</a>
            {else}
            <a href="login">Login</a> <span>|</span> <a href="register">Register</a>
            {/if}
            </nav>

        </header>

        <div id="content">
            {block name=content}{/block}
        </div>

    </div>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js" type="text/javascript"></script>
    {js('backbone.js')}
    {js('zebra.js')}

    {block name=footer}{/block}

    {if isset($story) || isset($stories)}
    <div id="downvotereason" title="Why are you downvoting?" style="display:none;">
        {assign var="downvote_reasons" value=get_downvote_reasons()}
        <h2>Please select a reason for downvoting:</h2>
        <select id="downvote_reason" name="downvote_reason">
            <option value="0" selected="selected">Select a downvote reason</option>
            {foreach $downvote_reasons AS $reason}
            <option value="{$reason->id}">{$reason->reason}</option>
            {/foreach}
        </select>
    </div>
    {/if}

</body>  
</html>