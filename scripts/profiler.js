var PTB =
{
	COOKIE_VISIBLE: 'PTB_visible',
	COOKIE_VISIBLE_ITEM: 'PTB_visible_item',
	COOKIE_VISIBLE_TAB: 'PTB_visible_tab',
	VISIBLE: false,
	VISIBLE_ITEM: null,
	VISIBLE_TAB: null,

	init: function ()
	{
		$('#ptb_toolbar > li').click(function() { PTB.onClickToolbarEl(this) });

		$('.ptb_tabs > li').each(function()
		{
			$(this).click(function(){ PTB.onClickDataTab(this) });
		});

		if(PTB.getCookie(PTB.COOKIE_VISIBLE) == 'true')
			PTB.toggleToolbar();
		if((tmp = PTB.getCookie(PTB.COOKIE_VISIBLE_TAB)) != undefined)
			PTB.toggleTab(tmp);
		if((tmp = PTB.getCookie(PTB.COOKIE_VISIBLE_ITEM)) != undefined)
			PTB.toggleToolbarItem(tmp);
	},

	onClickToolbarEl: function(el)
	{
		if(el.nodeName.toLowerCase() != 'li')
			el = el.parentNode;

		switch (el.className)
		{
			case 'hide':
			case 'show': PTB.toggleToolbar(); break;
			case 'info': break;
			default: PTB.toggleToolbarItem('ptb_data_cont_'+el.className);
		}
	},

	onClickDataTab: function(el)
	{
		if(el.nodeName.toLowerCase() == 'span')
			PTB.toggleTab(el.parentNode.id);
		else
			PTB.toggleTab(el.id);
	},

	toggleToolbar: function()
	{
		var items  = $('#ptb_toolbar').children();

		for (var i = 0; i < items.length; i++)
		{
			if ($(items[i]).prop("nodeName").toLowerCase() == 'li')
				$(items[i]).css('display', (PTB.VISIBLE) ? 'none' : 'block');
		}

		$(items[items.length-2]).css('display', (PTB.VISIBLE) ? 'block' : 'none');
		$('#ptb_data').css('display', (PTB.VISIBLE) ? 'none' : 'block');

		PTB.VISIBLE = !PTB.VISIBLE;
		PTB.setCookie(PTB.COOKIE_VISIBLE, PTB.VISIBLE);
	},

	toggleToolbarItem:function(id)
	{
		var el = $('#'+id);
		if (el === null)
			return;

		if(el.attr('id') == PTB.VISIBLE_ITEM)
		{
			el.hide();
			PTB.VISIBLE_ITEM = null;
			PTB.setCookie(PTB.COOKIE_VISIBLE_ITEM,null);
		}
		else
		{
			$('.ptb_data_cont').each(function()
			{
				$(this).hide();
			});

			el.show();

			PTB.VISIBLE_ITEM = id;
			PTB.setCookie(PTB.COOKIE_VISIBLE_ITEM, PTB.VISIBLE_ITEM);

			var tabs = el[0].childNodes[1].childNodes;
			var open = false;
			for (var i = 0; i < tabs.length; i++)
			{
				if (PTB.VISIBLE_TAB !== null && tabs[i].id == PTB.VISIBLE_TAB)
				{
					open = true;
					break;
				}
			}
			if(!open)
				PTB.toggleTab(tabs[1].id);
			else
				PTB.updateDataContPosition();
		}
	},

	toggleTab: function(id)
	{
		var tabName = id.substr('ptb_tab_'.length);

		var tabs = $('.ptb_tabs');
		for (var i = 0; i < tabs.length; i++)
		{
			for (var j = 0; j < tabs[i].childNodes.length; j++)
			{
				if (tabs[i].childNodes[j].nodeName.toLowerCase() == 'li')
					$(tabs[i].childNodes[j]).removeClass('use');
			}
		}

	    $('#'+id).addClass('use');

		$('.ptb_tab_cont').hide();

		var cont = $('#ptb_tab_cont_'+tabName);
		if (cont != null)
		{
			cont.show();
			PTB.VISIBLE_TAB = id;
			PTB.setCookie(PTB.COOKIE_VISIBLE_TAB, PTB.VISIBLE_TAB);
		}
		PTB.updateDataContPosition();
	},

	updateDataContPosition:function()
	{
		var cont = $('#ptb_data');
		if (cont.outerHeight() > $(window).height()){
			cont.css('position', 'absolute');
		} else {
			cont.css('position', 'fixed');
		}
	},
	/* ---------- help ---------- */
	setCookie: function(name, val)
	{
		if (html5_storage())
			localStorage.setItem(name, val);
		else
		{
			var date = new Date();
			date.setDate(date.getDate() + 7);
			document.cookie = name+"="+val+"; expires="+date.toGMTString()+"; path=/;";
		}
	},
	delCookie: function(name)
	{
		if (html5_storage())
			localStorage.removeItem(name);
		else
		{
			var date = new Date();
			date.setTime(date.getTime()-1);
			document.cookie = name += "=; expires="+date.toGMTString();
		}
	},
	getCookie: function(name)
	{
		if (html5_storage())
			return localStorage.getItem(name);
		else
		{
			var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
			return matches ? decodeURIComponent(matches[1]) : undefined
		}
	}
};

$(document).ready(function()
{
	PTB.init();
});