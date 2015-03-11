var app = {

    myScroll: false,

    initialize: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
   
    onDeviceReady: function() {
        app.fastClick();
        if(app.isFirstRun())
            app.showTutorial();
    },

    showTutorial: function () {
        
        app.myScroll = new IScroll('#wrapper', {
            scrollX: true,
            scrollY: false,
            momentum: false,
            snap: true,
            snapSpeed: 400,
            keyBindings: true,
            indicators: {
                el: document.getElementById('indicator'),
                resize: false
            }
        });
        document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
        
    },
   
    isFirstRun: function(){
        return true;
        if(!window.localStorage.getItem("firstRun"))
        {
            window.localStorage.setItem("firstRun", true);
            return true;
        }
        return false;
    },

    fastClick: function(){
       FastClick.attach(document.body); 
    }

};