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
                    <input type="text" size="40" name="author_name" id="input-author_name" value="{$item.author_name}">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-email">{lang key='author_email'}</label>
                <div class="col col-lg-4">
                    <input type="text" id="email" name="email" size="32" value="{$item.email}">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-author_url">{lang key='author_url'}</label>
                <div class="col col-lg-4">
                    <input type="text" id="author_url" name="author_url" size="32" value="{$item.author_url}">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
                <div class="col col-lg-8">
                    {ia_wysiwyg name="body" value=$item.body}
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-image">{lang key='avatar'}</label>
                <div class="col col-lg-4">
                    {if isset($item.avatar) && $item.avatar}
                        <div class="input-group thumbnail thumbnail-single with-actions">
                            <a href="{ia_image file=$item.avatar type='large' url=true}" rel="ia_lightbox">
                                {ia_image file=$item.avatar}
                            </a>

                            <div class="caption">
                                <a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$item.avatar}', this, 'guestbook', 'avatar', '{$id}')"><i class=" i-remove-sign"></i></a>
                            </div>
                        </div>
                    {/if}

                    {ia_html_file name='avatar' id='input-avatar'}
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-date">{lang key='date'}</label>
                <div class="col col-lg-4">
                    <div class="input-group">
                        <input type="text" class="js-datepicker" name="date" id="input-date" value="{$item.date}">
                        <span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-status">{lang key='status'}</label>
                <div class="col col-lg-4">
                    <select name="status" id="input-status">
                        <option value="active"{if $item.status == 'active'} selected="selected"{/if}>{lang key='active'}</option>
                        <option value="inactive"{if $item.status == 'inactive'} selected="selected"{/if}>{lang key='inactive'}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions inline">
            <input type="submit" name="save" class="btn btn-primary" value="{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}">
        </div>
    </div>
</form>

{ia_add_media files='datepicker' order=2}