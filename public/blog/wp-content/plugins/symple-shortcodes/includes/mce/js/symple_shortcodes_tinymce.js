(function() {	
	tinymce.create('tinymce.plugins.sympleShortcodeMce', {
		init : function(ed, url){
			//do nothing
		},
		createControl : function(btn, e) {
			if ( btn == "symple_shortcodes_button" ) {
				var a = this;	
				var btn = e.createSplitButton('symple_button', {
	                title: "Insert Shortcode",
					image: sympleShortcodesVars.template_url +"/images/shortcodes.png",
					icons: false,
	            });
	            btn.onRenderMenu.add(function (c, b) {
					a.render( b, "Accordion", "accordion" );
					a.render( b, "Box", "box" );
					a.render( b, "Button", "button" );
					a.render( b, "Clear Floats", "clear" );
					a.render( b, "Column", "column" );
					a.render( b, "Divider", "divider" );
					a.render( b, "Google Map", "googlemap" );
					a.render( b, "Heading", "heading" );
					a.render( b, "Highlight", "highlight" );
					a.render( b, "Pricing", "pricing" );
					a.render( b, "Spacing", "spacing" );
					a.render( b, "Social Icon", "social" );
					a.render( b, "Tabs", "tabs" );
					a.render( b, "Testimonial", "testimonial" );
					a.render( b, "Toggle", "toggle" );
				});
	            
	          return btn;
			}
			return null;               
		},
		render : function(ed, title, id) {
			ed.add({
				title: title,
				onclick: function () {
					
					// Accordion
					if(id == "accordion") {
						tinyMCE.activeEditor.selection.setContent('[symple_accordion]<br />[symple_accordion_section title="Section 1"]<br />Accordion Content<br />[/symple_accordion_section]<br />[symple_accordion_section title="Section 2"]<br />Accordion Content<br />[/symple_accordion_section]<br />[/symple_accordion]');
					}
					
					// Box
					if(id == "box") {
						tinyMCE.activeEditor.selection.setContent('[symple_box color="yellow" text_align="left" width="100%" float="none"]<br />Box Content<br />[/symple_box]');
					}
					
					// Button
					if(id == "button") {
						tinyMCE.activeEditor.selection.setContent('[symple_button color="blue" url="http://www.wpexplorer.com" title="Visit Site" target="blank" border_radius=""]Button Text[/symple_button]');
					}
					
					// Clear Floats
					if(id == "clear") {
						tinyMCE.activeEditor.selection.setContent('[symple_clear_floats]');
					}
					
					// Column
					if(id == "column") {
						tinyMCE.activeEditor.selection.setContent('[symple_column size="one-half" position="first"]<br />Content<br />[/symple_column]');
					}
				
					// Divider
					if(id == "divider") {
						tinyMCE.activeEditor.selection.setContent('[symple_divider style="solid" margin_top="20px" margin_bottom="20px"]');
					}
					
					// Google Map
					if(id == "googlemap") {
						tinyMCE.activeEditor.selection.setContent('[symple_googlemap title="Envato Office" location="2 Elizabeth St, Melbourne Victoria 3000 Australia" zoom="10" height=250]');
					}
					
					// Heading
					if(id == "heading") {
						tinyMCE.activeEditor.selection.setContent('[symple_heading type="h2" title="This is my title" margin_top="20px;" margin_bottom="20px"]');
					}
					
					// Highlight
					if(id == "highlight") {
						tinyMCE.activeEditor.selection.setContent('[symple_highlight color="yellow"]highlighted text[/symple_highlight]');
					}
					
					// Pricing
					if(id == "pricing") {
						tinyMCE.activeEditor.selection.setContent('[symple_pricing_table]<br />[symple_pricing size="one-half" plan="Basic" cost="$19.99" per="per month" button_url="#" button_text="Sign Up" button_color="gold" button_border_radius="" button_target="self" button_rel="nofollow" position=""]<br /><ul><li>30GB Storage</li><li>512MB Ram</li><li>10 databases</li><li>1,000 Emails</li><li>25GB Bandwidth</li></ul>[/symple_pricing]<br />[/symple_pricing_table]');
					}
					
					//Spacing
					if(id == "spacing") {
						tinyMCE.activeEditor.selection.setContent('[symple_spacing size="40px"]');
					}
					
					//Social
					if(id == "social") {
						tinyMCE.activeEditor.selection.setContent('[symple_social icon="twitter" url="http://www.twitter.com/wpexplorer" title="Follow Us" target="self" rel=""]');
					}
					
					//Tabs
					if(id == "tabs") {
						tinyMCE.activeEditor.selection.setContent('[symple_tabgroup]<br />[symple_tab title="First Tab"]<br />First tab content<br />[/symple_tab]<br />[symple_tab title="Second Tab"]<br />Third Tab Content.<br />[/symple_tab]<br />[/symple_tabgroup]');
					}
					
					//Testimonial
					if(id == "testimonial") {
						tinyMCE.activeEditor.selection.setContent('[symple_testimonial by="WPExplorer"]Your testimonial[/symple_testimonial]');
					}
					
					//Toggle
					if(id == "toggle") {
						tinyMCE.activeEditor.selection.setContent('[symple_toggle title="This Is Your Toggle Title"]Your Toggle Content[/symple_toggle]');
					}
					
					
					return false;
				}
			})
		}
	
	});
	tinymce.PluginManager.add("symple_shortcodes", tinymce.plugins.sympleShortcodeMce);
})();  