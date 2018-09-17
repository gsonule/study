(function(a) {
	a.fn.loader = function(c) {
		var g = {
			loader: "on",
			img_url: "images/loader/circularloader.gif",
			effect: "blur"
		};
		var e = a.extend(g, c);
		var b = e.loader;
		var f = e.img_url;
		var d = e.effect;
		this.each(function(k) {
			var p = a(this);
			var o = p.attr("id");
			if (!o) {
				o = k
			}
			var l = o;
			var j = "loader_div_" + l;
			var n = "loader_img_" + l;
			if (b == "on") {
				var m = a(document.createElement("div"));
				m.attr({
					id: j
				});
				var h = a(document.createElement("img"));
				h.attr({
					id: n,
					src: f + "?" + l
				});
				a(document.body).append(m);
				m.html(h);
				if (p.length > 0 && d == "blur") {
					p.css({
						opacity: "0.5",
						filter: "alpha(opacity=50)"
					})
				}
				h.on("load",function() {
					var i = a.get_center_axis(p, h);
					var q = i.y;
					var r = i.x;
					m.css({
						position: "absolute",
						top: q,
						left: r,
						"z-index": "2"
					});
					m.fadeIn("1000")
				}).on("error",(function() {}))
			} else {
				if (b == "off") {
					if (p.length > 0) {
						p.css({
							opacity: "1",
							filter: "alpha(opacity=100)"
						})
					}
					a("#" + j).fadeOut("1000", function() {
						a("#" + j).remove()
					})
				}
			}
		})
	};
	a.ui_alert = function(r) {
		var h = {
			msg: "",
			base_img_url: "images/ui_alert",
			img_name: "select_mark.png",
			auto_close: true,
			auto_close_duration: 3000,
			close_btn_display: true
		};
		var g = a.extend(h, r);
		var d = g.msg;
		var c = g.base_img_url;
		var j = g.img_name;
		var m = g.auto_close;
		var q = g.auto_close_duration;
		var b = g.close_btn_display;
		var o = c + "/icon";
		var l = "";
		var n = a(document.createElement("div"));
		n.attr({
			id: "ui_alert_container"
		});
		n.css({
			background: "url(" + c + "/ui_alert_bg.png)"
		});
		if (b) {
			var p = "<div id='ui_alert_close'><a href='javascript:void(0);' onclick='$.ui_alert_close()'><img src='" + c + "/ui_alert_close.png' /></a></div> <div id='ui_alert_text'></div>"
		} else {
			var p = "<br/><div id='ui_alert_text'></div>"
		}
		if (a("#ui_alert_container").length > 0) {
			a.ui_alert_close("fast")
		}
		a(document.body).append(n);
		n.html(p);
		a("#ui_alert_text").css("background-image", "url(" + o + "/" + j + ")");
		a("#ui_alert_text").html(d);
		var i = a(window);
		var f = a.get_center_axis(i, n);
		var e = f.y;
		var k = f.x;
		n.css({
			position: "fixed",
			top: e,
			left: k
		});
		if (m) {
			l = window.setTimeout(function() {
				a.ui_alert_close()
			}, q)
		}
		a.ui_alert_close = function(s) {
			if (l) {
				clearTimeout(l)
			}
			if (s) {
				n.remove()
			} else {
				n.fadeOut("1000", function() {
					n.remove()
				})
			}
		}
	};
	a.get_center_axis = function(i, c) {
		var f = i.outerHeight();
		var l = i.outerWidth();
		if (i.offset()) {
			var d = (i.offset().top);
			var g = (i.offset().left)
		} else {
			var d = 0;
			var g = 0
		}
		var j = (g + l);
		var h = (d + f);
		var k = c.outerHeight();
		var b = c.outerWidth();
		var e = {};
		e.y = h - (f / 2) - (k / 2);
		e.x = j - (l / 2) - (b / 2);
		return e
	}
})(jQuery);