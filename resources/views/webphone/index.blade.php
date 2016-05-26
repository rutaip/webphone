@extends('template')
    <!-- SIPML5 API:
    DEBUG VERSION: 'SIPml-api.js'
    RELEASE VERSION: 'release/SIPml-api.js'
    -->

@section('content')
    {!! Html::script('assets/js/SIPml-api.js?svn=250') !!}

    <!-- Styles -->
    {!! Html::style('assets/css/bootstrap.css') !!}
    <style type="text/css">
      /*  body {
            padding-top: 80px;
            padding-bottom: 40px;
        }
        .navbar-inner-red {
            background-color: #600000;
            background-image: none;
            background-repeat: no-repeat;
            filter: none;
        }
        .full-screen {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .normal-screen {
            position: relative;
        }*/
        .call-options {
            padding: 5px;
            background-color: #f0f0f0;
            border: 1px solid #eee;
            border: 1px solid rgba(0, 0, 0, 0.08);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            -webkit-transition-property: opacity;
            -moz-transition-property: opacity;
            -o-transition-property: opacity;
            -webkit-transition-duration: 2s;
            -moz-transition-duration: 2s;
            -o-transition-duration: 2s;
        }
        .tab-video,
        .div-video {
            width: 100%;
            height: 0px;
            -webkit-transition-property: height;
            -moz-transition-property: height;
            -o-transition-property: height;
            -webkit-transition-duration: 2s;
            -moz-transition-duration: 2s;
            -o-transition-duration: 2s;
        }
        .label-align {
            display: block;
            padding-left: 15px;
            text-indent: -15px;
        }
        .input-align {
            width: 13px;
            height: 13px;
            padding: 0;
            margin: 0;
            vertical-align: bottom;
            position: relative;
            top: -1px;
            *overflow: hidden;
        }
        .glass-panel {
            z-index: 99;
            position: fixed;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            top: 0;
            left: 0;
            opacity: 0.8;
            background-color: Gray;
        }
        .div-keypad {
            z-index: 100;
            position: fixed;
            -moz-transition-property: left top;
            -o-transition-property: left top;
            -webkit-transition-duration: 2s;
            -moz-transition-duration: 2s;
            -o-transition-duration: 2s;
        }
        .previewvideo {
            position: absolute;
            width: 88px;
            height: 72px;
            margin-top: -42px;
        }
    </style>
    {!! Html::style('assets/css/bootstrap-responsive.css') !!}
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png" />

    <!-- Javascript code -->
    <script type="text/javascript">
        // to avoid caching
        //if (window.location.href.indexOf("svn=") == -1) {
        //    window.location.href += (window.location.href.indexOf("?") == -1 ? "svn=236" : "&svn=229");
        //}
        var sTransferNumber;
        var oRingTone, oRingbackTone;
        var oSipStack, oSipSessionRegister, oSipSessionCall, oSipSessionTransferCall;
        var videoRemote, videoLocal, audioRemote;
        var bFullScreen = false;
        var oNotifICall;
        var bDisableVideo = false;
        var viewVideoLocal, viewVideoRemote, viewLocalScreencast; // <video> (webrtc) or <div> (webrtc4all)
        var oConfigCall;
        var oReadyStateTimer;
        C =
        {
            divKeyPadWidth: 220
        };
        window.onload = function () {
            window.console && window.console.info && window.console.info("location=" + window.location);
            videoLocal = document.getElementById("video_local");
            videoRemote = document.getElementById("video_remote");
            audioRemote = document.getElementById("audio_remote");
            document.onkeyup = onKeyUp;
            document.body.onkeyup = onKeyUp;
            divCallCtrl.onmousemove = onDivCallCtrlMouseMove;
            // set debug level
            SIPml.setDebugLevel((window.localStorage && window.localStorage.getItem('org.doubango.expert.disable_debug') == "true") ? "error" : "info");
            loadCredentials();
            loadCallOptions();
            // Initialize call button
            uiBtnCallSetText("Call");
            var getPVal = function (PName) {
                var query = window.location.search.substring(1);
                var vars = query.split('&');
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split('=');
                    if (decodeURIComponent(pair[0]) === PName) {
                        return decodeURIComponent(pair[1]);
                    }
                }
                return null;
            }
            var preInit = function () {
                // set default webrtc type (before initialization)
                var s_webrtc_type = getPVal("wt");
                var s_fps = getPVal("fps");
                var s_mvs = getPVal("mvs"); // maxVideoSize
                var s_mbwu = getPVal("mbwu"); // maxBandwidthUp (kbps)
                var s_mbwd = getPVal("mbwd"); // maxBandwidthUp (kbps)
                var s_za = getPVal("za"); // ZeroArtifacts
                var s_ndb = getPVal("ndb"); // NativeDebug
                if (s_webrtc_type) SIPml.setWebRtcType(s_webrtc_type);
                // initialize SIPML5
                SIPml.init(postInit);
                // set other options after initialization
                if (s_fps) SIPml.setFps(parseFloat(s_fps));
                if (s_mvs) SIPml.setMaxVideoSize(s_mvs);
                if (s_mbwu) SIPml.setMaxBandwidthUp(parseFloat(s_mbwu));
                if (s_mbwd) SIPml.setMaxBandwidthDown(parseFloat(s_mbwd));
                if (s_za) SIPml.setZeroArtifacts(s_za === "true");
                if (s_ndb == "true") SIPml.startNativeDebug();
                //var rinningApps = SIPml.getRunningApps();
                //var _rinningApps = Base64.decode(rinningApps);
                //tsk_utils_log_info(_rinningApps);
            }
            oReadyStateTimer = setInterval(function () {
                        if (document.readyState === "complete") {
                            clearInterval(oReadyStateTimer);
                            // initialize SIPML5
                            preInit();
                        }
                    },
                    500);
            /*if (document.readyState === "complete") {
             preInit();
             }
             else {
             document.onreadystatechange = function () {
             if (document.readyState === "complete") {
             preInit();
             }
             }
             }*/
        };
        function postInit() {
            // check for WebRTC support
            if (!SIPml.isWebRtcSupported()) {
                // is it chrome?
                if (SIPml.getNavigatorFriendlyName() == 'chrome') {
                    if (confirm("You're using an old Chrome version or WebRTC is not enabled.\nDo you want to see how to enable WebRTC?")) {
                        window.location = 'http://www.webrtc.org/running-the-demos';
                    }
                    else {
                        window.location = "index.html";
                    }
                    return;
                }
                else {
                    if (confirm("webrtc-everywhere extension is not installed. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
                        window.location = 'https://github.com/sarandogou/webrtc-everywhere';
                    }
                    else {
                        // Must do nothing: give the user the chance to accept the extension
                        // window.location = "index.html";
                    }
                }
            }
            // checks for WebSocket support
            if (!SIPml.isWebSocketSupported()) {
                if (confirm('Your browser don\'t support WebSockets.\nDo you want to download a WebSocket-capable browser?')) {
                    window.location = 'https://www.google.com/intl/en/chrome/browser/';
                }
                else {
                    window.location = "index.html";
                }
                return;
            }
            // FIXME: displays must be per session
            viewVideoLocal = videoLocal;
            viewVideoRemote = videoRemote;
            if (!SIPml.isWebRtcSupported()) {
                if (confirm('Your browser don\'t support WebRTC.\naudio/video calls will be disabled.\nDo you want to download a WebRTC-capable browser?')) {
                    window.location = 'https://www.google.com/intl/en/chrome/browser/';
                }
            }
            btnRegister.disabled = false;
            document.body.style.cursor = 'default';
            oConfigCall = {
                audio_remote: audioRemote,
                video_local: viewVideoLocal,
                video_remote: viewVideoRemote,
                screencast_window_id: 0x00000000, // entire desktop
                bandwidth: { audio: undefined, video: undefined },
                video_size: { minWidth: undefined, minHeight: undefined, maxWidth: undefined, maxHeight: undefined },
                events_listener: { events: '*', listener: onSipEventSession },
                sip_caps: [
                    { name: '+g.oma.sip-im' },
                    { name: 'language', value: '\"en,fr\"' }
                ]
            };
        }
        function loadCallOptions() {
            if (window.localStorage) {
                var s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.call.phone_number'))) txtPhoneNumber.value = s_value;
                bDisableVideo = {!! $extension->instance->video_enable !!};//(window.localStorage.getItem('org.doubango.expert.disable_video') == "true");
                txtCallStatus.innerHTML = '<i>Video ' + (bDisableVideo ? 'disabled' : 'enabled') + '</i>';
            }
        }
        function saveCallOptions() {
            if (window.localStorage) {
                window.localStorage.setItem('org.doubango.call.phone_number', txtPhoneNumber.value);
                window.localStorage.setItem('org.doubango.expert.disable_video', bDisableVideo ? "true" : "false");
            }
        }
        function loadCredentials() {
           /* if (window.localStorage) {
                // IE retuns 'null' if not defined
                var s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.display_name'))) txtDisplayName.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.impi'))) txtPrivateIdentity.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.impu'))) txtPublicIdentity.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.password'))) txtPassword.value = s_value;
                if ((s_value = window.localStorage.getItem('org.doubango.identity.realm'))) txtRealm.value = s_value;
            }
            else {*/
                txtDisplayName.value = "{{ $user->name }} {{ $user->lastname }}";
                txtPrivateIdentity.value = "{{ $extension->extension }}";
                txtPublicIdentity.value = "sip:{{ $extension->extension }}{!! '@' !!}{{ $extension->pbx_url }}";
                txtPassword.value = "{{ $extension->password }}";
                txtRealm.value = "{{ $extension->pbx_url }}";
                //txtPhoneNumber.value = "701020";
            //}
        };
        function saveCredentials() {
            if (window.localStorage) {
                window.localStorage.setItem('org.doubango.identity.display_name', txtDisplayName.value);
                window.localStorage.setItem('org.doubango.identity.impi', txtPrivateIdentity.value);
                window.localStorage.setItem('org.doubango.identity.impu', txtPublicIdentity.value);
                window.localStorage.setItem('org.doubango.identity.password', txtPassword.value);
                window.localStorage.setItem('org.doubango.identity.realm', txtRealm.value);
            }
        };
        // sends SIP REGISTER request to login
        function sipRegister() {
            // catch exception for IE (DOM not ready)
            try {
                btnRegister.disabled = true;
                if (!txtRealm.value || !txtPrivateIdentity.value || !txtPublicIdentity.value) {
                    txtRegStatus.innerHTML = '<b>Please fill madatory fields (*)</b>';
                    btnRegister.disabled = false;
                    return;
                }
                var o_impu = tsip_uri.prototype.Parse(txtPublicIdentity.value);
                if (!o_impu || !o_impu.s_user_name || !o_impu.s_host) {
                    txtRegStatus.innerHTML = "<b>[" + txtPublicIdentity.value + "] is not a valid Public identity</b>";
                    btnRegister.disabled = false;
                    return;
                }
                // enable notifications if not already done
                if (window.webkitNotifications && window.webkitNotifications.checkPermission() != 0) {
                    window.webkitNotifications.requestPermission();
                }
                // save credentials
                saveCredentials();
                // update debug level to be sure new values will be used if the user haven't updated the page
                SIPml.setDebugLevel((window.localStorage && window.localStorage.getItem('org.doubango.expert.disable_debug') == "true") ? "error" : "info");
                // create SIP stack
                oSipStack = new SIPml.Stack({
                            realm: txtRealm.value,
                            impi: txtPrivateIdentity.value,
                            impu: txtPublicIdentity.value,
                            password: txtPassword.value,
                            display_name: txtDisplayName.value,
                            //websocket_proxy_url: (window.localStorage ? window.localStorage.getItem('org.doubango.expert.websocket_server_url') : null),
                            websocket_proxy_url: '{!! $extension->instance->websocket_proxy_url !!}',
                            outbound_proxy_url: '{!! $extension->instance->outbound_proxy !!}',
                            ice_servers: '',
                            enable_rtcweb_breaker: {!! $extension->instance->rtcweb_breaker !!},
                            events_listener: { events: '*', listener: onSipEventStack },
                            enable_early_ims: (window.localStorage ? window.localStorage.getItem('org.doubango.expert.disable_early_ims') != "true" : true), // Must be true unless you're using a real IMS network
                            enable_media_stream_cache: (window.localStorage ? window.localStorage.getItem('org.doubango.expert.enable_media_caching') == "true" : false),
                            bandwidth: '{!! $extension->instance->bandwidth !!}', // could be redefined a session-level
                            video_size: '{!! $extension->instance->video_size !!}', // could be redefined a session-level
                            sip_headers: [
                                { name: 'User-Agent', value: 'IM-client/OMA1.0 sipML5-v1.2016.03.04' },
                                { name: 'Organization', value: 'Rutaip' }
                            ]
                        }
                );
                if (oSipStack.start() != 0) {
                    txtRegStatus.innerHTML = '<b>Failed to start the SIP stack</b>';
                }
                else return;
            }
            catch (e) {
                txtRegStatus.innerHTML = "<b>2:" + e + "</b>";
            }
            btnRegister.disabled = false;
        }
        // sends SIP REGISTER (expires=0) to logout
        function sipUnRegister() {
            if (oSipStack) {
                oSipStack.stop(); // shutdown all sessions
            }
        }
        // makes a call (SIP INVITE)
        function sipCall(s_type) {
            if (oSipStack && !oSipSessionCall && !tsk_string_is_null_or_empty(txtPhoneNumber.value)) {
                if (s_type == 'call-screenshare') {
                    if (!SIPml.isScreenShareSupported()) {
                        alert('Screen sharing not supported. Are you using chrome 26+?');
                        return;
                    }
                    if (!location.protocol.match('https')) {
                        if (confirm("Screen sharing requires https://. Do you want to be redirected?")) {
                            sipUnRegister();
                            window.location = 'https://ns313841.ovh.net/call.htm';
                        }
                        return;
                    }
                }
                btnCall.disabled = true;
                btnHangUp.disabled = false;
                if (window.localStorage) {
                    oConfigCall.bandwidth = tsk_string_to_object(window.localStorage.getItem('org.doubango.expert.bandwidth')); // already defined at stack-level but redifined to use latest values
                    oConfigCall.video_size = tsk_string_to_object(window.localStorage.getItem('org.doubango.expert.video_size')); // already defined at stack-level but redifined to use latest values
                }
                // create call session
                oSipSessionCall = oSipStack.newSession(s_type, oConfigCall);
                // make call
                if (oSipSessionCall.call(txtPhoneNumber.value) != 0) {
                    oSipSessionCall = null;
                    txtCallStatus.value = 'Failed to make call';
                    btnCall.disabled = false;
                    btnHangUp.disabled = true;
                    return;
                }
                saveCallOptions();
            }
            else if (oSipSessionCall) {
                txtCallStatus.innerHTML = '<i>Connecting...</i>';
                oSipSessionCall.accept(oConfigCall);
            }
        }
        // Share entire desktop aor application using BFCP or WebRTC native implementation
        function sipShareScreen() {
            if (SIPml.getWebRtcType() === 'w4a') {
                // Sharing using BFCP -> requires an active session
                if (!oSipSessionCall) {
                    txtCallStatus.innerHTML = '<i>No active session</i>';
                    return;
                }
                if (oSipSessionCall.bfcpSharing) {
                    if (oSipSessionCall.stopBfcpShare(oConfigCall) != 0) {
                        txtCallStatus.value = 'Failed to stop BFCP share';
                    }
                    else {
                        oSipSessionCall.bfcpSharing = false;
                    }
                }
                else {
                    oConfigCall.screencast_window_id = 0x00000000;
                    if (oSipSessionCall.startBfcpShare(oConfigCall) != 0) {
                        txtCallStatus.value = 'Failed to start BFCP share';
                    }
                    else {
                        oSipSessionCall.bfcpSharing = true;
                    }
                }
            }
            else {
                sipCall('call-screenshare');
            }
        }
        // transfers the call
        function sipTransfer() {
            if (oSipSessionCall) {
                var s_destination = prompt('Enter destination number', '');
                if (!tsk_string_is_null_or_empty(s_destination)) {
                    btnTransfer.disabled = true;
                    if (oSipSessionCall.transfer(s_destination) != 0) {
                        txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
                        btnTransfer.disabled = false;
                        return;
                    }
                    txtCallStatus.innerHTML = '<i>Transfering the call...</i>';
                }
            }
        }
        // holds or resumes the call
        function sipToggleHoldResume() {
            if (oSipSessionCall) {
                var i_ret;
                btnHoldResume.disabled = true;
                txtCallStatus.innerHTML = oSipSessionCall.bHeld ? '<i>Resuming the call...</i>' : '<i>Holding the call...</i>';
                i_ret = oSipSessionCall.bHeld ? oSipSessionCall.resume() : oSipSessionCall.hold();
                if (i_ret != 0) {
                    txtCallStatus.innerHTML = '<i>Hold / Resume failed</i>';
                    btnHoldResume.disabled = false;
                    return;
                }
            }
        }
        // Mute or Unmute the call
        function sipToggleMute() {
            if (oSipSessionCall) {
                var i_ret;
                var bMute = !oSipSessionCall.bMute;
                txtCallStatus.innerHTML = bMute ? '<i>Mute the call...</i>' : '<i>Unmute the call...</i>';
                i_ret = oSipSessionCall.mute('audio'/*could be 'video'*/, bMute);
                if (i_ret != 0) {
                    txtCallStatus.innerHTML = '<i>Mute / Unmute failed</i>';
                    return;
                }
                oSipSessionCall.bMute = bMute;
                btnMute.value = bMute ? "Unmute" : "Mute";
            }
        }
        // terminates the call (SIP BYE or CANCEL)
        function sipHangUp() {
            if (oSipSessionCall) {
                txtCallStatus.innerHTML = '<i>Terminating the call...</i>';
                oSipSessionCall.hangup({ events_listener: { events: '*', listener: onSipEventSession } });
            }
        }
        function sipSendDTMF(c) {
            if (oSipSessionCall && c) {
                if (oSipSessionCall.dtmf(c) == 0) {
                    try { dtmfTone.play(); } catch (e) { }
                }
            }
        }
        function startRingTone() {
            try { ringtone.play(); }
            catch (e) { }
        }
        function stopRingTone() {
            try { ringtone.pause(); }
            catch (e) { }
        }
        function startRingbackTone() {
            try { ringbacktone.play(); }
            catch (e) { }
        }
        function stopRingbackTone() {
            try { ringbacktone.pause(); }
            catch (e) { }
        }
        function toggleFullScreen() {
            if (videoRemote.webkitSupportsFullscreen) {
                fullScreen(!videoRemote.webkitDisplayingFullscreen);
            }
            else {
                fullScreen(!bFullScreen);
            }
        }
        function openKeyPad() {
            divKeyPad.style.visibility = 'visible';
            divKeyPad.style.left = ((document.body.clientWidth - C.divKeyPadWidth) >> 1) + 'px';
            divKeyPad.style.top = '70px';
            divGlassPanel.style.visibility = 'visible';
        }
        function closeKeyPad() {
            divKeyPad.style.left = '0px';
            divKeyPad.style.top = '0px';
            divKeyPad.style.visibility = 'hidden';
            divGlassPanel.style.visibility = 'hidden';
        }
        function fullScreen(b_fs) {
            bFullScreen = b_fs;
            if (tsk_utils_have_webrtc4native() && bFullScreen && videoRemote.webkitSupportsFullscreen) {
                if (bFullScreen) {
                    videoRemote.webkitEnterFullScreen();
                }
                else {
                    videoRemote.webkitExitFullscreen();
                }
            }
            else {
                if (tsk_utils_have_webrtc4npapi()) {
                    try { if (window.__o_display_remote) window.__o_display_remote.setFullScreen(b_fs); }
                    catch (e) { divVideo.setAttribute("class", b_fs ? "full-screen" : "normal-screen"); }
                }
                else {
                    divVideo.setAttribute("class", b_fs ? "full-screen" : "normal-screen");
                }
            }
        }
        function showNotifICall(s_number) {
            // permission already asked when we registered
            if (window.webkitNotifications && window.webkitNotifications.checkPermission() == 0) {
                if (oNotifICall) {
                    oNotifICall.cancel();
                }
                oNotifICall = window.webkitNotifications.createNotification('images/sipml-34x39.png', 'Incaming call', 'Incoming call from ' + s_number);
                oNotifICall.onclose = function () { oNotifICall = null; };
                oNotifICall.show();
            }
        }
        function onKeyUp(evt) {
            evt = (evt || window.event);
            if (evt.keyCode == 27) {
                fullScreen(false);
            }
            else if (evt.ctrlKey && evt.shiftKey) { // CTRL + SHIFT
                if (evt.keyCode == 65 || evt.keyCode == 86) { // A (65) or V (86)
                    bDisableVideo = (evt.keyCode == 65);
                    txtCallStatus.innerHTML = '<i>Video ' + (bDisableVideo ? 'disabled' : 'enabled') + '</i>';
                    window.localStorage.setItem('org.doubango.expert.disable_video', bDisableVideo);
                }
            }
        }
        function onDivCallCtrlMouseMove(evt) {
            try { // IE: DOM not ready
                if (tsk_utils_have_stream()) {
                    btnCall.disabled = (!tsk_utils_have_stream() || !oSipSessionRegister || !oSipSessionRegister.is_connected());
                    document.getElementById("divCallCtrl").onmousemove = null; // unsubscribe
                }
            }
            catch (e) { }
        }
        function uiOnConnectionEvent(b_connected, b_connecting) { // should be enum: connecting, connected, terminating, terminated
            btnRegister.disabled = b_connected || b_connecting;
            btnUnRegister.disabled = !b_connected && !b_connecting;
            btnCall.disabled = !(b_connected && tsk_utils_have_webrtc() && tsk_utils_have_stream());
            btnHangUp.disabled = !oSipSessionCall;
        }
        function uiVideoDisplayEvent(b_local, b_added) {
            var o_elt_video = b_local ? videoLocal : videoRemote;
            if (b_added) {
                o_elt_video.style.opacity = 1;
                uiVideoDisplayShowHide(true);
            }
            else {
                o_elt_video.style.opacity = 0;
                fullScreen(false);
            }
        }
        function uiVideoDisplayShowHide(b_show) {
            if (b_show) {
                tdVideo.style.height = '340px';
                divVideo.style.height = navigator.appName == 'Microsoft Internet Explorer' ? '100%' : '340px';
            }
            else {
                tdVideo.style.height = '0px';
                divVideo.style.height = '0px';
            }
            btnFullScreen.disabled = !b_show;
        }
        function uiDisableCallOptions() {
            if (window.localStorage) {
                window.localStorage.setItem('org.doubango.expert.disable_callbtn_options', 'true');
                uiBtnCallSetText('Call');
                alert('Use expert view to enable the options again (/!\\requires re-loading the page)');
            }
        }
        function uiBtnCallSetText(s_text) {
            switch (s_text) {
                case "Call":
                {
                    var bDisableCallBtnOptions = (window.localStorage && window.localStorage.getItem('org.doubango.expert.disable_callbtn_options') == "true");
                    btnCall.value = btnCall.innerHTML = bDisableCallBtnOptions ? 'Call' : 'Call <span id="spanCaret" class="caret">';
                    btnCall.setAttribute("class", bDisableCallBtnOptions ? "btn btn-primary" : "btn btn-primary dropdown-toggle");
                    btnCall.onclick = bDisableCallBtnOptions ? function () { sipCall(bDisableVideo ? 'call-audio' : 'call-audiovideo'); } : null;
                    ulCallOptions.style.visibility = bDisableCallBtnOptions ? "hidden" : "visible";
                    if (!bDisableCallBtnOptions && ulCallOptions.parentNode != divBtnCallGroup) {
                        divBtnCallGroup.appendChild(ulCallOptions);
                    }
                    else if (bDisableCallBtnOptions && ulCallOptions.parentNode == divBtnCallGroup) {
                        document.body.appendChild(ulCallOptions);
                    }
                    break;
                }
                default:
                {
                    btnCall.value = btnCall.innerHTML = s_text;
                    btnCall.setAttribute("class", "btn btn-primary");
                    btnCall.onclick = function () { sipCall(bDisableVideo ? 'call-audio' : 'call-audiovideo'); };
                    ulCallOptions.style.visibility = "hidden";
                    if (ulCallOptions.parentNode == divBtnCallGroup) {
                        document.body.appendChild(ulCallOptions);
                    }
                    break;
                }
            }
        }
        function uiCallTerminated(s_description) {
            uiBtnCallSetText("Call");
            btnHangUp.value = 'HangUp';
            btnHoldResume.value = 'hold';
            btnMute.value = "Mute";
            btnCall.disabled = false;
            btnHangUp.disabled = true;
            if (window.btnBFCP) window.btnBFCP.disabled = true;
            oSipSessionCall = null;
            stopRingbackTone();
            stopRingTone();
            txtCallStatus.innerHTML = "<i>" + s_description + "</i>";
            uiVideoDisplayShowHide(false);
            divCallOptions.style.opacity = 0;
            if (oNotifICall) {
                oNotifICall.cancel();
                oNotifICall = null;
            }
            uiVideoDisplayEvent(false, false);
            uiVideoDisplayEvent(true, false);
            setTimeout(function () { if (!oSipSessionCall) txtCallStatus.innerHTML = ''; }, 2500);
        }
        // Callback function for SIP Stacks
        function onSipEventStack(e /*SIPml.Stack.Event*/) {
            tsk_utils_log_info('==stack event = ' + e.type);
            switch (e.type) {
                case 'started':
                {
                    // catch exception for IE (DOM not ready)
                    try {
                        // LogIn (REGISTER) as soon as the stack finish starting
                        oSipSessionRegister = this.newSession('register', {
                            expires: 200,
                            events_listener: { events: '*', listener: onSipEventSession },
                            sip_caps: [
                                { name: '+g.oma.sip-im', value: null },
                                //{ name: '+sip.ice' }, // rfc5768: FIXME doesn't work with Polycom TelePresence
                                { name: '+audio', value: null },
                                { name: 'language', value: '\"en,fr\"' }
                            ]
                        });
                        oSipSessionRegister.register();
                    }
                    catch (e) {
                        txtRegStatus.value = txtRegStatus.innerHTML = "<b>1:" + e + "</b>";
                        btnRegister.disabled = false;
                    }
                    break;
                }
                case 'stopping': case 'stopped': case 'failed_to_start': case 'failed_to_stop':
            {
                var bFailure = (e.type == 'failed_to_start') || (e.type == 'failed_to_stop');
                oSipStack = null;
                oSipSessionRegister = null;
                oSipSessionCall = null;
                uiOnConnectionEvent(false, false);
                stopRingbackTone();
                stopRingTone();
                uiVideoDisplayShowHide(false);
                divCallOptions.style.opacity = 0;
                txtCallStatus.innerHTML = '';
                txtRegStatus.innerHTML = bFailure ? "<i>Disconnected: <b>" + e.description + "</b></i>" : "<i>Disconnected</i>";
                break;
            }
                case 'i_new_call':
                {
                    if (oSipSessionCall) {
                        // do not accept the incoming call if we're already 'in call'
                        e.newSession.hangup(); // comment this line for multi-line support
                    }
                    else {
                        oSipSessionCall = e.newSession;
                        // start listening for events
                        oSipSessionCall.setConfiguration(oConfigCall);
                        uiBtnCallSetText('Answer');
                        btnHangUp.value = 'Reject';
                        btnCall.disabled = false;
                        btnHangUp.disabled = false;
                        startRingTone();
                        var sRemoteNumber = (oSipSessionCall.getRemoteFriendlyName() || 'unknown');
                        txtCallStatus.innerHTML = "<i>Incoming call from [<b>" + sRemoteNumber + "</b>]</i>";
                        showNotifICall(sRemoteNumber);
                    }
                    break;
                }
                case 'm_permission_requested':
                {
                    divGlassPanel.style.visibility = 'visible';
                    break;
                }
                case 'm_permission_accepted':
                case 'm_permission_refused':
                {
                    divGlassPanel.style.visibility = 'hidden';
                    if (e.type == 'm_permission_refused') {
                        uiCallTerminated('Media stream permission denied');
                    }
                    break;
                }
                case 'starting': default: break;
            }
        };
        // Callback function for SIP sessions (INVITE, REGISTER, MESSAGE...)
        function onSipEventSession(e /* SIPml.Session.Event */) {
            tsk_utils_log_info('==session event = ' + e.type);
            switch (e.type) {
                case 'connecting': case 'connected':
            {
                var bConnected = (e.type == 'connected');
                if (e.session == oSipSessionRegister) {
                    uiOnConnectionEvent(bConnected, !bConnected);
                    txtRegStatus.innerHTML = "<i>" + e.description + "</i>";
                }
                else if (e.session == oSipSessionCall) {
                    btnHangUp.value = 'HangUp';
                    btnCall.disabled = true;
                    btnHangUp.disabled = false;
                    btnTransfer.disabled = false;
                    if (window.btnBFCP) window.btnBFCP.disabled = false;
                    if (bConnected) {
                        stopRingbackTone();
                        stopRingTone();
                        if (oNotifICall) {
                            oNotifICall.cancel();
                            oNotifICall = null;
                        }
                    }
                    txtCallStatus.innerHTML = "<i>" + e.description + "</i>";
                    divCallOptions.style.opacity = bConnected ? 1 : 0;
                    if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback
                        uiVideoDisplayEvent(false, true);
                        uiVideoDisplayEvent(true, true);
                    }
                }
                break;
            } // 'connecting' | 'connected'
                case 'terminating': case 'terminated':
            {
                if (e.session == oSipSessionRegister) {
                    uiOnConnectionEvent(false, false);
                    oSipSessionCall = null;
                    oSipSessionRegister = null;
                    txtRegStatus.innerHTML = "<i>" + e.description + "</i>";
                }
                else if (e.session == oSipSessionCall) {
                    uiCallTerminated(e.description);
                }
                break;
            } // 'terminating' | 'terminated'
                case 'm_stream_video_local_added':
                {
                    if (e.session == oSipSessionCall) {
                        uiVideoDisplayEvent(true, true);
                    }
                    break;
                }
                case 'm_stream_video_local_removed':
                {
                    if (e.session == oSipSessionCall) {
                        uiVideoDisplayEvent(true, false);
                    }
                    break;
                }
                case 'm_stream_video_remote_added':
                {
                    if (e.session == oSipSessionCall) {
                        uiVideoDisplayEvent(false, true);
                    }
                    break;
                }
                case 'm_stream_video_remote_removed':
                {
                    if (e.session == oSipSessionCall) {
                        uiVideoDisplayEvent(false, false);
                    }
                    break;
                }
                case 'm_stream_audio_local_added':
                case 'm_stream_audio_local_removed':
                case 'm_stream_audio_remote_added':
                case 'm_stream_audio_remote_removed':
                {
                    break;
                }
                case 'i_ect_new_call':
                {
                    oSipSessionTransferCall = e.session;
                    break;
                }
                case 'i_ao_request':
                {
                    if (e.session == oSipSessionCall) {
                        var iSipResponseCode = e.getSipResponseCode();
                        if (iSipResponseCode == 180 || iSipResponseCode == 183) {
                            startRingbackTone();
                            txtCallStatus.innerHTML = '<i>Remote ringing...</i>';
                        }
                    }
                    break;
                }
                case 'm_early_media':
                {
                    if (e.session == oSipSessionCall) {
                        stopRingbackTone();
                        stopRingTone();
                        txtCallStatus.innerHTML = '<i>Early media started</i>';
                    }
                    break;
                }
                case 'm_local_hold_ok':
                {
                    if (e.session == oSipSessionCall) {
                        if (oSipSessionCall.bTransfering) {
                            oSipSessionCall.bTransfering = false;
                            // this.AVSession.TransferCall(this.transferUri);
                        }
                        btnHoldResume.value = 'Resume';
                        btnHoldResume.disabled = false;
                        txtCallStatus.innerHTML = '<i>Call placed on hold</i>';
                        oSipSessionCall.bHeld = true;
                    }
                    break;
                }
                case 'm_local_hold_nok':
                {
                    if (e.session == oSipSessionCall) {
                        oSipSessionCall.bTransfering = false;
                        btnHoldResume.value = 'Hold';
                        btnHoldResume.disabled = false;
                        txtCallStatus.innerHTML = '<i>Failed to place remote party on hold</i>';
                    }
                    break;
                }
                case 'm_local_resume_ok':
                {
                    if (e.session == oSipSessionCall) {
                        oSipSessionCall.bTransfering = false;
                        btnHoldResume.value = 'Hold';
                        btnHoldResume.disabled = false;
                        txtCallStatus.innerHTML = '<i>Call taken off hold</i>';
                        oSipSessionCall.bHeld = false;
                        if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback yet
                            uiVideoDisplayEvent(false, true);
                            uiVideoDisplayEvent(true, true);
                        }
                    }
                    break;
                }
                case 'm_local_resume_nok':
                {
                    if (e.session == oSipSessionCall) {
                        oSipSessionCall.bTransfering = false;
                        btnHoldResume.disabled = false;
                        txtCallStatus.innerHTML = '<i>Failed to unhold call</i>';
                    }
                    break;
                }
                case 'm_remote_hold':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Placed on hold by remote party</i>';
                    }
                    break;
                }
                case 'm_remote_resume':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Taken off hold by remote party</i>';
                    }
                    break;
                }
                case 'm_bfcp_info':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = 'BFCP Info: <i>' + e.description + '</i>';
                    }
                    break;
                }
                case 'o_ect_trying':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Call transfer in progress...</i>';
                    }
                    break;
                }
                case 'o_ect_accepted':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Call transfer accepted</i>';
                    }
                    break;
                }
                case 'o_ect_completed':
                case 'i_ect_completed':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Call transfer completed</i>';
                        btnTransfer.disabled = false;
                        if (oSipSessionTransferCall) {
                            oSipSessionCall = oSipSessionTransferCall;
                        }
                        oSipSessionTransferCall = null;
                    }
                    break;
                }
                case 'o_ect_failed':
                case 'i_ect_failed':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
                        btnTransfer.disabled = false;
                    }
                    break;
                }
                case 'o_ect_notify':
                case 'i_ect_notify':
                {
                    if (e.session == oSipSessionCall) {
                        txtCallStatus.innerHTML = "<i>Call Transfer: <b>" + e.getSipResponseCode() + " " + e.description + "</b></i>";
                        if (e.getSipResponseCode() >= 300) {
                            if (oSipSessionCall.bHeld) {
                                oSipSessionCall.resume();
                            }
                            btnTransfer.disabled = false;
                        }
                    }
                    break;
                }
                case 'i_ect_requested':
                {
                    if (e.session == oSipSessionCall) {
                        var s_message = "Do you accept call transfer to [" + e.getTransferDestinationFriendlyName() + "]?";//FIXME
                        if (confirm(s_message)) {
                            txtCallStatus.innerHTML = "<i>Call transfer in progress...</i>";
                            oSipSessionCall.acceptTransfer();
                            break;
                        }
                        oSipSessionCall.rejectTransfer();
                    }
                    break;
                }
            }
        }
    </script>


