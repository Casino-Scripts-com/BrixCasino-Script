/**
 * notifyme.js v2.0
 * https://mwa.li
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Mohammad Wali
 * http://www.mohammadwali.in
 */

!(function($, win, doc) {
    "use strict";
    var _doc = $(doc),
        _win = $(win),
        notify = "notifyme",
        _notify = "#",
        error = function(e) {
            throw "error: Cannot Notify => " + e;
        },
        warn = function(l) {
            (console.warn == "undefiend") ? console.log("Notify Warning: " + l) : console.warn("Notify Warning: " + l);
        },
        in_array = function(array, value) {
            for (var i = 0; i < array.length; i++) {
                if (array[i] === value) return true;
            }
            return false;
        },
        PrefixedEvent = function(element, type, callback) {
            var pfx = ["webkit", "moz", "MS", "o", ""];
            for (var p = 0; p < pfx.length; p++) {
                if (!pfx[p]) type = type.toLowerCase();
                _doc.on(pfx[p] + type, element, callback);
            }
        },
        closeNotify = function(button) {
            button.parents("." + notify + "-notification").removeClass("" + notify + "-show");
            setTimeout(function() {
                button.parents("." + notify + "-notification").addClass("" + notify + "-hide")
            }, 25);
        },
        initialize = function(set) {
            var main = doc.createElement("div"),
                wrapper = doc.createElement("div"),
                icon = doc.createElement("i"),
                text = doc.createElement("p"),
                close = doc.createElement("span");
            main.className = "" + notify + "-notification " + notify + "-" + set.position + " " + notify + "-" + set.type + " " + notify + "-show";
            wrapper.className = notify + "-wrapper";
            if (set.type == "error") {
                icon.className = notify + "-icon fa fa-times-circle";
            } else if (set.type == "success") {
                icon.className = notify + "-icon fa fa-check-circle";
            } else if (set.type == "warning") {
                icon.className = notify + "-icon fa fa-exclamation-triangle";
            };
            close.className = "notifyme-close";
            doc.body.appendChild(main);
            main.appendChild(wrapper);
            main.appendChild(close);
            wrapper.appendChild(icon);
            wrapper.appendChild(text);
            text.innerHTML = set.message;
            if (set.autohide == true) {
                setTimeout(function() {
                    closeNotify($(close));
                }, set.autohideDelay)
            }
        };
    $.notify = function(options) {
        var positions = ["top-left", "bottom-left", "top-right", "bottom-right"],
            types = ["error", "success", "warning"],
            defaults = {
                position: positions[0]
            }, settings = {
                message: "",
                type: "",
                autohide: false,
                autohideDelay: 2500,
                position: positions[0],
            };
        $.extend(settings, options);
        if (settings.type == "" && !settings.type.length) error("Type is not defined!");
        if (!in_array(types, settings.type)) error("Uhh, invalid notify type!");
        if (settings.message == "" && !settings.message.length) error("Hmmm, Message seems to be empty or not defined!");
        if (!in_array(positions, settings.position)) {
            warn("Oh, Invalid position switching to default!");
            settings.position = defaults.position;
        }
        if ($("." + notify + "-" + settings.position).length) {
            closeNotify($("." + notify + "-" + settings.position).find("." + notify + "-close"));
        }
        initialize(settings);
    };
    //append style 
    if (!doc.getElementById(notify + "-style")) {
        var elm = doc.createElement("style");
        elm.id = notify + "-style",
        elm.innerHTML = "@import url(https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css);.notifyme-notification,.notifyme-notification *,.notifyme-notification :after,.notifyme-notification :before{-webkit-box-sizing:border-box;box-sizing:border-box}.notifyme-notification.notifyme-hide{-webkit-animation-name:animFade;animation-name:animFade;-webkit-animation-duration:.25s;animation-duration:.25s;-webkit-animation-direction:reverse;animation-direction:reverse}.notifyme-notification.notifyme-show{-webkit-animation-name:animJelly;animation-name:animJelly;-webkit-animation-duration:1s;animation-duration:1s;-webkit-animation-timing-function:linear;animation-timing-function:linear;pointer-events:auto}.notifyme-notification{border-radius:5px;position:fixed;background:rgba(42,45,50,.85);padding:22px;line-height:1.4;pointer-events:none;color:rgba(250,251,255,.95);font-size:90%;font-family:'Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;max-width:370px;z-index:9999999999999}.notifyme-notification p{margin:0;line-height:1.3;font-size:14px}.notifyme-notification a{opacity:.7;font-weight:700;text-decoration:none}.notifyme-notification a:focus,.notifyme-notification a:hover{color:#fff!important;opacity:1}.notifyme-close{width:20px;height:20px;position:absolute;right:4px;top:4px;overflow:hidden;text-indent:100%;cursor:pointer;-webkit-backface-visibility:hidden;backface-visibility:hidden}.notifyme-close::after,.notifyme-close::before{content:'';position:absolute;width:3px;height:60%;top:50%;left:50%;background:#6e6e6e}.notifyme-close::after{-webkit-transform:translate(-50%,-50%) rotate(-45deg);transform:translate(-50%,-50%) rotate(-45deg)}.notifyme-close::before{-webkit-transform:translate(-50%,-50%) rotate(45deg);transform:translate(-50%,-50%) rotate(45deg)}.notifyme-close:focus,.notifyme-close:hover{outline:0}.notifyme-close:hover::after,.notifyme-close:hover::before{background:#fff!important}@-webkit-keyframes animFade{0%{opacity:0}100%{opacity:1}}@keyframes animFade{0%{opacity:0}100%{opacity:1}}@-webkit-keyframes animJelly{0%{-webkit-transform:matrix3d(0.7,0,0,0,0,.7,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.7,0,0,0,0,.7,0,0,0,0,1,0,0,0,0,1)}2.083333%{-webkit-transform:matrix3d(0.75266,0,0,0,0,.76342,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.75266,0,0,0,0,.76342,0,0,0,0,1,0,0,0,0,1)}4.166667%{-webkit-transform:matrix3d(0.81071,0,0,0,0,.84545,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.81071,0,0,0,0,.84545,0,0,0,0,1,0,0,0,0,1)}6.25%{-webkit-transform:matrix3d(0.86808,0,0,0,0,.9286,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.86808,0,0,0,0,.9286,0,0,0,0,1,0,0,0,0,1)}8.333333%{-webkit-transform:matrix3d(0.92038,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.92038,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}10.416667%{-webkit-transform:matrix3d(0.96482,0,0,0,0,1.05202,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.96482,0,0,0,0,1.05202,0,0,0,0,1,0,0,0,0,1)}12.5%{-webkit-transform:matrix3d(1,0,0,0,0,1.08204,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1.08204,0,0,0,0,1,0,0,0,0,1)}14.583333%{-webkit-transform:matrix3d(1.02563,0,0,0,0,1.09149,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.02563,0,0,0,0,1.09149,0,0,0,0,1,0,0,0,0,1)}16.666667%{-webkit-transform:matrix3d(1.04227,0,0,0,0,1.08453,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.04227,0,0,0,0,1.08453,0,0,0,0,1,0,0,0,0,1)}18.75%{-webkit-transform:matrix3d(1.05102,0,0,0,0,1.06666,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05102,0,0,0,0,1.06666,0,0,0,0,1,0,0,0,0,1)}20.833333%{-webkit-transform:matrix3d(1.05334,0,0,0,0,1.04355,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05334,0,0,0,0,1.04355,0,0,0,0,1,0,0,0,0,1)}22.916667%{-webkit-transform:matrix3d(1.05078,0,0,0,0,1.02012,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05078,0,0,0,0,1.02012,0,0,0,0,1,0,0,0,0,1)}25%{-webkit-transform:matrix3d(1.04487,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.04487,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}27.083333%{-webkit-transform:matrix3d(1.03699,0,0,0,0,.98534,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.03699,0,0,0,0,.98534,0,0,0,0,1,0,0,0,0,1)}29.166667%{-webkit-transform:matrix3d(1.02831,0,0,0,0,.97688,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.02831,0,0,0,0,.97688,0,0,0,0,1,0,0,0,0,1)}31.25%{-webkit-transform:matrix3d(1.01973,0,0,0,0,.97422,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.01973,0,0,0,0,.97422,0,0,0,0,1,0,0,0,0,1)}33.333333%{-webkit-transform:matrix3d(1.01191,0,0,0,0,.97618,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.01191,0,0,0,0,.97618,0,0,0,0,1,0,0,0,0,1)}35.416667%{-webkit-transform:matrix3d(1.00526,0,0,0,0,.98122,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00526,0,0,0,0,.98122,0,0,0,0,1,0,0,0,0,1)}37.5%{-webkit-transform:matrix3d(1,0,0,0,0,.98773,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,.98773,0,0,0,0,1,0,0,0,0,1)}39.583333%{-webkit-transform:matrix3d(0.99617,0,0,0,0,.99433,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99617,0,0,0,0,.99433,0,0,0,0,1,0,0,0,0,1)}41.666667%{-webkit-transform:matrix3d(0.99368,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99368,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}43.75%{-webkit-transform:matrix3d(0.99237,0,0,0,0,1.00413,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99237,0,0,0,0,1.00413,0,0,0,0,1,0,0,0,0,1)}45.833333%{-webkit-transform:matrix3d(0.99202,0,0,0,0,1.00651,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99202,0,0,0,0,1.00651,0,0,0,0,1,0,0,0,0,1)}47.916667%{-webkit-transform:matrix3d(0.99241,0,0,0,0,1.00726,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99241,0,0,0,0,1.00726,0,0,0,0,1,0,0,0,0,1)}50%{-webkit-transform:matrix3d(0.99329,0,0,0,0,1.00671,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99329,0,0,0,0,1.00671,0,0,0,0,1,0,0,0,0,1)}52.083333%{-webkit-transform:matrix3d(0.99447,0,0,0,0,1.00529,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99447,0,0,0,0,1.00529,0,0,0,0,1,0,0,0,0,1)}54.166667%{-webkit-transform:matrix3d(0.99577,0,0,0,0,1.00346,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99577,0,0,0,0,1.00346,0,0,0,0,1,0,0,0,0,1)}56.25%{-webkit-transform:matrix3d(0.99705,0,0,0,0,1.0016,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99705,0,0,0,0,1.0016,0,0,0,0,1,0,0,0,0,1)}58.333333%{-webkit-transform:matrix3d(0.99822,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99822,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}60.416667%{-webkit-transform:matrix3d(0.99921,0,0,0,0,.99884,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99921,0,0,0,0,.99884,0,0,0,0,1,0,0,0,0,1)}62.5%{-webkit-transform:matrix3d(1,0,0,0,0,.99816,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,.99816,0,0,0,0,1,0,0,0,0,1)}64.583333%{-webkit-transform:matrix3d(1.00057,0,0,0,0,.99795,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00057,0,0,0,0,.99795,0,0,0,0,1,0,0,0,0,1)}66.666667%{-webkit-transform:matrix3d(1.00095,0,0,0,0,.99811,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00095,0,0,0,0,.99811,0,0,0,0,1,0,0,0,0,1)}68.75%{-webkit-transform:matrix3d(1.00114,0,0,0,0,.99851,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00114,0,0,0,0,.99851,0,0,0,0,1,0,0,0,0,1)}70.833333%{-webkit-transform:matrix3d(1.00119,0,0,0,0,.99903,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00119,0,0,0,0,.99903,0,0,0,0,1,0,0,0,0,1)}72.916667%{-webkit-transform:matrix3d(1.00114,0,0,0,0,.99955,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00114,0,0,0,0,.99955,0,0,0,0,1,0,0,0,0,1)}75%{-webkit-transform:matrix3d(1.001,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.001,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}77.083333%{-webkit-transform:matrix3d(1.00083,0,0,0,0,1.00033,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00083,0,0,0,0,1.00033,0,0,0,0,1,0,0,0,0,1)}79.166667%{-webkit-transform:matrix3d(1.00063,0,0,0,0,1.00052,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00063,0,0,0,0,1.00052,0,0,0,0,1,0,0,0,0,1)}81.25%{-webkit-transform:matrix3d(1.00044,0,0,0,0,1.00058,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00044,0,0,0,0,1.00058,0,0,0,0,1,0,0,0,0,1)}83.333333%{-webkit-transform:matrix3d(1.00027,0,0,0,0,1.00053,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00027,0,0,0,0,1.00053,0,0,0,0,1,0,0,0,0,1)}85.416667%{-webkit-transform:matrix3d(1.00012,0,0,0,0,1.00042,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00012,0,0,0,0,1.00042,0,0,0,0,1,0,0,0,0,1)}87.5%{-webkit-transform:matrix3d(1,0,0,0,0,1.00027,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1.00027,0,0,0,0,1,0,0,0,0,1)}89.583333%{-webkit-transform:matrix3d(0.99991,0,0,0,0,1.00013,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99991,0,0,0,0,1.00013,0,0,0,0,1,0,0,0,0,1)}91.666667%{-webkit-transform:matrix3d(0.99986,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99986,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}93.75%{-webkit-transform:matrix3d(0.99983,0,0,0,0,.99991,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99983,0,0,0,0,.99991,0,0,0,0,1,0,0,0,0,1)}95.833333%{-webkit-transform:matrix3d(0.99982,0,0,0,0,.99985,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99982,0,0,0,0,.99985,0,0,0,0,1,0,0,0,0,1)}97.916667%{-webkit-transform:matrix3d(0.99983,0,0,0,0,.99984,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99983,0,0,0,0,.99984,0,0,0,0,1,0,0,0,0,1)}100%{-webkit-transform:matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}}@keyframes animJelly{0%{-webkit-transform:matrix3d(0.7,0,0,0,0,.7,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.7,0,0,0,0,.7,0,0,0,0,1,0,0,0,0,1)}2.083333%{-webkit-transform:matrix3d(0.75266,0,0,0,0,.76342,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.75266,0,0,0,0,.76342,0,0,0,0,1,0,0,0,0,1)}4.166667%{-webkit-transform:matrix3d(0.81071,0,0,0,0,.84545,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.81071,0,0,0,0,.84545,0,0,0,0,1,0,0,0,0,1)}6.25%{-webkit-transform:matrix3d(0.86808,0,0,0,0,.9286,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.86808,0,0,0,0,.9286,0,0,0,0,1,0,0,0,0,1)}8.333333%{-webkit-transform:matrix3d(0.92038,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.92038,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}10.416667%{-webkit-transform:matrix3d(0.96482,0,0,0,0,1.05202,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.96482,0,0,0,0,1.05202,0,0,0,0,1,0,0,0,0,1)}12.5%{-webkit-transform:matrix3d(1,0,0,0,0,1.08204,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1.08204,0,0,0,0,1,0,0,0,0,1)}14.583333%{-webkit-transform:matrix3d(1.02563,0,0,0,0,1.09149,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.02563,0,0,0,0,1.09149,0,0,0,0,1,0,0,0,0,1)}16.666667%{-webkit-transform:matrix3d(1.04227,0,0,0,0,1.08453,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.04227,0,0,0,0,1.08453,0,0,0,0,1,0,0,0,0,1)}18.75%{-webkit-transform:matrix3d(1.05102,0,0,0,0,1.06666,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05102,0,0,0,0,1.06666,0,0,0,0,1,0,0,0,0,1)}20.833333%{-webkit-transform:matrix3d(1.05334,0,0,0,0,1.04355,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05334,0,0,0,0,1.04355,0,0,0,0,1,0,0,0,0,1)}22.916667%{-webkit-transform:matrix3d(1.05078,0,0,0,0,1.02012,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.05078,0,0,0,0,1.02012,0,0,0,0,1,0,0,0,0,1)}25%{-webkit-transform:matrix3d(1.04487,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.04487,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}27.083333%{-webkit-transform:matrix3d(1.03699,0,0,0,0,.98534,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.03699,0,0,0,0,.98534,0,0,0,0,1,0,0,0,0,1)}29.166667%{-webkit-transform:matrix3d(1.02831,0,0,0,0,.97688,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.02831,0,0,0,0,.97688,0,0,0,0,1,0,0,0,0,1)}31.25%{-webkit-transform:matrix3d(1.01973,0,0,0,0,.97422,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.01973,0,0,0,0,.97422,0,0,0,0,1,0,0,0,0,1)}33.333333%{-webkit-transform:matrix3d(1.01191,0,0,0,0,.97618,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.01191,0,0,0,0,.97618,0,0,0,0,1,0,0,0,0,1)}35.416667%{-webkit-transform:matrix3d(1.00526,0,0,0,0,.98122,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00526,0,0,0,0,.98122,0,0,0,0,1,0,0,0,0,1)}37.5%{-webkit-transform:matrix3d(1,0,0,0,0,.98773,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,.98773,0,0,0,0,1,0,0,0,0,1)}39.583333%{-webkit-transform:matrix3d(0.99617,0,0,0,0,.99433,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99617,0,0,0,0,.99433,0,0,0,0,1,0,0,0,0,1)}41.666667%{-webkit-transform:matrix3d(0.99368,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99368,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}43.75%{-webkit-transform:matrix3d(0.99237,0,0,0,0,1.00413,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99237,0,0,0,0,1.00413,0,0,0,0,1,0,0,0,0,1)}45.833333%{-webkit-transform:matrix3d(0.99202,0,0,0,0,1.00651,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99202,0,0,0,0,1.00651,0,0,0,0,1,0,0,0,0,1)}47.916667%{-webkit-transform:matrix3d(0.99241,0,0,0,0,1.00726,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99241,0,0,0,0,1.00726,0,0,0,0,1,0,0,0,0,1)}50%{-webkit-transform:matrix3d(0.99329,0,0,0,0,1.00671,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99329,0,0,0,0,1.00671,0,0,0,0,1,0,0,0,0,1)}52.083333%{-webkit-transform:matrix3d(0.99447,0,0,0,0,1.00529,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99447,0,0,0,0,1.00529,0,0,0,0,1,0,0,0,0,1)}54.166667%{-webkit-transform:matrix3d(0.99577,0,0,0,0,1.00346,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99577,0,0,0,0,1.00346,0,0,0,0,1,0,0,0,0,1)}56.25%{-webkit-transform:matrix3d(0.99705,0,0,0,0,1.0016,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99705,0,0,0,0,1.0016,0,0,0,0,1,0,0,0,0,1)}58.333333%{-webkit-transform:matrix3d(0.99822,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99822,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}60.416667%{-webkit-transform:matrix3d(0.99921,0,0,0,0,.99884,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99921,0,0,0,0,.99884,0,0,0,0,1,0,0,0,0,1)}62.5%{-webkit-transform:matrix3d(1,0,0,0,0,.99816,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,.99816,0,0,0,0,1,0,0,0,0,1)}64.583333%{-webkit-transform:matrix3d(1.00057,0,0,0,0,.99795,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00057,0,0,0,0,.99795,0,0,0,0,1,0,0,0,0,1)}66.666667%{-webkit-transform:matrix3d(1.00095,0,0,0,0,.99811,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00095,0,0,0,0,.99811,0,0,0,0,1,0,0,0,0,1)}68.75%{-webkit-transform:matrix3d(1.00114,0,0,0,0,.99851,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00114,0,0,0,0,.99851,0,0,0,0,1,0,0,0,0,1)}70.833333%{-webkit-transform:matrix3d(1.00119,0,0,0,0,.99903,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00119,0,0,0,0,.99903,0,0,0,0,1,0,0,0,0,1)}72.916667%{-webkit-transform:matrix3d(1.00114,0,0,0,0,.99955,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00114,0,0,0,0,.99955,0,0,0,0,1,0,0,0,0,1)}75%{-webkit-transform:matrix3d(1.001,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.001,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}77.083333%{-webkit-transform:matrix3d(1.00083,0,0,0,0,1.00033,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00083,0,0,0,0,1.00033,0,0,0,0,1,0,0,0,0,1)}79.166667%{-webkit-transform:matrix3d(1.00063,0,0,0,0,1.00052,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00063,0,0,0,0,1.00052,0,0,0,0,1,0,0,0,0,1)}81.25%{-webkit-transform:matrix3d(1.00044,0,0,0,0,1.00058,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00044,0,0,0,0,1.00058,0,0,0,0,1,0,0,0,0,1)}83.333333%{-webkit-transform:matrix3d(1.00027,0,0,0,0,1.00053,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00027,0,0,0,0,1.00053,0,0,0,0,1,0,0,0,0,1)}85.416667%{-webkit-transform:matrix3d(1.00012,0,0,0,0,1.00042,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1.00012,0,0,0,0,1.00042,0,0,0,0,1,0,0,0,0,1)}87.5%{-webkit-transform:matrix3d(1,0,0,0,0,1.00027,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1.00027,0,0,0,0,1,0,0,0,0,1)}89.583333%{-webkit-transform:matrix3d(0.99991,0,0,0,0,1.00013,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99991,0,0,0,0,1.00013,0,0,0,0,1,0,0,0,0,1)}91.666667%{-webkit-transform:matrix3d(0.99986,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99986,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}93.75%{-webkit-transform:matrix3d(0.99983,0,0,0,0,.99991,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99983,0,0,0,0,.99991,0,0,0,0,1,0,0,0,0,1)}95.833333%{-webkit-transform:matrix3d(0.99982,0,0,0,0,.99985,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99982,0,0,0,0,.99985,0,0,0,0,1,0,0,0,0,1)}97.916667%{-webkit-transform:matrix3d(0.99983,0,0,0,0,.99984,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(0.99983,0,0,0,0,.99984,0,0,0,0,1,0,0,0,0,1)}100%{-webkit-transform:matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1);transform:matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)}}.notifyme-notification .notifyme-wrapper i.fa.notifyme-icon{position:absolute;left:22px;top:50%;font-size:22px;margin-top:-11px}.notifyme-wrapper{padding-left:30px;padding-right:5px}.notifyme-notification.notifyme-success{background:#3ec569}.notifyme-notification.notifyme-success .notifyme-close::after,.notifyme-notification.notifyme-success .notifyme-close::before{background:#0e8942}.notifyme-notification.notifyme-success a{color:#007330}.notifyme-notification.notifyme-error{background:#e43e3e}.notifyme-notification.notifyme-error .notifyme-close::after,.notifyme-notification.notifyme-error .notifyme-close::before{background:#b31010}.notifyme-notification.notifyme-error a{color:#7c1313}.notifyme-notification.notifyme-warning{background:#ffe008;color:#c8922f}.notifyme-notification.notifyme-warning .notifyme-close::after,.notifyme-notification.notifyme-warning .notifyme-close::before{background:#c8922f}.notifyme-notification.notifyme-warning a{color:#a97515}.notifyme-notification.notifyme-top-left{top:30px;left:30px}.notifyme-notification.notifyme-top-right{top:30px;right:30px}.notifyme-notification.notifyme-bottom-left{bottom:30px;left:30px}.notifyme-notification.notifyme-bottom-right{bottom:30px;right:30px}";
        doc.getElementsByTagName("head")[0].appendChild(elm)
    }
    PrefixedEvent($(".notifyme-notification"), "AnimationEnd", function() {
        $(".notifyme-notification.notifyme-hide").remove();
    });
    _doc.on("click", ".notifyme-close", function() {
        closeNotify($(this));
    });
    console.log("Notify Initialized! Thanks for using %c Notifyme.js ", "background:#7266ba;color:#fff");
})(window.jQuery, window, document)