<form action="" method="post" enctype="multipart/form-data" class="sap-form form-horizontal">

	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='options'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-author_name">{lang key='message_author'}</label>
				<div class="col col-lg-4">
					<input type="text" size="40" name="author_name" id="input-author_name" value="{$guestbook.author_name}"/>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-email">{lang key='author_email'}</label>
				<div class="col col-lg-4">
					<input type="text" id="email" name="email" size="32" value="{$guestbook.email}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-author_url">{lang key='author_url'}</label>
				<div class="col col-lg-4">
					<input type="text" id="author_url" name="author_url" size="32" value="{$guestbook.author_url}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name="body" value=$guestbook.body}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-date">{lang key='date'}</label>
				<div class="col col-lg-4">
					<div class="input-group">
						<input type="text" class="js-datepicker" name="date" id="input-date" value="{$guestbook.date}">
						<span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
					</div>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-status">{lang key='status'}</label>
				<div class="col col-lg-4">
					<select name="status" id="input-status">
						<option value="active"{if $guestbook.status == 'active'} selected="selected"{/if}>{lang key='active'}</option>
						<option value="inactive"{if $guestbook.status == 'inactive'} selected="selected"{/if}>{lang key='inactive'}</option>
					</select>
				</div>
			</div>
		</div>

		<div class="form-actions inline">
			<input type="hidden" value="edit" name="action"/>
			<input type="submit" name="save" class="btn btn-primary" value="{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}">
		</div>
	</div>
</form>

{ia_add_media files='datepicker' order=2}