<div class="container">
    <div class="row-fluid">
        <div class="span4 well">
            <!--img src="data:image/x-icon;base64,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" /-->
            <label style="width: 100%;" align="center" id="txtRegStatus">
            </label>
            <h2>
                Registration
            </h2>
            <br />
            <table style='width: 100%'>
                <tr>
                    <td>
                        <label style="height: 100%">
                            Display Name:
                        </label>
                    </td>
                    <td>
                        <input type="text" style="width: 100%; height: 100%" id="txtDisplayName" value="{{ $user->name }} {{ $user->lastname }}"
                               placeholder="e.g. {{ $user->name }}" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label style="height: 100%">
                            Private Identity<sup>*</sup>:
                        </label>
                    </td>
                    <td>
                        <input type="text" style="width: 100%; height: 100%" id="txtPrivateIdentity" value="{{ $extension->extension }}"
                               placeholder="e.g. +33600000000" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label style="height: 100%">
                            Public Identity<sup>*</sup>:
                        </label>
                    </td>
                    <td>
                        <input type="text" style="width: 100%; height: 100%" id="txtPublicIdentity" value=""
                               placeholder="sip:{{ $extension->extension }}{!! '@' !!}{{ $extension->pbx_url }}" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label style="height: 100%">Password: </label>
                    </td>
                    <td>
                        <input type="password" style="width: 100%; height: 100%" id="txtPassword" value="{{ $extension->password }}" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label style="height: 100%">Realm<sup>*</sup>:</label>
                    </td>
                    <td>
                        <input type="text" style="width: 100%; height: 100%" id="txtRealm" value="{{ $extension->pbx_url }}" placeholder="e.g. doubango.org" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                       <!-- <input type="button" class="btn btn-success" id="btnRegister" value="LogIn" disabled onclick='sipRegister();' />
                        &nbsp;
                        <input type="button" class="btn btn-danger" id="btnUnRegister" value="LogOut" disabled onclick='sipUnRegister();' /> -->
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p class="small"><sup>*</sup> <i>Mandatory Field</i></p>
                    </td>
                </tr>

                <!--
                <tr>
                    <td colspan="3">
                        <a class="btn" href="http://code.google.com/p/sipml5/wiki/Public_SIP_Servers" target="_blank">Need SIP account?</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <a class="btn" href="./expert" target="_blank">Expert mode?</a>
                    </td>
                </tr>-->
            </table>
        </div>
        <div id="divCallCtrl" class="span7 well" style='display:table-cell; vertical-align:middle'>
            <label style="width: 100%;" align="center" id="txtCallStatus">
            </label>
            <h2>
                Call control
            </h2>
            <br />
            <table style='width: 100%;'>
                <tr>
                    <td style="white-space:nowrap;">
                        <input type="text" style="width: 100%; height:100%;" id="txtPhoneNumber" value="" placeholder="Enter phone number to call" />
                    </td>
                </tr>
                <tr>
                    <td colspan="1" align="right">
                        <div class="btn-toolbar" style="margin: 0; vertical-align:middle">
                            <!--div class="btn-group">
                                <input type="button" id="btnBFCP" style="margin: 0; vertical-align:middle; height: 100%;" class="btn btn-primary" value="BFCP" onclick='sipShareScreen();' disabled />
                            </div-->
                            <div id="divBtnCallGroup" class="btn-group">
                                <button id="btnCall" disabled class="btn btn-primary" data-toggle="dropdown">Call</button>
                            </div>&nbsp;&nbsp;
                            <div class="btn-group">
                                <input type="button" id="btnHangUp" style="margin: 0; vertical-align:middle; height: 100%;" class="btn btn-primary" value="HangUp" onclick='sipHangUp();' disabled />
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td id="tdVideo" class='tab-video'>
                        <div id="divVideo" class='div-video'>
                            <div id="divVideoRemote" style='position:relative; border:1px solid #009; height:100%; width:100%; z-index: auto; opacity: 1'>
                                <video class="video" width="100%" height="100%" id="video_remote" autoplay="autoplay" style="opacity: 0;
                                        background-color: #000000; -webkit-transition-property: opacity; -webkit-transition-duration: 2s;"></video>
                            </div>

                            <div id="divVideoLocalWrapper" style="margin-left: 0px; border:0px solid #009; z-index: 1000">
                                <iframe class="previewvideo" style="border:0px solid #009; z-index: 1000"> </iframe>
                                <div id="divVideoLocal" class="previewvideo" style=' border:0px solid #009; z-index: 1000'>
                                    <video class="video" width="100%" height="100%" id="video_local" autoplay="autoplay" muted="true" style="opacity: 0;
                                            background-color: #000000; -webkit-transition-property: opacity;
                                            -webkit-transition-duration: 2s;"></video>
                                </div>
                            </div>
                            <div id="divScreencastLocalWrapper" style="margin-left: 90px; border:0px solid #009; z-index: 1000">
                                <iframe class="previewvideo" style="border:0px solid #009; z-index: 1000"> </iframe>
                                <div id="divScreencastLocal" class="previewvideo" style=' border:0px solid #009; z-index: 1000'>
                                </div>
                            </div>
                            <!--div id="div1" style="margin-left: 300px; border:0px solid #009; z-index: 1000">
                                <iframe class="previewvideo" style="border:0px solid #009; z-index: 1000"> </iframe>
                                <div id="div2" class="previewvideo" style='border:0px solid #009; z-index: 1000'>
                                  <input type="button" class="btn" style="" id="Button1" value="Button1" /> &nbsp;
                                  <input type="button" class="btn" style="" id="Button2" value="Button2" /> &nbsp;
                                </div>
                            </div-->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align='center'>
                        <div id='divCallOptions' class='call-options' style='opacity: 0; margin-top: 0px'>
                            <input type="button" class="btn" style="" id="btnFullScreen" value="FullScreen" disabled onclick='toggleFullScreen();' /> &nbsp;
                            <input type="button" class="btn" style="" id="btnMute" value="Mute" onclick='sipToggleMute();' /> &nbsp;
                            <input type="button" class="btn" style="" id="btnHoldResume" value="Hold" onclick='sipToggleHoldResume();' /> &nbsp;
                            <input type="button" class="btn" style="" id="btnTransfer" value="Transfer" onclick='sipTransfer();' /> &nbsp;
                            <input type="button" class="btn" style="" id="btnKeyPad" value="KeyPad" onclick='openKeyPad();' />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <caption>User Details</caption>
                <thead>
                <tr class="active">
                    <th>Field</th>
                    <th>Content</th>
                </tr> </thead>
                <tbody>
                <tr>
                    <th scope="row">Name</th>
                    <td>{{ $user->name }} {{ $user->lastname }}</td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th scope="row">Options</th>
                    <td><input type="button" class="btn btn-success" id="btnRegister" value="LogIn" disabled onclick='sipRegister();' />
                        &nbsp;
                        <input type="button" class="btn btn-danger" id="btnUnRegister" value="LogOut" disabled onclick='sipUnRegister();' />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
        </div>
    </div>

    <br />
    <footer>

        <p>
            &copy; Rutaip 2016 <br />
            <i>Soluciones integrales VoIP</i>
        </p>
        <!-- Creates all ATL/COM objects right now
            Will open confirmation dialogs if not already done
        -->
        <!--object id="fakeVideoDisplay" classid="clsid:5C2C407B-09D9-449B-BB83-C39B7802A684" style="visibility:hidden;"> </object-->
        <!--object id="fakeLooper" classid="clsid:7082C446-54A8-4280-A18D-54143846211A" style="visibility:visible; width:0px; height:0px"> </object-->
        <!--object id="fakeSessionDescription" classid="clsid:DBA9F8E2-F9FB-47CF-8797-986A69A1CA9C" style="visibility:hidden;"> </object-->
        <!--object id="fakeNetTransport" classid="clsid:5A7D84EC-382C-4844-AB3A-9825DBE30DAE" style="visibility:hidden;"> </object-->
        <!--object id="fakePeerConnection" classid="clsid:56D10AD3-8F52-4AA4-854B-41F4D6F9CEA3" style="visibility:hidden;"> </object-->
        <object id="fakePluginInstance" classid="clsid:69E4A9D1-824C-40DA-9680-C7424A27B6A0" style="visibility:hidden;"> </object>

        <!--
            NPAPI  browsers: Safari, Opera and Firefox
        -->
        <!--embed id="WebRtc4npapi" type="application/w4a" width='1' height='1' style='visibility:hidden;' /-->
    </footer>
