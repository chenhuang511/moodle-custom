define(['strut/deck/Component'],
	function(Component) {
		'use strict';

		function getInitialText(attrs) {
			if (!attrs)
				return 'Text';
			else {
				var text = '<font';
				for (var style in attrs) {
					if (style == 'size') continue;
					else text += " ";

					text += style + '="' + attrs[style] + '"';
				}
				return text + '>Table Name</font>'
			}
		}

		/**
		 * @class Table
		 * @augments Component
		 */
		return Component.extend({
			initialize: function() {
				Component.prototype.initialize.apply(this, arguments);
				this.set('type', 'Table');
				if (!this.get('text')) {
					var text = getInitialText(this._opts && this._opts.fontStyles);
					if (this._opts && this._opts.fontStyles.size)
						this.set('size', this._opts.fontStyles.size);
					delete this._opts;
					this.set('text', text);
					if (!this.get('size'))
						this.set('size', 32);
				}
			},

			constructor: function Table(attrs, opts) {
				this._opts = opts;
				Component.prototype.constructor.call(this, attrs);
			}
		});
	});
