<div class="{$ElementStyles}">
    <div class="featured-video-element__content">
	    <% if $ShowTitle %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
	   $HTML
        <ul class="media-list media-release-list">
            <% loop $RecentPosts %>
                <li class="media $FirstLast">
                <div class="media-body">
                    <h4 class="media-heading"><a href="$Link" title="More information about $Title">$MenuTitle.XML</a></h4>
                    <p class="small">$Date.Full</p>
                </div>
            </li>
            <% end_loop %>
        </ul>
        <p class="more">
            <a title="View more news" href="$MediaHolder.Link">
                <% if $MediaHolderLinkTitle %>{$MediaHolderLinkTitle}<% else %>View more news<% end_if %> <span aria-hidden="true" class="fa fa-chevron-right"></span>
            </a>
        </p>
    </div>
</div>