</div>
<!-- /container -->
<!-- Glass Panel -->
<div id='divGlassPanel' class='glass-panel' style='visibility:hidden'></div>
<!-- KeyPad Div -->
<div id='divKeyPad' class='span2 well div-keypad' style="left:0px; top:0px; width:250; height:240; visibility:hidden">
    <table style="width: 100%; height: 100%">
        <tr><td><input type="button" style="width: 33%" class="btn" value="1" onclick="sipSendDTMF('1');" /><input type="button" style="width: 33%" class="btn" value="2" onclick="sipSendDTMF('2');" /><input type="button" style="width: 33%" class="btn" value="3" onclick="sipSendDTMF('3');" /></td></tr>
        <tr><td><input type="button" style="width: 33%" class="btn" value="4" onclick="sipSendDTMF('4');" /><input type="button" style="width: 33%" class="btn" value="5" onclick="sipSendDTMF('5');" /><input type="button" style="width: 33%" class="btn" value="6" onclick="sipSendDTMF('6');" /></td></tr>
        <tr><td><input type="button" style="width: 33%" class="btn" value="7" onclick="sipSendDTMF('7');" /><input type="button" style="width: 33%" class="btn" value="8" onclick="sipSendDTMF('8');" /><input type="button" style="width: 33%" class="btn" value="9" onclick="sipSendDTMF('9');" /></td></tr>
        <tr><td><input type="button" style="width: 33%" class="btn" value="*" onclick="sipSendDTMF('*');" /><input type="button" style="width: 33%" class="btn" value="0" onclick="sipSendDTMF('0');" /><input type="button" style="width: 33%" class="btn" value="#" onclick="sipSendDTMF('#');" /></td></tr>
        <tr><td colspan=3><input type="button" style="width: 100%" class="btn btn-medium btn-danger" value="close" onclick="closeKeyPad();" /></td></tr>
    </table>
