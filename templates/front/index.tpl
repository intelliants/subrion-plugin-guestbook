{if $guestbook}
	<div class="media-items">
		{foreach $guestbook as $message}
			<div class="media">
				<div class="media-body">
					{if !$core.config.html_guestbook && $core.config.gb_auto_approval}
						{$message.body|escape:'html'}
					{else}
						{$message.body}
					{/if}
				</div>
				<div class="media-info">
					<span class="fa fa-user"></span>
					{if !$message.member_id}
						<a href="{$message.author_url|escape:'html'}" rel="nofollow">{$message.author|escape:'html'}</a>
					{else}
						{ia_url type='link' item='members' data=$message text=$message.author}
					{/if}

					&nbsp; <span class="fa fa-calendar"></span> {$message.date|date_format:$core.config.date_format}

					{if iaCore::STATUS_INACTIVE == $message.status && ($message.sess_id == $sess_id)}<span class="label label-warning">{lang key='message_approval'}</span>{/if}
				</div>
			</div>
		{/foreach}
	</div>
{else}
	<div class="alert alert-info">{lang key='no_guestbook_messages'}</div>
{/if}

{navigation aTotal=$total_messages aTemplate=$aTemplate aItemsPerPage=$core.config.gb_messages_per_page aNumPageItems=5 aTruncateParam="guestbook/%page%"}

{if !$core.config.gb_account_submissions_only || $member}
	<form action="" method="post" id="guestbook" class="ia-form ia-form--bordered">
		{preventCsrf}
		<div class="fieldset">
			<div class="fieldset__header">{lang key='add_message'}</div>

			<div class="fieldset__content">
				{if $member}
					<input type="hidden" name="author" value="{$member.username}">
					<input type="hidden" name="email" value="{$member.email}">
					<input type="hidden" name="aurl" value="">
				{else}
					<div class="form-group">
						<label for="message-author">{lang key='message_author'}:</label>
						<input class="form-control" type="text" id="message-author" name="author" value="{if isset($smarty.post.author)}{$smarty.post.author|escape:'html'}{/if}">
					</div>
					<div class="form-group">
						<label for="author-email">{lang key='author_email'}:</label>
						<input class="form-control" type="text" id="author-email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'html'}{/if}">
					</div>
					<div class="form-group">
						<label for="author-url">{lang key='author_url'}:</label>
						<input class="form-control" type="text" id="author-url" name="aurl" value="{if isset($smarty.post.aurl)}{$smarty.post.aurl|escape:'html'}{else}http://{/if}">
					</div>
				{/if}

				<div class="form-group">
					{if !$member}
						<label for="guestbook_form">{lang key='msg'}:</label>
					{/if}

					{if $core.config.html_guestbook}
						{ia_wysiwyg value=$body name=message}
					{else}
						<textarea name="message" class="form-control" rows="8" id="guestbook_form">{$body}</textarea>
						{ia_print_js files='jquery/plugins/jquery.textcounter'}
					{/if}
				</div>

				{include file='captcha.tpl'}

				<input type="hidden" name="action" value="add">
			</div>

			<div class="form-actions">
				<button type="submit" id="add_message" name="add_message" class="btn btn-primary">{lang key='leave_message'}</button>
			</div>
		</div>
	</form>
{/if}

{ia_print_js files='ckeditor/ckeditor, _IA_URL_plugins/guestbook/js/front/index'}