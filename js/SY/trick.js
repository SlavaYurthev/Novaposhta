Ajax.Responders.register({
    onComplete: function() {
    	novaposhta.init();
    }
});
window.onload = function(){
    novaposhta.init();
}