</div>
<!-- Call button options -->
<ul id="ulCallOptions" class="dropdown-menu" style="visibility:hidden">
    <li><a href="#" onclick='sipCall("call-audio");'>Audio</a></li>
    <li><a href="#" onclick='sipCall("call-audiovideo");'>Video</a></li>
    <li id='liScreenShare'><a href="#" onclick='sipShareScreen();'>Screen Share</a></li>
    <li class="divider"></li>
    <li><a href="#" onclick='uiDisableCallOptions();'><b>Disable these options</b></a></li>
</ul>

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{!! Html::script('assets/js/jquery.js') !!}
{!! Html::script('assets/js/bootstrap-transition.js') !!}
{!! Html::script('assets/js/bootstrap-alert.js') !!}
{!! Html::script('assets/js/bootstrap-modal.js') !!}
{!! Html::script('assets/js/bootstrap-dropdown.js') !!}
{!! Html::script('assets/js/bootstrap-scrollspy.js') !!}
{!! Html::script('assets/js/bootstrap-tab.js') !!}
{!! Html::script('assets/js/bootstrap-tooltip.js') !!}
{!! Html::script('assets/js/bootstrap-popover.js') !!}
{!! Html::script('assets/js/bootstrap-button.js') !!}
{!! Html::script('assets/js/bootstrap-collapse.js') !!}
{!! Html::script('assets/js/bootstrap-carousel.js') !!}
{!! Html::script('assets/js/bootstrap-typeahead.js') !!}


<!-- Audios -->
<audio id="audio_remote" autoplay="autoplay"> </audio>
<audio id="ringtone" loop src="assets/sounds/ringtone.wav"> </audio>
<audio id="ringbacktone" loop src="assets/sounds/ringbacktone.wav"> </audio>
<audio id="dtmfTone" src="assets/sounds/dtmf.wav"> </audio>
@stop