Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/guestbook/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.guestbook =
		{
			columns: [
				'selection',
				'expander',
				{name: 'author_name', title: _t('author'), width: 2},
				{name: 'email', title: _t('email'), width: 250},
				'status',
				{name: 'date', title: _t('date'), width: 120, editor: 'date'},
				'update',
				'delete'
			],
			expanderTemplate: '{body}',
			fields: ['body'],
			sorters: [{property: 'date', direction: 'DESC'}],
			storeParams: urlParam ? {status: urlParam} : null,
			url: pageUrl
		};

		intelli.guestbook = new IntelliGrid(intelli.guestbook, false);
		intelli.guestbook.toolbar = Ext.create('Ext.Toolbar', {items:[
		{
			emptyText: _t('text'),
			name: 'text',
			listeners: intelli.gridHelper.listener.specialKey,
			width: 275,
			xtype: 'textfield'
		},{
			displayField: 'title',
			editable: false,
			emptyText: _t('status'),
			id: 'fltStatus',
			name: 'status',
			store: intelli.guestbook.stores.statuses,
			typeAhead: true,
			valueField: 'value',
			xtype: 'combo'
		},{
			handler: function(){intelli.gridHelper.search(intelli.guestbook);},
			id: 'fltBtn',
			text: '<i class="i-search"></i> ' + _t('search')
		},{
			handler: function(){intelli.gridHelper.search(intelli.guestbook, true);},
			text: '<i class="i-close"></i> ' + _t('reset')
		}]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.guestbook.init();
	}
});