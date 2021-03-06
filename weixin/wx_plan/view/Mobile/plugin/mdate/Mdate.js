(function() {
	var d = document;
	var includeCss = function(url) {
		var link = d.createElement("link");
		link.rel = "stylesheet";
		link.type = "text/css";
		link.href = url;
		d.getElementsByTagName("head")[0].appendChild(link)
	};
	var dateopts = {
		beginYear: 2018,
		beginMonth: 1,
		beginDay: 1,
		endYear: 2030,
		endMonth: 1,
		endDay: 1,
		format: "YMD",
		onPreDateShow:function(){},
		onOkDown:function(selectedDate){},
	};
	var MdSelectId = "";
	var MdAcceptId = "";
	var dateContentBox = "";
	var datePlugs = "";
	var yearTag = "";
	var monthTag = "";
	var dayTag = "";
	var indexY = 1;
	var indexM = 1;
	var indexD = 1;
	var initM = null;
	var initD = null;
	var yearScroll = null;
	var monthScroll = null;
	var dayScroll = null;
	var onPreDateShow=function(){
	};
	var onOkDown=function(selectedDate){

	};
	var Mdate = function(el, opts) {
		if (!opts) {
			opts = {}
		}
		this.id = el;
		this.selectorId = d.getElementById(this.id);
		this.acceptId = d.getElementById(opts.acceptId) || d.getElementById(this.id);
		this.beginYear = opts.beginYear || dateopts.beginYear;
		this.beginMonth = opts.beginMonth || dateopts.beginMonth;
		this.beginDay = opts.beginDay || dateopts.beginDay;
		this.endYear = opts.endYear || dateopts.endYear;
		this.endMonth = opts.endMonth || dateopts.endMonth;
		this.endDay = opts.endDay || dateopts.endDay;
		this.format = opts.format || dateopts.format;
		this.onPreDateShow=opts.onPreDateShow || dateopts.onPreDateShow;
		this.onOkDown=opts.onOkDown || dateopts.onOkDown;
		this.dateBoxShow()
	};
	Mdate.prototype = {
		constructor: Mdate,
		dateBoxShow: function() {
			var that = this;
			that.selectorId.onclick = function() {
				var rtv= that.onPreDateShow();
				if(rtv==false){
					return;
				}
				that.createDateBox();
				that.dateSure()
			}
		},


		createDateBox: function() {
			var that = this;
			MdatePlugin = d.getElementById("MdatePlugin");
			if (!MdatePlugin) {
				dateContentBox = d.createElement("div");
				dateContentBox.id = "MdatePlugin";
				d.body.appendChild(dateContentBox);
				MdatePlugin = d.getElementById("MdatePlugin")
			}
			MdatePlugin.setAttribute("class", "slideIn");
			that.createDateUi();
			var yearUl = d.getElementById("yearUl");
			var monthUl = d.getElementById("monthUl");
			var dayUl = d.getElementById("dayUl");
			yearUl.innerHTML = that.createDateYMD("year");
			that.initScroll();
			that.refreshScroll()
		},
		createDateUi: function() {
			var str = "" + '<section class="getDateBg"></section>' + '<section class="getDateBox" id="getDateBox">' + '<div class="choiceDateTitle">' + '<button id="dateCancel">??????</button>' + '<button id="dateSure" class="fr">??????</button>' + "</div>" + '<div class="dateContent">' + '<div class="checkeDate"></div>' + '<div id="yearwrapper">' + '<ul id="yearUl"></ul>' + "</div>" + '<div id="monthwrapper">' + '<ul id="monthUl"></ul>' + "</div>" + '<div id="daywrapper">' + '<ul id="dayUl"></ul>' + "</div>" + "</div>" + "</section>";
			MdatePlugin.innerHTML = str
		},
		createDateYMD: function(type) {
			var that = this;
			var str = "<li>&nbsp;</li>";
			var beginNum = null,
				endNum = null,
				unitName = "???",
				dataStyle = "data-year";
			if (type == "year") {
				beginNum = that.beginYear;
				endNum = that.endYear
			}
			if (type == "month") {
				unitName = "???";
				dataStyle = "data-month";
				beginNum = that.beginMonth;
				endNum = 12;
				if (yearTag != that.beginYear) {
					beginNum = 1
				}
				if (yearTag == dateopts.endYear) {
					endNum = that.endMonth
				}
			}
			if (type == "day") {
				unitName = "???";
				dataStyle = "data-day";
				beginNum = 1;
				endNum = new Date(yearTag, monthTag, 0).getDate();
				if (yearTag == that.beginYear && monthTag == that.beginMonth) {
					beginNum = that.beginDay
				}
				if (yearTag == that.endYear && monthTag == that.endMonth) {
					endNum = that.endDay
				}
			}
			for (var i = beginNum; i <= endNum; i++) {
				str += "<li " + dataStyle + "=" + i + ">" + that.dateForTen(i) + unitName + "</li>"
			}
			return str + "<li>&nbsp;</li>"
		},
		initScroll: function() {
			var that = this;
			yearScroll = new iScroll("yearwrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexY = Math.ceil(this.y / 40 * -1 + 1);
					yearTag = yearUl.getElementsByTagName("li")[indexY].getAttribute("data-year");
					monthUl.innerHTML = that.createDateYMD("month");
					monthScroll.refresh();
					try {
						monthTag = monthUl.getElementsByTagName("li")[indexM].getAttribute("data-month")
					} catch (err) {
						return true
					}
					dayUl.innerHTML = that.createDateYMD("day");
					dayScroll.refresh();
					try {
						dayTag = dayUl.getElementsByTagName("li")[indexD].getAttribute("data-day")
					} catch (err) {
						return true
					}
				}
			});
			monthScroll = new iScroll("monthwrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexM = Math.ceil(this.y / 40 * -1 + 1);
					if (indexM == 1 && yearTag != that.beginYear) {
						monthTag = 1
					} else {
						monthTag = monthUl.getElementsByTagName("li")[indexM].getAttribute("data-month")
					}
					dayUl.innerHTML = that.createDateYMD("day");
					dayScroll.refresh();
					try {
						dayTag = dayUl.getElementsByTagName("li")[indexD].getAttribute("data-day")
					} catch (err) {
						return true
					}
				}
			});
			dayScroll = new iScroll("daywrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexD = Math.ceil(this.y / 40 * -1 + 1);
					if (indexD == 1 && monthTag != that.beginMonth) {
						dayTag = 1
					} else {
						dayTag = dayUl.getElementsByTagName("li")[indexD].getAttribute("data-day")
					}
				}
			})
		},
		refreshScroll: function() {
			var that = this;
			var inputYear = that.acceptId.getAttribute("data-year");
			var inputMonth = that.acceptId.getAttribute("data-month");
			var inputDay = that.acceptId.getAttribute("data-day");
			inputYear = inputYear || that.beginYear;
			inputMonth = inputMonth || that.beginMonth;
			inputDay = inputDay || that.beginDay;
			initM = that.beginMonth;
			initD = that.beginDay;
			if (inputYear != that.beginYear && initM != 1) {
				initM = 1
			}
			if (inputMonth != that.beginMonth && initD != 1) {
				initD = 1
			}
			inputYear -= that.beginYear;
			inputMonth -= initM;
			inputDay -= initD;
			yearScroll.refresh();
			yearScroll.scrollTo(0, inputYear * 40, 300, true);
			monthScroll.scrollTo(0, inputMonth * 40, 300, true);
			dayScroll.scrollTo(0, inputDay * 40, 300, true)
		},
		dateSure: function() {
			var that = this;
			var sureBtn = d.getElementById("dateSure");
			var cancelBtn = d.getElementById("dateCancel");
			sureBtn.onclick = function() {
				var _selected_date;
				if (that.format == "YMD") {
					_selected_date = yearTag + "???" + monthTag + "???" + dayTag + "???"
				} else {
					_selected_date = yearTag + that.format + that.dateForTen(monthTag) + that.format + that.dateForTen(dayTag)
				}
				that.acceptId.value=_selected_date;
				that.onOkDown(_selected_date);

				that.acceptId.setAttribute("data-year", yearTag);
				that.acceptId.setAttribute("data-month", monthTag);
				that.acceptId.setAttribute("data-day", dayTag);
				that.dateCancel()
			};
			cancelBtn.onclick = function() {
				that.dateCancel()
			}
		},
		dateForTen: function(n) {
			if (n < 10) {
				return "0" + n
			} else {
				return n
			}
		},
		dateCancel: function() {
			MdatePlugin.setAttribute("class", "slideOut");
			setTimeout(function() {
				MdatePlugin.innerHTML = ""
			}, 400)
		}
	};
	if (typeof exports !== "undefined") {
		exports.Mdate = Mdate
	} else {
		window.Mdate = Mdate
	}
})();