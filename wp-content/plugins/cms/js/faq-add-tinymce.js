(function() {
		  	
	tinymce.create('tinymce.plugins.FaqPlugin', {
				   
		init : function(ed, url) {
			
			ed.addCommand('mceFaqInsert', function() {
				
				
				ed.windowManager.open({
					title : "Insert FAQ",
					url : '?page=faq-questions&insert=true',
					width : 300,
					height : 10
				});
								
			});
			
			ed.addButton('faq', {
				title : 'Insert FAQ',
				cmd : 'mceFaqInsert',
				image : url + '/../images/faq-icon_tinymce.png'
			});
			
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('faq', n.nodeName == 'IMG');
			});
			
		},

		createControl : function(n, cm) {
			return null;
		},
		
		getInfo : function() {
			return {
				longname : 'FAQ',
				author : 'Ontwerpstudio Trendwerk',
				authorurl : 'http://www.trendwerk.nl',
				infourl : 'http://plugins.trendwerk.nl',
				version : '1.0'
			};
		}
		
	});
	
	tinymce.PluginManager.add('faq', tinymce.plugins.FaqPlugin);

})(); 